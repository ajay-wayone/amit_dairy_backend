<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Get all products with pagination
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'subcategory'])
                ->where('status', true);

            // Filter by category
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by subcategory
            if ($request->has('subcategory_id')) {
                $query->where('subcategory_id', $request->subcategory_id);
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filter by availability
            if ($request->has('in_stock')) {
                $query->where('stock_quantity', '>', 0);
            }

            // Filter by featured products
            if ($request->has('featured')) {
                $query->where('best_seller', true);
            }

            // Filter by special products
            if ($request->has('special')) {
                $query->where('specialities', true);
            }

            // Sort products
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Validate sort fields
            $allowedSortFields = ['name', 'price', 'created_at', 'updated_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'created_at';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 10);
            $products = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'products' => $products->items(),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
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
     * Get featured products
     */
    public function featured()
    {
        try {
            $products = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where('best_seller', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Featured products retrieved successfully',
                'data' => $products
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
     * Get special products
     */
    public function special()
    {
        try {
            $products = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where('specialities', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Special products retrieved successfully',
                'data' => $products
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
     * Get new arrivals
     */
    public function newArrivals()
    {
        try {
            $products = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'New arrivals retrieved successfully',
                'data' => $products
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
     * Get product details
     */
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->find($id);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            // Get related products
            $relatedProducts = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(5)
                ->get();

            // Get similar products (same subcategory)
            $similarProducts = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where('subcategory_id', $product->subcategory_id)
                ->where('id', '!=', $product->id)
                ->limit(5)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Product details retrieved successfully',
                'data' => [
                    'product' => $product,
                    'related_products' => $relatedProducts,
                    'similar_products' => $similarProducts
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
     * Search products
     */
    public function search(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $query = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->query . '%')
                      ->orWhere('description', 'like', '%' . $request->query . '%')
                      ->orWhere('tags', 'like', '%' . $request->query . '%');
                });

            $perPage = $request->get('per_page', 10);
            $products = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Search results retrieved successfully',
                'data' => [
                    'products' => $products->items(),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
                    ],
                    'search_query' => $request->query
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
     * Get product filters
     */
    public function filters()
    {
        try {
            $filters = [
                'categories' => Category::where('is_active', true)
                    ->withCount(['products' => function($query) {
                        $query->where('status', true);
                    }])
                    ->orderBy('category_name')
                    ->get(),
                'price_ranges' => [
                    ['min' => 0, 'max' => 100, 'label' => 'Under ₹100'],
                    ['min' => 100, 'max' => 500, 'label' => '₹100 - ₹500'],
                    ['min' => 500, 'max' => 1000, 'label' => '₹500 - ₹1000'],
                    ['min' => 1000, 'max' => 5000, 'label' => '₹1000 - ₹5000'],
                    ['min' => 5000, 'max' => null, 'label' => 'Above ₹5000'],
                ],
                'availability' => [
                    ['value' => 'in_stock', 'label' => 'In Stock'],
                    ['value' => 'out_of_stock', 'label' => 'Out of Stock'],
                ],
                'sort_options' => [
                    ['value' => 'name_asc', 'label' => 'Name A-Z'],
                    ['value' => 'name_desc', 'label' => 'Name Z-A'],
                    ['value' => 'price_asc', 'label' => 'Price Low to High'],
                    ['value' => 'price_desc', 'label' => 'Price High to Low'],
                    ['value' => 'created_at_desc', 'label' => 'Newest First'],
                    ['value' => 'created_at_asc', 'label' => 'Oldest First'],
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Product filters retrieved successfully',
                'data' => $filters
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
     * Get categories
     */
    public function categories()
    {
        try {
            $categories = Category::where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('status', true);
                }])
                ->orderBy('category_name')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Categories retrieved successfully',
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

    /**
     * Get subcategories by category
     */
    public function subcategories(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:categories,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $subcategories = Subcategory::where('category_id', $request->category_id)
                ->where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('status', true);
                }])
                ->orderBy('subcategory_name')
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
     * Get products by category
     */
    public function productsByCategory($categoryId)
    {
        try {
            $category = Category::where('id', $categoryId)
                ->where('is_active', true)
                ->first();

            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            $products = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where('category_id', $categoryId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'category' => $category,
                    'products' => $products->items(),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
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
     * Get products by subcategory
     */
    public function productsBySubcategory($subcategoryId)
    {
        try {
            $subcategory = Subcategory::where('id', $subcategoryId)
                ->where('is_active', true)
                ->with('category')
                ->first();

            if (!$subcategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subcategory not found',
                ], 404);
            }

            $products = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where('subcategory_id', $subcategoryId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'subcategory' => $subcategory,
                    'products' => $products->items(),
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total(),
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
} 