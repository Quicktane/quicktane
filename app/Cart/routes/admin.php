<?php

declare(strict_types=1);

use App\Cart\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/admin/cart')
    ->middleware(['api', 'auth', 'permission:cart.carts.view'])
    ->group(function (): void {
        Route::get('/carts', [CartController::class, 'index']);
        Route::get('/carts/{cart}', [CartController::class, 'show']);
    });
