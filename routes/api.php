<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShortUrlController;

// API route pour raccourcir une URL
Route::post('/shorten', [ShortUrlController::class, 'shorten']);

// API route pour obtenir les stats d'une URL courte
Route::get('/stats/{short_code}', [ShortUrlController::class, 'stats']);
