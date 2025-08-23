<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    /**
     * Get all testimonials
     */
    public function index(Request $request)
    {
        try {
            $query = Testimonial::where('is_active', true);

            // Search by customer name
            if ($request->has('search')) {
                $query->where('customer_name', 'like', '%' . $request->search . '%');
            }

            // Filter by rating
            if ($request->has('rating')) {
                $query->where('rating', $request->rating);
            }

            // Sort testimonials
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $allowedSortFields = ['customer_name', 'rating', 'sort_order', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'sort_order';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 10);
            $testimonials = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Testimonials retrieved successfully',
                'data' => [
                    'testimonials' => $testimonials->items(),
                    'pagination' => [
                        'current_page' => $testimonials->currentPage(),
                        'last_page' => $testimonials->lastPage(),
                        'per_page' => $testimonials->perPage(),
                        'total' => $testimonials->total(),
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
     * Get featured testimonials
     */
    public function featured()
    {
        try {
            $testimonials = Testimonial::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->limit(6)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Featured testimonials retrieved successfully',
                'data' => $testimonials
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
     * Get testimonial details
     */
    public function show($id)
    {
        try {
            $testimonial = Testimonial::where('is_active', true)
                ->find($id);

            if (!$testimonial) {
                return response()->json([
                    'status' => false,
                    'message' => 'Testimonial not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Testimonial details retrieved successfully',
                'data' => $testimonial
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
     * Create testimonial (for customers)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'testimonial' => 'required|string|min:10',
                'rating' => 'required|integer|min:1|max:5',
                'customer_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $request->only(['customer_name', 'testimonial', 'rating']);
            $data['is_active'] = false; // New testimonials need approval
            $data['sort_order'] = 0;

            if ($request->hasFile('customer_image')) {
                $image = $request->file('customer_image');
                $imageName = 'testimonial_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $data['customer_image'] = $image->storeAs('testimonials', $imageName, 'public');
            }

            $testimonial = Testimonial::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Testimonial submitted successfully. It will be reviewed and published soon.',
                'data' => $testimonial
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