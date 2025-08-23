<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteBannerController extends Controller
{
    public function index(Request $request)
    {
        $query = WebsiteBanner::query();

        if ($request->has('page')) {
            $query->where('page_name', $request->page);
        }

        $banners = $query->orderBy('id', 'asc')->paginate(10);
        return view('admin.banners.website.index', compact('banners'));
    }

    public function create(Request $request)
    {
        // URL se page_name parameter lenge (eg: ?page_name=home)
        $page_name = $request->query('page_name', 'home');

        return view('admin.banners.website.create', compact('page_name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_name' => 'required|string',
            'title'     => 'required|string|max:255',
            'subtitle'  => 'nullable|string|max:255',
            'image'     => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image     = $request->file('image');
                $imageName = 'banner_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('website-banners', $imageName, 'public');
            }

            WebsiteBanner::create([
                'page_name' => $request->page_name,
                'title'     => $request->title,
                'subtitle'  => $request->subtitle,
                'image'     => $imagePath,
                'status'    => 1, // default active
            ]);

            return redirect()->route('admin.website-banners.index')
                ->with('success', 'Banner created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create banner: ' . $e->getMessage());
        }
    }

   public function edit(WebsiteBanner $websiteBanner)
{
    // Route model binding se $websiteBanner already fetched hai
    return view('admin.banners.website.edit', [
        'banner' => $websiteBanner // Blade me $banner variable ke liye
    ]);
}


    public function update(Request $request, WebsiteBanner $websiteBanner)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                // delete old image
                if ($websiteBanner->image && Storage::disk('public')->exists($websiteBanner->image)) {
                    Storage::disk('public')->delete($websiteBanner->image);
                }

                $image     = $request->file('image');
                $imageName = 'banner_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('website-banners', $imageName, 'public');
                $websiteBanner->image = $imagePath;
            }

            $websiteBanner->title    = $request->title;
            $websiteBanner->subtitle = $request->subtitle;
            $websiteBanner->save();

            return redirect()->route('admin.website-banners.index')
                             ->with('success', 'Banner updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function destroy(WebsiteBanner $websiteBanner)
    {
        try {
            // Delete image from storage
            if ($websiteBanner->image && Storage::disk('public')->exists($websiteBanner->image)) {
                Storage::disk('public')->delete($websiteBanner->image);
            }
            
            $websiteBanner->delete();

            return redirect()->route('admin.website-banners.index')
                             ->with('success', 'Banner deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.website-banners.index')
                             ->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }
    public function toggleStatus(WebsiteBanner $websiteBanner)
{
    try {
        $websiteBanner->status = !$websiteBanner->status;
        $websiteBanner->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Status updated successfully',
            'status' => $websiteBanner->status
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update status'
        ], 500);
    }
}
}