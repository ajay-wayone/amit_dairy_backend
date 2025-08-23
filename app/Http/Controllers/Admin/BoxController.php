<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoxController extends Controller
{
    // Show list
   public function index(Request $request)
{
    $query = Box::query();

    // Check for search input
    if ($request->has('search') && !empty($request->search)) {
        $query->where('box_name', 'like', '%' . $request->search . '%');
    }

    $boxes = $query->orderBy('box_name')->paginate(10);

    return view('admin.boxes.index', compact('boxes'));
}


    // Show create form
    public function create()
    {
        return view('admin.boxes.create');
    }

    // Store new box
    public function store(Request $request)
    {
        $request->validate([
            'box_name'  => 'required|string|max:255',
            'box_price' => 'required|numeric',
            'box_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $imagePath = $request->file('box_image')->store('box_images', 'public');

        Box::create([
            'box_name'  => $request->box_name,
            'box_price' => $request->box_price,
            'box_image' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.boxes.index')->with('success', 'Box saved!');
    }

    // Show single box (optional)
    public function show(Box $box)
    {
        return view('admin.boxes.show', compact('box'));
    }

    // Edit form
    public function edit(Box $box)
    {
        return view('admin.boxes.update', compact('box'));
    }

    // Update box
   public function update(Request $request, $id)
{
    $box = Box::findOrFail($id);

    // Validation
    $validated = $request->validate([
        'box_name'  => 'nullable|string|max:255',
        'box_price' => 'nullable|numeric',
        'box_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        'is_active' => 'nullable|boolean',
    ]);

    // Update fields if provided
    if ($request->filled('box_name')) {
        $box->box_name = $request->box_name;
    }

    if ($request->filled('box_price')) {
        $box->box_price = $request->box_price;
    }

    // Checkbox handling
    $box->is_active = $request->has('is_active');

    // Image replacement
    if ($request->hasFile('box_image')) {
        // Delete old image if exists
        if ($box->box_image && Storage::disk('public')->exists($box->box_image)) {
            Storage::disk('public')->delete($box->box_image);
        }

        // Store new image with unique name
        $imageName = 'box_' . uniqid() . '.' . $request->file('box_image')->getClientOriginalExtension();
        $imagePath = $request->file('box_image')->storeAs('box_images', $imageName, 'public');

        // Update DB
        $box->box_image = $imagePath;
    }

    // Save box
    $box->save();

    return redirect()->route('admin.boxes.index')
        ->with('success', 'Box updated successfully.');
}


    // Delete box and image
    public function destroy(Box $box)
    {
        if ($box->box_image && Storage::disk('public')->exists($box->box_image)) {
            Storage::disk('public')->delete($box->box_image);
        }

        $box->delete();

        return redirect()->route('admin.boxes.index')->with('success', 'Box deleted successfully.');
    }

    // Toggle status (AJAX)
    public function toggleStatus(Box $box)
    {
        $box->update(['is_active' => !$box->is_active]);

        return response()->json([
            'success'   => true,
            'message'   => 'Box status updated successfully.',
            'is_active' => $box->is_active
        ]);
    }
}
