<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Show list of all banners
    public function index()
    {
        $banners = Banner::orderBy('id', 'desc')->get(); // just order by id
        return view('admin.banners.index', compact('banners'));
    }

    // Show form to create banner
    public function create()
    {
        return view('admin.banners.create');
    }

    // Store new banner
    public function store(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:20480',
            'is_active' => 'nullable|boolean',
        ]);

        $banner = new Banner();

        if ($request->hasFile('image')) {
            $imageName = 'banner_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('banners', $imageName, 'public');
            $banner->image = $path;
        }

        $banner->is_active = $request->has('is_active') ? 1 : 0;
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Banner added successfully!');
    }

    // Edit banner by id
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    // Update banner
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:20480',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }

            $imageName = 'banner_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('banners', $imageName, 'public');
            $banner->image = $path;
        }

        $banner->is_active = $request->has('is_active') ? 1 : 0;
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    // Delete banner
    public function destroy(Banner $banner)
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }

    // Toggle status (AJAX)
    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return response()->json([
            'success'   => true,
            'message'   => 'Banner status updated.',
            'is_active' => $banner->is_active,
        ]);
    }
  

}
