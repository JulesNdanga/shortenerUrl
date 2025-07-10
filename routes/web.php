<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route de redirection pour les codes courts
Route::get('/{short_code}', [\App\Http\Controllers\RedirectController::class, 'redirect'])->where('short_code', '[A-Za-z0-9]+');
