<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class SubcategoryController extends Controller
{
    public function index(Request $request)
{
    $query = Subcategory::query();

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where('subcategory_name', 'like', '%' . $search . '%');
    }

    $subcategories = $query->orderBy('id', 'desc')->paginate(15);

    return view('admin.subcategories.index', compact('subcategories'));
}


   public function create()
{
    $categories = Category::where('is_active', true)->get();
    return view('admin.subcategories.create', compact('categories'));
}


    public function store(Request $request)
    {
          

        $request->validate([
        'category_id' => 'required|exists:categories,id',
        'subcategory_name' => 'required|string|max:255',
        'subcategory_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20248',
        'is_active' => 'required|boolean',
        'sort_order' => 'nullable|integer',
    ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('subcategory_image')) {
            $data['subcategory_image'] = $request->file('subcategory_image')->store('subcategories', 'public');
        }

        Subcategory::create($data);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory created successfully.');
    }

    public function show(Subcategory $subcategory)
    {
        return view('admin.subcategories.show', compact('subcategory'));
    }

    public function edit(Subcategory $subcategory)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.subcategories.update', compact('subcategory', 'categories'));
    }

   public function update(Request $request, Subcategory $subcategory)
{
    $request->validate([
        'category_id' => 'exists:categories,id',
        'subcategory_name' => 'required|string|max:255',
        'subcategory_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20248',
        'is_active' => 'boolean',
        'sort_order' => 'nullable|integer',
    ]);

    $data = $request->only(['category_id', 'subcategory_name', 'sort_order']);
    $data['slug'] = Str::slug($request->subcategory_name);
    $data['is_active'] = $request->has('is_active');

    // Handle image upload
    if ($request->hasFile('subcategory_image')) {
        // Delete old image
        if ($subcategory->subcategory_image && Storage::disk('public')->exists($subcategory->subcategory_image)) {
            Storage::disk('public')->delete($subcategory->subcategory_image);
        }

        // Upload new image
        $data['subcategory_image'] = $request->file('subcategory_image')->store('subcategories', 'public');
    }

    $subcategory->update($data);

    return redirect()->route('admin.subcategories.index')
        ->with('success', 'Subcategory updated successfully.');
}
   public function destroy(Subcategory $subcategory)
{
    // Delete image from storage if exists
    if ($subcategory->subcategory_image && \Storage::disk('public')->exists($subcategory->subcategory_image)) {
        \Storage::disk('public')->delete($subcategory->subcategory_image);
    }

    // Delete subcategory record from DB
    $subcategory->delete();

    return redirect()->route('admin.subcategories.index')
        ->with('success', 'Subcategory deleted successfully.');
}

    public function toggleStatus(Subcategory $subcategory)
    {
        $subcategory->update(['is_active' => !$subcategory->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subcategory status updated successfully.',
            'is_active' => $subcategory->is_active
        ]);
    }
}
