<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Search\Http\Controllers\SearchAdminController;
use Quicktane\Search\Http\Controllers\SynonymController;

Route::prefix('api/v1/admin/search')
    ->middleware(['api', 'auth', 'permission:search.manage'])
    ->group(function (): void {
        Route::apiResource('synonyms', SynonymController::class)->parameters(['synonyms' => 'searchSynonym']);
        Route::post('reindex', [SearchAdminController::class, 'reindex']);
        Route::get('status', [SearchAdminController::class, 'status']);
    });
