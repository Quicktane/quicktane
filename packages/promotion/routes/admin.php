<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Promotion\Http\Controllers\CartPriceRuleController;
use Quicktane\Promotion\Http\Controllers\CouponController;

Route::prefix('api/v1/admin/promotion')
    ->middleware(['api', 'auth', 'permission:promotion.manage'])
    ->group(function (): void {
        Route::apiResource('rules', CartPriceRuleController::class);
        Route::apiResource('coupons', CouponController::class);
    });
