<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Patient\Models\Patient;
use Modules\User\database\seeders\RoleSeeder;
use Tests\TestCase;

class DatePickerIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_layout_includes_datepicker_assets_and_initialization(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('invoices.create'));

        $response->assertStatus(200);
        $response->assertSee('flatpickr.min.css', false);
        $response->assertSee('flatpickr.min.js', false);
        $response->assertSee('initDatepickers', false);
        $response->assertSee('flatpickr-calendar', false);
    }

    public function test_patient_create_contains_date_input_ready_for_datepicker(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('patients.create'));

        $response->assertStatus(200);
        $response->assertSee('id="date_of_birth"', false);
    }

    public function test_patient_store_persists_standard_date_format(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('patients.store'), [
            'patient_number' => 'P-TEST-001',
            'full_name' => 'Datepicker Test Patient',
            'date_of_birth' => '1995-08-20',
            'sex' => 'female',
            'phone' => '+1 555-0199',
        ]);

        $response->assertRedirect();
        $patient = Patient::where('patient_number', 'P-TEST-001')->first();
        $this->assertNotNull($patient);
        $this->assertEquals('1995-08-20', $patient->date_of_birth->format('Y-m-d'));
    }
}
