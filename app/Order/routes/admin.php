<?php

declare(strict_types=1);

use App\Order\Http\Controllers\InvoiceController;
use App\Order\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/admin/order')
    ->middleware(['api', 'auth', 'permission:order.manage'])
    ->group(function (): void {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
        Route::post('/orders/{order}/status', [OrderController::class, 'changeStatus']);
        Route::post('/orders/{order}/comment', [OrderController::class, 'addComment']);
        Route::get('/orders/{order}/history', [OrderController::class, 'history']);
        Route::post('/orders/{order}/invoice', [OrderController::class, 'createInvoice']);
        Route::post('/orders/{order}/credit-memo', [OrderController::class, 'createCreditMemo']);

        Route::get('/invoices', [InvoiceController::class, 'index']);
    });
