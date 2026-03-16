<?php

declare(strict_types=1);

use App\Store\Http\Controllers\ConfigurationController;
use App\Store\Http\Controllers\StoreController;
use App\Store\Http\Controllers\StoreViewController;
use App\Store\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/admin/store')
    ->middleware(['api', 'auth', 'permission:store.websites.manage'])
    ->group(function (): void {
        Route::apiResource('websites', WebsiteController::class);
        Route::apiResource('stores', StoreController::class);
        Route::apiResource('store-views', StoreViewController::class);

        Route::get('/config', [ConfigurationController::class, 'show']);
        Route::put('/config', [ConfigurationController::class, 'update']);
        Route::delete('/config', [ConfigurationController::class, 'destroy']);
    });
