<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::prefix('logs')
    ->name('logs.')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::post('/', [LogController::class, 'store'])->name('store');
        Route::post('/broadcast', [LogController::class, 'broadcast'])->name('broadcast');
    });
