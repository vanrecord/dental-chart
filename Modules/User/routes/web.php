<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\RoleController;
use Modules\User\Http\Controllers\UserController;

Route::middleware('web')->group(function () {
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
