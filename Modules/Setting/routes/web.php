<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\GeneralSettingController;

Route::middleware(['auth'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', fn () => redirect()->route('settings.general'));
    Route::get('general', [GeneralSettingController::class, 'edit'])->name('general');
    Route::put('general', [GeneralSettingController::class, 'update'])->name('general.update');
    Route::delete('general/logo', [GeneralSettingController::class, 'destroyLogo'])->name('general.logo.destroy');
});
