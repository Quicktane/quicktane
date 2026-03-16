<?php

declare(strict_types=1);

use App\Directory\Http\Controllers\CountryController;
use App\Directory\Http\Controllers\CurrencyController;
use App\Directory\Http\Controllers\CurrencyRateController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/admin/directory')
    ->middleware(['api', 'auth', 'permission:directory.countries.manage'])
    ->group(function (): void {
        Route::get('/countries', [CountryController::class, 'index']);
        Route::get('/countries/{country}', [CountryController::class, 'show']);
        Route::put('/countries/{country}', [CountryController::class, 'update']);
        Route::get('/countries/{country}/regions', [CountryController::class, 'regions']);
        Route::put('/countries/{country}/regions/{region}', [CountryController::class, 'updateRegion']);

        Route::get('/currencies', [CurrencyController::class, 'index']);
        Route::get('/currencies/{currency}', [CurrencyController::class, 'show']);
        Route::put('/currencies/{currency}', [CurrencyController::class, 'update']);

        Route::get('/currency-rates', [CurrencyRateController::class, 'index']);
        Route::put('/currency-rates', [CurrencyRateController::class, 'update']);
    });
