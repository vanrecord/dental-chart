<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\database\seeders\RoleSeeder;
use Tests\TestCase;

class DataTableColumnVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_patient_list_renders_colvis_button_and_dropdown(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('patients.index'));

        $response->assertStatus(200);
        $response->assertSee('initColumnVisibility', false);
        $response->assertSee('btn-colvis', false);
        $response->assertSee('colvis-dropdown', false);
        $response->assertSee('Show / Hide Columns', false);
        $response->assertSee('Show/Hide Columns', false);
    }

    public function test_doctor_list_renders_colvis_button_and_dropdown(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('hospital.doctors.index'));

        $response->assertStatus(200);
        $response->assertSee('initColumnVisibility', false);
        $response->assertSee('btn-colvis', false);
        $response->assertSee('colvis-dropdown', false);
    }

    public function test_invoice_list_renders_colvis_button_and_dropdown(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('invoices.index'));

        $response->assertStatus(200);
        $response->assertSee('btn-colvis', false);
        $response->assertSee('colvis-dropdown', false);
    }
}

