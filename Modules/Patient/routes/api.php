<?php

use Illuminate\Support\Facades\Route;
use Modules\Patient\Http\Controllers\Api\PatientController;

Route::apiResource('patients', PatientController::class);
