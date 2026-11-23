<?php

use App\Http\Controllers\Api\V1\Admin\AboutUsSectionController as AdminAboutUsSectionController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\V1\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Public\AboutUsController;
use App\Http\Controllers\Api\V1\Public\MemberController;
use App\Http\Controllers\Api\V1\Public\ProductController;
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

    Route::prefix('admin')->middleware(['auth:sanctum', 'can:access-admin'])->group(function (): void {
        Route::get('dashboard', DashboardController::class);
        Route::apiResource('products', AdminProductController::class);
        Route::apiResource('members', AdminMemberController::class);
        Route::apiResource('testimonials', AdminTestimonialController::class);
        Route::apiResource('about-us', AdminAboutUsSectionController::class)
            ->parameters(['about-us' => 'aboutUsSection']);
    });
});
