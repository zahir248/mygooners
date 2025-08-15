<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ArticleController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\VideoController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\RequestController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\BillingDetailController;
use App\Http\Controllers\Client\ShippingDetailController;
use App\Http\Controllers\Client\DirectCheckoutController;
use App\Http\Controllers\Client\RefundController;
use App\Http\Controllers\Client\FavouriteController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\SellerRequestController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\RefundController as AdminRefundController;
use App\Http\Controllers\Admin\ProductReviewController;

// Home Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Blog/Articles
Route::prefix('blog')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('blog.index');
    Route::get('/category/{category}', [ArticleController::class, 'category'])->name('blog.category');
    Route::get('/{slug}', [ArticleController::class, 'show'])->name('blog.show');
});

// Services Marketplace
Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('services.show');
});

// Product Shop
Route::prefix('shop')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('shop.index');
    Route::get('/category/{category}', [ProductController::class, 'category'])->name('shop.category');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('shop.show');
});

// Product Reviews (requires authentication)
Route::prefix('shop')->middleware('auth')->group(function () {
    Route::get('/{product}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/{product}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/{product}/review/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/{product}/review/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/{product}/review/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Cart Routes
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/count', [CartController::class, 'count'])->name('cart.count');
});

// Favourites Routes
Route::prefix('favourites')->middleware('auth')->group(function () {
    Route::get('/', [FavouriteController::class, 'index'])->name('favourites.index');
    Route::post('/add', [FavouriteController::class, 'store'])->name('favourites.store');
    Route::delete('/remove', [FavouriteController::class, 'destroy'])->name('favourites.destroy');
    Route::get('/check', [FavouriteController::class, 'check'])->name('favourites.check');
    Route::get('/count', [FavouriteController::class, 'count'])->name('favourites.count');
    Route::post('/flash-message', [FavouriteController::class, 'flashMessage'])->name('favourites.flash-message');
});

// Checkout Routes
Route::prefix('checkout')->middleware('auth')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/stripe-payment', [CheckoutController::class, 'stripePayment'])->name('checkout.stripe-payment');
    Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/orders', [CheckoutController::class, 'indexOrders'])->name('checkout.orders');
    Route::get('/orders/{orderId}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/orders/{order}/cancel', [CheckoutController::class, 'cancelOrder'])->name('checkout.cancel-order');
    Route::post('/orders/{order}/mark-delivered', [CheckoutController::class, 'markAsDelivered'])->name('checkout.mark-delivered');
    Route::post('/orders/{order}/retry-payment', [CheckoutController::class, 'retryPayment'])->name('checkout.retry-payment');
    Route::get('/orders/{order}/retry-payment', [CheckoutController::class, 'showRetryPayment'])->name('checkout.show-retry-payment');
    Route::post('/orders/{order}/retry-payment-with-method', [CheckoutController::class, 'retryPaymentWithMethod'])->name('checkout.retry-payment-with-method');
    
    // Invoice routes
    Route::get('/orders/{order}/invoice/download', [CheckoutController::class, 'downloadInvoice'])->name('checkout.invoice.download');
    Route::get('/orders/{order}/invoice/view', [CheckoutController::class, 'viewInvoice'])->name('checkout.invoice.view');
    
    // Refund routes
    Route::get('/refunds', [RefundController::class, 'index'])->name('checkout.refunds');
Route::get('/refunds/{refund}', [RefundController::class, 'show'])->name('checkout.refunds.show');
Route::patch('/refunds/{refund}', [RefundController::class, 'update'])->name('checkout.refunds.update');
Route::get('/orders/{order}/refund', [RefundController::class, 'create'])->name('checkout.refunds.create');
Route::post('/orders/{order}/refund', [RefundController::class, 'store'])->name('checkout.refunds.store');
    
    // ToyyibPay callback routes
    Route::get('/toyyibpay/return', [CheckoutController::class, 'toyyibpayReturn'])->name('checkout.toyyibpay.return');
    Route::post('/toyyibpay/callback', [CheckoutController::class, 'toyyibpayCallback'])->name('checkout.toyyibpay.callback');
    Route::get('/toyyibpay/cancel', [CheckoutController::class, 'toyyibpayCancel'])->name('checkout.toyyibpay.cancel');

// Stripe payment routes
Route::get('/stripe/payment', [CheckoutController::class, 'stripePayment'])->name('checkout.stripe.payment');
Route::get('/stripe/return', [CheckoutController::class, 'stripeReturn'])->name('checkout.stripe.return');

// Stripe webhook route (no auth middleware needed)
Route::post('/stripe/webhook', function (\Illuminate\Http\Request $request) {
    $stripeService = new \App\Services\StripeService();
    $result = $stripeService->handleWebhook($request->getContent(), $request->header('Stripe-Signature'));
    
    if ($result['success']) {
        return response('Webhook handled successfully', 200);
    } else {
        return response('Webhook error: ' . $result['message'], 400);
    }
})->name('stripe.webhook');
});

