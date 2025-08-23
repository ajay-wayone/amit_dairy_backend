<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255|unique:newsletters,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $newsletter = Newsletter::create([
                'email' => $request->email,
                'is_active' => true,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Successfully subscribed to newsletter!',
                'data' => [
                    'id' => $newsletter->id,
                    'email' => $newsletter->email,
                    'subscribed_at' => $newsletter->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255|exists:newsletters,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $newsletter = Newsletter::where('email', $request->email)->first();
            
            if (!$newsletter) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email not found in newsletter subscriptions',
                ], 404);
            }

            $newsletter->update(['is_active' => false]);

            return response()->json([
                'status' => true,
                'message' => 'Successfully unsubscribed from newsletter!',
                'data' => [
                    'email' => $newsletter->email,
                    'unsubscribed_at' => now()
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
     * Check subscription status
     */
    public function checkStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $newsletter = Newsletter::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'Subscription status retrieved successfully',
                'data' => [
                    'email' => $request->email,
                    'is_subscribed' => $newsletter ? $newsletter->is_active : false,
                    'subscribed_at' => $newsletter ? $newsletter->created_at : null
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