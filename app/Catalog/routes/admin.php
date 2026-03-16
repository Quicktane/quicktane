<?php

declare(strict_types=1);

use App\Catalog\Http\Controllers\AttributeController;
use App\Catalog\Http\Controllers\AttributeSetController;
use App\Catalog\Http\Controllers\CategoryController;
use App\Catalog\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/admin/catalog')
    ->middleware(['api', 'auth'])
    ->group(function (): void {
        Route::middleware('permission:catalog.attributes.manage')->group(function (): void {
            Route::apiResource('attributes', AttributeController::class);
            Route::apiResource('attribute-sets', AttributeSetController::class);
            Route::put('attribute-sets/{attribute_set}/sync-attributes', [AttributeSetController::class, 'syncAttributes']);
        });

        Route::middleware('permission:catalog.categories.manage')->group(function (): void {
            Route::apiResource('categories', CategoryController::class);
            Route::put('categories/{category}/move', [CategoryController::class, 'move']);
        });

        Route::middleware('permission:catalog.products.manage')->group(function (): void {
            Route::apiResource('products', ProductController::class);
        });
    });
