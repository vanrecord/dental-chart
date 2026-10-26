<?php

use Illuminate\Support\Facades\Route;
use Modules\DentalChart\Http\Controllers\DentalChartController;
use Modules\DentalChart\Http\Controllers\DentalViewController;

Route::middleware('auth')->group(function () {
    Route::resource('dental-charts', DentalChartController::class);

    Route::get('/dental', [DentalViewController::class, 'index'])->name('dental.index');
    Route::get('/dental/{dentalChart}', [DentalViewController::class, 'show'])->name('dental.show');
});
