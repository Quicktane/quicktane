<?php

declare(strict_types=1);

use App\Payment\Http\Controllers\PaymentMethodController;
use App\Payment\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/admin/payment')
    ->middleware(['api', 'auth', 'permission:payment.manage'])
    ->group(function (): void {
        Route::get('/methods', [PaymentMethodController::class, 'index']);
        Route::post('/methods', [PaymentMethodController::class, 'store']);
        Route::get('/methods/{paymentMethod}', [PaymentMethodController::class, 'show']);
        Route::put('/methods/{paymentMethod}', [PaymentMethodController::class, 'update']);
        Route::delete('/methods/{paymentMethod}', [PaymentMethodController::class, 'destroy']);

        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    });
