<?php

namespace Modules\Register\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Invoice\Models\Invoice;
use Modules\Patient\Models\Patient;
use Modules\Payment\Models\Payment;
use Modules\Register\Models\Reception;

class RegisterDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        // Seed demo patient P000125
        $patient = Patient::firstOrCreate(
            ['patient_number' => 'P000125'],
            [
                'full_name' => 'Demo Patient',
                'date_of_birth' => '1990-05-15',
                'sex' => 'male',
                'phone' => '+1 (555) 789-0123',
                'address' => '123 Medical Center Blvd',
                'medical_notes' => 'Dental cleaning and follow-up orthodontic review.',
            ]
        );

        // Seed invoice INV-2026-001 ($80.00 total)
        $invoice = Invoice::firstOrCreate(
            ['invoice_number' => 'INV-2026-001'],
            [
                'patient_id' => $patient->id,
                'invoice_date' => now()->subDays(5)->toDateString(),
                'total_amount' => 80.00,
                'paid_amount' => 50.00,
                'balance_amount' => 30.00,
                'status' => 'partial',
                'notes' => 'Routine dental checkup and scaling procedure.',
                'created_by' => $user?->id,
            ]
        );

        // Seed initial reception REC-001
        Reception::firstOrCreate(
            ['reception_number' => 'REC-001'],
            [
                'patient_id' => $patient->id,
                'invoice_id' => $invoice->id,
                'reception_date' => now()->subDays(5),
                'visit_type' => 'initial',
                'chief_complaint' => 'Initial dental checkup and examination.',
                'status' => 'completed',
                'created_by' => $user?->id,
            ]
        );

        // Seed second reception REC-002
        Reception::firstOrCreate(
            ['reception_number' => 'REC-002'],
            [
                'patient_id' => $patient->id,
                'invoice_id' => $invoice->id,
                'reception_date' => now()->subDays(2),
                'visit_type' => 'follow_up',
                'chief_complaint' => 'Follow-up dental cleaning session.',
                'status' => 'completed',
                'created_by' => $user?->id,
            ]
        );

        // Seed payment for $50.00
        Payment::firstOrCreate(
            ['payment_number' => 'PAY-2026-001'],
            [
                'invoice_id' => $invoice->id,
                'patient_id' => $patient->id,
                'payment_date' => now()->subDays(5)->toDateString(),
                'amount' => 50.00,
                'currency' => 'USD',
                'payment_method' => 'cash',
                'transaction_reference' => 'TXN-001',
                'status' => 'completed',
                'notes' => 'Initial deposit payment.',
                'created_by' => $user?->id,
            ]
        );

        // Recalculate balance
        $invoice->recalculateBalance();
    }
}
