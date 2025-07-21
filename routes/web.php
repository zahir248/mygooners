<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ArticleController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\VideoController;
use App\Http\Controllers\Client\AuthController;
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
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
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
    });
    
    // Products Management
    Route::prefix('products')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('admin.products.index');
        Route::get('/pending', [AdminProductController::class, 'pending'])->name('admin.products.pending');
        Route::get('/create', [AdminProductController::class, 'create'])->name('admin.products.create');
        Route::post('/', [AdminProductController::class, 'store'])->name('admin.products.store');
        Route::get('/{id}', [AdminProductController::class, 'show'])->name('admin.products.show');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
        Route::post('/{id}/approve', [AdminProductController::class, 'approve'])->name('admin.products.approve');
        Route::post('/{id}/reject', [AdminProductController::class, 'reject'])->name('admin.products.reject');
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
