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
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AdvancePaymentController;
use App\Http\Controllers\Api\OfferController;



use Razorpay\Api\Api;

use App\Http\Controllers\Api\PolicyController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\BoxController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Validator;







Route::get('test', function () {
    return response()->json([
        'status' => true,
        'message' => 'API routes working fine'
    ]);
});



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

    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::get('get-current-user', [AuthController::class, 'getCurrentUser']);
    // });







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
    // Public advance payment routes
    // Public advance payment routes
    Route::prefix('advance-payments')->group(function () {
        Route::get('/', [AdvancePaymentController::class, 'index']);   // All advance payments
        Route::get('{id}', [AdvancePaymentController::class, 'show']); // Single advance payment
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
        Route::get('/', [NotificationController::class, 'getNotifications']);
        Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
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
        Route::get('razorpay-key', function () {
            $config = \App\Services\GatewayService::getConfig('razorpay');
            return response()->json([
                'status' => true,
                'key' => $config['key'] ?? config('services.razorpay.key'),
            ]);
        });
        Route::get('{key}', [WebsiteSettingsController::class, 'show']);
    });

    // Public policy routes
    // Route::prefix('policies')->group(function () {
    //     Route::get('/', [PolicyController::class, 'index']);
    //     Route::get('types', [PolicyController::class, 'types']);
    //     Route::get('terms', [PolicyController::class, 'terms']);
    //     Route::get('privacy', [PolicyController::class, 'privacy']);
    //     Route::get('refund', [PolicyController::class, 'refund']);
    //     Route::get('return', [PolicyController::class, 'return']);
    //     Route::get('disclaimer', [PolicyController::class, 'disclaimer']);
    //     Route::get('shipping', [PolicyController::class, 'shipping']);
    //     Route::get('cancellation', [PolicyController::class, 'cancellation']);
    //     Route::get('{type}', [PolicyController::class, 'show']);
    // });

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('profile', [AuthController::class, 'profile']);
        });











        // Cart routes
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('add', [CartController::class, 'addToCart']);
            Route::put('quantity/{id}', [CartController::class, 'updateQuantity']);
            Route::delete('{id}', [CartController::class, 'removeFromCart']);
            Route::delete('/', [CartController::class, 'clearCart']);
            Route::get('summary', [CartController::class, 'summary']);
            Route::post('select-item-box', [CartController::class, 'selectItemBox']);
        });

        // Route::post('select-item-box', [CartController::class, 'selectItemBox']);




        // Wishlist routes
        // Route::prefix('wishlist')->group(function () {
        //     Route::get('/', [WishlistController::class, 'index']);
        //     Route::post('add', [WishlistController::class, 'addToWishlist']);
        //     Route::delete('{id}', [WishlistController::class, 'removeFromWishlist']);
        //     Route::delete('/', [WishlistController::class, 'clearWishlist']);
        //     Route::get('check/{productId}', [WishlistController::class, 'checkWishlist']);
        //     Route::get('summary', [WishlistController::class, 'summary']);
        // });








        Route::get('offers', [OfferController::class, 'getOffers']);

        Route::post('createOffer', [OfferController::class, 'createOffer']);

        Route::post('offer/update/{id}', [OfferController::class, 'updateOffer']);
        Route::delete('offer/delete/{id}', [OfferController::class, 'deleteOffer']);
        Route::get('latest-offer', [OfferController::class, 'latestOffer']);








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
        // Public payment routes
        Route::prefix(prefix: 'payments')->group(function () {
            Route::post('create', [PaymentController::class, 'pay']);

            Route::post('webhook', [PaymentController::class, 'webhook']);
        });


        Route::post('verify-payment', [PaymentController::class, 'verifyRazorpayPayment']);




        Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
            Route::get('/', [PaymentController::class, 'index']);

            Route::get('{id}', [PaymentController::class, 'show']);
        });
        // address

        // Route::middleware('auth:sanctum')->group(function () {
        //     Route::get('/addresses', [AddressController::class, 'index']);   // GET
        //     Route::post('/addresses', [AddressController::class, 'store']);  // POST
        //     Route::put('/addresses/{id}', [AddressController::class, 'update']); // UPDATE
        // });

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


// Route::prefix('wishlist')->group(function () {
// Route::middleware('auth:sanctum')->prefix('wishlist')->group(function () {
//     Route::get('index', [WishlistController::class, 'index']);
//     Route::post('add', [WishlistController::class, 'addToWishlist']);
//     Route::delete('remove/{id}', [WishlistController::class, 'removeFromWishlist']);
//     Route::delete('clear', [WishlistController::class, 'clearWishlist']);
//     Route::get('check/{productId}', [WishlistController::class, 'checkWishlist']);
//     Route::get('summary', [WishlistController::class, 'summary']);
// });



Route::get('get-wishlist', [WishlistController::class, 'getWishlist']);


Route::post('add-wishlist', [WishlistController::class, 'addWishlist']);

Route::post('delete-wishlist', [WishlistController::class, 'deleteWishlist']);




Route::middleware('auth:sanctum')->group(function () {
    Route::get('addresses', [AddressController::class, 'index']); // GET current user's addresses

    Route::post('addresses/store', [AddressController::class, 'store']);

    Route::put('update/addresses/{id}', [AddressController::class, 'update']);


    // Route::post('addresses/store', [AddressController::class, 'store']);  // POST

    // Route::put('update/addresses/{id}', [AddressController::class, 'update']); // UPDATE
});



Route::get('terms-condition', [PolicyController::class, 'terms']);       // Terms & Conditions
Route::get('privacy-policy', [PolicyController::class, 'privacy']);   // Privacy Policy
Route::get('refund-policy', [PolicyController::class, 'refund']);     // Refund Policy




Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('get-current-user', [AuthController::class, 'getCurrentUser']);
});

Route::middleware('auth:api')->post('update-profile', [AuthController::class, 'updateProfile']);


Route::get('/pincodes', [AddressController::class, 'Pincode']);


