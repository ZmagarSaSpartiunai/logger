<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::post('logs', [LogController::class, 'store'])
    ->middleware('throttle:60,1')
    ->name('logs.store');
