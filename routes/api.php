<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    
    // Public routes (no authentication required)
    Route::prefix('auth')->group(function () {
        Route::post('signup', [AuthController::class, 'signup']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Public product routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('featured', [ProductController::class, 'featured']);
        Route::get('special', [ProductController::class, 'special']);
        Route::get('new-arrivals', [ProductController::class, 'newArrivals']);
        Route::get('search', [ProductController::class, 'search']);
        Route::get('filters', [ProductController::class, 'filters']);
        Route::get('category/{categoryId}', [ProductController::class, 'productsByCategory']);
        Route::get('subcategory/{subcategoryId}', [ProductController::class, 'productsBySubcategory']);
        Route::get('{id}', [ProductController::class, 'show']);
    });

    // Public category routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [ProductController::class, 'categories']);
        Route::get('subcategories', [ProductController::class, 'subcategories']);
    });

    // Public policy routes
    Route::prefix('policies')->group(function () {
        Route::get('/', [PolicyController::class, 'index']);
        Route::get('terms', [PolicyController::class, 'terms']);
        Route::get('privacy', [PolicyController::class, 'privacy']);
        Route::get('refund', [PolicyController::class, 'refund']);
        Route::get('return', [PolicyController::class, 'return']);
        Route::get('disclaimer', [PolicyController::class, 'disclaimer']);
        Route::get('shipping', [PolicyController::class, 'shipping']);
        Route::get('cancellation', [PolicyController::class, 'cancellation']);
        Route::get('{type}', [PolicyController::class, 'show']);
    });

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('profile', [AuthController::class, 'profile']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
        });

        // Order routes
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('statistics', [OrderController::class, 'statistics']);
            Route::get('number/{orderNumber}', [OrderController::class, 'getByOrderNumber']);
            Route::get('{id}', [OrderController::class, 'show']);
            Route::post('{id}/cancel', [OrderController::class, 'cancel']);
            Route::get('{id}/track', [OrderController::class, 'track']);
        });
    });
});
