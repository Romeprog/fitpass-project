<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DietController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordResetController;
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

    // Forgot password (emails the member a one-time link)
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Only for logged-in members
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::view('/home', 'home')->name('home');
    Route::view('/workouts', 'workouts')->name('workouts');
    Route::view('/diet', 'diet')->name('diet');
    Route::view('/about', 'about')->name('about');

    // My account: change password (limited to 6 tries a minute)
    Route::get('/account', [AccountController::class, 'show'])->name('account');
    Route::post('/account/password', [AccountController::class, 'updatePassword'])
        ->middleware('throttle:6,1')
        ->name('account.password');

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

        // Data for the admin panel (JSON, used by public/js/script.js)
        Route::get('/api/data', [AdminController::class, 'data'])->name('admin.api.data');

        // Members
        Route::post('/api/members', [AdminController::class, 'createMember'])->name('admin.api.members.create');
        Route::patch('/api/members/{user}/status', [AdminController::class, 'setMemberStatus'])->name('admin.api.members.status');
        Route::post('/api/members/{user}/goals', [AdminController::class, 'saveMemberGoals'])->name('admin.api.members.goals');
        Route::post('/api/members/{user}/password', [AdminController::class, 'resetMemberPassword'])->name('admin.api.members.password');
        Route::delete('/api/members/{user}', [AdminController::class, 'deleteMember'])->name('admin.api.members.destroy');

        // Workouts
        Route::post('/api/workouts', [AdminController::class, 'createWorkout'])->name('admin.api.workouts.create');
        Route::put('/api/workouts/{workout}', [AdminController::class, 'updateWorkout'])->name('admin.api.workouts.update');
        Route::delete('/api/workouts/{workout}', [AdminController::class, 'deleteWorkout'])->name('admin.api.workouts.destroy');

        // Foods
        Route::post('/api/foods', [AdminController::class, 'createFood'])->name('admin.api.foods.create');
        Route::put('/api/foods/{food}', [AdminController::class, 'updateFood'])->name('admin.api.foods.update');
        Route::delete('/api/foods/{food}', [AdminController::class, 'deleteFood'])->name('admin.api.foods.destroy');

        // Nutrition plans
        Route::post('/api/plans', [AdminController::class, 'createPlan'])->name('admin.api.plans.create');
        Route::put('/api/plans/{plan}', [AdminController::class, 'updatePlan'])->name('admin.api.plans.update');
        Route::delete('/api/plans/{plan}', [AdminController::class, 'deletePlan'])->name('admin.api.plans.destroy');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});
