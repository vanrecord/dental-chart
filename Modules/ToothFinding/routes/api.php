<?php

use Illuminate\Support\Facades\Route;
use Modules\ToothFinding\Http\Controllers\Api\ToothFindingController;

Route::apiResource('tooth-findings', ToothFindingController::class);
