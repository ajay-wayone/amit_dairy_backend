<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index(Request $request)
    {
        try {
            $query = Category::with(['subcategories' => function($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->withCount(['products' => function($query) {
                $query->where('status', true);
            }]);

            // Search by name
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Sort categories
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $allowedSortFields = ['name', 'sort_order', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'sort_order';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 20);
            $categories = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Categories retrieved successfully',
                'data' => [
                    'categories' => $categories->items(),
                    'pagination' => [
                        'current_page' => $categories->currentPage(),
                        'last_page' => $categories->lastPage(),
                        'per_page' => $categories->perPage(),
                        'total' => $categories->total(),
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
     * Get category details with subcategories and products
     */
    public function show($id)
    {
        try {
            $category = Category::with(['subcategories' => function($query) {
                $query->where('is_active', true)
                      ->withCount(['products' => function($q) {
                          $q->where('status', true);
                      }]);
            }])
            ->withCount(['products' => function($query) {
                $query->where('status', true);
            }])
            ->where('is_active', true)
            ->find($id);

            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            // Get featured products from this category
            $featuredProducts = $category->products()
                ->where('status', true)
                ->where('best_seller', true)
                ->with(['category', 'subcategory'])
                ->limit(5)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Category details retrieved successfully',
                'data' => [
                    'category' => $category,
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
    public function subcategories(Request $request, $categoryId)
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
     * Get featured categories
     */
  public function featured()
{
    try {
        $categories = Category::with(['subcategories' => function($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->where('featured', 1) 
            ->withCount(['products' => function($query) {
                $query->where('status', true);
            }])
            ->orderBy('sort_order', 'asc')
            ->limit(6)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Featured categories retrieved successfully',
            'data' => $categories
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
