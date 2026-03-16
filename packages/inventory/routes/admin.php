<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Inventory\Http\Controllers\InventorySourceController;
use Quicktane\Inventory\Http\Controllers\StockItemController;

Route::prefix('api/v1/admin/inventory')
    ->middleware(['api', 'auth'])
    ->group(function (): void {
        Route::middleware('permission:inventory.sources.manage')->group(function (): void {
            Route::apiResource('sources', InventorySourceController::class);
        });

        Route::middleware('permission:inventory.stock.manage')->group(function (): void {
            Route::get('stock', [StockItemController::class, 'index']);
            Route::put('stock', [StockItemController::class, 'update']);
            Route::post('stock/adjust', [StockItemController::class, 'adjust']);
            Route::get('stock/low', [StockItemController::class, 'lowStock']);
        });
    });
