<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShortUrlController;
use App\Http\Controllers\Api\AuthController; // Auth API

// Authentification API
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // API route pour raccourcir une URL
    Route::post('/shorten', [ShortUrlController::class, 'shorten'])->middleware('throttle:10,1');
    Route::post('/shorten/batch', [ShortUrlController::class, 'shortenBatch'])->middleware('throttle:10,1'); // Raccourcissement en masse
    Route::get('/history', [ShortUrlController::class, 'history']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// API route pour obtenir les stats d'une URL courte (publique)
Route::get('/stats/{short_code}', [ShortUrlController::class, 'stats']);
