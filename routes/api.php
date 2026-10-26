<?php

use App\Http\Controllers\Api\ChartToothController;
use App\Http\Controllers\Api\DentalChartController;
use App\Http\Controllers\Api\DentalConditionController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ToothFindingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefix: /api, name: api.*)
|--------------------------------------------------------------------------
*/

Route::name('api.')->group(function () {
    Route::apiResource('patients', PatientController::class);

    Route::apiResource('dental-charts', DentalChartController::class);
    Route::post('dental-charts/{dentalChart}/sync-findings', [DentalChartController::class, 'syncFindings'])
        ->name('dental-charts.sync-findings');

    Route::apiResource('chart-teeth', ChartToothController::class);
    Route::apiResource('dental-conditions', DentalConditionController::class);
    Route::apiResource('tooth-findings', ToothFindingController::class);
});

