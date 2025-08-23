<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Get all subscription plans
     */
    public function index(Request $request)
    {
        try {
            $query = Subscription::where('is_active', true);

            // Sort subscriptions
            $sortBy = $request->get('sort_by', 'valid_days');
            $sortOrder = $request->get('sort_order', 'asc');
            
            $allowedSortFields = ['plan_name', 'valid_days', 'amount', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'valid_days';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 20);
            $subscriptions = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'message' => 'Subscription plans retrieved successfully',
                'data' => [
                    'subscriptions' => $subscriptions->items(),
                    'pagination' => [
                        'current_page' => $subscriptions->currentPage(),
                        'last_page' => $subscriptions->lastPage(),
                        'per_page' => $subscriptions->perPage(),
                        'total' => $subscriptions->total(),
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
     * Get featured subscription plans
     */
    public function featured()
    {
        try {
            $subscriptions = Subscription::where('is_active', true)
                ->orderBy('valid_days', 'asc')
                ->limit(5)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Featured subscription plans retrieved successfully',
                'data' => $subscriptions
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
     * Get subscription plan details
     */
    public function show($id)
    {
        try {
            $subscription = Subscription::where('is_active', true)
                ->find($id);

            if (!$subscription) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subscription plan not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Subscription plan details retrieved successfully',
                'data' => $subscription
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
     * Get subscription plans by validity
     */
    public function byValidity(Request $request, $validDays)
    {
        try {
            $subscriptions = Subscription::where('is_active', true)
                ->where('valid_days', $validDays)
                ->orderBy('amount', 'asc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Subscription plans retrieved successfully',
                'data' => [
                    'valid_days' => $validDays,
                    'subscriptions' => $subscriptions
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