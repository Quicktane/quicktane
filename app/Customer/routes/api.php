<?php

declare(strict_types=1);

use App\Customer\Http\Controllers\Storefront\AddressController;
use App\Customer\Http\Controllers\Storefront\AuthController;
use App\Customer\Http\Controllers\Storefront\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/customer')
    ->middleware(['api'])
    ->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware(['auth', 'customer'])->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);

            Route::get('/me', [ProfileController::class, 'show']);
            Route::put('/me', [ProfileController::class, 'update']);
            Route::put('/me/password', [ProfileController::class, 'changePassword']);

            Route::get('/me/addresses', [AddressController::class, 'index']);
            Route::post('/me/addresses', [AddressController::class, 'store']);
            Route::put('/me/addresses/{address}', [AddressController::class, 'update']);
            Route::delete('/me/addresses/{address}', [AddressController::class, 'destroy']);
        });
    });
