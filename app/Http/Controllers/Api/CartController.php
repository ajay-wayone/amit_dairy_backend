<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Offer;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\UserNotification;

class CartController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $cartItems = Cart::with([
                'product:id,name,price,product_image,description',
                'box:id,box_name,box_price,box_image,desc'
            ])
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            // total quantity
            $totalItems = $cartItems->sum('quantity');

            // cart items total (without box)
            $cartAmount = $cartItems->sum('total_price');

            // total box amount (per item box)
            $boxAmount = $cartItems->sum(function ($item) {
                return $item->box ? ($item->box->box_price * ($item->box_qty ?? 1)) : 0;
            });

            // final amount
            $finalAmount = $cartAmount + $boxAmount;

            return response()->json([
                'status' => true,
                'message' => 'Cart items retrieved successfully',
                'data' => [
                    'items' => $cartItems,
                    'total_items' => $totalItems,
                    'cart_amount' => number_format($cartAmount, 2),
                    'box_amount' => number_format($boxAmount, 2),
                    'total_amount' => number_format($finalAmount, 2),
                    'item_count' => $cartItems->count()
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
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $product = Product::findOrFail($request->product_id);

            $existingCartItem = Cart::where('user_id', $user->id)
                ->where('product_id', $request->product_id)
                ->where('is_active', true)
                ->first();

            if ($existingCartItem) {
                $existingCartItem->quantity += $request->quantity;
                $existingCartItem->total_price = $existingCartItem->price * $existingCartItem->quantity;
                $existingCartItem->save();

                // Notification for quantity update
                $user->notify(new UserNotification(
                    'Cart Updated',
                    'Quantity for "' . $product->name . '" has been updated in your cart.',
                    ['cart_id' => $existingCartItem->id, 'type' => 'cart_updated']
                ));

                return response()->json([
                    'status' => true,
                    'message' => 'Cart item quantity updated successfully',
                    'data' => $existingCartItem->load('product:id,name,price,product_image,description')
                ]);
            }

            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'total_price' => $product->price * $request->quantity,
                'is_active' => true
            ]);

            // Notification for new cart item
            $user->notify(new UserNotification(
                'New Item Added',
                '"' . $product->name . '" has been added to your cart.',
                ['cart_id' => $cartItem->id, 'type' => 'cart_item_added']
            ));

            return response()->json([
                'status' => true,
                'message' => 'Item added to cart successfully',
                'data' => $cartItem->load('product:id,name,price,product_image,description')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();
            $cartItem = Cart::where('id', $id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found',
                ], 404);
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->total_price = $cartItem->price * $request->quantity;
            $cartItem->save();

            // Notification for quantity update
            $user->notify(new UserNotification(
                'Cart Updated',
                'Quantity for "' . $cartItem->product->name . '" has been updated in your cart.',
                ['cart_id' => $cartItem->id, 'type' => 'cart_updated']
            ));

            return response()->json([
                'status' => true,
                'message' => 'Cart item quantity updated successfully',
                'data' => $cartItem->load('product:id,name,price,product_image,description')
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
     * Remove item from cart
     */
    public function removeFromCart(Request $request, $id)
    {
        try {
            $user = $request->user();
            $cartItem = Cart::where('id', $id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found',
                ], 404);
            }

            $cartItem->is_active = false;
            $cartItem->save();

            // Notification for removal
            $user->notify(new UserNotification(
                'Cart Item Removed',
                '"' . $cartItem->product->name . '" has been removed from your cart.',
                ['cart_id' => $cartItem->id, 'type' => 'cart_item_removed']
            ));

            return response()->json([
                'status' => true,
                'message' => 'Item removed from cart successfully',
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
     * Clear entire cart
     */
    public function clearCart(Request $request)
    {
        try {
            $user = $request->user();

            Cart::where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Notification for clearing cart
            $user->notify(new UserNotification(
                'Cart Cleared',
                'All items have been removed from your cart.',
                ['type' => 'cart_cleared']
            ));

            return response()->json([
                'status' => true,
                'message' => 'Cart cleared successfully',
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
     * Get cart summary
     */
    public function summary(Request $request)
    {
        try {
            $user = $request->user();

            $cartItems = Cart::with(['product:id,name,price,product_image', 'box:id,box_price'])
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            $totalItems = $cartItems->sum('quantity');
            $cartAmount = $cartItems->sum('total_price');
            $boxAmount = $cartItems->sum(function ($item) {
                return $item->box ? ($item->box->box_price * ($item->box_qty ?? 1)) : 0;
            });

            $totalAmount = $cartAmount + $boxAmount;
            $itemCount = $cartItems->count();

            // Check if coupon is provided
            $discountAmount = 0;
            $couponCode = $request->coupon_code;
            $appliedCoupon = null;

            if ($couponCode) {
                $offer = Offer::where('coupon_code', $couponCode)->where('status', 1)->first();
                if ($offer) {
                    $discountAmount = ($totalAmount * $offer->discount_percentage) / 100;
                    if ($discountAmount > $offer->max_discount) {
                        $discountAmount = $offer->max_discount;
                    }
                    $appliedCoupon = [
                        'id' => $offer->id,
                        'code' => $offer->coupon_code,
                        'discount_percentage' => (float)$offer->discount_percentage,
                        'discount_amount' => round($discountAmount, 2)
                    ];
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Cart summary retrieved successfully',
                'data' => [
                    'total_items' => $totalItems,
                    'cart_amount' => number_format($cartAmount, 2),
                    'box_amount' => number_format($boxAmount, 2),
                    'subtotal' => number_format($totalAmount, 2),
                    'discount_amount' => number_format($discountAmount, 2),
                    'total_amount' => number_format($totalAmount - $discountAmount, 2),
                    'item_count' => $itemCount,
                    'has_items' => $itemCount > 0,
                    'applied_coupon' => $appliedCoupon
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


    public function selectItemBox(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'box_id' => 'nullable|exists:boxes,id',
            'box_qty' => 'nullable|integer|min:1'
        ]);

        $user = $request->user();

        $cartItem = Cart::where('id', $request->cart_id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        $cartItem->box_id = $request->box_id;
        $cartItem->box_qty = $request->box_qty;
        $cartItem->save();

        return response()->json([
            'status' => true,
            'message' => 'Box updated for cart item'
        ]);
    }

    public function removeItemBox(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
        ]);

        $user = $request->user();

        $cartItem = Cart::where('id', $request->cart_id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->firstOrFail();

        $cartItem->box_id = null;
        $cartItem->box_qty = null;
        $cartItem->save();

        return response()->json([
            'status' => true,
            'message' => 'Box removed from cart item'
        ]);
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'coupon_code' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $offer = Offer::where('coupon_code', $request->coupon_code)
                ->where('status', 1)
                ->first();

            if (!$offer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or inactive coupon code',
                ], 404);
            }

            // Check if user already used this coupon
            $usageCount = CouponUsage::where('user_id', $user->id)
                ->where('offer_id', $offer->id)
                ->count();

            if ($usageCount > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already used this coupon.',
                ], 400);
            }

            // Calculate current total
            $cartItems = Cart::with(['box:id,box_price'])
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your cart is empty',
                ], 400);
            }

            $cartAmount = $cartItems->sum('total_price');
            $boxAmount = $cartItems->sum(function ($item) {
                return $item->box ? ($item->box->box_price * ($item->box_qty ?? 1)) : 0;
            });
            $subtotal = $cartAmount + $boxAmount;

            $discountAmount = ($subtotal * $offer->discount_percentage) / 100;
            if ($discountAmount > $offer->max_discount) {
                $discountAmount = (float)$offer->max_discount;
            }

            return response()->json([
                'status' => true,
                'message' => 'Coupon applied successfully',
                'data' => [
                    'coupon_id' => $offer->id,
                    'coupon_code' => $offer->coupon_code,
                    'discount_percentage' => (float)$offer->discount_percentage,
                    'discount_amount' => round($discountAmount, 2),
                    'max_discount' => (float)$offer->max_discount,
                    'subtotal' => round($subtotal, 2),
                    'payable_amount' => round($subtotal - $discountAmount, 2)
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

}
