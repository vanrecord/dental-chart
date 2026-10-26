<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests are redirected to login and authenticated users access dashboard.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Unauthenticated guests redirect to login
        $this->get('/')->assertRedirect(route('login'));

        // Authenticated users access dashboard
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'doctor@example.com')->first();
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }
}