// Temporary route to run composer update (REMOVE AFTER USE) - NO AUTH REQUIRED
Route::get('/run-composer-update', function () {
    try {
        // Run composer update
        $output = shell_exec('composer update 2>&1');
        
        return response()->json([
            'success' => true,
            'message' => 'Composer update completed successfully',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error running composer update: ' . $e->getMessage()
        ], 500);
    }
})->name('composer.update');

// Addresses Routes (Unified Billing and Shipping)
Route::prefix('addresses')->middleware('auth')->group(function () {
    Route::get('/', [BillingDetailController::class, 'index'])->name('addresses.index');
    Route::get('/billing/create', [BillingDetailController::class, 'create'])->name('addresses.billing.create');
    Route::post('/billing', [BillingDetailController::class, 'store'])->name('addresses.billing.store');
    Route::get('/billing/{billingDetail}/edit', [BillingDetailController::class, 'edit'])->name('addresses.billing.edit');
    Route::put('/billing/{billingDetail}', [BillingDetailController::class, 'update'])->name('addresses.billing.update');
    Route::delete('/billing/{billingDetail}', [BillingDetailController::class, 'destroy'])->name('addresses.billing.destroy');
    Route::post('/billing/{billingDetail}/set-default', [BillingDetailController::class, 'setDefault'])->name('addresses.billing.set-default');
    
    Route::get('/shipping/create', [ShippingDetailController::class, 'create'])->name('addresses.shipping.create');
    Route::post('/shipping', [ShippingDetailController::class, 'store'])->name('addresses.shipping.store');
    Route::get('/shipping/{shippingDetail}/edit', [ShippingDetailController::class, 'edit'])->name('addresses.shipping.edit');
    Route::put('/shipping/{shippingDetail}', [ShippingDetailController::class, 'update'])->name('addresses.shipping.update');
    Route::delete('/shipping/{shippingDetail}', [ShippingDetailController::class, 'destroy'])->name('addresses.shipping.destroy');
    Route::post('/shipping/{shippingDetail}/set-default', [ShippingDetailController::class, 'setDefault'])->name('addresses.shipping.set-default');
});



// Direct Checkout Routes
Route::prefix('direct-checkout')->middleware('auth')->group(function () {
    Route::get('/', [DirectCheckoutController::class, 'index'])->name('direct-checkout.index');
    Route::post('/', [DirectCheckoutController::class, 'store'])->name('direct-checkout.store');
    Route::get('/stripe/payment', [DirectCheckoutController::class, 'stripePayment'])->name('direct-checkout.stripe.payment');
    Route::get('/success/{orderId}', [DirectCheckoutController::class, 'success'])->name('direct-checkout.success');
    Route::post('/orders/{order}/cancel', [DirectCheckoutController::class, 'cancelOrder'])->name('direct-checkout.cancel-order');
    Route::post('/orders/{order}/mark-delivered', [DirectCheckoutController::class, 'markAsDelivered'])->name('direct-checkout.mark-delivered');
    Route::post('/orders/{order}/retry-payment', [DirectCheckoutController::class, 'retryPayment'])->name('direct-checkout.retry-payment');
    Route::get('/orders/{order}/retry-payment', [DirectCheckoutController::class, 'showRetryPayment'])->name('direct-checkout.show-retry-payment');
    Route::post('/orders/{order}/retry-payment-with-method', [DirectCheckoutController::class, 'retryPaymentWithMethod'])->name('direct-checkout.retry-payment-with-method');
    
    // Invoice routes
    Route::get('/orders/{order}/invoice/download', [DirectCheckoutController::class, 'downloadInvoice'])->name('direct-checkout.invoice.download');
    Route::get('/orders/{order}/invoice/view', [DirectCheckoutController::class, 'viewInvoice'])->name('direct-checkout.invoice.view');
});

