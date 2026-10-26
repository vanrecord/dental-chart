<?php

use Illuminate\Support\Facades\Route;
use Modules\DentalCondition\Http\Controllers\Api\DentalConditionController;

Route::apiResource('dental-conditions', DentalConditionController::class);
