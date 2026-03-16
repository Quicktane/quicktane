<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Search\Http\Controllers\Storefront\SearchController;

Route::prefix('api/v1/search')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('products', [SearchController::class, 'search']);
        Route::get('autocomplete', [SearchController::class, 'autocomplete']);
    });
