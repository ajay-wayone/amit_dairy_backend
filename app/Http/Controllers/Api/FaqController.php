<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Get all FAQs
     */
    public function index(Request $request)
    {
        try {
            $query = Faq::where('is_active', true);

            // Search by question or answer
            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('question', 'like', '%' . $request->search . '%')
                      ->orWhere('answer', 'like', '%' . $request->search . '%');
                });
            }

            // Sort FAQs
            $sortBy = $request->get('sort_by', 'sort_order');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $allowedSortFields = ['question', 'sort_order', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'sort_order';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 20);
            $faqs = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'FAQs retrieved successfully',
                'data' => [
                    'faqs' => $faqs->items(),
                    'pagination' => [
                        'current_page' => $faqs->currentPage(),
                        'last_page' => $faqs->lastPage(),
                        'per_page' => $faqs->perPage(),
                        'total' => $faqs->total(),
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
     * Get featured FAQs
     */
    public function featured()
    {
        try {
            $faqs = Faq::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->limit(10)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Featured FAQs retrieved successfully',
                'data' => $faqs
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
     * Search FAQs
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

            $faqs = Faq::where('is_active', true)
                ->where(function($q) use ($request) {
                    $q->where('question', 'like', '%' . $request->query . '%')
                      ->orWhere('answer', 'like', '%' . $request->query . '%');
                })
                ->orderBy('sort_order', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'FAQ search results retrieved successfully',
                'data' => [
                    'faqs' => $faqs,
                    'search_query' => $request->query,
                    'total_results' => $faqs->count()
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
     * Get FAQ details
     */
    public function show($id)
    {
        try {
            $faq = Faq::where('is_active', true)
                ->find($id);

            if (!$faq) {
                return response()->json([
                    'status' => false,
                    'message' => 'FAQ not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'FAQ details retrieved successfully',
                'data' => $faq
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