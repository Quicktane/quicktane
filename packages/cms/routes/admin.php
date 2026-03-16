<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\CMS\Http\Controllers\BlockController;
use Quicktane\CMS\Http\Controllers\PageController;
use Quicktane\CMS\Http\Controllers\UrlRewriteController;

Route::prefix('api/v1/admin/cms')
    ->middleware(['api', 'auth'])
    ->group(function (): void {
        Route::middleware('permission:cms.pages.manage')->group(function (): void {
            Route::apiResource('pages', PageController::class);
        });

        Route::middleware('permission:cms.blocks.manage')->group(function (): void {
            Route::apiResource('blocks', BlockController::class);
        });

        Route::middleware('permission:cms.url-rewrites.manage')->group(function (): void {
            Route::apiResource('url-rewrites', UrlRewriteController::class)
                ->parameters(['url-rewrites' => 'urlRewrite']);
        });
    });
