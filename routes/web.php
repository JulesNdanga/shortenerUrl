<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('home');
});

// Authentification web (Blade)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    Route::get('/shorten', function () { return view('shorten'); });
    Route::get('/batch', function () { return view('batch'); });
    Route::get('/history', function () { return view('history'); });
});

Route::get('/stats', function () { return view('stats'); });

// Admin
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Route de redirection pour les codes courts
Route::get('/{short_code}', [\App\Http\Controllers\RedirectController::class, 'redirect'])->where('short_code', '[A-Za-z0-9]+');
