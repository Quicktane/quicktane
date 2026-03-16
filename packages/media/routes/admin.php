<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Media\Http\Controllers\MediaFileController;

Route::prefix('api/v1/admin/media')
    ->middleware(['api', 'auth', 'permission:media.files.manage'])
    ->group(function (): void {
        Route::apiResource('files', MediaFileController::class);
    });
