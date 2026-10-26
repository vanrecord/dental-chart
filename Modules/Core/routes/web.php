<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [CoreController::class, 'index'])->name('dashboard');
    Route::get('/', [CoreController::class, 'index'])->name('home');
});

