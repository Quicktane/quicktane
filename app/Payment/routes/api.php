<?php

declare(strict_types=1);

use App\Payment\Http\Controllers\Storefront\PaymentCallbackController;
use App\Payment\Http\Controllers\Storefront\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/payment')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('/methods', [PaymentMethodController::class, 'index']);
        Route::post('/callback/{gateway}', PaymentCallbackController::class);
    });
