<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\AuthController;
use App\Http\Controllers\Mobile\ProductController;
use App\Http\Controllers\Mobile\MobilePaymentController;
use App\Http\Controllers\Mobile\MobileOrderController;
use App\Http\Controllers\Mobile\MobileProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('mobile')->group(function () {
    // Authentication routes for mobile
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login/google', [AuthController::class, 'loginWithGoogle']);
    Route::post('/password/email', [AuthController::class, 'forgotPassword']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Product routes for mobile
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // ToyyibPay checkout routes for mobile
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/checkout/toyyibpay/create', [MobilePaymentController::class, 'createToyyibPayPayment'])
            ->name('mobile.checkout.toyyibpay.create');
        Route::get('/checkout/toyyibpay/status/{billCode}', [MobilePaymentController::class, 'getToyyibPayStatus'])
            ->name('mobile.checkout.toyyibpay.status');

        Route::get('/orders', [MobileOrderController::class, 'index']);
        Route::get('/orders/{id}', [MobileOrderController::class, 'show']);
        Route::post('/orders/{id}/cancel', [MobileOrderController::class, 'cancel']);
        Route::get('/orders/{id}/invoice/view', [MobileOrderController::class, 'viewInvoice']);
        Route::get('/orders/{id}/invoice/download', [MobileOrderController::class, 'downloadInvoice']);
        Route::post('/orders/{id}/mark-received', [MobileOrderController::class, 'markReceived']);
        Route::post('/orders/{id}/reviews', [MobileOrderController::class, 'submitReview']);

        Route::get('/profile', [MobileProfileController::class, 'profile']);
        Route::put('/profile', [MobileProfileController::class, 'updateProfile']);
        Route::post('/change-password', [MobileProfileController::class, 'changePassword']);
    });

    // Keep callback public so ToyyibPay server can access it
    Route::post('/checkout/toyyibpay/callback', [MobilePaymentController::class, 'handleToyyibPayCallback'])
        ->name('mobile.checkout.toyyibpay.callback');
});

