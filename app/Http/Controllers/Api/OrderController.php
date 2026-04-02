<?php

namespace App\Http\Controllers\Api;
use Razorpay\Api\Api;
use App\Services\GatewayService;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PaymentSlab;
use App\Models\Box;
use App\Models\User;
use App\Models\Offer;
use App\Models\CouponUsage;
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
            // Robust check for cart_data (handle double-encoded JSON if it persists)
            $cartData = $order->cart_data;
            if (is_string($cartData)) {
                $cartData = json_decode($cartData, true);
            }
            $cartData = is_array($cartData) ? $cartData : [];

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

    public function buyNow(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cod,online,card',
            'delivery_address' => 'nullable|string',
            'delivery_city' => 'nullable|string',
            'delivery_state' => 'nullable|string',
            'delivery_pincode' => 'nullable|string',
            'address_details' => 'nullable|string',
            'house_block' => 'nullable|string',
            'area_road' => 'nullable|string',
            'order_notes' => 'nullable|string',
            'number_of_boxes' => 'nullable|integer',
            'receiver_name' => 'nullable|string',
            'receiver_phone' => 'nullable|string',
            'delivery_time' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'box_id' => 'nullable|exists:boxes,id',
            'box_qty' => 'nullable|integer|min:1',
            'custom_text' => 'nullable|string',
            'coupon_code' => 'nullable|string|exists:offers,coupon_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        // 🟢 Calculate total weight
        $itemWeight = $product->weight ?? 0;
        $weightType = strtolower($product->weight_type ?? 'kg');
        
        // Convert to KG if in grams
        if ($weightType == 'g' || $weightType == 'gram') {
            $itemWeight = $itemWeight / 1000;
        }
        
        $totalWeightKg = $itemWeight * $request->quantity;

        // 🟢 Apply Slab Charging Logic
        $advancePercentage = 100; // Default: 100% if no slab matches

        $slab = PaymentSlab::where('min_km', '<=', $totalWeightKg)
            ->where('max_km', '>=', $totalWeightKg)
            ->where('status', 1)
            ->first();

        if ($slab) {
            $advancePercentage = $slab->advance_percentage;
        }

        $subtotal = ($product->discount_price ?? $product->price) * $request->quantity;

        // 🟡 Handle Mithai Box (if selected)
        $boxId = $request->box_id;
        $boxName = null;
        $boxPrice = 0;
        $boxQty = $request->box_qty ?? 0;
        $customText = $request->custom_text;
        $boxImage = null;

        if ($boxId) {
            $box = Box::find($boxId);
            if ($box) {
                $boxName = $box->box_name;
                $boxPrice = $box->box_price;
                $boxImage = $box->box_image;
                // If box_qty is not provided, default to 1
                $boxQty = $boxQty > 0 ? $boxQty : 1;
            }
        }

        $boxTotal = $boxPrice * $boxQty;
        $itemTotal = $subtotal + $boxTotal;

        $delivery_charge = $request->delivery_charge ?? 0;
        $total_amount = $itemTotal + $delivery_charge;
        
        // 🎫 Coupon Logic
        $discountAmount = 0;
        $couponId = null;
        if ($request->coupon_code) {
            $offer = Offer::where('coupon_code', $request->coupon_code)->where('status', 1)->first();
            if ($offer) {
                // Check usage
                $usageCheck = CouponUsage::where('user_id', $user->id)->where('offer_id', $offer->id)->count();
                if ($usageCheck == 0) {
                    $discountAmount = ($total_amount * $offer->discount_percentage) / 100;
                    if ($discountAmount > $offer->max_discount) {
                        $discountAmount = $offer->max_discount;
                    }
                    $couponId = $offer->id;
                    $total_amount -= $discountAmount;
                }
            }
        }

        $advance_amount = ($total_amount * $advancePercentage) / 100;

        DB::beginTransaction();

        try {
            // 🔴 Razorpay Order Create (Only Online)
            $razorpayOrderId = null;
            $chargeAmount = $advance_amount; // Amount to be charged now

            if (in_array($request->payment_method, ['online', 'card'])) {
                $api = GatewayService::getRazorpayApi();
                $razorpayOrder = $api->order->create([
                    'receipt' => 'bn_rcpt_' . time(),
                    'amount' => round($chargeAmount * 100), // paise
                    'currency' => 'INR'
                ]);
                $razorpayOrderId = $razorpayOrder['id'];
            }

            // 🟢 Create Order
            $cartData = [[
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $request->quantity,
                'price' => $product->discount_price ?? $product->price,
                'total' => $itemTotal,
                'product_image' => $product->image ? url($product->image) : null,
                'product_sku' => $product->sku ?? null,
                'box_id' => $boxId,
                'box_name' => $boxName,
                'box_price' => $boxPrice,
                'box_qty' => $boxQty,
                'box_image' => $boxImage ? url($boxImage) : null,
                'custom_text' => $customText,
            ]];

            $order = Order::create([
                'user_id' => $user->id,
                'customer_id' => $user->id,
                'order_code' => 'ORD-BN-' . time(),
                'order_id' => 'ORD-BN-' . time() . '-' . $user->id,
                'customer_name' => $request->customer_name ?? $user->full_name ?? $user->name ?? 'Customer',
                'customer_email' => $request->customer_email ?? $user->email ?? 'N/A',
                'customer_phone' => $request->customer_phone ?? $user->phone ?? 'N/A',
                'delivery_address' => $request->delivery_address ?? (($request->house_block ? $request->house_block . ', ' : '') . ($request->area_road ? $request->area_road . ', ' : '') . ($request->address_details ?? '')),
                'delivery_city' => $request->delivery_city ?? '',
                'delivery_state' => $request->delivery_state ?? '',
                'delivery_pincode' => $request->delivery_pincode ?? '',
                'coupon_id' => $couponId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'delivery_charge' => $delivery_charge,
                'total_amount' => $total_amount,
                'advance_amount' => $advance_amount,
                'total_weight_kg' => $totalWeightKg,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'cart_data' => $cartData,
                'address_details' => $request->address_details ?? '',
                'house_block' => $request->house_block ?? '',
                'area_road' => $request->area_road ?? '',
                'save_as' => $request->save_as ?? '',
                'order_notes' => $request->order_notes ?? '',
                'number_of_boxes' => $request->number_of_boxes ?? null,
                'receiver_name' => $request->receiver_name ?? '',
                'receiver_phone' => $request->receiver_phone ?? '',
                'delivery_time' => $request->delivery_time ?? null,
                'latitude' => $request->latitude ?? '',
                'longitude' => $request->longitude ?? '',
                'delivery_date' => $request->delivery_date ?? @now()->addDay(), 
                'razorpay_order_id' => $razorpayOrderId ?? '',
                'razorpay_payment_id' => $request->razorpay_payment_id ?? '',
                'razorpay_signature' => $request->razorpay_signature ?? '',
            ]);

            // Save coupon usage
            if ($couponId) {
                CouponUsage::create([
                    'user_id' => $user->id,
                    'offer_id' => $couponId,
                    'order_id' => $order->id,
                ]);
            }

            // Save order item
            OrderItem::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $product->discount_price ?? $product->price,
                'quantity' => $request->quantity,
                'total' => $itemTotal,
                'box_id' => $boxId,
                'box_name' => $boxName,
                'box_price' => $boxPrice,
                'box_qty' => $boxQty,
                'custom_text' => $customText,
            ]);

            // Send and Save notification for Buy Now
            $user->notify(new UserNotification(
                'Order Placed Successfully',
                'Your order ' . $order->order_code . ' has been placed. Total: ₹' . number_format($total_amount, 2),
                ['order_id' => $order->id, 'order_code' => $order->order_code, 'type' => 'order_created']
            ));

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created via Buy Now',
                'data' => [
                    'order_id' => $order->id,
                    'razorpay_order_id' => $razorpayOrderId,
                    'total_amount' => $total_amount,
                    'advance_amount' => $advance_amount,
                    'advance_percentage' => $advancePercentage,
                    'currency' => 'INR',
                    'product_details' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'quantity' => $request->quantity,
                        'price' => $product->discount_price ?? $product->price,
                        'total' => $subtotal,
                        'image' => $product->image ? url($product->image) : null,
                        'sku' => $product->sku ?? null
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
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
            'coupon_code' => 'nullable|string|exists:offers,coupon_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            // 🟢 Calculate totals, combined weight and prepare cart data
            $subtotal = 0;
            $cartDataWithImages = [];
            $totalWeightKg = 0;

            foreach ($request->cart_data as $key => $item) {
                $productId = $item['product_id'] ?? null;
                if (!$productId) continue;

                $product = Product::find($productId);
                if ($product) {
                    $itemPrice = $item['price'] ?? $item['product_price'] ?? $product->price ?? 0;
                    $itemQty = $item['quantity'] ?? 1;
                    $productTotal = $itemPrice * $itemQty;
                    
                    // Box logic for this item
                    $boxId = $item['box_id'] ?? null;
                    $boxName = null;
                    $boxPrice = 0;
                    $boxQty = $item['box_qty'] ?? 0;
                    $customText = $item['custom_text'] ?? null;
                    $boxImage = null;

                    if ($boxId) {
                        $box = Box::find($boxId);
                        if ($box) {
                            $boxName = $box->box_name;
                            $boxPrice = $box->box_price;
                            $boxImage = $box->box_image;
                            $boxQty = $boxQty > 0 ? $boxQty : 1;
                        }
                    }

                    $boxTotal = $boxPrice * $boxQty;
                    $itemTotal = $productTotal + $boxTotal;
                    
                    $subtotal += $itemTotal;
                    
                    // Sum up weights
                    $itemWeight = $product->weight ?? 0;
                    $weightType = strtolower($product->weight_type ?? 'kg');
                    if ($weightType == 'g' || $weightType == 'gram') {
                        $itemWeight = $itemWeight / 1000;
                    }
                    $totalWeightKg += ($itemWeight * $itemQty);

                    $cartDataWithImages[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $itemQty,
                        'price' => $itemPrice,
                        'total' => $itemTotal,
                        'product_image' => $product->image ? url($product->image) : null,
                        'product_sku' => $product->sku ?? null,
                        'box_id' => $boxId,
                        'box_name' => $boxName,
                        'box_price' => $boxPrice,
                        'box_qty' => $boxQty,
                        'box_image' => $boxImage ? url($boxImage) : null,
                        'custom_text' => $customText,
                    ];
                }
            }

            $subtotal = $request->subtotal ?? $subtotal;
            $delivery_charge = $request->delivery_charge ?? 0;
            $total_amount = $subtotal + $delivery_charge;

            // 🎫 Coupon Logic
            $discountAmount = 0;
            $couponId = null;
            if ($request->coupon_code) {
                $offer = Offer::where('coupon_code', $request->coupon_code)->where('status', 1)->first();
                if ($offer) {
                    // Check usage
                    $usageCheck = CouponUsage::where('user_id', $user->id)->where('offer_id', $offer->id)->count();
                    if ($usageCheck == 0) {
                        $discountAmount = ($total_amount * $offer->discount_percentage) / 100;
                        if ($discountAmount > $offer->max_discount) {
                            $discountAmount = $offer->max_discount;
                        }
                        $couponId = $offer->id;
                        $total_amount -= $discountAmount;
                    }
                }
            }

            // 🟡 Apply Slab Charging Logic (Universal)
            $advancePercentage = 100; // Default
            $slab = PaymentSlab::where('min_km', '<=', $totalWeightKg)
                ->where('max_km', '>=', $totalWeightKg)
                ->where('status', 1)
                ->first();

            if ($slab) {
                $advancePercentage = $slab->advance_percentage;
            }

            $advance_amount = ($total_amount * $advancePercentage) / 100;

            $razorpayOrderId = null;

            if (in_array($request->payment_method, ['online', 'card'])) {
                try {
                    $api = GatewayService::getRazorpayApi();
                    $chargeAmount = $advance_amount; // Only charge advance now

                    $razorpayOrder = $api->order->create([
                        'receipt' => 'order_rcpt_' . time(),
                        'amount' => round($chargeAmount * 100), // paise
                        'currency' => 'INR'
                    ]);
                    $razorpayOrderId = $razorpayOrder['id'];
                } catch (\Exception $e) {
                    throw $e;
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'customer_id' => $user->id,
                'order_code' => 'ORD-' . time(),
                'order_id' => 'ORD-' . time() . '-' . $user->id,
                'customer_name' => $request->customer_name ?? $user->full_name ?? $user->name ?? 'Customer',
                'customer_email' => $request->customer_email ?? $user->email ?? 'N/A',
                'customer_phone' => $request->customer_phone ?? $user->phone ?? 'N/A',
                'delivery_address' => $request->delivery_address ?? (($request->house_block ? $request->house_block . ', ' : '') . ($request->area_road ? $request->area_road . ', ' : '') . ($request->address_details ?? '')),
                'delivery_city' => $request->delivery_city ?? '',
                'delivery_state' => $request->delivery_state ?? '',
                'delivery_pincode' => $request->delivery_pincode ?? '',
                'coupon_id' => $couponId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'delivery_charge' => $delivery_charge,
                'total_amount' => $total_amount,
                'advance_amount' => $advance_amount,
                'total_weight_kg' => $totalWeightKg,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'cart_data' => $cartDataWithImages,
                'address_details' => $request->address_details ?? '',
                'house_block' => $request->house_block ?? '',
                'area_road' => $request->area_road ?? '',
                'save_as' => $request->save_as ?? '',
                'order_notes' => $request->order_notes ?? '',
                'number_of_boxes' => $request->number_of_boxes ?? null,
                'receiver_name' => $request->receiver_name ?? '',
                'receiver_phone' => $request->receiver_phone ?? '',
                'delivery_time' => $request->delivery_time ?? null,
                'latitude' => $request->latitude ?? '',
                'longitude' => $request->longitude ?? '',
                'delivery_date' => $request->delivery_date ?? @now()->addDay(), 
                'razorpay_order_id' => $razorpayOrderId ?? '',
                'razorpay_payment_id' => $request->razorpay_payment_id ?? '',
                'razorpay_signature' => $request->razorpay_signature ?? '',
            ]);

            // Save coupon usage
            if ($couponId) {
                CouponUsage::create([
                    'user_id' => $user->id,
                    'offer_id' => $couponId,
                    'order_id' => $order->id,
                ]);
            }

            foreach ($cartDataWithImages as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                    'box_id' => $item['box_id'] ?? null,
                    'box_name' => $item['box_name'] ?? null,
                    'box_price' => $item['box_price'] ?? null,
                    'box_qty' => $item['box_qty'] ?? null,
                    'custom_text' => $item['custom_text'] ?? null,
                ]);
            }

            // Send and Save notification for Order
            $user->notify(new UserNotification(
                'Order Placed Successfully',
                'Your order ' . $order->order_code . ' has been placed. Total: ₹' . number_format($total_amount, 2),
                ['order_id' => $order->id, 'order_code' => $order->order_code, 'type' => 'order_created']
            ));

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created',
                'data' => [
                    'order_id' => $order->id,
                    'razorpay_order_id' => $razorpayOrderId,
                    'total_amount' => $total_amount,
                    'advance_amount' => $advance_amount,
                    'advance_percentage' => $advancePercentage,
                    'currency' => 'INR'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
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

        // Robust check for cart_data
        $cartData = $order->cart_data;
        if (is_string($cartData)) {
            $cartData = json_decode($cartData, true);
        }
        $cartData = is_array($cartData) ? $cartData : [];

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
