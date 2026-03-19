<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class WishlistController extends Controller
{

    public function getWishlist()
    {
        try {
            $wishlist = DB::table('wishlists')
                ->join('products', 'products.id', '=', 'wishlists.product_id')
                ->where('wishlists.is_active', 1)
                ->select(
                    'wishlists.id as wishlist_id',
                    'wishlists.user_id',
                    'wishlists.product_id',

                    'products.product_code',
                    'products.slug',
                    'products.category_id',
                    'products.subcategory_id',
                    'products.name',
                    'products.description',
                    'products.short_description',
                    'products.price',
                    'products.discount_price',
                    'products.weight',
                    'products.weight_type',
                    'products.product_image',
                    'products.status'
                )
                ->orderBy('wishlists.id', 'desc')
                ->get();

            if ($wishlist->isEmpty()) {
                return response()->json([
                    'code' => 404,
                    'message' => 'No wishlist data found',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'code' => 200,
                'message' => 'All wishlist fetched successfully',
                'data' => $wishlist
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    /**
     * Add item to wishlist
     */

    // public function addWishlist(Request $request)
    // {
    //     // ✅ Validation
    //     $validator = Validator::make($request->all(), [
    //         'product_id' => 'required|numeric'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'code' => 401,
    //             'message' => $validator->errors()->first()
    //         ], 401);
    //     }

    //     try {
    //         // ⚠️ TEMP user_id (jab tak login/auth nahi hai)
    //         $user_id = 1;

    //         // ✅ Check duplicate wishlist
    //         $alreadyExists = DB::table('wishlists')
    //             ->where('user_id', $user_id)
    //             ->where('product_id', $request->product_id)
    //             ->where('is_active', 1)
    //             ->first();

    //         if ($alreadyExists) {
    //             return response()->json([
    //                 'code' => 409,
    //                 'message' => 'Product already in wishlist'
    //             ], 409);
    //         }

    //         // ✅ Insert wishlist
    //         DB::table('wishlists')->insert([
    //             'user_id' => $user_id,
    //             'product_id' => $request->product_id,
    //             'status' => 1,
    //             'is_active' => 1,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ]);

    //         return response()->json([
    //             'code' => 200,
    //             'message' => 'Product added to wishlist successfully'
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'code' => 500,
    //             'message' => 'Internal Server Error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function addWishlist(Request $request)
    {
        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 401,
                'message' => $validator->errors()->first()
            ], 401);
        }

        try {
            // ⚠️ TEMP user_id
            $user_id = 1;

            // ✅ Check duplicate wishlist
            $alreadyExists = DB::table('wishlists')
                ->where('user_id', $user_id)
                ->where('product_id', $request->product_id)
                ->where('is_active', 1)
                ->first();

            // ✅ CHANGE HERE (NO ERROR CODE)
            if ($alreadyExists) {
                return response()->json([
                    'code' => 200,
                    'message' => 'Product already added in wishlist'
                ], 200);
            }

            // ✅ Insert wishlist
            DB::table('wishlists')->insert([
                'user_id' => $user_id,
                'product_id' => $request->product_id,
                'status' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Product added to wishlist successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }







    /**
     * Remove item from wishlist
     */
    public function deleteWishlist(Request $request)
    {
        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'wishlist_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 401,
                'message' => $validator->errors()->first()
            ], 401);
        }

        try {
            // ✅ Check wishlist exists
            $wishlist = DB::table('wishlists')
                ->where('id', $request->wishlist_id)
                ->where('is_active', 1)
                ->first();

            if (!$wishlist) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Wishlist not found'
                ], 404);
            }

            // ✅ Soft delete (recommended)
            DB::table('wishlists')
                ->where('id', $request->wishlist_id)
                ->update([
                    'is_active' => 0,
                    'updated_at' => now()
                ]);

            return response()->json([
                'code' => 200,
                'message' => 'Wishlist deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
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

            $wishlistItems = Wishlist::with(['product:id,name,price,product_image'])
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
