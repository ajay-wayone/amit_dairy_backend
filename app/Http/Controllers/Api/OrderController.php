<?php

namespace App\Http\Controllers\Api;
use Razorpay\Api\Api;
use App\Services\GatewayService;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Notifications\UserNotification;

class OrderController extends Controller
{
    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::with(['orderItems.product'])->where('user_id', $user->id);

        if ($request->has('id')) {
            $query->where('id', $request->id);
        }

        if ($request->has('status')) {
            $query->where('order_status', $request->status);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 10);
        $orders = $query->paginate($perPage);

        // ✅ Decode cart_data and attach image URLs
        $orders->getCollection()->transform(function ($order) {
            $cartData = json_decode($order->cart_data, true) ?? [];

            foreach ($cartData as &$item) {
                if (isset($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    $item['product_image'] = $product && $product->image
                        ? url($product->image)
                        : null;
                }
            }

            $order->cart_data = $cartData;
            return $order;
        });

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
    }

    /**
     * Create a new order
     */
    // public function store(Request $request)
    // {
    //     $user = $request->user();

    //     $validator = Validator::make($request->all(), [
    //         'payment_method' => 'required|in:cod,online,card',
    //         'delivery_charge' => 'nullable|numeric|min:0',
    //         'subtotal' => 'nullable|numeric|min:0',
    //         'total_amount' => 'nullable|numeric|min:0',
    //         'order_notes' => 'nullable|string',
    //         'number_of_boxes' => 'nullable|integer|min:1',
    //         'receiver_name' => 'nullable|string',
    //         'receiver_phone' => 'nullable|string',
    //         'delivery_time' => 'nullable|string',
    //         'delivery_address' => 'nullable|string',
    //         'delivery_city' => 'nullable|string',
    //         'delivery_state' => 'nullable|string',
    //         'delivery_pincode' => 'nullable|string',
    //         'cart_data' => 'required|array',
    //         'house_block' => 'nullable|string',
    //         'area_road' => 'nullable|string',
    //         'save_as' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $subtotal = $request->subtotal ?? 0;
    //         $delivery_charge = $request->delivery_charge ?? 0;
    //         $total_amount = $request->total_amount ?? ($subtotal + $delivery_charge);

    //         // ✅ Prepare cart data with product images
    //         $cartDataWithImages = [];
    //         foreach ($request->cart_data as $item) {
    //             $product = Product::find($item['product_id']);
    //             if ($product) {
    //                 $cartDataWithImages[] = [
    //                     'product_id' => $product->id,
    //                     'product_name' => $product->name,
    //                     'quantity' => $item['quantity'],
    //                     'price' => $item['price'],
    //                     'total' => $item['price'] * $item['quantity'],
    //                     'product_image' => $product->image ? url($product->image) : null,
    //                     'product_sku' => $product->sku ?? null,
    //                 ];
    //             }
    //         }

    //         // Create order
    //         $order = Order::create([
    //             'user_id' => $user->id,
    //             'order_code' => 'ORD-' . time() . '-' . $user->id,
    //             'order_id' => 'ORD-' . time() . '-' . $user->id,
    //             'customer_name' => $request->customer_name ?? $user->name,
    //             'customer_email' => $request->customer_email ?? $user->email,
    //             'customer_phone' => $request->customer_phone ?? $user->phone,
    //             'delivery_address' => $request->delivery_address ?? '',
    //             'delivery_city' => $request->delivery_city ?? '',
    //             'delivery_state' => $request->delivery_state ?? '',
    //             'delivery_pincode' => $request->delivery_pincode ?? '',
    //             'subtotal' => $subtotal,
    //             'delivery_charge' => $delivery_charge,
    //             'total_amount' => $total_amount,
    //             'payment_method' => $request->payment_method,
    //             'payment_status' => $request->payment_status ?? 'pending',
    //             'order_status' => $request->order_status ?? 'pending',
    //             'order_notes' => $request->order_notes ?? null,
    //             'number_of_boxes' => $request->number_of_boxes ?? 1,
    //             'cart_data' => json_encode($cartDataWithImages),
    //             'address_details' => $request->address_details ?? null,
    //             'house_block' => $request->house_block ?? null,
    //             'area_road' => $request->area_road ?? null,
    //             'save_as' => $request->save_as ?? null,
    //             'receiver_name' => $request->receiver_name ?? $user->name,
    //             'receiver_phone' => $request->receiver_phone ?? $user->phone,
    //             'delivery_time' => $request->delivery_time ?? null,
    //             'delivered_at' => $request->delivered_at ?? null,
    //         ]);

