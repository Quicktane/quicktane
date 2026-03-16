<?php

declare(strict_types=1);

use App\Checkout\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/checkout')
    ->middleware(['api'])
    ->group(function (): void {
        Route::post('/start', [CheckoutController::class, 'start']);
        Route::get('/session', [CheckoutController::class, 'session']);
        Route::put('/shipping-address', [CheckoutController::class, 'setShippingAddress']);
        Route::put('/billing-address', [CheckoutController::class, 'setBillingAddress']);
        Route::put('/shipping-method', [CheckoutController::class, 'setShippingMethod']);
        Route::put('/payment-method', [CheckoutController::class, 'setPaymentMethod']);
        Route::post('/coupon', [CheckoutController::class, 'applyCoupon']);
        Route::delete('/coupon', [CheckoutController::class, 'removeCoupon']);
        Route::get('/totals', [CheckoutController::class, 'totals']);
        Route::post('/place-order', [CheckoutController::class, 'placeOrder']);
        Route::post('/resume', [CheckoutController::class, 'resume']);
    });
