<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryLocation;
use Illuminate\Http\Request;

class DeliveryLocationController extends Controller
{
    /**
     * Get all delivery locations
     */
    public function index(Request $request)
    {
        try {
            $query = DeliveryLocation::where('is_active', true);

            // Search by location or pincode
            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('location', 'like', '%' . $request->search . '%')
                      ->orWhere('pincode', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by pincode
            if ($request->has('pincode')) {
                $query->where('pincode', $request->pincode);
            }

            // Sort locations
            $sortBy = $request->get('sort_by', 'location');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $allowedSortFields = ['location', 'pincode', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'location';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 50);
            $locations = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Delivery locations retrieved successfully',
                'data' => [
                    'locations' => $locations->items(),
                    'pagination' => [
                        'current_page' => $locations->currentPage(),
                        'last_page' => $locations->lastPage(),
                        'per_page' => $locations->perPage(),
                        'total' => $locations->total(),
                    ]
                ]
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
     * Check delivery availability by pincode
     */
    public function checkAvailability(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pincode' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $location = DeliveryLocation::where('pincode', $request->pincode)
                ->where('is_active', true)
                ->first();

            return response()->json([
                'status' => true,
                'message' => 'Delivery availability checked successfully',
                'data' => [
                    'pincode' => $request->pincode,
                    'is_available' => $location ? true : false,
                    'location' => $location ? $location->location : null
                ]
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
     * Get delivery location details
     */
    public function show($id)
    {
        try {
            $location = DeliveryLocation::where('is_active', true)
                ->find($id);

            if (!$location) {
                return response()->json([
                    'status' => false,
                    'message' => 'Delivery location not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Delivery location details retrieved successfully',
                'data' => $location
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
     * Search delivery locations
     */
    public function search(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'query' => 'required|string|min:2',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $locations = DeliveryLocation::where('is_active', true)
                ->where(function($q) use ($request) {
                    $q->where('location', 'like', '%' . $request->query . '%')
                      ->orWhere('pincode', 'like', '%' . $request->query . '%');
                })
                ->orderBy('location', 'asc')
                ->limit(20)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Delivery locations search results retrieved successfully',
                'data' => [
                    'locations' => $locations,
                    'search_query' => $request->query,
                    'total_results' => $locations->count()
                ]
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