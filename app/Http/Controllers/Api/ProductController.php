<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\UserNotification;

class ProductController extends Controller
{
    /**
     * Get all products (NO pagination)
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'subcategory'])
                ->where('status', true);

            if ($request->has('id')) {
                $query->where('id', 'like', '%' . $request->id . '%');
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('subcategory_id')) {
                $query->where('subcategory_id', $request->subcategory_id);
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->has('in_stock')) {
                $query->where('stock_quantity', '>', 0);
            }

            if ($request->has('featured')) {
                $query->where('best_seller', true);
            }

            if ($request->has('special')) {
                $query->where('specialities', true);
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            $allowedSortFields = ['name', 'price', 'created_at', 'updated_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'created_at';
            }

            $query->orderBy($sortBy, $sortOrder);

            $products = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Featured products
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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Special products
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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * New arrivals
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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show product details
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

            $relatedProducts = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(5)
                ->get();

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
                'error' => $e->getMessage()
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

            $products = Product::with(['category', 'subcategory'])
                ->where('status', true)
                ->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->query . '%')
                        ->orWhere('description', 'like', '%' . $request->query . '%');
                })
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Search results retrieved successfully',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Products by category
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
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'category' => $category,
                    'products' => $products
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Products by subcategory
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
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'subcategory' => $subcategory,
                    'products' => $products
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notify admins about new product (optional, if creating products via API)
     */
    protected function notifyAdmins(Product $product)
    {
        try {
            $admins = User::where('role', 'admin')->where('is_active', true)->get();

            foreach ($admins as $admin) {
                $admin->notify(new UserNotification(
                    'New Product Added',
                    'A new product "' . $product->name . '" has been added.',
                    ['product_id' => $product->id, 'type' => 'new_product']
                ));
            }
        } catch (\Exception $e) {
            \Log::error('Product notification failed: ' . $e->getMessage());
        }
    }
}
