<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Octane\Octane;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'octane' => class_exists(Octane::class),
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');
