<?php

declare(strict_types=1);

use App\Order\Http\Controllers\Storefront\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/order')
    ->middleware(['api', 'auth', 'customer'])
    ->group(function (): void {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
    });
