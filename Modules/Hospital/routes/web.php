<?php

use Illuminate\Support\Facades\Route;
use Modules\Hospital\Http\Controllers\ProductCategoryController;
use Modules\Hospital\Http\Controllers\ProductController;
use Modules\Hospital\Http\Controllers\ServiceCategoryController;
use Modules\Hospital\Http\Controllers\ServiceController;

Route::middleware('auth')->prefix('hospital')->name('hospital.')->group(function () {
    Route::resource('service-categories', ServiceCategoryController::class);
    Route::resource('product-categories', ProductCategoryController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('products', ProductController::class);
});
