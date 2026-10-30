<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Hospital\Models\Doctor;
use Modules\Hospital\Models\Profile;
use Modules\User\Models\Role;
use Tests\TestCase;

class DoctorManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'doctor@example.com')->first();

        // Create regular user with no special permissions
        $this->regularUser = User::create([
            'name' => 'Regular Staff',
            'username' => 'regular_staff',
            'email' => 'regular@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function test_sidebar_displays_doctors_menu_for_authorized_users(): void
    {
        $response = $this->actingAs($this->admin)->get(route('hospital.doctors.index'));
        $response->assertStatus(200);
        $response->assertSee('Doctors');
        $response->assertSee(route('hospital.doctors.index'));
    }

    public function test_unauthorized_user_cannot_access_doctor_routes(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('hospital.doctors.index'));
        $response->assertStatus(403);

        $createResponse = $this->actingAs($this->regularUser)->get(route('hospital.doctors.create'));
        $createResponse->assertStatus(403);
    }

    public function test_admin_can_create_doctor_profile_and_user_in_one_operation(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image('doctor_photo.jpg', 300, 300);

        $payload = [
            // User info
            'name' => 'Dr. Jessica Jones',
            'username' => 'dr_jessica',
            'email' => 'jessica@dental.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'is_active' => '1',

            // Profile info
            'first_name' => 'Jessica',
            'last_name' => 'Jones',
            'gender' => 'female',
            'date_of_birth' => '1988-06-15',
            'phone' => '+855 12 999 888',
            'address' => 'St. 2004, Phnom Penh',
            'photo' => $photo,

            // Doctor professional info
            'doctor_code' => 'DOC-9001',
            'specialization' => 'Orthodontics',
            'qualification' => 'DDS, MSc Ortho',
            'license_number' => 'MED-LIC-777',
            'experience_years' => 10,
            'consultation_fee' => 75.00,
            'bio' => 'Certified orthodontics specialist with 10+ years experience.',
            'status' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('hospital.doctors.store'), $payload);

        $response->assertRedirect(route('hospital.doctors.index'));
        $response->assertSessionHas('success');

        // Verify User was created
        $user = User::where('email', 'jessica@dental.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Dr. Jessica Jones', $user->name);
        $this->assertEquals('dr_jessica', $user->username);
        $this->assertTrue(Hash::check('secret1234', $user->password));
        $this->assertTrue($user->is_active);

        // Verify Profile was created and linked
        $profile = $user->profile;
        $this->assertNotNull($profile);
        $this->assertEquals('Jessica', $profile->first_name);
        $this->assertEquals('Jones', $profile->last_name);
        $this->assertEquals('+855 12 999 888', $profile->phone);
        $this->assertNotNull($profile->photo);
        Storage::disk('public')->assertExists($profile->photo);

        // Verify Doctor record was created and linked
        $doctor = $user->doctor;
        $this->assertNotNull($doctor);
        $this->assertEquals('DOC-9001', $doctor->doctor_code);
        $this->assertEquals('Orthodontics', $doctor->specialization);
        $this->assertEquals(75.00, (float) $doctor->consultation_fee);
        $this->assertTrue($doctor->status);

        // Verify doctor role assigned
        $this->assertTrue($user->hasRole('doctor') || $user->hasRole('dentist'));
    }

    public function test_created_doctor_can_log_in_to_system_using_email_or_username(): void
    {
        // Create a doctor through the store route
        $payload = [
            'name' => 'Dr. Robert Brown',
            'username' => 'dr_robert',
            'email' => 'robert@dental.com',
            'password' => 'clinicPass123',
            'password_confirmation' => 'clinicPass123',
            'is_active' => '1',
            'first_name' => 'Robert',
            'last_name' => 'Brown',
            'doctor_code' => 'DOC-9002',
            'specialization' => 'Endodontics',
            'status' => '1',
        ];

        $this->actingAs($this->admin)->post(route('hospital.doctors.store'), $payload);

        // Log out admin
        $this->post(route('logout'));
        $this->assertGuest();

        // 1. Doctor logs in with email
        $loginWithEmail = $this->post(route('login'), [
            'username' => 'robert@dental.com',
            'password' => 'clinicPass123',
        ]);
        $loginWithEmail->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        // Log out
        $this->post(route('logout'));
        $this->assertGuest();

        // 2. Doctor logs in with username
        $loginWithUsername = $this->post(route('login'), [
            'username' => 'dr_robert',
            'password' => 'clinicPass123',
        ]);
        $loginWithUsername->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_inactive_doctor_account_cannot_log_in(): void
    {
        $payload = [
            'name' => 'Dr. Inactive Doctor',
            'username' => 'dr_inactive',
            'email' => 'inactive@dental.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'is_active' => '0', // Disabled
            'first_name' => 'Inactive',
            'last_name' => 'Doctor',
            'doctor_code' => 'DOC-9003',
            'status' => '0',
        ];

        $this->actingAs($this->admin)->post(route('hospital.doctors.store'), $payload);

        // Log out admin
        $this->post(route('logout'));
        $this->assertGuest();

        // Attempt login with disabled credentials
        $loginResponse = $this->post(route('login'), [
            'username' => 'dr_inactive',
            'password' => 'secret12345',
        ]);

        $loginResponse->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_edit_doctor_updates_user_profile_and_doctor_preserving_password(): void
    {
        $user = User::create([
            'name' => 'Dr. Old Name',
            'username' => 'dr_old',
            'email' => 'old@dental.com',
            'password' => Hash::make('originalPassword'),
            'is_active' => true,
        ]);

        $profile = $user->profile()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'phone' => '+855 12 000 111',
        ]);

        $doctor = $user->doctor()->create([
            'doctor_code' => 'DOC-1111',
            'specialization' => 'General Dentistry',
            'qualification' => 'BDS',
            'consultation_fee' => 30.00,
            'status' => true,
        ]);

        $updatePayload = [
            'name' => 'Dr. Updated Name',
            'username' => 'dr_updated',
            'email' => 'updated@dental.com',
            'password' => '', // Empty password must keep originalPassword
            'password_confirmation' => '',
            'is_active' => '1',
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'phone' => '+855 12 999 000',
            'doctor_code' => 'DOC-1111',
            'specialization' => 'Periodontics',
            'qualification' => 'DDS, Periodontist',
            'consultation_fee' => 60.00,
            'status' => '1',
        ];

        $response = $this->actingAs($this->admin)->put(route('hospital.doctors.update', $doctor), $updatePayload);
        $response->assertRedirect(route('hospital.doctors.show', $doctor));

        $user->refresh();
        $this->assertEquals('Dr. Updated Name', $user->name);
        $this->assertEquals('updated@dental.com', $user->email);
        $this->assertTrue(Hash::check('originalPassword', $user->password)); // Password preserved!

        $profile->refresh();
        $this->assertEquals('Updated', $profile->first_name);
        $this->assertEquals('+855 12 999 000', $profile->phone);

        $doctor->refresh();
        $this->assertEquals('Periodontics', $doctor->specialization);
        $this->assertEquals(60.00, (float) $doctor->consultation_fee);
    }

    public function test_view_doctor_renders_all_sections(): void
    {
        $user = User::create([
            'name' => 'Dr. Sarah Connor',
            'username' => 'dr_sarah',
            'email' => 'sarah@dental.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $user->profile()->create([
            'first_name' => 'Sarah',
            'last_name' => 'Connor',
            'phone' => '+855 77 123 456',
        ]);

        $doctor = $user->doctor()->create([
            'doctor_code' => 'DOC-2222',
            'specialization' => 'Pediatric Dentistry',
            'qualification' => 'DMD, Pediatric Specialist',
            'license_number' => 'PED-9988',
            'consultation_fee' => 50.00,
            'status' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('hospital.doctors.show', $doctor));
        $response->assertStatus(200);
        $response->assertSee('DOC-2222');
        $response->assertSee('Dr. Sarah Connor');
        $response->assertSee('sarah@dental.com');
        $response->assertSee('Pediatric Dentistry');
        $response->assertSee('$50.00');
    }

    public function test_delete_doctor_soft_deletes_doctor_record(): void
    {
        $user = User::create([
            'name' => 'Dr. Temporary Doctor',
            'username' => 'dr_temp',
            'email' => 'temp@dental.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $doctor = $user->doctor()->create([
            'doctor_code' => 'DOC-9999',
            'specialization' => 'General Dentistry',
            'status' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('hospital.doctors.destroy', $doctor));
        $response->assertRedirect(route('hospital.doctors.index'));

        // Doctor is soft deleted
        $this->assertSoftDeleted('doctors', ['id' => $doctor->id]);

        // User account is preserved
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}

