<?php

namespace Tests\Feature;

use App\Models\ChartTooth;
use App\Models\DentalChart;
use App\Models\DentalCondition;
use App\Models\Patient;
use App\Models\ToothFinding;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DentalApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_patients_crud(): void
    {
        // 1. Store
        $res = $this->postJson(route('api.patients.store'), [
            'patient_number' => 'API-P01',
            'full_name' => 'API Patient',
            'phone' => '555-1234',
        ]);
        $res->assertStatus(201);
        $patientId = $res->json('data.id');

        // 2. Index
        $res = $this->getJson(route('api.patients.index'));
        $res->assertStatus(200)->assertJsonFragment(['patient_number' => 'API-P01']);

        // 3. Show
        $res = $this->getJson(route('api.patients.show', $patientId));
        $res->assertStatus(200)->assertJsonPath('data.full_name', 'API Patient');

        // 4. Update
        $res = $this->putJson(route('api.patients.update', $patientId), [
            'patient_number' => 'API-P01',
            'full_name' => 'API Patient Updated',
        ]);
        $res->assertStatus(200)->assertJsonPath('data.full_name', 'API Patient Updated');

        // 5. Destroy
        $res = $this->deleteJson(route('api.patients.destroy', $patientId));
        $res->assertStatus(200);
        $this->assertSoftDeleted('patients', ['id' => $patientId]);
    }

    public function test_api_conditions_crud(): void
    {
        // 1. Store
        $res = $this->postJson(route('api.dental-conditions.store'), [
            'name' => 'Fluorosis',
            'code' => 'fluorosis',
            'default_color' => '#10b981',
            'category' => 'finding',
        ]);
        $res->assertStatus(201);
        $condId = $res->json('data.id');

        // 2. Index
        $res = $this->getJson(route('api.dental-conditions.index'));
        $res->assertStatus(200)->assertJsonFragment(['code' => 'fluorosis']);

        // 3. Update
        $res = $this->putJson(route('api.dental-conditions.update', $condId), [
            'name' => 'Mild Fluorosis',
            'code' => 'fluorosis',
            'default_color' => '#059669',
        ]);
        $res->assertStatus(200);

        // 4. Delete
        $res = $this->deleteJson(route('api.dental-conditions.destroy', $condId));
        $res->assertStatus(200);
    }

    public function test_api_chart_teeth_and_findings_crud(): void
    {
        $patient = Patient::create(['patient_number' => 'P-99', 'full_name' => 'Charlie']);
        $chart = DentalChart::create(['patient_id' => $patient->id, 'chart_date' => '2026-10-26']);
        $condition = DentalCondition::create(['name' => 'Plaque', 'code' => 'plaque', 'default_color' => '#fbbf24']);

        // 1. Create ChartTooth
        $res = $this->postJson(route('api.chart-teeth.store'), [
            'dental_chart_id' => $chart->id,
            'tooth_number' => 11,
            'tooth_type' => 'permanent',
            'tooth_status' => 'present',
        ]);
        $res->assertStatus(201);
        $toothId = $res->json('data.id');

        // 2. Create ToothFinding
        $res = $this->postJson(route('api.tooth-findings.store'), [
            'chart_tooth_id' => $toothId,
            'dental_condition_id' => $condition->id,
            'surface' => 'left',
            'custom_color' => '#fbbf24',
            'note' => 'Cervical buildup',
            'treatment_status' => 'observed',
        ]);
        $res->assertStatus(201);
        $findingId = $res->json('data.id');

        // 3. Update Finding
        $res = $this->putJson(route('api.tooth-findings.update', $findingId), [
            'chart_tooth_id' => $toothId,
            'dental_condition_id' => $condition->id,
            'surface' => 'left',
            'note' => 'Cleaned during prophylaxis',
            'treatment_status' => 'completed',
        ]);
        $res->assertStatus(200)->assertJsonPath('data.treatment_status', 'completed');

        // 4. Delete Finding
        $res = $this->deleteJson(route('api.tooth-findings.destroy', $findingId));
        $res->assertStatus(200);
        $this->assertSoftDeleted('tooth_findings', ['id' => $findingId]);
    }
}

