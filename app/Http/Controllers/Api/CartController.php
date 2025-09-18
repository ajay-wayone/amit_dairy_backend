<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
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
            
            $cartItems = Cart::with(['product:id,name,price,product_image,description'])
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            $totalItems = $cartItems->sum('quantity');
            $totalAmount = $cartItems->sum('total_price');

            return response()->json([
                'status' => true,
                'message' => 'Cart items retrieved successfully',
                'data' => [
                    'items' => $cartItems,
                    'total_items' => $totalItems,
                    'total_amount' => number_format($totalAmount, 2),
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
            
            $cartItems = Cart::with(['product:id,name,price,product_image'])
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            $totalItems = $cartItems->sum('quantity');
            $totalAmount = $cartItems->sum('total_price');
            $itemCount = $cartItems->count();

            return response()->json([
                'status' => true,
                'message' => 'Cart summary retrieved successfully',
                'data' => [
                    'total_items' => $totalItems,
                    'total_amount' => number_format($totalAmount, 2),
                    'item_count' => $itemCount,
                    'has_items' => $itemCount > 0
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
