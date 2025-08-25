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
    // Get user's orders
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::with(['orderItems.product'])->where('user_id', $user->id);

        if ($request->has('status')) {
            $query->where('order_status', $request->status);
        }

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
    }

    // Create a new orderpublic 
    public function store(Request $request)
{
    $user = $request->user();

    $validator = Validator::make($request->all(), [
        'payment_method' => 'required|in:cod,online',
        'delivery_charge' => 'nullable|numeric|min:0',
        'order_notes' => 'nullable|string',
        'number_of_boxes' => 'nullable|integer|min:1',
        'receiver_name' => 'nullable|string',
        'receiver_phone' => 'nullable|string',
        'delivery_date' => 'nullable|date',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'delivery_time' => 'nullable|string',
        'delivery_address' => 'nullable|string',
        'delivery_city' => 'nullable|string',
        'delivery_state' => 'nullable|string',
        'delivery_pincode' => 'nullable|string',
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
        $delivery_charge = $request->delivery_charge ?? 0;
        $subtotal = 0;   // default 0
        $total_amount = $subtotal + $delivery_charge;

        $order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'ORD-' . time() . '-' . $user->id,
            'customer_name' => $user->full_name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'delivery_address' => $request->delivery_address ?? '',
            'delivery_city' => $request->delivery_city ?? '',
            'delivery_state' => $request->delivery_state ?? '',
            'delivery_pincode' => $request->delivery_pincode ?? '',
            'subtotal' => $subtotal,
            'delivery_charge' => $delivery_charge,
            'total_amount' => $total_amount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'order_notes' => $request->order_notes ?? null,
            'number_of_boxes' => $request->number_of_boxes ?? 1,
            'receiver_name' => $request->receiver_name ?? '',
            'receiver_phone' => $request->receiver_phone ?? '',
            'delivery_date' => $request->delivery_date ?? null,
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
            'delivery_time' => $request->delivery_time ?? null
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Order created successfully',
            'data' => [
                'order' => $order,
                'order_code' => $order->order_code
            ]
        ], 201);

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong!',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Get order details
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

        return response()->json([
            'status' => true,
            'message' => 'Order details retrieved successfully',
            'data' => $order
        ]);
    }

    // Cancel order
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

        if (!in_array($order->order_status, ['pending', 'confirmed'])) {
            return response()->json([
                'status' => false,
                'message' => 'Order cannot be cancelled at this stage',
            ], 400);
        }

        $order->update(['order_status' => 'cancelled']);

        return response()->json([
            'status' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order
        ]);
    }

    // Track order
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

        $tracking = [
            'order_code' => $order->order_code,
            'status' => $order->order_status,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'delivery_date' => $order->delivery_date ?? null,
            'delivered_at' => $order->delivered_at ?? null,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Order tracking information retrieved successfully',
            'data' => $tracking
        ]);
    }
}
