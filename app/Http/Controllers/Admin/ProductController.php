<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Validate request parameters
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'category_id' => 'nullable|integer|exists:categories,id',
                'status' => 'nullable|in:0,1',
                'page' => 'nullable|integer|min:1'
            ]);

            // Base query with eager loading
            $query = Product::with(['category', 'subcategory'])
                ->orderBy('created_at', 'desc');

            // Search Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subcategory', function ($q) use ($search) {
                            $q->where('subcategory_name', 'like', "%{$search}%");
                        });
                });
            }

            // Category Filter
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Status Filter - Fixed condition
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Pagination with configurable per page count
            $perPage = $request->per_page ?? 10;
            $products = $query->paginate($perPage)->withQueryString();
            $categories = Category::active()->ordered()->get();

            // AJAX Response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('admin.products.partials.table', compact('products'))->render(),
                    'pagination' => $products->links()->render(),
                    'count' => $products->total(),
                ]);
            }

            return view('admin.products.index', compact('products', 'categories'));

        } catch (\Exception $e) {
            \Log::error('ProductController@index error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load products. Please try again.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return back()->with('error', 'Failed to load products. Please try again.');
        }
    }


    public function create()
    {
        $categories = Category::with('subcategories')->active()->ordered()->get();
        $subcategories = collect();

        if (request()->has('category_id')) {
            $subcategories = Subcategory::where('category_id', request('category_id'))
                ->where('status', true)
                ->orderBy('name')
                ->get();
        }

        return view('admin.products.create', compact('categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([

            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'description' => 'nullable|string',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0|lt:price',
            'min_order' => 'required|numeric|min:1',
            'max_order' => 'required|numeric|min:1',
            'weight' => 'required|numeric|min:0',
            'weight_type' => 'required|string',
            'best_seller' => 'required|boolean',
            'specialities' => 'required|boolean',
            'status' => 'required|boolean',
            'featured_type' => 'required|string',
            'product_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('product_image')) {
                $image = $request->file('product_image');
                $imageName = 'prod_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
            }

            $product = Product::create([
                'product_code' => 'PROD' . uniqid(),
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'product_image' => $imagePath,
                'types' => $request->type,
                'weight' => $request->weight,
                'weight_type' => $request->weight_type,
                'min_order' => $request->min_order ?? 1,
                'max_order' => $request->max_order,
                'best_seller' => $request->has('best_seller'),
                'specialities' => $request->has('specialities'),
                'status' => $request->has('status'),
                'featured_type' => $request->featured_type,
            ]);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }
    public function toggleStatus(Product $product)
    {
        try {
            $product->status = !$product->status;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully',
                'status' => $product->status
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to toggle product status: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product status'
            ], 500);
        }
    }
    public function show(Product $product)
    {
        $product->load(['category', 'subcategory']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->ordered()->get();
        $subcategories = Subcategory::where('category_id', $product->category_id)
            ->where('is_active', true)
            ->orderBy('subcategory_name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories', 'subcategories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'weight' => 'nullable|numeric|min:0',
            'weight_type' => 'nullable|string',
            'min_order' => 'nullable|numeric|min:1',
            'max_order' => 'nullable|numeric|min:1',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string',
            'product_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'best_seller' => 'boolean',
            'specialities' => 'boolean',
            'status' => 'boolean',
            'featured_type' => 'nullable|string',
        ]);

        try {
            $data = [
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'types' => $request->type,
                'price' => $request->price,
                'discounted_price' => $request->discount_price,
                'minimum_quantity' => $request->min_order,
                'max_order' => $request->max_order,
                'featured_type' => $request->featured_type,
                'status' => $request->has('status'),
            ];

            // Image replacement
            if ($request->hasFile('product_image')) {
                // Column name check: DB me jo column hai wahi use karo
                $oldImage = $product->product_image; // Agar DB column 'product_image' hai
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }

                // Store new image
                $image = $request->file('product_image');
                $imageName = 'prod_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $product->product_image = $image->storeAs('products', $imageName, 'public');
            }


            // Update product
            $product->update($data);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }


    public function destroy(Product $product)
    {
        try {
            // Check if product has orders
            if ($product->orderItems()->count() > 0) {
                return back()->with('error', 'Cannot delete product with existing orders!');
            }

            // Delete image from storage if it exists
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }

            // Delete product record
            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function getSubcategories(Request $request)
    {
        $categoryId = $request->category_id;

        try {
            \Log::info('getSubcategories method called with category ID: ' . $categoryId);
            \Log::info('Request data: ' . json_encode($request->all()));

            // Check if category exists
            $category = \App\Models\Category::find($categoryId);
            if (!$category) {
                \Log::warning('Category not found: ' . $categoryId);
                return response()->json(['error' => 'Category not found'], 404);
            }

            $subcategories = Subcategory::where('category_id', $categoryId)
                ->where('is_active', true)
                ->orderBy('subcategory_name')
                ->get(['id', 'subcategory_name']);

            \Log::info('Subcategories fetched for category ' . $categoryId . ': ' . $subcategories->count() . ' found');

            return response()->json([
                'success' => true,
                'count' => $subcategories->count(),
                'data' => $subcategories,
                'category_id' => $categoryId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching subcategories: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch subcategories: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }

    }
    public function websiteIndex()
    {
        return view('admin.banners.website.index');
    }


}