// Direct Checkout ToyyibPay return route
Route::get('/direct-checkout/toyyibpay/return', [DirectCheckoutController::class, 'toyyibpayReturn'])->name('direct-checkout.toyyibpay.return');
Route::get('/direct-checkout/toyyibpay/cancel', [DirectCheckoutController::class, 'toyyibpayCancel'])->name('direct-checkout.toyyibpay.cancel');

// Direct Checkout Stripe routes
Route::get('/direct-checkout/stripe/payment', [DirectCheckoutController::class, 'stripePayment'])->name('direct-checkout.stripe.payment');
Route::get('/direct-checkout/stripe/return', [DirectCheckoutController::class, 'stripeReturn'])->name('direct-checkout.stripe.return');

// Video Podcast Gallery
Route::prefix('videos')->group(function () {
    Route::get('/', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/{slug}', [VideoController::class, 'show'])->name('videos.show');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});
  
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $services = \App\Models\Service::where('user_id', $user->id)->whereIn('status', ['active', 'inactive'])->orderBy('created_at', 'desc')->get();
        $pendingServices = \App\Models\Service::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('is_update_request', false)
            ->orderBy('created_at', 'desc')
            ->get();
        $rejectedServices = \App\Models\Service::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->where('is_update_request', false)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get update requests for services
        $serviceUpdateRequests = \App\Models\Service::where('user_id', $user->id)
            ->where('is_update_request', true)
            ->whereIn('status', ['pending', 'rejected'])
            ->get()
            ->keyBy('original_service_id');
        
        return view('client.dashboard', compact('services', 'pendingServices', 'rejectedServices', 'serviceUpdateRequests'));
    })->name('dashboard');

    Route::post('/dashboard/show-seller-form', function () {
        session(['show_seller_form' => true]);
        return redirect()->route('dashboard');
    })->name('dashboard.show_seller_form');

    Route::post('/dashboard/hide-seller-form', function () {
        session()->forget('show_seller_form');
        return redirect()->route('dashboard');
    })->name('dashboard.hide_seller_form');

    Route::post('/dashboard/become-seller', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'bio' => 'required|string',
            'location' => 'required|string',
            'phone' => 'required|string',
            'business_name' => 'required|string',
            'business_type' => 'required|string',
            'business_registration' => 'nullable|string',
            'business_address' => 'required|string',
            'operating_area' => 'required|string',
            'website' => 'nullable|string',
            'years_experience' => 'required|integer|min:0',
            'skills' => 'required|string',
            'service_areas' => 'required|string',
            'id_document' => 'required|file|mimes:jpg,jpeg,png,pdf',
            'selfie_with_id' => 'required|file|mimes:jpg,jpeg,png',
        ]);
        // Handle file uploads
        if ($request->hasFile('id_document')) {
            $validated['id_document'] = $request->file('id_document')->store('seller_ids', 'public');
        }
        if ($request->hasFile('selfie_with_id')) {
            $validated['selfie_with_id'] = $request->file('selfie_with_id')->store('seller_selfies', 'public');
        }
        // Save all seller details to user
        $user->bio = $validated['bio'];
        $user->location = $validated['location'];
        $user->phone = $validated['phone'];
        $user->business_name = $validated['business_name'];
        $user->business_type = $validated['business_type'];
        $user->business_registration = $validated['business_registration'] ?? null;
        $user->business_address = $validated['business_address'];
        $user->operating_area = $validated['operating_area'];
        $user->website = $validated['website'] ?? null;
        $user->years_experience = $validated['years_experience'];
        $user->skills = $validated['skills'];
        $user->service_areas = $validated['service_areas'];
        $user->id_document = $validated['id_document'];
        $user->selfie_with_id = $validated['selfie_with_id'];
        $user->seller_status = 'pending';
        $user->seller_application_date = now();
        $user->save();
        session()->forget('show_seller_form');
        return redirect()->route('dashboard')->with('success', 'Permohonan penjual anda telah dihantar dan sedang menunggu kelulusan admin!');
    })->name('dashboard.become_seller');

    Route::get('/seller-info', function () {
        $user = auth()->user();
        if (!$user->is_seller) {
            return redirect()->route('dashboard')->with('error', 'Anda bukan penjual.');
        }
        return view('client.seller-info', compact('user'));
    })->name('seller.info');

    Route::get('/profile', function () {
        $user = auth()->user();
        return view('client.profile', compact('user'));
    })->name('profile.info');

    Route::post('/dashboard/update-profile', function (\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'profile_image' => 'nullable|file|mimes:jpg,jpeg,png',
            'business_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'business_registration' => 'nullable|string|max:255',
            'business_address' => 'nullable|string|max:255',
            'operating_area' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'years_experience' => 'nullable|integer|min:0',
            'skills' => 'nullable|string',
            'service_areas' => 'nullable|string',
            'id_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'selfie_with_id' => 'nullable|file|mimes:jpg,jpeg,png',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->bio = $validated['bio'] ?? $user->bio;
        $user->location = $validated['location'] ?? $user->location;
        if ($request->hasFile('profile_image')) {
            $user->profile_image = $request->file('profile_image')->store('profile_images', 'public');
        }
        if ($user->is_seller) {
            $user->business_name = $validated['business_name'] ?? $user->business_name;
            $user->business_type = $validated['business_type'] ?? $user->business_type;
            $user->business_registration = $validated['business_registration'] ?? $user->business_registration;
            $user->business_address = $validated['business_address'] ?? $user->business_address;
            $user->operating_area = $validated['operating_area'] ?? $user->operating_area;
            $user->website = $validated['website'] ?? $user->website;
            $user->years_experience = $validated['years_experience'] ?? $user->years_experience;
            $user->skills = $validated['skills'] ?? $user->skills;
            $user->service_areas = $validated['service_areas'] ?? $user->service_areas;
            if ($request->hasFile('id_document')) {
                $user->id_document = $request->file('id_document')->store('seller_ids', 'public');
            }
            if ($request->hasFile('selfie_with_id')) {
                $user->selfie_with_id = $request->file('selfie_with_id')->store('seller_selfies', 'public');
            }
        }
        $user->save();
        // Redirect to appropriate page based on user type
        if ($user->is_seller) {
            return redirect()->route('seller.info')->with('success', 'Maklumat peribadi berjaya dikemaskini!');
        } else {
            return redirect()->route('profile.info')->with('success', 'Maklumat peribadi berjaya dikemaskini!');
        }
    })->name('dashboard.update_profile');

    // Request Routes
    Route::get('/service-request', [RequestController::class, 'showServiceRequestForm'])->name('service.request.create');
    Route::post('/service-request', [RequestController::class, 'storeServiceRequest'])->name('service.request.store');
    
    // Preview pending requests (only for the owner)
            Route::get('/pending-service/{id}', [RequestController::class, 'previewPendingService'])->name('pending.service.preview');
        Route::get('/rejected-service/{id}', [RequestController::class, 'previewRejectedService'])->name('rejected.service.preview');
        Route::get('/edit-rejected-service/{id}', [RequestController::class, 'editRejectedService'])->name('rejected.service.edit');
        Route::put('/edit-rejected-service/{id}', [RequestController::class, 'updateRejectedService'])->name('rejected.service.update');
        
        // Seller request routes
        Route::get('/pending-seller-request', [RequestController::class, 'previewPendingSellerRequest'])->name('pending.seller.preview');
        Route::delete('/seller-request/cancel', [RequestController::class, 'cancelSellerRequest'])->name('seller.request.cancel');
        Route::get('/rejected-seller-request', [RequestController::class, 'previewRejectedSellerRequest'])->name('rejected.seller.preview');
        Route::get('/edit-rejected-seller-request', [RequestController::class, 'editRejectedSellerRequest'])->name('rejected.seller.edit');
        Route::put('/edit-rejected-seller-request', [RequestController::class, 'updateRejectedSellerRequest'])->name('rejected.seller.update');
    
    // Status Update Routes
    Route::put('/service/{id}/status', [RequestController::class, 'updateServiceStatus'])->name('service.status.update');
    
    // Service Update Request Routes
    Route::get('/service/{id}/edit-request', [RequestController::class, 'showServiceEditRequestForm'])->name('service.edit.request.create');
    Route::post('/service/{id}/edit-request', [RequestController::class, 'storeServiceEditRequest'])->name('service.edit.request.store');
    
    Route::delete('/service-update/{id}/forget', [RequestController::class, 'forgetServiceUpdateRequest'])->name('service.update.forget');
    Route::delete('/service/{id}/forget', [RequestController::class, 'forgetServiceRequest'])->name('service.forget');
    Route::get('/service-update/{id}/preview', [RequestController::class, 'previewServiceUpdateRequest'])->name('service.update.preview');
    Route::delete('/service-update/{id}/cancel', [RequestController::class, 'cancelServiceUpdateRequest'])->name('service.update.cancel');
    
    // Cancel Service Request Routes
    Route::delete('/service/{id}/cancel', [RequestController::class, 'cancelServiceRequest'])->name('service.cancel');
    Route::get('/service/{id}/preview', [RequestController::class, 'previewOwnService'])->name('service.preview');
});

