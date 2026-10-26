<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Modules\ActivityLog\Services\ActivityLogger;
use Modules\DentalChart\Models\ChartTooth;
use Modules\DentalChart\Models\DentalChart;
use Modules\DentalChart\Services\DentalChartSyncService;
use Modules\DentalCondition\Models\DentalCondition;
use Modules\Patient\Models\Patient;
use Modules\ToothFinding\Models\ToothFinding;
use Nwidart\Modules\Facades\Module;
use Tests\TestCase;

class ModularArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_modules_are_registered_and_enabled(): void
    {
        $expectedModules = [
            'Core',
            'Patient',
            'DentalChart',
            'DentalCondition',
            'ToothFinding',
            'ActivityLog',
        ];

        foreach ($expectedModules as $moduleName) {
            $this->assertTrue(Module::has($moduleName), "Module [{$moduleName}] should exist.");
            $this->assertTrue(Module::isEnabled($moduleName), "Module [{$moduleName}] should be enabled.");
        }
    }

    public function test_module_views_are_accessible_via_namespaces(): void
    {
        $this->assertTrue(View::exists('core::layouts.app'), 'core::layouts.app view should exist');
        $this->assertTrue(View::exists('core::layouts.sidebar'), 'core::layouts.sidebar view should exist');
        $this->assertTrue(View::exists('core::layouts.header'), 'core::layouts.header view should exist');
        $this->assertTrue(View::exists('core::welcome'), 'core::welcome view should exist');
        $this->assertTrue(View::exists('patient::index'), 'patient::index view should exist');
        $this->assertTrue(View::exists('dentalchart::dental'), 'dentalchart::dental view should exist');
        $this->assertTrue(View::exists('dentalcondition::index'), 'dentalcondition::index view should exist');
    }

    public function test_collapsible_left_sidebar_renders_across_views(): void
    {
        // 1. Dashboard View
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('id="appSidebar"', false);
        $response->assertSee('id="sidebarToggle"', false);
        $response->assertSee('id="sidebarBackdrop"', false);
        $response->assertSee('OdontoCare');
        $response->assertSee('data-tooltip="Patients"', false);
        $response->assertSee('data-tooltip="Dental Charts"', false);
        $response->assertSee('data-tooltip="Interactive Odontogram"', false);

        // 2. Patients View
        $response = $this->get(route('patients.index'));
        $response->assertStatus(200);
        $response->assertSee('app-sidebar', false);
        $response->assertSee('sidebarToggle', false);
        $response->assertDontSee('Register Patient');
        $response->assertSee('+ Create Patient');

        // 3. Create Patient Screen (focused create patient screen only)
        $response = $this->get(route('patients.create'));
        $response->assertStatus(200);
        $response->assertSee('app-sidebar', false);
        $response->assertSee('Create Patient');
        $response->assertDontSee('Register Patient');
        $response->assertSee('name="patient_number"', false);
        $response->assertSee('name="full_name"', false);

        // 4. Interactive Odontogram View
        $response = $this->get(url('/dental'));
        $response->assertStatus(200);
        $response->assertSee('app-sidebar', false);
        $response->assertSee('sidebarToggle', false);
        $response->assertSee('upperPermanent');
        $response->assertSee('lowerPermanent');
    }

    public function test_cross_module_relationships(): void
    {
        $user = User::factory()->create();

        // 1. Patient in Patient module
        $patient = Patient::create([
            'patient_number' => 'MOD-P100',
            'full_name' => 'Modular Patient',
            'phone' => '123-456',
        ]);

        // 2. DentalChart in DentalChart module linked to Patient
        $chart = DentalChart::create([
            'patient_id' => $patient->id,
            'chart_date' => now()->toDateString(),
            'dentition_type' => 'mixed',
            'examination_notes' => 'Exam notes',
            'created_by' => $user->id,
        ]);

        // 3. ChartTooth in DentalChart module
        $tooth = ChartTooth::create([
            'dental_chart_id' => $chart->id,
            'tooth_number' => 24,
            'tooth_type' => 'permanent',
            'tooth_status' => 'present',
        ]);

        // 4. DentalCondition in DentalCondition module
        $condition = DentalCondition::create([
            'name' => 'Calculus',
            'code' => 'calculus',
            'default_color' => '#8b5bc7',
            'category' => 'finding',
        ]);

        // 5. ToothFinding in ToothFinding module linking ChartTooth and DentalCondition
        $finding = ToothFinding::create([
            'chart_tooth_id' => $tooth->id,
            'dental_condition_id' => $condition->id,
            'surface' => 'bottom',
            'custom_color' => '#8b5bc7',
            'note' => 'Subgingival calculus',
            'treatment_status' => 'observed',
        ]);

        // Assert relationships traverse across modules accurately
        $this->assertEquals($patient->id, $chart->patient->id);
        $this->assertInstanceOf(Patient::class, $chart->patient);
        $this->assertCount(1, $patient->dentalCharts);
        $this->assertEquals($chart->id, $patient->dentalCharts->first()->id);

        $this->assertEquals($chart->id, $tooth->dentalChart->id);
        $this->assertCount(1, $tooth->findings);

        $this->assertEquals($finding->id, $tooth->findings->first()->id);
        $this->assertEquals($condition->id, $finding->condition->id);
        $this->assertInstanceOf(DentalCondition::class, $finding->condition);
        $this->assertInstanceOf(ChartTooth::class, $finding->chartTooth);
    }

    public function test_dental_chart_sync_service(): void
    {
        $patient = Patient::create([
            'patient_number' => 'MOD-P200',
            'full_name' => 'Sync Test Patient',
        ]);

        $chart = DentalChart::create([
            'patient_id' => $patient->id,
            'chart_date' => now()->toDateString(),
        ]);

        $condition = DentalCondition::create([
            'name' => 'Sealant',
            'code' => 'sealant',
            'default_color' => '#169d9a',
        ]);

        $service = app(DentalChartSyncService::class);
        $synced = $service->sync($chart, [
            'examination_notes' => 'Tested via sync service',
            'dentition_type' => 'permanent',
            'findings' => [
                [
                    'tooth' => 36,
                    'surface' => 'top',
                    'condition_id' => $condition->id,
                    'color' => '#169d9a',
                    'status' => 'completed',
                    'note' => 'Pit sealant placed',
                ],
            ],
        ]);

        $this->assertEquals('Tested via sync service', $synced->examination_notes);
        $this->assertDatabaseHas('chart_teeth', [
            'dental_chart_id' => $chart->id,
            'tooth_number' => 36,
        ]);
        $this->assertDatabaseHas('tooth_findings', [
            'surface' => 'top',
            'treatment_status' => 'completed',
        ]);
    }

    public function test_activity_logger_service(): void
    {
        $patient = Patient::create([
            'patient_number' => 'LOG-001',
            'full_name' => 'Log Patient',
        ]);

        $logger = app(ActivityLogger::class);
        $logger->log('patient.created', 'New patient registered', $patient, ['source' => 'test']);
        $this->assertTrue(true);
    }
}
