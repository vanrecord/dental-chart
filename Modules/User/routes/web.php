<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\RoleController;
use Modules\User\Http\Controllers\UserController;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

// Authenticated Logout Route
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('logout', [AuthController::class, 'logout']);
});

// Protected Administration Routes (User CRUD & Role CRUD)
Route::middleware('auth')->group(function () {
    // User Management CRUD
    Route::resource('users', UserController::class);

    // Role Management CRUD
    Route::resource('roles', RoleController::class);

    // Named route aliases for module-prefixed compatibility
    Route::get('users/manage/index', [UserController::class, 'index'])->name('users.users.index');
    Route::get('users/manage/create', [UserController::class, 'create'])->name('users.users.create');
    Route::get('roles/manage/index', [RoleController::class, 'index'])->name('users.roles.index');
    Route::get('roles/manage/create', [RoleController::class, 'create'])->name('users.roles.create');
});