// Admin Authentication Routes (not protected)
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login']);
        Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
        Route::post('/register', [AdminAuthController::class, 'register']);
    });
    
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

// Protected Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats'])->name('admin.dashboard.stats');
    
    // Articles Management
    Route::prefix('articles')->group(function () {
        Route::get('/', [AdminArticleController::class, 'index'])->name('admin.articles.index');
        Route::get('/create', [AdminArticleController::class, 'create'])->name('admin.articles.create');
        Route::post('/', [AdminArticleController::class, 'store'])->name('admin.articles.store');
        Route::get('/{id}/edit', [AdminArticleController::class, 'edit'])->name('admin.articles.edit');
        Route::put('/{id}', [AdminArticleController::class, 'update'])->name('admin.articles.update');
        Route::delete('/{id}', [AdminArticleController::class, 'destroy'])->name('admin.articles.destroy');
    });
    
    // Services Management
    Route::prefix('services')->group(function () {
        Route::get('/', [AdminServiceController::class, 'index'])->name('admin.services.index');
        Route::get('/pending', [AdminServiceController::class, 'pending'])->name('admin.services.pending');
        Route::get('/create', [AdminServiceController::class, 'create'])->name('admin.services.create');
        Route::post('/', [AdminServiceController::class, 'store'])->name('admin.services.store');
        Route::get('/{id}/edit', [AdminServiceController::class, 'edit'])->name('admin.services.edit');
        Route::put('/{id}', [AdminServiceController::class, 'update'])->name('admin.services.update');
        Route::get('/{id}', [AdminServiceController::class, 'show'])->name('admin.services.show');
        Route::post('/{id}/approve', [AdminServiceController::class, 'approve'])->name('admin.services.approve');
        Route::post('/{id}/reject', [AdminServiceController::class, 'reject'])->name('admin.services.reject');
        Route::post('/{id}/toggle-status', [AdminServiceController::class, 'toggleStatus'])->name('admin.services.toggle-status');
        Route::patch('/{id}/update-status', [AdminServiceController::class, 'updateStatus'])->name('admin.services.update-status');
        Route::delete('/{id}', [AdminServiceController::class, 'destroy'])->name('admin.services.destroy');
        Route::get('/{id}/details', [AdminServiceController::class, 'getServiceDetails'])->name('admin.services.details');
    });
    
    // Products Management
    Route::prefix('products')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('admin.products.create');
        Route::post('/', [AdminProductController::class, 'store'])->name('admin.products.store');
        Route::get('/{id}', [AdminProductController::class, 'show'])->name('admin.products.show');
        Route::get('/{id}/details', [AdminProductController::class, 'details'])->name('admin.products.details');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
        Route::post('/{id}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('admin.products.toggle-status');
        Route::patch('/{id}/update-status', [AdminProductController::class, 'updateStatus'])->name('admin.products.update-status');
        Route::post('/{id}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('admin.products.toggle-featured');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
        
        // Variation management
        Route::get('/variations/{variationId}/edit', [AdminProductController::class, 'getVariationForEdit'])->name('admin.products.variations.edit');
        Route::delete('/variations/{variationId}', [AdminProductController::class, 'deleteVariation'])->name('admin.products.variations.destroy');
        Route::put('/variations/{variationId}', [AdminProductController::class, 'updateVariation'])->name('admin.products.variations.update');
    });
    
    // Orders Management
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/export', [OrderController::class, 'export'])->name('admin.orders.export');
        Route::get('/stats', [OrderController::class, 'getStats'])->name('admin.orders.stats');
        Route::get('/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
        Route::patch('/{id}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('admin.orders.update-payment-status');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
        Route::get('/{id}/invoice', [OrderController::class, 'viewInvoice'])->name('admin.orders.invoice');
        Route::get('/{id}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('admin.orders.invoice.download');
    });

// Admin Refund Routes
Route::prefix('refunds')->group(function () {
    Route::get('/', [AdminRefundController::class, 'index'])->name('admin.refunds.index');
    Route::get('/export', [AdminRefundController::class, 'export'])->name('admin.refunds.export');
    Route::get('/stats', [AdminRefundController::class, 'getStats'])->name('admin.refunds.stats');
    Route::get('/{refund}', [AdminRefundController::class, 'show'])->name('admin.refunds.show');
    Route::patch('/{refund}/status', [AdminRefundController::class, 'updateStatus'])->name('admin.refunds.update-status');
});

// Admin Product Review Routes
Route::prefix('reviews')->group(function () {
    Route::get('/', [ProductReviewController::class, 'index'])->name('admin.reviews.index');
    Route::get('/statistics', [ProductReviewController::class, 'statistics'])->name('admin.reviews.statistics');
    Route::get('/{review}', [ProductReviewController::class, 'show'])->name('admin.reviews.show');
    Route::patch('/{review}/approve', [ProductReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::patch('/{review}/reject', [ProductReviewController::class, 'reject'])->name('admin.reviews.reject');
    Route::delete('/{review}', [ProductReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    Route::post('/bulk-action', [ProductReviewController::class, 'bulkAction'])->name('admin.reviews.bulk-action');
});
    
    // Users Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::get('/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
        Route::post('/{id}/verify', [AdminUserController::class, 'verify'])->name('admin.users.verify');
        Route::post('/{id}/suspend', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('/{id}/activate', [AdminUserController::class, 'activate'])->name('admin.users.activate');
        Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    });
    
    // Seller Requests Management
    Route::prefix('seller-requests')->group(function () {
        Route::get('/', [SellerRequestController::class, 'index'])->name('admin.seller-requests.index');
        Route::get('/pending', [SellerRequestController::class, 'pending'])->name('admin.seller-requests.pending');
        Route::get('/{id}/services', [SellerRequestController::class, 'getServices'])->name('admin.seller-requests.services');
        Route::get('/{id}', [SellerRequestController::class, 'show'])->name('admin.seller-requests.show');
        Route::post('/{id}/approve', [SellerRequestController::class, 'approve'])->name('admin.seller-requests.approve');
        Route::post('/{id}/reject', [SellerRequestController::class, 'reject'])->name('admin.seller-requests.reject');
        Route::post('/{id}/toggle-status', [SellerRequestController::class, 'toggleStatus'])->name('admin.seller-requests.toggle-status');
        Route::delete('/{id}', [SellerRequestController::class, 'destroy'])->name('admin.seller-requests.destroy');
    });
    
    // Settings Management
    Route::prefix('settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings.index');
        Route::post('/', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');
        Route::post('/reset', [\App\Http\Controllers\Admin\SettingsController::class, 'reset'])->name('admin.settings.reset');
    });
    
    // Logs Management
    Route::prefix('logs')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('admin.logs.index');
        Route::post('/clear', [\App\Http\Controllers\Admin\LogController::class, 'clear'])->name('admin.logs.clear');
        Route::get('/download', [\App\Http\Controllers\Admin\LogController::class, 'download'])->name('admin.logs.download');
    });
    
    // Videos Management
    Route::prefix('videos')->group(function () {
        Route::get('/', [AdminVideoController::class, 'index'])->name('admin.videos.index');
        Route::get('/create', [AdminVideoController::class, 'create'])->name('admin.videos.create');
        Route::post('/', [AdminVideoController::class, 'store'])->name('admin.videos.store');
        Route::get('/{id}/edit', [AdminVideoController::class, 'edit'])->name('admin.videos.edit');
        Route::put('/{id}', [AdminVideoController::class, 'update'])->name('admin.videos.update');
        Route::delete('/{id}', [AdminVideoController::class, 'destroy'])->name('admin.videos.destroy');
    });
});

