<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist items
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $wishlistItems = Wishlist::with(['product:id,name,price,image,description,stock_quantity,is_active'])
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Wishlist items retrieved successfully',
                'data' => [
                    'items' => $wishlistItems,
                    'total_items' => $wishlistItems->count()
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
     * Add item to wishlist
     */
    public function addToWishlist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
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

            // Check if product is already in wishlist
            $existingWishlistItem = Wishlist::where('user_id', $user->id)
                ->where('product_id', $request->product_id)
                ->where('is_active', true)
                ->first();

            if ($existingWishlistItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product is already in your wishlist',
                ], 400);
            }

            // Create new wishlist item
            $wishlistItem = Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'is_active' => true
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Item added to wishlist successfully',
                'data' => $wishlistItem->load('product:id,name,price,image,description,stock_quantity,is_active')
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
     * Remove item from wishlist
     */
    public function removeFromWishlist(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $wishlistItem = Wishlist::where('id', $id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$wishlistItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Wishlist item not found',
                ], 404);
            }

            $wishlistItem->is_active = false;
            $wishlistItem->save();

            return response()->json([
                'status' => true,
                'message' => 'Item removed from wishlist successfully',
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
     * Clear entire wishlist
     */
    public function clearWishlist(Request $request)
    {
        try {
            $user = $request->user();
            
            Wishlist::where('user_id', $user->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            return response()->json([
                'status' => true,
                'message' => 'Wishlist cleared successfully',
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
     * Check if product is in wishlist
     */
    public function checkWishlist(Request $request, $productId)
    {
        try {
            $user = $request->user();
            
            $wishlistItem = Wishlist::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->where('is_active', true)
                ->first();

            return response()->json([
                'status' => true,
                'message' => 'Wishlist status checked successfully',
                'data' => [
                    'is_in_wishlist' => $wishlistItem ? true : false,
                    'wishlist_id' => $wishlistItem ? $wishlistItem->id : null
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
     * Get wishlist summary
     */
    public function summary(Request $request)
    {
        try {
            $user = $request->user();
            
            $wishlistItems = Wishlist::with(['product:id,name,price,image,stock_quantity,is_active'])
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            $itemCount = $wishlistItems->count();

            return response()->json([
                'status' => true,
                'message' => 'Wishlist summary retrieved successfully',
                'data' => [
                    'total_items' => $itemCount,
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