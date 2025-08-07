<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $query = Order::with(['items.product', 'items.product.category', 'items.product.subcategory'])
                ->where('user_id', $user->id);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Sort orders
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 10);
            $orders = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Orders retrieved successfully',
                'data' => [
                    'orders' => $orders->items(),
                    'pagination' => [
                        'current_page' => $orders->currentPage(),
                        'last_page' => $orders->lastPage(),
                        'per_page' => $orders->perPage(),
                        'total' => $orders->total(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'shipping_address' => 'required|string',
                'shipping_city' => 'required|string',
                'shipping_state' => 'required|string',
                'shipping_pincode' => 'required|string',
                'shipping_phone' => 'required|string',
                'payment_method' => 'required|in:cod,online',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Calculate order total
                $total = 0;
                $items = [];

                foreach ($request->items as $item) {
                    $product = Product::find($item['product_id']);
                    
                    if (!$product || !$product->status) {
                        throw new \Exception("Product {$product->name} is not available");
                    }

                    $price = $product->discount_price ?? $product->price;
                    $itemTotal = $price * $item['quantity'];
                    $total += $itemTotal;

                    $items[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $price,
                        'total' => $itemTotal
                    ];
                }

                // Create order
                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . time() . '-' . $user->id,
                    'total_amount' => $total,
                    'shipping_address' => $request->shipping_address,
                    'shipping_city' => $request->shipping_city,
                    'shipping_state' => $request->shipping_state,
                    'shipping_pincode' => $request->shipping_pincode,
                    'shipping_phone' => $request->shipping_phone,
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                    'status' => 'pending',
                    'notes' => $request->notes
                ]);

                // Create order items
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['total']
                    ]);
                }

                DB::commit();

                // Load order with items and products
                $order->load(['items.product', 'items.product.category', 'items.product.subcategory']);

                return response()->json([
                    'status' => true,
                    'message' => 'Order created successfully',
                    'data' => [
                        'order' => $order,
                        'order_number' => $order->order_number
                    ]
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order details
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            $order = Order::with(['items.product', 'items.product.category', 'items.product.subcategory'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Order details retrieved successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, $id)
    {
        try {
            $user = $request->user();

            $order = Order::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            // Check if order can be cancelled
            if (!in_array($order->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order cannot be cancelled at this stage',
                ], 400);
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Order cancelled successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order by order number
     */
    public function getByOrderNumber(Request $request, $orderNumber)
    {
        try {
            $user = $request->user();

            $order = Order::with(['items.product', 'items.product.category', 'items.product.subcategory'])
                ->where('order_number', $orderNumber)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Order details retrieved successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order statistics
     */
    public function statistics(Request $request)
    {
        try {
            $user = $request->user();

            $stats = [
                'total_orders' => Order::where('user_id', $user->id)->count(),
                'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
                'confirmed_orders' => Order::where('user_id', $user->id)->where('status', 'confirmed')->count(),
                'shipped_orders' => Order::where('user_id', $user->id)->where('status', 'shipped')->count(),
                'delivered_orders' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
                'cancelled_orders' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count(),
                'total_spent' => Order::where('user_id', $user->id)->where('status', 'delivered')->sum('total_amount'),
            ];

            return response()->json([
                'status' => true,
                'message' => 'Order statistics retrieved successfully',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track order
     */
    public function track(Request $request, $id)
    {
        try {
            $user = $request->user();

            $order = Order::with(['items.product'])
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            $tracking = [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'estimated_delivery' => $order->estimated_delivery,
                'tracking_number' => $order->tracking_number,
                'tracking_url' => $order->tracking_url,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Order tracking information retrieved successfully',
                'data' => $tracking
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 