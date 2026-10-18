<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('home') : view('landing');
})->name('landing');

// Only for visitors who are NOT logged in
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Only for logged-in members
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::view('/home', 'home')->name('home');
    Route::view('/workouts', 'workouts')->name('workouts');
    Route::view('/diet', 'diet')->name('diet');
    Route::view('/about', 'about')->name('about');
});

// Admin area
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    // Uses your teammate's AdminMiddleware (alias "admin")
    Route::middleware('admin')->group(function () {
        Route::view('/', 'admin')->name('admin');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});