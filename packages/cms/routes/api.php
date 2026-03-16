<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\CMS\Http\Controllers\Storefront\BlockController;
use Quicktane\CMS\Http\Controllers\Storefront\PageController;

Route::prefix('api/v1/cms')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('pages', [PageController::class, 'index']);
        Route::get('pages/{identifier}', [PageController::class, 'show']);
        Route::get('blocks/{identifier}', [BlockController::class, 'show']);
        Route::get('resolve', [PageController::class, 'resolve']);
    });
