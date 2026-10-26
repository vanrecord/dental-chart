<?php

use Illuminate\Support\Facades\Route;
use Modules\DentalChart\Http\Controllers\Api\ChartToothController;
use Modules\DentalChart\Http\Controllers\Api\DentalChartController;

Route::apiResource('dental-charts', DentalChartController::class);
Route::post('dental-charts/{dentalChart}/sync-findings', [DentalChartController::class, 'syncFindings'])
    ->name('dental-charts.sync-findings');

Route::apiResource('chart-teeth', ChartToothController::class);
