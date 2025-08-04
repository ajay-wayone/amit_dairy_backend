<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Box;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    public function index()
    {
        $boxes = Box::orderBy('sort_order')->paginate(10);
        return view('admin.boxes.index', compact('boxes'));
    }

    public function create()
    {
        return view('admin.boxes.create');
    }
    // store the all value 
public function store(Request $request)
{
    $request->validate([
        'box_name' => 'required|string|max:255',
        'box_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5040',
        'box_price' => 'required|numeric|min:0',
        'is_active' => 'nullable|boolean',
    ]);
dd ($request->all());
    $data = $request->only(['box_name', 'box_price']);
    $data['is_active'] = $request->has('is_active') ? 1 : 0;

    if ($request->hasFile('box_image')) {
        $data['box_image'] = $request->file('box_image')->store('boxes', 'public');
    }

    Box::create($data);
// Check what's being sent to DB

    return redirect()->route('admin.boxes.index')->with('success', 'Box created successfully.');
}


    public function show(Box $box)
    {
        return view('admin.boxes.show', compact('box'));
    }

    public function edit(Box $box)
    {
        return view('admin.boxes.edit', compact('box'));
    }

    public function update(Request $request, Box $box)
    {
        $request->validate([
            'box_name' => 'required|string|max:255',
            'box_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'box_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
         
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('box_image')) {
            if ($box->box_image) {
                \Storage::disk('public')->delete($box->box_image);
            }
            $data['box_image'] = $request->file('box_image')->store('boxes', 'public');
        }

        $box->update($data);

        return redirect()->route('admin.boxes.index')
            ->with('success', 'Box updated successfully.');
    }

    public function destroy(Box $box)
    {
        if ($box->box_image) {
            \Storage::disk('public')->delete($box->box_image);
        }
        
        $box->delete();

        return redirect()->route('admin.boxes.index')
            ->with('success', 'Box deleted successfully.');
    }

    public function toggleStatus(Box $box)
    {
        $box->update(['is_active' => !$box->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Box status updated successfully.',
            'is_active' => $box->is_active
        ]);
    }
}
