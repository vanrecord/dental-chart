<?php

use Illuminate\Support\Facades\Route;
use Modules\Register\Http\Controllers\RegisterController;

Route::middleware('auth')->group(function () {
    Route::get('register', [RegisterController::class, 'index'])->name('register.index');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('register/search-patients', [RegisterController::class, 'searchPatients'])->name('register.search_patients');
    Route::get('register/patient-invoices/{patient}', [RegisterController::class, 'patientInvoices'])->name('register.patient_invoices');
    Route::post('register/quick-create-patient', [RegisterController::class, 'quickCreatePatient'])->name('register.quick_create_patient');
});
