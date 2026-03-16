<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Quicktane\Notification\Http\Controllers\NotificationLogController;

Route::prefix('api/v1/admin/notification')
    ->middleware(['api', 'auth', 'permission:notification.manage'])
    ->group(function (): void {
        Route::get('logs', [NotificationLogController::class, 'index']);
        Route::get('logs/{notificationLog}', [NotificationLogController::class, 'show']);
        Route::get('templates', [NotificationLogController::class, 'templates']);
    });
