<?php

use Illuminate\Support\Facades\Route;
use Modules\DentalCondition\Http\Controllers\DentalConditionController;

Route::resource('dental-conditions', DentalConditionController::class);
