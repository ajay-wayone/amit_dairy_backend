<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\UserNotification;

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

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

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

    /**
     * Notify all users about a new category
     */
    public function notifyNewCategory(Category $category)
    {
        try {
            $users = User::where('is_active', true)->get();

            foreach ($users as $user) {
                $user->notify(new UserNotification(
                    'New Category Added',
                    'A new category "' . $category->name . '" has been added. Check it out!',
                    ['category_id' => $category->id, 'type' => 'new_category']
                ));
            }

        } catch (\Exception $e) {
            // Log error but don't break main process
            \Log::error('Category notification failed: '.$e->getMessage());
        }
    }

    /**
     * Notify all users about a new subcategory
     */
    public function notifyNewSubcategory(Subcategory $subcategory)
    {
        try {
            $users = User::where('is_active', true)->get();

            foreach ($users as $user) {
                $user->notify(new UserNotification(
                    'New Subcategory Added',
                    'A new subcategory "' . $subcategory->subcategory_name . '" under category "' . $subcategory->category->name . '" has been added.',
                    ['subcategory_id' => $subcategory->id, 'type' => 'new_subcategory']
                ));
            }

        } catch (\Exception $e) {
            \Log::error('Subcategory notification failed: '.$e->getMessage());
        }
    }
}