// Serve article images directly from storage
Route::get('/article-image/{filename}', function ($filename) {
    $path = storage_path('app/public/articles/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->name('article.image');

// Serve video thumbnails directly from storage
Route::get('/video-thumbnail/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);
    if (!file_exists($path)) {
        $path = storage_path('app/public/videos/' . $filename);
    }
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->where('filename', '.*')->name('video.thumbnail');

// Serve service images directly from storage
Route::get('/service-image/{filename}', function ($filename) {
    $path = storage_path('app/public/services/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->name('service.image');

// Serve product images directly from storage
Route::get('/product-image/{filename}', function ($filename) {
    $path = storage_path('app/public/products/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->name('product.image');

// Serve variation images directly from storage
Route::get('/variation-image/{filename}', function ($filename) {
    $path = storage_path('app/public/variations/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->name('variation.image');

// Serve seller documents directly from storage
Route::get('/seller-document/{filename}', function ($filename) {
    // Check multiple possible storage locations
    $paths = [
        storage_path('app/public/seller-documents/' . $filename),
        storage_path('app/public/seller_ids/' . $filename),
        storage_path('app/public/seller_selfies/' . $filename)
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            return response()->file($path);
        }
    }
    
    abort(404);
})->name('seller.document');

// Serve seller images directly from storage
Route::get('/seller-image/{filename}', function ($filename) {
    // Check multiple possible storage locations
    $paths = [
        storage_path('app/public/seller-documents/' . $filename),
        storage_path('app/public/seller_ids/' . $filename),
        storage_path('app/public/seller_selfies/' . $filename)
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            return response()->file($path);
        }
    }
    
    abort(404);
})->name('seller.image');

// Test email route for cPanel SMTP testing
Route::get('/test-email', function () {
    // Set a shorter timeout for testing
    set_time_limit(15);
    
    try {
        // Test connection first
        $transport = Mail::mailer()->getSymfonyTransport();
        if (method_exists($transport, 'start')) {
            $transport->start();
        }
        
        Mail::raw('This is a test email from Laravel using cPanel SMTP.', function ($message) {
            $message->to('muhdzahir248@gmail.com')
                    ->subject('Laravel SMTP Test');
        });
        
        return '✅ Test email sent successfully!';
    } catch (\Exception $e) {
        return '❌ Error: ' . $e->getMessage() . '<br><br>Check your .env file for correct SMTP settings:<br>' .
               'MAIL_MAILER=smtp<br>' .
               'MAIL_HOST=your-cpanel-server.com<br>' .
               'MAIL_PORT=587 (or 465)<br>' .
               'MAIL_USERNAME=your-email@yourdomain.com<br>' .
               'MAIL_PASSWORD=your-password<br>' .
               'MAIL_ENCRYPTION=tls (or ssl)<br>' .
               'MAIL_FROM_ADDRESS=your-email@yourdomain.com';
    }
});

// Test SMTP connection only (without sending email)
Route::get('/test-smtp-connection', function () {
    set_time_limit(20);
    
    // Get current mail configuration first
    $config = config('mail.mailers.smtp');
    $encryption = isset($config['encryption']) ? $config['encryption'] : 'not set';
    $configInfo = "Current config: Host={$config['host']}, Port={$config['port']}, Encryption={$encryption}<br>";
    
    // Test basic socket connection first
    $host = $config['host'];
    $port = $config['port'];
    
    $socket = @fsockopen($host, $port, $errno, $errstr, 5);
    if ($socket) {
        fclose($socket);
        $socketTest = "✅ Socket connection to {$host}:{$port} successful<br>";
    } else {
        $socketTest = "❌ Socket connection to {$host}:{$port} failed: {$errstr} (Error {$errno})<br>";
    }
    
    // Return config and socket info immediately
    $result = $configInfo . $socketTest;
    
    try {
        // Try to get transport with a shorter timeout
        $transport = Mail::mailer()->getSymfonyTransport();
        if (method_exists($transport, 'start')) {
            $transport->start();
            return $result . '✅ SMTP connection successful!';
        }
        return $result . '⚠️ Transport method not available';
    } catch (\Exception $e) {
        return $result . '❌ SMTP Connection Error: ' . $e->getMessage();
    }
});

// Simple ping test to check server reachability
Route::get('/ping-test', function () {
    $hosts = [
        'mygooners.my',
        'mail.mygooners.my',
        'www.mygooners.my'
    ];
    
    $results = [];
    foreach ($hosts as $host) {
        $start = microtime(true);
        $socket = @fsockopen($host, 80, $errno, $errstr, 5);
        $end = microtime(true);
        
        if ($socket) {
            fclose($socket);
            $results[] = "✅ {$host} - Reachable in " . round(($end - $start) * 1000, 2) . "ms";
        } else {
            $results[] = "❌ {$host} - Not reachable: {$errstr} (Error {$errno})";
        }
    }
    
    return implode('<br>', $results);
});

// Quick mail config check (no timeout risk)
Route::get('/mail-config', function () {
    $config = config('mail.mailers.smtp');
    $encryption = isset($config['encryption']) ? $config['encryption'] : 'not set';
    
    $envVars = [
        'MAIL_MAILER' => env('MAIL_MAILER'),
        'MAIL_HOST' => env('MAIL_HOST'),
        'MAIL_PORT' => env('MAIL_PORT'),
        'MAIL_USERNAME' => env('MAIL_USERNAME'),
        'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
        'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
        'MAIL_TIMEOUT' => env('MAIL_TIMEOUT')
    ];
    
    $result = "<h3>Mail Configuration</h3>";
    $result .= "<strong>Config Array:</strong><br>";
    $result .= "Host: {$config['host']}, Port: {$config['port']}, Encryption: {$encryption}<br><br>";
    
    $result .= "<strong>Environment Variables:</strong><br>";
    foreach ($envVars as $key => $value) {
        $result .= "{$key}: " . ($value ?: 'not set') . "<br>";
    }
    
    return $result;
});

// Test multiple SMTP configurations
Route::get('/test-multiple-smtp', function () {
    $configs = [
        ['host' => 'mygooners.my', 'port' => 465, 'encryption' => 'ssl'],
        ['host' => 'mail.mygooners.my', 'port' => 587, 'encryption' => 'tls'],
        ['host' => 'mygooners.my', 'port' => 587, 'encryption' => 'tls'],
        ['host' => 'mail.mygooners.my', 'port' => 465, 'encryption' => 'ssl'],
    ];
    
    $results = [];
    foreach ($configs as $config) {
        $host = $config['host'];
        $port = $config['port'];
        $encryption = $config['encryption'];
        
        $socket = @fsockopen($host, $port, $errno, $errstr, 3);
        if ($socket) {
            fclose($socket);
            $results[] = "✅ {$host}:{$port} ({$encryption}) - Connection successful";
        } else {
            $results[] = "❌ {$host}:{$port} ({$encryption}) - Failed: {$errstr} (Error {$errno})";
        }
    }
    
    return "<h3>SMTP Port Testing</h3>" . implode('<br>', $results);
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
