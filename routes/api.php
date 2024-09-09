<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::post('/log', [LogController::class, 'log']);
Route::post('/logTo/{type}', [LogController::class, 'logTo']);
Route::post('/logToAll', [LogController::class, 'logToAll']);
