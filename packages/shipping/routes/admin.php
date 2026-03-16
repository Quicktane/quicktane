<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Shipping\Http\Controllers\ShippingMethodController;
use Quicktane\Shipping\Http\Controllers\ShippingRateController;
use Quicktane\Shipping\Http\Controllers\ShippingZoneController;

Route::prefix('api/v1/admin/shipping')
    ->middleware(['api', 'auth', 'permission:shipping.manage'])
    ->group(function (): void {
        Route::apiResource('methods', ShippingMethodController::class);
        Route::apiResource('zones', ShippingZoneController::class);
        Route::apiResource('rates', ShippingRateController::class);
    });
