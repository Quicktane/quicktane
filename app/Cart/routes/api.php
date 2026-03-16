<?php

declare(strict_types=1);

use App\Cart\Http\Controllers\Storefront\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/cart')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('/', [CartController::class, 'show']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::put('/items/{item}', [CartController::class, 'updateItem']);
        Route::delete('/items/{item}', [CartController::class, 'removeItem']);
        Route::delete('/', [CartController::class, 'clear']);
        Route::post('/validate', [CartController::class, 'validate']);
        Route::post('/confirm-prices', [CartController::class, 'confirmPrices']);
    });
