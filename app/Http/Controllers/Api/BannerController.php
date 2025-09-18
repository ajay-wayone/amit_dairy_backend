<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\UserNotification;

class BannerController extends Controller
{
    /**
     * Get all active banners
     */
    public function index()
    {
        try {
            $banners = Banner::where('is_active', true)
                ->select('id', 'image', 'is_active')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Banners retrieved successfully',
                'data' => $banners
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single banner details
     */
    public function show($id)
    {
        try {
            $banner = Banner::where('is_active', true)
                ->select('id', 'image', 'is_active')
                ->find($id);

            if (!$banner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Banner not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Banner details retrieved successfully',
                'data' => $banner
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add new banner
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|max:2048',
                'is_active' => 'required|boolean',
            ]);

            // Upload banner image
            $imagePath = $request->file('image')->store('banners', 'public');

            $banner = Banner::create([
                'image' => $imagePath,
                'is_active' => $request->is_active,
            ]);

            // Notify all users about new banner
            $users = User::where('is_active', true)->get();
            foreach ($users as $user) {
                $user->notify(new UserNotification(
                    'New Banner Added',
                    'A new banner has been added. Check it out!',
                    ['banner_id' => $banner->id, 'type' => 'new_banner']
                ));
            }

            return response()->json([
                'status' => true,
                'message' => 'Banner added successfully and users notified',
                'data' => $banner
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
