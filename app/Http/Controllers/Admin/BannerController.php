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
        $banners = Banner::orderBy('page_name')->get();
        return view('admin.banners.index', compact('banners'));
    }
public function create(Request $request)
{
    $selectedPage = $request->input('page_name'); // Get selected page from form
    $currentImage = null;

    if ($selectedPage) {
        $banner = Banner::where('page_name', $selectedPage)->first();
        if ($banner && $banner->image) {
            $currentImage = $banner->image;
        }
    }

    return view('admin.banners.create', compact('selectedPage', 'currentImage'));
}

public function store(Request $request)
{
    if ($request->has('submit_banner')) {
        $request->validate([
            'page_name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $selectedPage = $request->input('page_name');

        $banner = Banner::firstOrNew(['page_name' => $selectedPage]);

        if ($request->hasFile('image')) {
            if ($banner->image && file_exists(public_path($banner->image))) {
                unlink(public_path($banner->image));
            }

            $image = $request->file('image');
            $imagePath = 'uploads/banners/' . time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banners'), $imagePath);
            $banner->image = $imagePath;
        }

        $banner->page_name = $selectedPage;
        $banner->save();

    return redirect()->route('admin.banners.create', ['page_name' => $selectedPage])
    ->with('success', 'Banner updated successfully!');
    }

    // Show the form with selected page (for first load or error case)
    $selectedPage = $request->input('page_name');
    $currentImage = null;

    if ($selectedPage) {
        $banner = Banner::where('page_name', $selectedPage)->first();
        if ($banner && $banner->image) {
            $currentImage = $banner->image;
        }
    }

    return view('admin.banners.create', compact('selectedPage', 'currentImage'));
}



    // // Show edit form for banner (optional if using only per-page form)
    // public function edit(Banner $banner)
    // {
    //     return view('admin.banners.edit', compact('banner'));
    // }

    // // Update banner (if using individual banner edit page)
    // public function update(Request $request, Banner $banner)
    // {
    //     $request->validate([
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
    //     ]);

    //     if ($request->hasFile('image')) {
    //         if ($banner->image) {
    //             Storage::disk('public')->delete($banner->image);
    //         }

    //         $banner->image = $request->file('image')->store('banners', 'public');
    //     }

    //     $banner->save();

    //     return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    // }

    // // Delete a banner
    // public function destroy(Banner $banner)
    // {
    //     if ($banner->image) {
    //         Storage::disk('public')->delete($banner->image);
    //     }

    //     $banner->delete();

    //     return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    // }

    // // Toggle banner status via AJAX (optional)
    // public function toggleStatus(Banner $banner)
    // {
    //     $banner->update(['is_active' => !$banner->is_active]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Banner status updated.',
    //         'is_active' => $banner->is_active,
    //     ]);
    // }
}
