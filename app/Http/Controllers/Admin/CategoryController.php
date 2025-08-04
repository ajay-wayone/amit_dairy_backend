<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('subcategories')->orderBy('sort_order', 'asc');

        if ($request->ajax() && $request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.categories.partials.table', compact('categories'))->render(),
                'pagination' => view('admin.categories.partials.pagination', compact('categories'))->render()
            ]);
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $boxes = Box::where('is_active', true)->orderBy('box_name')->get();
        return view('admin.categories.create', compact('boxes'));
    }

   public function store(Request $request)
{
    $request->validate([
        'box_category' => 'required|string|max:255',
        'description' => 'nullable|string',
        'box_ids_json' => 'nullable|array',
        'box_ids_json.*' => 'exists:boxes,id',
        'category_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:20248',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer|min:0',
    ]);

    try {
        $imagePath = null;

        if ($request->hasFile('category_image')) {
            $image = $request->file('category_image');
            $imageName = 'cat_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories', $imageName, 'public');
        }

        $category = Category::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'image'       => $imagePath,
            'is_active'   => $request->has('is_active'),
            'sort_order'  => $request->sort_order ?? 0,
        ]);

        if (!empty($request->box_ids_json)) {
            $category->boxes()->attach($request->box_ids_json);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Failed to create category: ' . $e->getMessage());
    }
}

    public function show(Category $category)
    {
        $category->load(['subcategories', 'products']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $boxes = Box::where('is_active', true)->orderBy('box_name')->get();
        $selectedBoxes = $category->boxes->pluck('id')->toArray();
        return view('admin.categories.edit', compact('category', 'boxes', 'selectedBoxes'));
    }
public function update(Request $request, Category $category)
{
    $request->validate([
        'box_category' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'category_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer|min:0',
        'box_ids_json' => 'nullable|array',
        'box_ids_json.*' => 'exists:boxes,id',
    ]);

    try {
        $data = [];

        if ($request->filled('box_category')) {
            $data['box_category'] = $request->box_category;
            $data['slug'] = Str::slug($request->box_category);
        }

        if ($request->filled('description')) {
            $data['description'] = $request->description;
        }

        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        if ($request->hasFile('category_image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $image = $request->file('category_image');
            $imageName = 'cat_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $data['image'] = $image->storeAs('categories', $imageName, 'public');
        }

        $category->update($data);

        if ($request->has('box_ids_json')) {
            $category->boxes()->sync($request->box_ids_json);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Failed to update category: ' . $e->getMessage());
    }
}

    public function destroy(Category $category)
    {
        try {
            if ($category->subcategories()->count() > 0) {
                return back()->with('error', 'Cannot delete category with subcategories!');
            }

            if ($category->products()->count() > 0) {
                return back()->with('error', 'Cannot delete category with products!');
            }

            // Delete image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // Detach relationships
            $category->boxes()->detach();

            // Delete from DB
            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Category $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Category status updated!',
                'is_active' => $category->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.'
            ], 500);
        }
    }
}
