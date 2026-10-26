<?php

use Illuminate\Support\Facades\Route;
use Modules\DentalCondition\Http\Controllers\DentalConditionController;

Route::middleware('auth')->group(function () {
    Route::resource('dental-conditions', DentalConditionController::class);
});
