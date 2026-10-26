<?php

namespace Database\Seeders;

use App\Models\ChartTooth;
use App\Models\DentalChart;
use App\Models\DentalCondition;
use App\Models\Patient;
use App\Models\ToothFinding;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'doctor@example.com'],
            [
                'name' => 'Dr. Alex Smith',
                'password' => bcrypt('password'),
            ]
        );

        $this->call(\Modules\User\Database\Seeders\UserDatabaseSeeder::class);
        $this->call(DentalConditionSeeder::class);

        // Seed a sample patient
        $patient = Patient::firstOrCreate(
            ['patient_number' => 'P-2026-0001'],
            [
                'full_name' => 'Jane Williams',
                'date_of_birth' => '1995-04-12',
                'sex' => 'female',
                'phone' => '+1 (555) 234-5678',
                'address' => '456 Dental Care Way, Suite 100',
                'medical_notes' => 'No known allergies. Routine 6-month checkup.',
            ]
        );

        // Seed a sample dental chart
        $chart = DentalChart::firstOrCreate(
            [
                'patient_id' => $patient->id,
                'chart_date' => now()->toDateString(),
            ],
            [
                'dentition_type' => 'mixed',
                'examination_notes' => 'Patient reports mild sensitivity on upper right premolar during cold drinks.',
                'created_by' => $user->id,
            ]
        );

        // Seed tooth 23 with caries on top surface matching reference demo
        $tooth23 = ChartTooth::firstOrCreate(
            [
                'dental_chart_id' => $chart->id,
                'tooth_number' => 23,
            ],
            [
                'tooth_type' => 'permanent',
                'tooth_status' => 'present',
            ]
        );

        $cariesCond = DentalCondition::where('code', 'caries')->first();

        ToothFinding::updateOrCreate(
            [
                'chart_tooth_id' => $tooth23->id,
                'surface' => 'top',
            ],
            [
                'dental_condition_id' => $cariesCond?->id,
                'custom_color' => '#d83c43',
                'treatment_status' => 'observed',
                'note' => 'Initial occlusal pit caries. Recommend monitoring.',
                'created_by' => $user->id,
            ]
        );
    }
}
