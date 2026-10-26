<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\CoreController;

Route::get('/', [CoreController::class, 'index'])->name('home');
