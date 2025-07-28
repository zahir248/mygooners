<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ArticleController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\VideoController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\RequestController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;

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
        $user->is_seller = true;
        $user->save();
        session()->forget('show_seller_form');
        return redirect()->route('dashboard')->with('success', 'Permohonan penjual anda telah diterima!');
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

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
