<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::middleware(['auth', 'verified', 'universal'])->group(function () {
    
    // Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::get('/', function(){
        dd(tenant('id'));
    })->name('dashboard');

    Route::get('/dashboard', function(){
        dd(tenant('id'));
    })->name('dashboard');
});

require __DIR__.'/settings.php';
