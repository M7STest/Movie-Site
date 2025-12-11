<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::get('/status', function () {
    return response()->json(['status' => 'API is running']);
});

// Protected routes
Route::middleware('jwt.auth')->group(function () {
    Route::get('/me', [App\Http\Controllers\AuthController::class, 'me']);
    Route::post('/movies/search', [App\Http\Controllers\MovieController::class, 'search']);
    Route::get('/movies/recent', [App\Http\Controllers\MovieController::class, 'recent']);
    Route::get('/movie/{imdb_id}', [App\Http\Controllers\MovieController::class, 'get']);
});