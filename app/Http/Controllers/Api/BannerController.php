<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Get all active banners
     */
    public function index()
    {
        try {
            $banners = Banner::where('is_active', true)
                ->select('image', 'is_active')
                
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
                ->select('image', 'is_active')
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
}
