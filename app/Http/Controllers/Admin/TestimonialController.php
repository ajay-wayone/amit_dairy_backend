<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
   public function index(Request $request)
{
    $query = Testimonial::query();

    // Apply search filter if 'search' is present
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where('customer_name', 'like', '%' . $search . '%')
              ->orWhere('testimonial', 'like', '%' . $search . '%');
    }

    // Apply sort order and paginate
    $testimonials = $query->orderBy('sort_order')->paginate(10);

    // Return view with results
    return view('admin.testimonials.index', compact('testimonials'));
}

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'testimonial' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('customer_image')) {
            $data['customer_image'] = $request->file('customer_image')->store('testimonials', 'public');
        }

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    public function show(Testimonial $testimonial)
    {
        return view('admin.testimonials.show', compact('testimonial'));
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.update', compact('testimonial'));
    }

   public function update(Request $request, Testimonial $testimonial)
{
    $validated = $request->validate([
        'customer_name' => 'required|string|max:255',
        'customer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'testimonial' => 'required|string',
        'rating' => 'required|integer|min:1|max:5',
        'is_active' => 'nullable|boolean',
        'sort_order' => 'nullable|integer',
    ]);

    // Set the correct value for is_active (checkbox-like behavior)
    $validated['is_active'] = $request->has('is_active') ? 1 : 0;

    // Handle image upload
    if ($request->hasFile('customer_image')) {
        // Delete old image if it exists
        if ($testimonial->customer_image && \Storage::disk('public')->exists($testimonial->customer_image)) {
            \Storage::disk('public')->delete($testimonial->customer_image);
        }

        // Store new image
        $validated['customer_image'] = $request->file('customer_image')->store('testimonials', 'public');
    }

    // Update the testimonial
    $testimonial->update($validated);

    return redirect()->route('admin.testimonials.index')
        ->with('success', 'Testimonial updated successfully.');
}

public function destroy(Testimonial $testimonial)
{
    try {
        // Delete image if exists
        if ($testimonial->customer_image && \Storage::disk('public')->exists($testimonial->customer_image)) {
            \Storage::disk('public')->delete($testimonial->customer_image);
        }

        // Delete the testimonial from DB
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    } catch (\Exception $e) {
        return redirect()->route('admin.testimonials.index')
            ->with('error', 'Failed to delete testimonial: ' . $e->getMessage());
    }
}

    public function toggleStatus(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Testimonial status updated successfully.',
            'is_active' => $testimonial->is_active
        ]);
    }
}
