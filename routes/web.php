<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BoxController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryLocationController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\PolicyController;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminController::class, 'login']);

        // Forgot Password Routes
        Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('forgot-password');
        Route::post('send-otp', [ForgotPasswordController::class, 'sendOTP'])->name('send-otp');
        Route::get('verify-otp', [ForgotPasswordController::class, 'showVerifyOTP'])->name('verify-otp');
        Route::post('verify-otp', [ForgotPasswordController::class, 'verifyOTP'])->name('verify-otp');
        Route::get('reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('reset-password');
        Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset-password');
    });

    // Protected admin routes
    Route::middleware('admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AdminController::class, 'logout'])->name('logout');

        // Categories
        Route::resource('categories', CategoryController::class);
        Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

        // Subcategories
        Route::resource('subcategories', SubcategoryController::class);

        Route::post('subcategories/{subcategory}/toggle-status', [SubcategoryController::class, 'toggleStatus'])->name('subcategories.toggle-status');

        // Products
        Route::get('products/get-subcategories', [ProductController::class, 'getSubcategories'])->name('products.get-subcategories');

// Products - Main routes
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

        // Orders
        Route::resource('orders', OrderController::class);
        Route::get('orders/new', [OrderController::class, 'newOrders'])->name('orders.new');
        Route::get('orders/ready', [OrderController::class, 'readyOrders'])->name('orders.ready');
        Route::get('orders/dispatched', [OrderController::class, 'dispatchedOrders'])->name('orders.dispatched');
        Route::get('orders/delivered', [OrderController::class, 'deliveredOrders'])->name('orders.delivered');
        Route::get('orders/cancelled', [OrderController::class, 'cancelledOrders'])->name('orders.cancelled');
        Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

        // Customers
        Route::resource('customers', CustomerController::class);
        Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::post('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

        // Banners
        Route::resource('banners', BannerController::class);
        Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');

        // Boxes
        Route::resource('boxes', BoxController::class);
        Route::post('boxes/{box}/toggle-status', [BoxController::class, 'toggleStatus'])->name('boxes.toggle-status');

        // Testimonials
        Route::resource('testimonials', TestimonialController::class);
        Route::post('testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');

        // Subscriptions
        Route::resource('subscriptions', SubscriptionController::class);
        Route::get('subscriptions/list', [SubscriptionController::class, 'list'])->name('subscriptions.list');
        Route::post('subscriptions/{subscription}/toggle-status', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle-status');

        // FAQs
        Route::resource('faqs', FaqController::class);
        Route::post('faqs/{faq}/toggle-status', [FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');

        // Newsletters
        Route::resource('newsletters', NewsletterController::class);
        Route::post('newsletters/{newsletter}/toggle-status', [NewsletterController::class, 'toggleStatus'])->name('newsletters.toggle-status');

        // Contact Enquiries
        Route::resource('contacts', ContactController::class)->except(['create', 'store']);
        Route::post('contacts/{contact}/update-status', [ContactController::class, 'updateStatus'])->name('contacts.update-status');

        // Delivery Locations
        Route::resource('delivery-locations', DeliveryLocationController::class);
        Route::post('delivery-locations/{location}/toggle-status', [DeliveryLocationController::class, 'toggleStatus'])->name('delivery-locations.toggle-status');

                // Policies
        Route::resource('policies', PolicyController::class);
        Route::post('policies/{policy}/toggle-status', [PolicyController::class, 'toggleStatus'])->name('policies.toggle-status');

        Route::prefix('admin/policies')->name('policies.')->group(function () {
            Route::get('{type}', [PolicyController::class, 'show'])->name('show');
            Route::put('{type}', [PolicyController::class, 'update'])->name('update');
        });
        // Contact Details
        Route::get('contact-details', function () {
            return view('admin.contact-details.index');
        })->name('contact-details');
        Route::put('contact-details', function () {
            // TODO: Implement contact details update
            return redirect()->back()->with('success', 'Settings updated successfully!');
        })->name('contact-details.update');

        // Change Credentials
        Route::get('change-credentials', function () {
            return view('admin.change-credentials.index');
        })->name('change-credentials');
        Route::put('change-credentials', function () {
            // TODO: Implement password change
            return redirect()->back()->with('success', 'Password updated successfully!');
        })->name('change-credentials.update');
    });
});

// Redirect root to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});
