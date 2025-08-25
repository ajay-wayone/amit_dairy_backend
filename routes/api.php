<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\DeliveryLocationController;
use App\Http\Controllers\Api\WebsiteSettingsController;
use App\Http\Controllers\Api\NotificationController;

use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\BoxController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    
    // Public routes (no authentication required)
    Route::prefix('auth')->group(function () {
        Route::post('signup', [AuthController::class, 'signup']);
        Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('create-password', [AuthController::class, 'createPassword']);
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

    // Public review routes
    Route::prefix('reviews')->group(function () {
        Route::get('product/{productId}', [ReviewController::class, 'productReviews']);
        Route::get('product/{productId}/stats', [ReviewController::class, 'productReviewStats']);
    });

    // Public category routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('featured', [CategoryController::class, 'featured']);
        Route::get('{id}', [CategoryController::class, 'show']);
        Route::get('{id}/subcategories', [CategoryController::class, 'subcategories']);
    });

    // Public subcategory routes
    Route::prefix('subcategories')->group(function () {
        Route::get('/', [SubcategoryController::class, 'index']);
        Route::get('featured', [SubcategoryController::class, 'featured']);
        Route::get('category/{categoryId}', [SubcategoryController::class, 'byCategory']);
        Route::get('{id}', [SubcategoryController::class, 'show']);
    });

    // Public testimonial routes
    Route::prefix('testimonials')->group(function () {
        Route::get('/', [TestimonialController::class, 'index']);
        Route::get('featured', [TestimonialController::class, 'featured']);
        Route::get('{id}', [TestimonialController::class, 'show']);
        Route::post('/', [TestimonialController::class, 'store']);
    });

    // Public banner routes
   Route::prefix('banners')->group(function () {
    // Get all active banners
    Route::get('/', [BannerController::class, 'index']);

    // Get single banner by ID
    Route::get('{id}', [BannerController::class, 'show']);
});
Route::prefix('boxes')->group(function () {
    Route::get('/', [BoxController::class, 'index']);   // All boxes
    Route::get('{id}', [BoxController::class, 'show']); // Single box
});
    // Public FAQ routes
    Route::prefix('faqs')->group(function () {
        Route::get('/', [FaqController::class, 'index']);
        Route::get('featured', [FaqController::class, 'featured']);
        Route::get('search', [FaqController::class, 'search']);
        Route::get('{id}', [FaqController::class, 'show']);
    });
  Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    // User की सभी notifications fetch करना
    Route::get('/', [NotificationController::class, 'getNotifications']);

    // Single notification mark as read
    Route::post('{id}/read', [NotificationController::class, 'markAsRead']);

    // Unread notifications count
    Route::get('unread-count', [NotificationController::class, 'unreadCount']);
});
    // Public contact routes
    Route::post('contact', [ContactController::class, 'store']);

    // Public subscription routes
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::get('featured', [SubscriptionController::class, 'featured']);
        Route::get('validity/{validDays}', [SubscriptionController::class, 'byValidity']);
        Route::get('{id}', [SubscriptionController::class, 'show']);
    });

    // Public newsletter routes
    Route::prefix('newsletter')->group(function () {
        Route::post('subscribe', [NewsletterController::class, 'subscribe']);
        Route::post('unsubscribe', [NewsletterController::class, 'unsubscribe']);
        Route::post('check-status', [NewsletterController::class, 'checkStatus']);
    });

    // Public delivery location routes
    Route::prefix('delivery-locations')->group(function () {
        Route::get('/', [DeliveryLocationController::class, 'index']);
        Route::get('search', [DeliveryLocationController::class, 'search']);
        Route::get('check-availability', [DeliveryLocationController::class, 'checkAvailability']);
        Route::get('{id}', [DeliveryLocationController::class, 'show']);
    });

    // Public website settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', [WebsiteSettingsController::class, 'index']);
        Route::get('contact', [WebsiteSettingsController::class, 'contact']);
        Route::get('delivery', [WebsiteSettingsController::class, 'delivery']);
        Route::get('payment-methods', [WebsiteSettingsController::class, 'paymentMethods']);
        Route::get('{key}', [WebsiteSettingsController::class, 'show']);
    });

    // Public policy routes
    Route::prefix('policies')->group(function () {
        Route::get('/', [PolicyController::class, 'index']);
        Route::get('types', [PolicyController::class, 'types']);
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
        // Cart routes
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('add', [CartController::class, 'addToCart']);
            Route::put('{id}/quantity', [CartController::class, 'updateQuantity']);
            Route::delete('{id}', [CartController::class, 'removeFromCart']);
            Route::delete('/', [CartController::class, 'clearCart']);
            Route::get('summary', [CartController::class, 'summary']);
        });

        // Wishlist routes
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class, 'index']);
            Route::post('add', [WishlistController::class, 'addToWishlist']);
            Route::delete('{id}', [WishlistController::class, 'removeFromWishlist']);
            Route::delete('/', [WishlistController::class, 'clearWishlist']);
            Route::get('check/{productId}', [WishlistController::class, 'checkWishlist']);
            Route::get('summary', [WishlistController::class, 'summary']);
        });

        // Order routes
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('statistics', [OrderController::class, 'statistics']);
            Route::get('subscription-orders', [OrderController::class, 'subscriptionOrders']);
            Route::get('new-orders', [OrderController::class, 'newOrders']);
            Route::get('number/{orderNumber}', [OrderController::class, 'getByOrderNumber']);
            Route::get('{id}', [OrderController::class, 'show']);
            Route::post('{id}/cancel', [OrderController::class, 'cancel']);
            Route::get('{id}/track', [OrderController::class, 'track']);
        });

        // Review routes (protected)
        Route::prefix('reviews')->group(function () {
            Route::get('/', [ReviewController::class, 'userReviews']);
            Route::post('/', [ReviewController::class, 'store']);
            Route::put('{id}', [ReviewController::class, 'update']);
            Route::delete('{id}', [ReviewController::class, 'destroy']);
        });

        // Contact routes (protected)
        Route::prefix('contact')->group(function () {
            Route::get('/', [ContactController::class, 'index']);
            Route::get('{id}', [ContactController::class, 'show']);
        });
    });
});
