<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory'])->orderBy('created_at', 'desc');

        // AJAX Search
        if ($request->ajax() && $request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Status Filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $products = $query->paginate(10);
        $categories = Category::active()->ordered()->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.products.partials.table', compact('products'))->render(),
                'pagination' => view('admin.products.partials.pagination', compact('products'))->render()
            ]);
        }

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::with('subcategories')->active()->ordered()->get();
        $subcategories = collect();
       
        
        if (request()->has('category_id')) {
            $subcategories = Subcategory::where('category_id', request('category_id'))
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('admin.products.create', compact('categories', 'subcategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'description' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|in:gram,kilogram,unit',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|lt:price',
            'minimum_quantity' => 'nullable|numeric|min:0',
            'max_order' => 'nullable|numeric|min:1',
            'featured_type' => 'nullable|string|in:hot,new_arrival,featured',
            'products_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean'
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('products_image')) {
                $image = $request->file('products_image');
                $imageName = 'prod_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'types' => $request->type,
                'price' => $request->price,
                'discounted_price' => $request->discounted_price,
                'minimum_quantity' => $request->minimum_quantity,
                'max_order' => $request->max_order,
                'featured_type' => $request->featured_type,
                'image' => $imagePath,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
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
            ->orderBy('name')
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
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|in:gram,kilogram,unit',
            'type' => 'nullable|array',
            'type.*' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|lt:price',
            'minimum_quantity' => 'nullable|numeric|min:0',
            'max_order' => 'nullable|numeric|min:1',
            'featured_type' => 'nullable|string|in:hot,new_arrival,featured',
            'products_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean'
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
                'discounted_price' => $request->discounted_price,
                'minimum_quantity' => $request->minimum_quantity,
                'max_order' => $request->max_order,
                'featured_type' => $request->featured_type,
                'is_active' => $request->has('is_active')
            ];

            if ($request->hasFile('products_image')) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = $request->file('products_image');
                $imageName = 'prod_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $data['image'] = $image->storeAs('products', $imageName, 'public');
            }

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

            // Delete image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Product $product)
    {
        try {
            $product->update(['is_active' => !$product->is_active]);
            
            return response()->json([
                'success' => true,
                'message' => 'Product status updated successfully!',
                'is_active' => $product->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product status!'
            ], 500);
        }
    }

public function getSubcategories(Request $request)
{
    $categoryId = $request->category_id;

    $subcategories = Subcategory::where('category_id', $categoryId)
        ->where('is_active', true)
        ->orderBy('subcategory_name')
        ->get();
    return response()->json($subcategories);
}


}