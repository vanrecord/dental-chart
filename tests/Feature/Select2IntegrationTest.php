<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\database\seeders\RoleSeeder;
use Tests\TestCase;

class Select2IntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_layout_includes_select2_assets_and_initialization(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('invoices.create'));

        $response->assertStatus(200);
        $response->assertSee('select2.min.css', false);
        $response->assertSee('select2.min.js', false);
        $response->assertSee('jquery.min.js', false);
        $response->assertSee('initSelect2', false);
        $response->assertSee('select2-container', false);
    }

    public function test_doctor_creation_page_includes_select2_ready_selects(): void
    {
        $admin = User::factory()->create([
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('hospital.doctors.create'));

        $response->assertStatus(200);
        $response->assertSee('id="gender"', false);
        $response->assertSee('id="status"', false);
        $response->assertSee('id="is_active"', false);
    }
}
