<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WebsiteSettingsController extends Controller
{
    /**
     * Get website settings
     */
    public function index()
    {
        try {
            $settings = Cache::remember('website_settings', 3600, function () {
                return [
                    'app_name' => config('app.name', 'Amit Kumar'),
                    'app_description' => config('app.description', 'Your trusted online store'),
                    'contact_email' => config('mail.from.address', 'info@amitkumar.com'),
                    'contact_phone' => '+91 1234567890',
                    'contact_address' => '123 Main Street, City, State, PIN',
                    'social_media' => [
                        'facebook' => 'https://facebook.com/amitkumar',
                        'twitter' => 'https://twitter.com/amitkumar',
                        'instagram' => 'https://instagram.com/amitkumar',
                        'linkedin' => 'https://linkedin.com/company/amitkumar',
                    ],
                    'business_hours' => [
                        'monday' => '9:00 AM - 6:00 PM',
                        'tuesday' => '9:00 AM - 6:00 PM',
                        'wednesday' => '9:00 AM - 6:00 PM',
                        'thursday' => '9:00 AM - 6:00 PM',
                        'friday' => '9:00 AM - 6:00 PM',
                        'saturday' => '10:00 AM - 4:00 PM',
                        'sunday' => 'Closed',
                    ],
                    'delivery_info' => [
                        'free_delivery_threshold' => 500,
                        'delivery_charge' => 50,
                        'delivery_time' => '2-3 business days',
                    ],
                    'payment_methods' => [
                        'cod' => true,
                        'online' => true,
                        'upi' => true,
                        'card' => true,
                    ],
                    'currency' => 'INR',
                    'currency_symbol' => '₹',
                    'maintenance_mode' => false,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Website settings retrieved successfully',
                'data' => $settings
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
     * Get specific setting
     */
    public function show($key)
    {
        try {
            $settings = Cache::remember('website_settings', 3600, function () {
                return [
                    'app_name' => config('app.name', 'Amit Kumar'),
                    'app_description' => config('app.description', 'Your trusted online store'),
                    'contact_email' => config('mail.from.address', 'info@amitkumar.com'),
                    'contact_phone' => '+91 1234567890',
                    'contact_address' => '123 Main Street, City, State, PIN',
                    'social_media' => [
                        'facebook' => 'https://facebook.com/amitkumar',
                        'twitter' => 'https://twitter.com/amitkumar',
                        'instagram' => 'https://instagram.com/amitkumar',
                        'linkedin' => 'https://linkedin.com/company/amitkumar',
                    ],
                    'business_hours' => [
                        'monday' => '9:00 AM - 6:00 PM',
                        'tuesday' => '9:00 AM - 6:00 PM',
                        'wednesday' => '9:00 AM - 6:00 PM',
                        'thursday' => '9:00 AM - 6:00 PM',
                        'friday' => '9:00 AM - 6:00 PM',
                        'saturday' => '10:00 AM - 4:00 PM',
                        'sunday' => 'Closed',
                    ],
                    'delivery_info' => [
                        'free_delivery_threshold' => 500,
                        'delivery_charge' => 50,
                        'delivery_time' => '2-3 business days',
                    ],
                    'payment_methods' => [
                        'cod' => true,
                        'online' => true,
                        'upi' => true,
                        'card' => true,
                    ],
                    'currency' => 'INR',
                    'currency_symbol' => '₹',
                    'maintenance_mode' => false,
                ];
            });

            if (!isset($settings[$key])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Setting not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Setting retrieved successfully',
                'data' => [
                    'key' => $key,
                    'value' => $settings[$key]
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
     * Get contact information
     */
    public function contact()
    {
        try {
            $contact = [
                'email' => config('mail.from.address', 'info@amitkumar.com'),
                'phone' => '+91 1234567890',
                'address' => '123 Main Street, City, State, PIN',
                'business_hours' => [
                    'monday' => '9:00 AM - 6:00 PM',
                    'tuesday' => '9:00 AM - 6:00 PM',
                    'wednesday' => '9:00 AM - 6:00 PM',
                    'thursday' => '9:00 AM - 6:00 PM',
                    'friday' => '9:00 AM - 6:00 PM',
                    'saturday' => '10:00 AM - 4:00 PM',
                    'sunday' => 'Closed',
                ],
                'social_media' => [
                    'facebook' => 'https://facebook.com/amitkumar',
                    'twitter' => 'https://twitter.com/amitkumar',
                    'instagram' => 'https://instagram.com/amitkumar',
                    'linkedin' => 'https://linkedin.com/company/amitkumar',
                ],
            ];

            return response()->json([
                'status' => true,
                'message' => 'Contact information retrieved successfully',
                'data' => $contact
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
     * Get delivery information
     */
    public function delivery()
    {
        try {
            $delivery = [
                'free_delivery_threshold' => 500,
                'delivery_charge' => 50,
                'delivery_time' => '2-3 business days',
                'express_delivery' => [
                    'available' => true,
                    'charge' => 100,
                    'time' => '1 business day',
                ],
                'return_policy' => [
                    'days' => 7,
                    'conditions' => 'Product must be unused and in original packaging',
                ],
            ];

            return response()->json([
                'status' => true,
                'message' => 'Delivery information retrieved successfully',
                'data' => $delivery
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
     * Get payment methods
     */
    public function paymentMethods()
    {
        try {
            $paymentMethods = [
                'cod' => [
                    'name' => 'Cash on Delivery',
                    'available' => true,
                    'description' => 'Pay when you receive your order',
                ],
                'online' => [
                    'name' => 'Online Payment',
                    'available' => true,
                    'description' => 'Pay securely online',
                ],
                'upi' => [
                    'name' => 'UPI Payment',
                    'available' => true,
                    'description' => 'Pay using UPI',
                ],
                'card' => [
                    'name' => 'Credit/Debit Card',
                    'available' => true,
                    'description' => 'Pay using credit or debit card',
                ],
            ];

            return response()->json([
                'status' => true,
                'message' => 'Payment methods retrieved successfully',
                'data' => $paymentMethods
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