<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

Route::get('/dashboard', [CoreController::class, 'index'])->name('dashboard');
Route::get('/', [CoreController::class, 'index'])->name('home');

