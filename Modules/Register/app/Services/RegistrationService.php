<?php

namespace Modules\Register\Services;

use Illuminate\Support\Facades\DB;
use Modules\Invoice\Models\Invoice;
use Modules\Patient\Models\Patient;
use Modules\Register\Models\Reception;

class RegistrationService
{
    /**
     * Register a visit with a new invoice.
     */
    public function registerWithNewInvoice(Patient $patient, array $data, ?int $userId = null): array
    {
        return DB::transaction(function () use ($patient, $data, $userId) {
            $invoiceNumber = $data['invoice_number'] ?? Invoice::generateInvoiceNumber();
            $totalAmount = isset($data['total_amount']) ? (float) $data['total_amount'] : 0.00;

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'patient_id' => $patient->id,
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'total_amount' => $totalAmount,
                'paid_amount' => 0.00,
                'balance_amount' => $totalAmount,
                'status' => 'unpaid',
                'notes' => $data['invoice_notes'] ?? null,
                'created_by' => $userId,
            ]);

            $receptionNumber = Reception::generateReceptionNumber();
            $reception = Reception::create([
                'reception_number' => $receptionNumber,
                'patient_id' => $patient->id,
                'invoice_id' => $invoice->id,
                'reception_date' => $data['reception_date'] ?? now(),
                'visit_type' => $data['visit_type'] ?? 'initial',
                'chief_complaint' => $data['chief_complaint'] ?? null,
                'status' => 'checked_in',
                'notes' => $data['reception_notes'] ?? null,
                'created_by' => $userId,
            ]);

            return [
                'patient' => $patient,
                'invoice' => $invoice,
                'reception' => $reception,
                'is_new_invoice' => true,
            ];
        });
    }

    /**
     * Register a follow-up visit linked to an existing eligible invoice.
     */
    public function registerFollowUp(Patient $patient, Invoice $invoice, array $data, ?int $userId = null): array
    {
        return DB::transaction(function () use ($patient, $invoice, $data, $userId) {
            if ($invoice->patient_id !== $patient->id) {
                throw new \InvalidArgumentException("The selected invoice does not belong to patient [{$patient->full_name}].");
            }

            if (! $invoice->isEligibleForReuse()) {
                throw new \InvalidArgumentException("Invoice [{$invoice->invoice_number}] is [{$invoice->status}] and cannot be reused for follow-up visits.");
            }

            $receptionNumber = Reception::generateReceptionNumber();
            $reception = Reception::create([
                'reception_number' => $receptionNumber,
                'patient_id' => $patient->id,
                'invoice_id' => $invoice->id,
                'reception_date' => $data['reception_date'] ?? now(),
                'visit_type' => 'follow_up',
                'chief_complaint' => $data['chief_complaint'] ?? 'Follow-up consultation',
                'status' => 'checked_in',
                'notes' => $data['reception_notes'] ?? null,
                'created_by' => $userId,
            ]);

            return [
                'patient' => $patient,
                'invoice' => $invoice,
                'reception' => $reception,
                'is_new_invoice' => false,
            ];
        });
    }
}

