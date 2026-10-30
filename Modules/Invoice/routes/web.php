<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoice\Http\Controllers\InvoiceController;

Route::middleware('auth')->group(function () {
    Route::get('invoices/patient-visits/{patient}', [InvoiceController::class, 'patientVisits'])->name('invoices.patient-visits');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::resource('invoices', InvoiceController::class);
});
