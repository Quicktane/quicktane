<?php

declare(strict_types=1);

use App\Customer\Http\Controllers\CustomerController;
use App\Customer\Http\Controllers\CustomerGroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/admin/customer')
    ->middleware(['api', 'auth'])
    ->group(function (): void {
        Route::middleware('permission:customer.customers.manage')->group(function (): void {
            Route::apiResource('customers', CustomerController::class);
        });

        Route::middleware('permission:customer.groups.manage')->group(function (): void {
            Route::apiResource('groups', CustomerGroupController::class);
        });
    });