    //         // ✅ Save order items
    //         foreach ($cartDataWithImages as $item) {
    //             OrderItem::create([
    //                 'order_id' => $order->id,
    //                 'user_id' => $user->id,
    //                 'product_id' => $item['product_id'],
    //                 'product_name' => $item['product_name'],
    //                 'product_sku' => $item['product_sku'] ?? null,
    //                 'price' => $item['price'],
    //                 'quantity' => $item['quantity'],
    //                 'total' => $item['total'],
    //             ]);
    //         }

    //         $this->notifyAdmins($order);

    //         DB::commit();

    //         $order->cart_data = $cartDataWithImages;

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Order created successfully',
    //             'data' => [
    //                 'order' => $order->load('orderItems.product'),
    //                 'order_code' => $order->order_code
    //             ]
    //         ], 201);

    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong!',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function store(Request $request)
    {
        \Log::info('OrderController store called', [
            'headers' => $request->headers->all(),
            'bearer_token' => $request->bearerToken(),
            'user_agent' => $request->userAgent(),
        ]);

        $user = $request->user();
        file_put_contents(base_path('request_debug.log'), json_encode([
            'time' => date('Y-m-d H:i:s'),
            'user' => $user ? $user->id : 'null',
            'request' => $request->all()
        ], JSON_PRETTY_PRINT) . "\n", FILE_APPEND);

        if (!$user) {
            \Log::error('Authentication failed: No user found');
            return response()->json([
                'status' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:cod,online,card',
            'cart_data' => 'required|array',
            'delivery_address' => 'nullable|string',
            'delivery_city' => 'nullable|string',
            'delivery_state' => 'nullable|string',
            'delivery_pincode' => 'nullable|string',
            'address_details' => 'nullable|string',
            'house_block' => 'nullable|string',
            'area_road' => 'nullable|string',
            'save_as' => 'nullable|string',
            'order_notes' => 'nullable|string',
            'number_of_boxes' => 'nullable|integer',
            'receiver_name' => 'nullable|string',
            'receiver_phone' => 'nullable|string',
            'delivery_time' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'razorpay_payment_id' => 'nullable|string',
            'razorpay_signature' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            // 🟢 Calculate totals and prepare cart data
            $subtotal = 0;
            $cartDataWithImages = [];
            foreach ($request->cart_data as $key => $item) {
                // Determine product ID (handle associative or sequential array)
                $productId = $item['product_id'] ?? null;
                if (!$productId) continue;

                $product = Product::find($productId);
                if ($product) {
                    // Handle both 'price' and 'product_price' field names
                    $itemPrice = $item['price'] ?? $item['product_price'] ?? $product->price ?? 0;
                    $itemQty = $item['quantity'] ?? 1;
                    $itemTotal = $itemPrice * $itemQty;
                    
                    $subtotal += $itemTotal;
                    
                    $cartDataWithImages[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $itemQty,
                        'price' => $itemPrice,
                        'total' => $itemTotal,
                        'product_image' => $product->image ? url($product->image) : null,
                        'product_sku' => $product->sku ?? null,
                    ];
                }
            }

            // Use provided subtotal/total if sent, otherwise use calculated ones
            $subtotal = $request->subtotal ?? $subtotal;
            $delivery_charge = $request->delivery_charge ?? 0;
            $total_amount = $subtotal + $delivery_charge;

            // 🔴 Razorpay Order Create (Only Online)
            $razorpayOrderId = null;

            if (in_array($request->payment_method, ['online', 'card'])) {
                \Log::info('Creating Razorpay order', [
                    'payment_method' => $request->payment_method,
                    'total_amount' => $total_amount,
                    'razorpay_key' => config('services.razorpay.key') ? 'SET' : 'NOT SET',
                    'razorpay_secret' => config('services.razorpay.secret') ? 'SET' : 'NOT SET',
                ]);

                try {
                    $api = GatewayService::getRazorpayApi();

                    $razorpayOrder = $api->order->create([
                        'receipt' => 'order_rcpt_' . time(),
                        'amount' => $total_amount * 100, // paise
                        'currency' => 'INR'
                    ]);

                    $razorpayOrderId = $razorpayOrder['id'];
                    \Log::info('Razorpay order created successfully', ['razorpay_order_id' => $razorpayOrderId]);
                } catch (\Exception $e) {
                    \Log::error('Razorpay order creation failed', [
                        'error' => $e->getMessage(),
                        'payment_method' => $request->payment_method,
                    ]);
                    throw $e;
                }
            }

            // 🟢 Create Order
            \Log::info('About to create order with data', [
                'user_id' => $user->id,
                'customer_id' => $user->id,
                'order_code' => 'ORD-' . time(),
                'order_id' => 'ORD-' . time() . '-' . $user->id,
                'delivery_address' => $request->delivery_address ?? '',
                'delivery_city' => $request->delivery_city ?? '',
                'delivery_state' => $request->delivery_state ?? '',
                'delivery_pincode' => $request->delivery_pincode ?? '',
                'address_details' => $request->address_details ?? null,
                'house_block' => $request->house_block ?? null,
                'area_road' => $request->area_road ?? null,
                'save_as' => $request->save_as ?? null,
                'order_notes' => $request->order_notes ?? null,
                'number_of_boxes' => $request->number_of_boxes ?? null,
                'receiver_name' => $request->receiver_name ?? null,
                'receiver_phone' => $request->receiver_phone ?? null,
                'delivery_time' => $request->delivery_time ?? null,
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
                'razorpay_payment_id' => $request->razorpay_payment_id ?? '',
                'razorpay_signature' => $request->razorpay_signature ?? '',
            ]);
            $order = Order::create([
                'user_id' => $user->id,
                'customer_id' => $user->id, // Assuming user and customer are the same
                'order_code' => 'ORD-' . time(),
                'order_id' => 'ORD-' . time() . '-' . $user->id,
                'customer_name' => $request->customer_name ?? $user->full_name ?? $user->name ?? 'Customer',
                'customer_email' => $request->customer_email ?? $user->email ?? 'N/A',
                'customer_phone' => $request->customer_phone ?? $user->phone ?? 'N/A',
                'delivery_address' => $request->delivery_address ?? (($request->house_block ? $request->house_block . ', ' : '') . ($request->area_road ? $request->area_road . ', ' : '') . ($request->address_details ?? '')),
                'delivery_city' => $request->delivery_city ?? '',
                'delivery_state' => $request->delivery_state ?? '',
                'delivery_pincode' => $request->delivery_pincode ?? '',
                'subtotal' => $subtotal,
                'delivery_charge' => $delivery_charge,
                'total_amount' => $total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'cart_data' => json_encode($cartDataWithImages),
                'address_details' => $request->address_details ?? null,
                'house_block' => $request->house_block ?? null,
                'area_road' => $request->area_road ?? null,
                'save_as' => $request->save_as ?? null,
                'order_notes' => $request->order_notes ?? null,
                'number_of_boxes' => $request->number_of_boxes ?? null,
                'receiver_name' => $request->receiver_name ?? null,
                'receiver_phone' => $request->receiver_phone ?? null,
                'delivery_time' => $request->delivery_time ?? null,
                'latitude' => $request->latitude ?? null,
                'longitude' => $request->longitude ?? null,
                'delivery_date' => $request->delivery_date ?? now()->addDay(), 
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $request->razorpay_payment_id ?? '',
                'razorpay_signature' => $request->razorpay_signature ?? '',
            ]);

            // ðŸŸ¢ Save order items
            foreach ($cartDataWithImages as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created',
                'data' => [
                    'order_id' => $order->id,
                    'razorpay_order_id' => $razorpayOrderId,
                    'amount' => $total_amount,
                    'currency' => 'INR'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? null,
                'payment_method' => $request->payment_method ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get order details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::with('orderItems.product')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $cartData = json_decode($order->cart_data, true) ?? [];
        foreach ($cartData as &$item) {
            $product = Product::find($item['product_id']);
            $item['product_image'] = $product && $product->image ? url($product->image) : null;
        }
        $order->cart_data = $cartData;

        return response()->json([
            'status' => true,
            'message' => 'Order details retrieved successfully',
            'data' => $order
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->order_status === 'cancelled') {
            return response()->json([
                'status' => false,
                'message' => 'Order already cancelled',
            ], 400);
        }

        $order->order_status = 'cancelled';
        $order->save();

        return response()->json([
            'status' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order
        ]);
    }

    /**
     * Track order
     */
    public function track(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::with('orderItems.product')
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
            'message' => 'Order tracking details',
            'data' => [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at->toDateTimeString(),
                'updated_at' => $order->updated_at->toDateTimeString(),
            ]
        ]);
    }

    protected function notifyAdmins(Order $order)
    {
        try {
            $admins = User::where('role', 'admin')->where('is_active', true)->get();

            foreach ($admins as $admin) {
                $admin->notify(new UserNotification(
                    'New Order Placed',
                    'A new order has been placed by ' . $order->customer_name,
                    ['order_id' => $order->id, 'type' => 'new_order']
                ));
            }
        } catch (\Exception $e) {
            \Log::error('Order notification failed: ' . $e->getMessage());
        }
    }
}
