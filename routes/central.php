<?php

use App\Http\Controllers\Central\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', function () {
        return 'Central Users Area';
    })->name('users.index');
    Route::get('/admin', function () {
        return 'Central Admin Area';
    })->name('admin.index');
});
