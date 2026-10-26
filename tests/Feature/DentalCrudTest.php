<?php

namespace Tests\Feature;

use App\Models\ChartTooth;
use App\Models\DentalChart;
use App\Models\DentalCondition;
use App\Models\Patient;
use App\Models\ToothFinding;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DentalCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = \Modules\User\Models\Role::firstOrCreate(['name' => 'admin', 'label' => 'Administrator']);
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole($adminRole);
        $this->actingAs($admin);
    }

    public function test_database_seeder_runs_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('patients', ['patient_number' => 'P-2026-0001']);
        $this->assertDatabaseHas('dental_conditions', ['code' => 'caries']);
        $this->assertDatabaseHas('chart_teeth', ['tooth_number' => 23]);
        $this->assertDatabaseHas('tooth_findings', ['surface' => 'top']);
    }

    public function test_patient_crud(): void
    {
        // 1. Create Patient
        $response = $this->post(route('patients.store'), [
            'patient_number' => 'P-TEST-001',
            'full_name' => 'Alice Johnson',
            'date_of_birth' => '1990-01-15',
            'sex' => 'female',
            'phone' => '1234567890',
            'address' => '123 Test St',
            'medical_notes' => 'Penicillin allergy',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('patients', ['patient_number' => 'P-TEST-001', 'full_name' => 'Alice Johnson']);

        $patient = Patient::where('patient_number', 'P-TEST-001')->firstOrFail();

        // 2. Read Patient
        $response = $this->get(route('patients.show', $patient));
        $response->assertStatus(200);
        $response->assertSee('Alice Johnson');

        // 3. Update Patient
        $response = $this->put(route('patients.update', $patient), [
            'patient_number' => 'P-TEST-001',
            'full_name' => 'Alice J. Smith',
            'sex' => 'female',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'full_name' => 'Alice J. Smith']);

        // 4. Delete Patient (Soft Delete)
        $response = $this->delete(route('patients.destroy', $patient));
        $response->assertRedirect(route('patients.index'));
        $this->assertSoftDeleted('patients', ['id' => $patient->id]);
    }

    public function test_dental_condition_crud(): void
    {
        // 1. Create Condition
        $response = $this->post(route('dental-conditions.store'), [
            'name' => 'Gingivitis',
            'code' => 'gingivitis',
            'description' => 'Gum inflammation',
            'default_color' => '#ff5500',
            'category' => 'finding',
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('dental-conditions.index'));
        $this->assertDatabaseHas('dental_conditions', ['code' => 'gingivitis']);

        $condition = DentalCondition::where('code', 'gingivitis')->firstOrFail();

        // 2. Read / Edit view
        $response = $this->get(route('dental-conditions.edit', $condition));
        $response->assertStatus(200);

        // 3. Update Condition
        $response = $this->put(route('dental-conditions.update', $condition), [
            'name' => 'Severe Gingivitis',
            'code' => 'gingivitis',
            'default_color' => '#ff3300',
            'category' => 'finding',
            'is_active' => 1,
        ]);
        $response->assertRedirect(route('dental-conditions.index'));
        $this->assertDatabaseHas('dental_conditions', ['name' => 'Severe Gingivitis']);

        // 4. Delete Condition
        $response = $this->delete(route('dental-conditions.destroy', $condition));
        $response->assertRedirect(route('dental-conditions.index'));
        $this->assertDatabaseMissing('dental_conditions', ['id' => $condition->id]);
    }

    public function test_dental_chart_crud_and_sync_findings(): void
    {
        $patient = Patient::create([
            'patient_number' => 'P-CHART-01',
            'full_name' => 'Bob Miller',
        ]);

        $condition = DentalCondition::create([
            'name' => 'Caries',
            'code' => 'caries',
            'default_color' => '#d83c43',
        ]);

        // 1. Create Chart
        $response = $this->post(route('dental-charts.store'), [
            'patient_id' => $patient->id,
            'chart_date' => '2026-10-26',
            'dentition_type' => 'mixed',
            'examination_notes' => 'Routine exam',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('dental_charts', ['patient_id' => $patient->id]);

        $chart = DentalChart::where('patient_id', $patient->id)->firstOrFail();

        // 2. Test Sync Findings API (used by the interactive UI)
        $syncResponse = $this->postJson(route('api.dental-charts.sync-findings', $chart), [
            'examination_notes' => 'Patient has sensitivity on upper right molar',
            'dentition_type' => 'permanent',
            'findings' => [
                [
                    'tooth' => 16,
                    'surface' => 'top',
                    'condition_id' => $condition->id,
                    'color' => '#d83c43',
                    'status' => 'planned',
                    'note' => 'Occlusal decay detected',
                ],
                [
                    'tooth' => 16,
                    'surface' => 'center',
                    'condition_id' => $condition->id,
                    'color' => '#d83c43',
                    'status' => 'planned',
                    'note' => 'Fissure involvement',
                ],
                [
                    'tooth' => 21,
                    'surface' => 'whole',
                    'color' => '#8b5bc7',
                    'status' => 'completed',
                    'note' => 'Porcelain crown intact',
                ],
            ],
        ]);

        $syncResponse->assertStatus(200);

        // Verify tooth 16 was created in chart_teeth
        $this->assertDatabaseHas('chart_teeth', [
            'dental_chart_id' => $chart->id,
            'tooth_number' => 16,
            'tooth_type' => 'permanent',
        ]);

        // Verify findings were recorded in tooth_findings
        $tooth16 = ChartTooth::where('dental_chart_id', $chart->id)->where('tooth_number', 16)->firstOrFail();
        $this->assertDatabaseHas('tooth_findings', [
            'chart_tooth_id' => $tooth16->id,
            'surface' => 'top',
            'dental_condition_id' => $condition->id,
        ]);
        $this->assertDatabaseHas('tooth_findings', [
            'chart_tooth_id' => $tooth16->id,
            'surface' => 'center',
        ]);

        // Verify tooth 21 whole
        $tooth21 = ChartTooth::where('dental_chart_id', $chart->id)->where('tooth_number', 21)->firstOrFail();
        $this->assertDatabaseHas('tooth_findings', [
            'chart_tooth_id' => $tooth21->id,
            'surface' => 'whole',
            'treatment_status' => 'completed',
        ]);

        // 3. Test Interactive View with loaded chart
        $viewResponse = $this->get(route('dental.show', $chart));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Bob Miller');

        // 4. Update Chart via Web
        $response = $this->put(route('dental-charts.update', $chart), [
            'patient_id' => $patient->id,
            'chart_date' => '2026-10-27',
            'dentition_type' => 'permanent',
            'examination_notes' => 'Updated clinical notes',
        ]);
        $response->assertRedirect(route('dental-charts.index'));
        $this->assertDatabaseHas('dental_charts', [
            'id' => $chart->id,
            'examination_notes' => 'Updated clinical notes',
        ]);

        // 5. Delete Chart
        $response = $this->delete(route('dental-charts.destroy', $chart));
        $response->assertRedirect();
        $this->assertDatabaseMissing('dental_charts', ['id' => $chart->id]);
        $this->assertDatabaseMissing('chart_teeth', ['dental_chart_id' => $chart->id]);
    }
}
