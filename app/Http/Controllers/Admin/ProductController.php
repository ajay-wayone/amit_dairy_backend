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
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
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
        $categories = Category::active()->ordered()->get();
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
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:100|unique:products,sku',
            'product_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50'
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('product_image')) {
                $image = $request->file('product_image');
                $imageName = 'prod_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
            }

            $galleryImages = [];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $galleryImage) {
                    $galleryName = 'gallery_' . uniqid() . '.' . $galleryImage->getClientOriginalExtension();
                    $galleryPath = $galleryImage->storeAs('products/gallery', $galleryName, 'public');
                    $galleryImages[] = $galleryPath;
                }
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'short_description' => $request->short_description,
                'price' => $request->price,
                'sale_price' => $request->sale_price,
                'stock_quantity' => $request->stock_quantity,
                'sku' => $request->sku,
                'image' => $imagePath,
                'gallery_images' => $galleryImages,
                'is_featured' => $request->has('is_featured'),
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0,
                'weight' => $request->weight,
                'unit' => $request->unit
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
        $product->load(['category', 'subcategory', 'orderItems']);
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
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'product_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50'
        ]);

        try {
            $data = [
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'short_description' => $request->short_description,
                'price' => $request->price,
                'sale_price' => $request->sale_price,
                'stock_quantity' => $request->stock_quantity,
                'sku' => $request->sku,
                'is_featured' => $request->has('is_featured'),
                'is_active' => $request->has('is_active'),
                'sort_order' => $request->sort_order ?? 0,
                'weight' => $request->weight,
                'unit' => $request->unit
            ];

            if ($request->hasFile('product_image')) {
                // Delete old image
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = $request->file('product_image');
                $imageName = 'prod_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $data['image'] = $image->storeAs('products', $imageName, 'public');
            }

            if ($request->hasFile('gallery_images')) {
                // Delete old gallery images
                if ($product->gallery_images) {
                    foreach ($product->gallery_images as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                $galleryImages = [];
                foreach ($request->file('gallery_images') as $galleryImage) {
                    $galleryName = 'gallery_' . uniqid() . '.' . $galleryImage->getClientOriginalExtension();
                    $galleryPath = $galleryImage->storeAs('products/gallery', $galleryName, 'public');
                    $galleryImages[] = $galleryPath;
                }
                $data['gallery_images'] = $galleryImages;
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

            // Delete images
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            if ($product->gallery_images) {
                foreach ($product->gallery_images as $galleryImage) {
                    Storage::disk('public')->delete($galleryImage);
                }
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

    public function toggleFeatured(Product $product)
    {
        try {
            $product->update(['is_featured' => !$product->is_featured]);
            
            return response()->json([
                'success' => true,
                'message' => 'Product featured status updated successfully!',
                'is_featured' => $product->is_featured
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product featured status!'
            ], 500);
        }
    }

    public function getSubcategories(Request $request)
    {
        $subcategories = Subcategory::where('category_id', $request->category_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($subcategories);
    }
}
