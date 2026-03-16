<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Shipping\Http\Controllers\Storefront\ShippingEstimateController;

Route::prefix('api/v1/shipping')
    ->middleware(['api'])
    ->group(function (): void {
        Route::post('/estimate', [ShippingEstimateController::class, 'estimate']);
    });
