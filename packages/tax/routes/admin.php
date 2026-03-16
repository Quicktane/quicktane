<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Tax\Http\Controllers\TaxClassController;
use Quicktane\Tax\Http\Controllers\TaxRateController;
use Quicktane\Tax\Http\Controllers\TaxRuleController;
use Quicktane\Tax\Http\Controllers\TaxZoneController;

Route::prefix('api/v1/admin/tax')
    ->middleware(['api', 'auth', 'permission:tax.manage'])
    ->group(function (): void {
        Route::apiResource('classes', TaxClassController::class)->parameters(['classes' => 'taxClass']);
        Route::apiResource('zones', TaxZoneController::class)->parameters(['zones' => 'taxZone']);
        Route::apiResource('rates', TaxRateController::class)->parameters(['rates' => 'taxRate']);
        Route::apiResource('rules', TaxRuleController::class)->parameters(['rules' => 'taxRule']);
    });
