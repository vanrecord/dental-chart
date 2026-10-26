<?php

use Illuminate\Support\Facades\Route;
use Modules\Patient\Http\Controllers\PatientController;

Route::middleware('auth')->group(function () {
    Route::resource('patients', PatientController::class);
});
