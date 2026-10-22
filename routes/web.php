<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DietController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WorkoutController;
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

    // JSON endpoints used by public/js/script.js (login session + CSRF token, no API tokens)
    Route::prefix('api')->group(function () {
        Route::get('/workouts', [WorkoutController::class, 'catalog'])->name('api.workouts');

        Route::get('/workout-sessions', [WorkoutController::class, 'sessions'])->name('api.workout-sessions');
        Route::post('/workout-sessions', [WorkoutController::class, 'save'])->name('api.workout-sessions.save');
        Route::delete('/workout-sessions/{workoutSession}', [WorkoutController::class, 'destroy'])->name('api.workout-sessions.destroy');
        Route::delete('/workout-sessions', [WorkoutController::class, 'clear'])->name('api.workout-sessions.clear');

        // Home page summary
        Route::get('/home', [HomeController::class, 'summary'])->name('api.home');

        // Diet tracker
        Route::get('/diet', [DietController::class, 'index'])->name('api.diet');
        Route::post('/goals', [DietController::class, 'saveGoals'])->name('api.goals.save');

        Route::get('/meals', [DietController::class, 'meals'])->name('api.meals');
        Route::post('/meals', [DietController::class, 'addMeal'])->name('api.meals.add');
        Route::delete('/meals/{meal}', [DietController::class, 'deleteMeal'])->name('api.meals.destroy');

        Route::delete('/foods/{food}', [DietController::class, 'deleteFood'])->name('api.foods.destroy');
    });
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
