<?php

use Illuminate\Support\Facades\Route;

Route::get('/activity-logs/status', function () {
    return response()->json(['status' => 'active', 'module' => 'ActivityLog']);
});
