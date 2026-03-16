<?php

declare(strict_types=1);

use App\Directory\Http\Controllers\Storefront\CountryController;
use App\Directory\Http\Controllers\Storefront\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/directory')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('/countries', [CountryController::class, 'index']);
        Route::get('/countries/{country}/regions', [CountryController::class, 'regions']);
        Route::get('/currencies', [CurrencyController::class, 'index']);
    });
