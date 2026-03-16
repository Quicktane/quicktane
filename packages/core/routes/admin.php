<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Core\Http\Controllers\MaintenanceController;
use Quicktane\Core\Http\Controllers\MenuController;
use Quicktane\Core\Http\Controllers\ModuleController;
use Quicktane\Core\Http\Controllers\SuspendedPipelineController;

Route::prefix('api/v1/admin')
    ->middleware(['api', 'auth'])
    ->group(function (): void {
        Route::prefix('maintenance')->group(function (): void {
            Route::post('/enable', [MaintenanceController::class, 'enable']);
            Route::get('/status', [MaintenanceController::class, 'status']);
            Route::post('/disable', [MaintenanceController::class, 'disable']);
        });

        Route::prefix('pipelines')->group(function (): void {
            Route::get('/', [SuspendedPipelineController::class, 'index']);
            Route::get('/{token}', [SuspendedPipelineController::class, 'show']);
            Route::post('/{token}/expire', [SuspendedPipelineController::class, 'expire']);
            Route::post('/force-complete', [SuspendedPipelineController::class, 'forceCompleteAll']);
        });

        Route::prefix('modules')->group(function (): void {
            Route::get('/', [ModuleController::class, 'index']);
            Route::get('/{module}/config', [ModuleController::class, 'config']);
            Route::put('/{module}/config', [ModuleController::class, 'updateConfig']);
        });

        Route::get('/menu', [MenuController::class, 'index']);
    });
