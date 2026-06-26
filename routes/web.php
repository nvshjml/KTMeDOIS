<?php

use Illuminate\Support\Facades\Route;

// 1. The main dashboard route (for customers)
Route::get('/dashboard', function () {
    return view('dashboard'); // This loads your dashboard.blade.php
})->middleware(['auth'])->name('dashboard');

// 2. The supplier dashboard route
Route::get('/supplier/dashboard', function () {
    return view('dashboard'); // We will use the same view for now just to test!
})->middleware(['auth'])->name('supplier.dashboard');
