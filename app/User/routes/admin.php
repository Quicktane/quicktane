<?php

declare(strict_types=1);

use App\User\Http\Controllers\AuthController;
use App\User\Http\Controllers\RoleController;
use App\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/admin/user')
    ->middleware(['api'])
    ->group(function (): void {
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);

            Route::middleware('permission:user.users.manage')->group(function (): void {
                Route::apiResource('users', UserController::class);
            });

            Route::middleware('permission:user.roles.manage')->group(function (): void {
                Route::apiResource('roles', RoleController::class);
                Route::get('/permissions', [RoleController::class, 'permissions']);
            });
        });
    });
