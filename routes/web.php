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
use App\Http\Controllers\Admin\AdvancePaymentController;

use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\WebsiteBannerController;
use App\Http\Controllers\Admin\WebsiteSettingsController;
use App\Http\Controllers\Admin\BlockedSlotController;
use App\Http\Controllers\Admin\OfferController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PaymentSlabController;
use App\Http\Controllers\Admin\GatewayController;
use App\Http\Controllers\Admin\SmtpController;



// Admin Routes 
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminController::class, 'login']);

        // Forgot Password Routes
        Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('forgot-password');
        Route::post('send-otp', [ForgotPasswordController::class, 'sendOTP'])->name('send-otp');
        // Route::get('verify-otp', [ForgotPasswordController::class, 'showVerifyOTP'])->name('verify-otp');
        Route::post('verify-otp', [ForgotPasswordController::class, 'verifyOTP'])->name('verify-otp');
        // Route::get('reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('reset-password');
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
        Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');
        Route::post('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

        // Orders

        Route::get('orders/ready', [OrderController::class, 'readyOrders'])->name('orders.ready');
        Route::get('orders/dispatched', [OrderController::class, 'dispatchedOrders'])->name('orders.dispatched');
        Route::get('orders/delivered', [OrderController::class, 'deliveredOrders'])->name('orders.delivered');
        Route::get('orders/cancelled', [OrderController::class, 'cancelledOrders'])->name('orders.cancelled');
        Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::resource('orders', OrderController::class);
        // Customers
        Route::resource('customers', CustomerController::class);
        Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::post('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');






















        // Website Banners
        Route::resource('banners', BannerController::class);
        Route::post('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
        Route::resource('payments', AdvancePaymentController::class)->only(['index', 'store', 'update']);


        Route::resource('website-banners', WebsiteBannerController::class);
        Route::post('website-banners/{website_banner}/toggle-status', [WebsiteBannerController::class, 'toggleStatus'])->name('website-banners.toggle-status');
        // Boxes
        Route::resource('boxes', BoxController::class);
        Route::post('boxes/{box}/toggle-status', [BoxController::class, 'toggleStatus'])->name('boxes.toggle-status');

        // Testimonials
        Route::resource('testimonials', TestimonialController::class);
        Route::post('testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');

        // Subscriptions
        Route::resource('subscriptions', SubscriptionController::class)->except(['show']);
        Route::get('subscriptions/list', [SubscriptionController::class, 'list'])->name('subscriptions.list');
        Route::post('subscriptions/{subscription}/toggle-status', [SubscriptionController::class, 'toggleStatus'])->name('subscriptions.toggle-status');

        // FAQs
        Route::resource('faqs', FaqController::class);
        Route::post('faqs/{faq}/toggle-status', [FaqController::class, 'toggleStatus'])->name('faqs.toggle-status');

        // Newsletters
        Route::post('newsletters/{newsletter}/toggle-status', [NewsletterController::class, 'toggleStatus'])->name('newsletters.toggle-status');
        Route::resource('newsletters', NewsletterController::class);

        // Contact Enquiries
        Route::resource('contacts', ContactController::class)->except(['create', 'store']);
        Route::post('contacts/{contact}/update-status', [ContactController::class, 'updateStatus'])->name('contacts.update-status');

        // Delivery Locations
        Route::resource('delivery-locations', DeliveryLocationController::class);
        Route::post('delivery-locations/{location}/toggle-status', [DeliveryLocationController::class, 'toggleStatus'])->name('delivery-locations.toggle-status');

        // Policies
        Route::resource('policies', PolicyController::class);
        Route::post('policies/{policy}/toggle-status', [PolicyController::class, 'toggleStatus'])->name('policies.toggle-status');
        // Contact Details

        Route::get('contact-details', function () {
            $id = \App\Models\WebsiteSetting::first()?->id ?? 1;
            return redirect()->route('admin.contact-details.edit', $id);
        })->name('contact-details.index');
        // Contact Details API 
        Route::get('contact-details/{id}', [WebsiteSettingsController::class, 'show'])->name('contact-details.show');

        Route::get('contact-details/{id}/edit', [WebsiteSettingsController::class, 'edit'])->name('contact-details.edit');
        Route::put('contact-details/{id}', [WebsiteSettingsController::class, 'update'])->name('contact-details.update');

        // Change Credentials
        Route::get('change-credentials', function () {
            return view('admin.change-credentials.index');
        })->name('change-credentials');
        Route::put('change-credentials', function () {
            // TODO: Implement password change
            return redirect()->back()->with('success', 'Password updated successfully!');
        })->name('change-credentials.update');

        // Gateway Settings
        Route::resource('gateways', GatewayController::class)->only(['index', 'edit', 'update']);
        Route::post('gateways/{gateway}/toggle-mode', [GatewayController::class, 'toggleMode'])->name('gateways.toggle-mode');
        Route::post('gateways/{gateway}/toggle-status', [GatewayController::class, 'toggleStatus'])->name('gateways.toggle-status');

        // SMTP Settings
        Route::get('smtp', [SmtpController::class, 'edit'])->name('smtp.edit');
        Route::put('smtp', [SmtpController::class, 'update'])->name('smtp.update');
        Route::post('smtp/toggle-status', [SmtpController::class, 'toggleStatus'])->name('smtp.toggle-status');
    });
});
// block dates routs
Route::get('blocked-slots', [BlockedSlotController::class, 'index'])->name('block.index');
Route::post('blocked-slots', [BlockedSlotController::class, 'store'])->name('block.store');
Route::delete('blocked-slots/{blockedSlot}', [BlockedSlotController::class, 'destroy'])->name('block.destroy');
Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});



// Route::prefix('admin')->name('admin.')->group(function () {

//     Route::get('/offers/create', [OfferController::class, 'create'])->name('offer.create');

//     Route::post('/offers/store', [OfferController::class, 'store'])->name('offer.store');

//     Route::put('/offers/update/{id}', [OfferController::class, 'update'])->name('offer.update');

//     Route::delete('/offers/delete/{id}', [OfferController::class, 'destroy'])->name('offer.destroy');

// });

// OFFERS + PAYMENT SLABS (INSIDE ADMIN MIDDLEWARE)
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {

    // Offers
    Route::get('/offers/create', [OfferController::class, 'create'])->name('offer.create');
    Route::post('/offers/store', [OfferController::class, 'store'])->name('offer.store');
    Route::put('/offers/update/{id}', [OfferController::class, 'update'])->name('offer.update');
    Route::delete('/offers/delete/{id}', [OfferController::class, 'destroy'])->name('offer.destroy');

    // Payment Slabs
    // PAYMENT SLABS ROUTES
    Route::post('slabs/store', [PaymentSlabController::class, 'store'])->name('slabs.store');
    Route::put('slabs/update/{slab}', [PaymentSlabController::class, 'update'])->name('slabs.update');
    Route::delete('slabs/delete/{slab}', [PaymentSlabController::class, 'destroy'])->name('slabs.delete');



});





