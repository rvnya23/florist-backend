<?php

use App\Http\Controllers\Api\V1\Admin\AboutUsSectionController as AdminAboutUsSectionController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Api\V1\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\V1\Admin\OrderPaymentController as AdminOrderPaymentController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\V1\Admin\RekeningBankController as AdminRekeningBankController;
use App\Http\Controllers\Api\V1\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Public\AboutUsController;
use App\Http\Controllers\Api\V1\Public\CartController;
use App\Http\Controllers\Api\V1\Public\MemberController;
use App\Http\Controllers\Api\V1\Public\OrderController;
use App\Http\Controllers\Api\V1\Public\OrderPaymentController;
use App\Http\Controllers\Api\V1\Public\ProductController;
use App\Http\Controllers\Api\V1\Public\RekeningBankController;
use App\Http\Controllers\Api\V1\Public\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('about-us', [AboutUsController::class, 'index']);
    Route::post('members', [MemberController::class, 'store']);
    Route::post('testimonials', [TestimonialController::class, 'store']);

    Route::prefix('auth')->group(function (): void {
        Route::post('login', [AuthController::class, 'login'])->middleware('guest');
        Route::post('customer/login', [AuthController::class, 'customerLogin'])->middleware('guest');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('user', [AuthController::class, 'user']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware(['auth:sanctum', 'can:access-customer'])->group(function (): void {
        Route::get('cart', [CartController::class, 'show']);
        Route::post('cart/items', [CartController::class, 'store']);
        Route::patch('cart/items/{cartItem}', [CartController::class, 'update']);
        Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::post('orders/{order}/payment', [OrderPaymentController::class, 'store']);
        Route::get('bank-accounts', [RekeningBankController::class, 'index']);
    });

    Route::prefix('admin')->middleware(['auth:sanctum', 'can:access-admin'])->group(function (): void {
        Route::get('dashboard', DashboardController::class);
        Route::get('orders', [AdminOrderController::class, 'index']);
        Route::get('orders/{order}', [AdminOrderController::class, 'show']);
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
        Route::post('orders/{order}/payment', [AdminOrderPaymentController::class, 'store']);
        Route::post('orders/{order}/payment/verify', [AdminOrderPaymentController::class, 'verify']);
        Route::post('orders/{order}/payment/reject', [AdminOrderPaymentController::class, 'reject']);
        Route::get('bank-accounts', [AdminRekeningBankController::class, 'index']);
        Route::post('bank-accounts', [AdminRekeningBankController::class, 'store']);
        Route::put('bank-accounts/{bankAccount}', [AdminRekeningBankController::class, 'update']);
        Route::patch('bank-accounts/{bankAccount}/status', [AdminRekeningBankController::class, 'updateStatus']);
        Route::apiResource('products', AdminProductController::class);
        Route::apiResource('members', AdminMemberController::class);
        Route::apiResource('testimonials', AdminTestimonialController::class);
        Route::apiResource('about-us', AdminAboutUsSectionController::class)
            ->parameters(['about-us' => 'aboutUsSection']);
    });
});
