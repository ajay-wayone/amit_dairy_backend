<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /**
     * Get all policies
     */
    public function index()
    {
        try {
            $policies = Policy::where('is_active', true)
                ->select('id', 'type', 'title', 'meta_title', 'meta_description', 'updated_at')
                ->orderBy('type')
                ->get();

            // Group policies by type for better organization
            $groupedPolicies = $policies->groupBy('type');

            return response()->json([
                'status' => true,
                'message' => 'Policies retrieved successfully',
                'data' => [
                    'policies' => $policies,
                    'grouped_policies' => $groupedPolicies,
                    'total_count' => $policies->count()
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
     * Get specific policy content
     */
    public function show($type)
    {
        try {
            // Validate policy type
            $validTypes = Policy::getTypes();
            if (!array_key_exists($type, $validTypes)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid policy type',
                    'valid_types' => array_keys($validTypes)
                ], 400);
            }

            $policy = Policy::where('type', $type)
                ->where('is_active', true)
                ->first();

            if (!$policy) {
                return response()->json([
                    'status' => false,
                    'message' => 'Policy not found or inactive',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Policy retrieved successfully',
                'data' => [
                    'id' => $policy->id,
                    'type' => $policy->type,
                    'type_label' => $policy->type_label,
                    'title' => $policy->title,
                    'content' => $policy->content,
                    'meta_title' => $policy->meta_title,
                    'meta_description' => $policy->meta_description,
                    'is_active' => $policy->is_active,
                    'last_updated' => $policy->updated_at->format('Y-m-d H:i:s'),
                    'created_at' => $policy->created_at->format('Y-m-d H:i:s')
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
     * Get terms and conditions
     */
    public function terms()
    {
        return $this->show('terms');
    }

    /**
     * Get privacy policy
     */
    public function privacy()
    {
        return $this->show('privacy');
    }

    /**
     * Get refund policy
     */
    public function refund()
    {
        return $this->show('refund');
    }

    /**
     * Get return policy
     */
    public function return()
    {
        return $this->show('return');
    }

    /**
     * Get disclaimer
     */
    public function disclaimer()
    {
        return $this->show('disclaimer');
    }

    /**
     * Get shipping policy
     */
    public function shipping()
    {
        return $this->show('shipping');
    }

    /**
     * Get cancellation policy
     */
    public function cancellation()
    {
        return $this->show('cancellation');
    }

    /**
     * Get all available policy types
     */
    public function types()
    {
        try {
            $types = Policy::getTypes();
            
            return response()->json([
                'status' => true,
                'message' => 'Policy types retrieved successfully',
                'data' => [
                    'types' => $types,
                    'total_types' => count($types)
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