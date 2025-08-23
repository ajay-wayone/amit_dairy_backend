<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubcategoryController extends Controller
{
    /**
     * Get all subcategories
     */
    public function index(Request $request)
    {
        try {
            $query = Subcategory::with(['category'])
                ->where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('status', true);
                }]);

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('subcategory_name', 'like', '%' . $request->search . '%');
            }

            // Sort subcategories
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $allowedSortFields = ['subcategory_name', 'sort_order', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'sort_order';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 20);
            $subcategories = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Subcategories retrieved successfully',
                'data' => [
                    'subcategories' => $subcategories->items(),
                    'pagination' => [
                        'current_page' => $subcategories->currentPage(),
                        'last_page' => $subcategories->lastPage(),
                        'per_page' => $subcategories->perPage(),
                        'total' => $subcategories->total(),
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
     * Get subcategory details with products
     */
    public function show($id)
    {
        try {
            $subcategory = Subcategory::with(['category'])
                ->withCount(['products' => function($query) {
                    $query->where('status', true);
                }])
                ->where('is_active', true)
                ->find($id);

            if (!$subcategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subcategory not found',
                ], 404);
            }

            // Get featured products from this subcategory
            $featuredProducts = $subcategory->products()
                ->where('status', true)
                ->where('best_seller', true)
                ->with(['category', 'subcategory'])
                ->limit(5)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Subcategory details retrieved successfully',
                'data' => [
                    'subcategory' => $subcategory,
                    'featured_products' => $featuredProducts
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
     * Get subcategories by category ID
     */
    public function byCategory(Request $request, $categoryId)
    {
        try {
            $validator = Validator::make(['category_id' => $categoryId], [
                'category_id' => 'required|exists:categories,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $subcategories = Subcategory::where('category_id', $categoryId)
                ->where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('status', true);
                }])
                ->orderBy('sort_order', 'asc')
                ->orderBy('subcategory_name', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Subcategories retrieved successfully',
                'data' => $subcategories
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
     * Get featured subcategories
     */
    public function featured()
    {
        try {
            $subcategories = Subcategory::with(['category'])
                ->where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('status', true);
                }])
                ->orderBy('sort_order', 'asc')
                ->limit(8)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Featured subcategories retrieved successfully',
                'data' => $subcategories
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