<?php

declare(strict_types=1);

use App\Catalog\Http\Controllers\Storefront\CategoryController;
use App\Catalog\Http\Controllers\Storefront\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/catalog')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{slug}', [ProductController::class, 'show']);
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{slug}', [CategoryController::class, 'show']);
    });
