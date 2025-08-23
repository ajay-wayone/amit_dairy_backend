<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Get reviews for a product (public)
     */
    public function productReviews($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $reviews = Review::with(['user:id,full_name'])
                ->where('product_id', $productId)
                ->where('is_approved', true)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $averageRating = Review::where('product_id', $productId)
                ->where('is_approved', true)
                ->where('is_active', true)
                ->avg('rating');

            $ratingCounts = Review::where('product_id', $productId)
                ->where('is_approved', true)
                ->where('is_active', true)
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
                ->toArray();

            return response()->json([
                'status' => true,
                'message' => 'Product reviews retrieved successfully',
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name
                    ],
                    'reviews' => $reviews,
                    'average_rating' => round($averageRating, 1),
                    'total_reviews' => $reviews->total(),
                    'rating_distribution' => $ratingCounts
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
     * Get user's reviews (protected)
     */
    public function userReviews(Request $request)
    {
        try {
            $user = $request->user();

            $reviews = Review::with(['product:id,name,image'])
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'User reviews retrieved successfully',
                'data' => [
                    'reviews' => $reviews,
                    'total_reviews' => $reviews->total()
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
     * Add a review (protected)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'rating' => 'required|integer|min:1|max:5',
                'title' => 'nullable|string|max:255',
                'comment' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();

            // Check if user has already reviewed this product
            $existingReview = Review::where('user_id', $user->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have already reviewed this product',
                ], 400);
            }

            // Create new review
            $review = Review::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'title' => $request->title,
                'comment' => $request->comment,
                'is_approved' => false, // Requires admin approval
                'is_active' => true
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Review submitted successfully and pending approval',
                'data' => $review->load(['product:id,name,image'])
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
     * Update a review (protected)
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'sometimes|integer|min:1|max:5',
                'title' => 'nullable|string|max:255',
                'comment' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();

            $review = Review::where('id', $id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$review) {
                return response()->json([
                    'status' => false,
                    'message' => 'Review not found',
                ], 404);
            }

            // If review is approved, it might need re-approval after update
            if ($review->is_approved) {
                $review->is_approved = false;
            }

            $review->update($request->only(['rating', 'title', 'comment']));

            return response()->json([
                'status' => true,
                'message' => 'Review updated successfully',
                'data' => $review->load(['product:id,name,image'])
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
     * Delete a review (protected)
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();

            $review = Review::where('id', $id)
                ->where('user_id', $user->id)
                ->where('is_active', true)
                ->first();

            if (!$review) {
                return response()->json([
                    'status' => false,
                    'message' => 'Review not found',
                ], 404);
            }

            $review->is_active = false;
            $review->save();

            return response()->json([
                'status' => true,
                'message' => 'Review deleted successfully',
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
     * Get review statistics for a product (public)
     */
    public function productReviewStats($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $stats = Review::where('product_id', $productId)
                ->where('is_approved', true)
                ->where('is_active', true)
                ->selectRaw('
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    MIN(rating) as min_rating,
                    MAX(rating) as max_rating
                ')
                ->first();

            $ratingDistribution = Review::where('product_id', $productId)
                ->where('is_approved', true)
                ->where('is_active', true)
                ->selectRaw('rating, COUNT(*) as count')
                ->groupBy('rating')
                ->orderBy('rating', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Product review statistics retrieved successfully',
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name
                    ],
                    'statistics' => [
                        'total_reviews' => $stats->total_reviews,
                        'average_rating' => round($stats->average_rating, 1),
                        'min_rating' => $stats->min_rating,
                        'max_rating' => $stats->max_rating
                    ],
                    'rating_distribution' => $ratingDistribution
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