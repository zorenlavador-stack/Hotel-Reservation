<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Public landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Only for visitors who are NOT logged in
Route::middleware(['guest', 'nocache'])->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Only for logged-in users
Route::middleware(['auth', 'nocache'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});