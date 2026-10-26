<?php

use App\Http\Controllers\DentalChartController;
use App\Http\Controllers\DentalConditionController;
use App\Http\Controllers\DentalViewController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Interactive Dental Chart views
Route::get('/dental', [DentalViewController::class, 'index'])->name('dental.index');
Route::get('/dental/{dentalChart}', [DentalViewController::class, 'show'])->name('dental.show');

// Resource CRUD Routes
Route::resource('patients', PatientController::class);
Route::resource('dental-charts', DentalChartController::class);
Route::resource('dental-conditions', DentalConditionController::class);
