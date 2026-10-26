<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_page_renders_custom_template_successfully(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('WELCOME');
        $response->assertSee('YOUR PRACTICE. ONE SIMPLE PLACE.');
        $response->assertSee('Sign in');
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('name="remember"', false);
        $response->assertSee('togglePassword');
        $response->assertSee('Forgot password?');
        $response->assertSee('Sign in</button>', false);

        // Verify NO demo credentials or demo account block is present
        $response->assertDontSee('Demo Account');
        $response->assertDontSee('demo-credentials');
    }

    public function test_authenticated_user_visiting_login_redirects_to_dashboard(): void
    {
        $user = User::where('email', 'doctor@example.com')->first();
        $this->actingAs($user);

        $response = $this->get(route('login'));
        $response->assertRedirect(route('dashboard'));
    }

    public function test_login_with_valid_credentials_authenticates_and_redirects(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'doctor@example.com',
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('doctor@example.com', Auth::user()->email);
    }

    public function test_login_with_invalid_credentials_fails_with_errors(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'doctor@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_validation_requires_email_and_password(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_logout_invalidates_session_and_redirects_to_login(): void
    {
        $user = User::where('email', 'doctor@example.com')->first();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_unauthenticated_user_accessing_protected_routes_is_redirected_to_login(): void
    {
        // Unauthenticated access to user management
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('login'));

        // Unauthenticated access to role management
        $response = $this->get(route('roles.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_can_access_protected_pages(): void
    {
        $user = User::where('email', 'doctor@example.com')->first();
        $this->actingAs($user);

        $response = $this->get(route('users.index'));
        $response->assertStatus(200);

        $response = $this->get(route('roles.index'));
        $response->assertStatus(200);
    }
}
