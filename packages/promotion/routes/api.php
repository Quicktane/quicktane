<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Promotion\Http\Controllers\Storefront\CouponController;

Route::prefix('api/v1/promotion')
    ->middleware(['api'])
    ->group(function (): void {
        Route::post('/apply-coupon', [CouponController::class, 'applyCoupon']);
        Route::delete('/coupon', [CouponController::class, 'removeCoupon']);
    });
