<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\User\Models\Role;
use Tests\TestCase;

class GeneralSettingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $doctor1;
    protected User $doctor2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        // Super Admin user
        $this->admin = User::factory()->create([
            'is_super_addmin' => true,
        ]);

        $dentistRole = Role::where('name', 'dentist')->first();

        $this->doctor1 = User::factory()->create(['name' => 'Dr. John Doe', 'username' => 'johndoe']);
        if ($dentistRole) {
            $this->doctor1->assignRole($dentistRole);
        }

        $this->doctor2 = User::factory()->create(['name' => 'Dr. Mary Jane', 'username' => 'maryjane']);
        if ($dentistRole) {
            $this->doctor2->assignRole($dentistRole);
        }
    }

    public function test_settings_general_sidebar_menu_renders_for_authorized_users(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('data-tooltip="Settings"', false);
        $response->assertSee(route('settings.general'));
        $response->assertSee('General');
    }

    public function test_unauthenticated_user_cannot_access_settings(): void
    {
        $response = $this->get(route('settings.general'));
        $response->assertRedirect(route('login'));

        $putResponse = $this->put(route('settings.general.update'), []);
        $putResponse->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_without_permission_is_forbidden(): void
    {
        $guestUser = User::factory()->create(['is_super_addmin' => false]);

        $response = $this->actingAs($guestUser)->get(route('settings.general'));
        $response->assertStatus(403);

        $putResponse = $this->actingAs($guestUser)->put(route('settings.general.update'), [
            'english_title' => 'Hacked Clinic',
        ]);
        $putResponse->assertStatus(403);
    }

    public function test_general_settings_page_opens_and_renders_both_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('settings.general'));
        $response->assertStatus(200);
        $response->assertSee('Clinic Info');
        $response->assertSee('Except Income Doctor');
        $response->assertSee('Clinic Logo');
        $response->assertSee('Khmer Title');
        $response->assertSee('English Title');
        $response->assertSee('Facebook Account');
        $response->assertSee('Facebook Page');
        $response->assertSee('Clinic Address');
        $response->assertSee('Dr. John Doe');
        $response->assertSee('Dr. Mary Jane');
    }

    public function test_saves_khmer_and_english_titles_with_colors_and_contact_info(): void
    {
        $payload = [
            'active_tab' => 'clinic_info',
            'khmer_title' => 'គ្លីនិកព្យាបាលធ្មេញ មិត្តភាព',
            'khmer_title_color' => '#1A56DB',
            'english_title' => 'Friendship Dental Clinic',
            'english_title_color' => '#0E9F6E',
            'description' => 'Gentle and Professional Dental Care',
            'description_color' => '#374151',
            'facebook_account' => 'FriendshipDentalKH',
            'facebook_page' => 'https://facebook.com/friendshipdentalkh',
            'email' => 'contact@friendshipdental.com',
            'phone' => '+855 23 999 111',
            'address' => '<p><strong>Building A</strong>, St 271, Phnom Penh</p>',
        ];

        $response = $this->actingAs($this->admin)->put(route('settings.general.update'), $payload);
        $response->assertRedirect(route('settings.general', ['tab' => 'clinic_info']));
        $response->assertSessionHas('success');

        $setting = ClinicSetting::getSettings();
        $this->assertEquals('គ្លីនិកព្យាបាលធ្មេញ មិត្តភាព', $setting->khmer_title);
        $this->assertEquals('#1A56DB', $setting->khmer_title_color);
        $this->assertEquals('Friendship Dental Clinic', $setting->english_title);
        $this->assertEquals('#0E9F6E', $setting->english_title_color);
        $this->assertEquals('Gentle and Professional Dental Care', $setting->description);
        $this->assertEquals('#374151', $setting->description_color);
        $this->assertEquals('FriendshipDentalKH', $setting->facebook_account);
        $this->assertEquals('https://facebook.com/friendshipdentalkh', $setting->facebook_page);
        $this->assertEquals('contact@friendshipdental.com', $setting->email);
        $this->assertEquals('+855 23 999 111', $setting->phone);
        $this->assertStringContainsString('Building A', $setting->address);

        // Reload page to verify values displayed in HTML
        $reload = $this->actingAs($this->admin)->get(route('settings.general'));
        $reload->assertStatus(200);
        $reload->assertSee('គ្លីនិកព្យាបាលធ្មេញ មិត្តភាព');
        $reload->assertSee('#1A56DB');
        $reload->assertSee('Friendship Dental Clinic');
        $reload->assertSee('#0E9F6E');
    }

    public function test_logo_upload_preview_replacement_and_removal(): void
    {
        Storage::fake('public');

        $fakeLogo = UploadedFile::fake()->image('clinic_logo.png', 300, 300);

        // 1. Upload Logo
        $response = $this->actingAs($this->admin)->put(route('settings.general.update'), [
            'english_title' => 'Test Clinic',
            'logo' => $fakeLogo,
        ]);
        $response->assertRedirect();

        $setting = ClinicSetting::getSettings();
        $this->assertNotNull($setting->logo_path);
        Storage::disk('public')->assertExists($setting->logo_path);
        $oldPath = $setting->logo_path;

        // 2. Replace Logo with new file
        $newFakeLogo = UploadedFile::fake()->image('new_clinic_logo.webp', 400, 400);
        $replaceResponse = $this->actingAs($this->admin)->put(route('settings.general.update'), [
            'english_title' => 'Test Clinic',
            'logo' => $newFakeLogo,
        ]);
        $replaceResponse->assertRedirect();

        $setting->refresh();
        $this->assertNotEquals($oldPath, $setting->logo_path);
        Storage::disk('public')->assertExists($setting->logo_path);
        Storage::disk('public')->assertMissing($oldPath);

        // 3. Delete Logo
        $deleteResponse = $this->actingAs($this->admin)->delete(route('settings.general.logo.destroy'));
        $deleteResponse->assertRedirect();

        $setting->refresh();
        $this->assertNull($setting->logo_path);
    }

    public function test_address_html_sanitizer_removes_dangerous_scripts_and_events(): void
    {
        $maliciousAddress = '<p>Normal Address</p><script>alert("xss")</script><a href="javascript:steal()" onclick="hack()">Link</a>';

        $this->actingAs($this->admin)->put(route('settings.general.update'), [
            'address' => $maliciousAddress,
        ]);

        $setting = ClinicSetting::getSettings();
        $this->assertStringNotContainsString('<script>', $setting->address);
        $this->assertStringNotContainsString('alert("xss")', $setting->address);
        $this->assertStringNotContainsString('onclick', $setting->address);
        $this->assertStringNotContainsString('javascript:steal()', $setting->address);
        $this->assertStringContainsString('Normal Address', $setting->address);
    }

    public function test_except_income_doctor_selection_and_persistence(): void
    {
        $payload = [
            'active_tab' => 'except_doctor',
            'except_doctor_ids' => [
                $this->doctor1->id,
                $this->doctor2->id,
            ],
        ];

        $response = $this->actingAs($this->admin)->put(route('settings.general.update'), $payload);
        $response->assertRedirect(route('settings.general', ['tab' => 'except_doctor']));

        $setting = ClinicSetting::getSettings();
        $this->assertTrue($setting->isDoctorExcluded($this->doctor1->id));
        $this->assertTrue($setting->isDoctorExcluded($this->doctor2->id));
        $this->assertCount(2, $setting->except_doctor_ids);

        // Re-visit the Except Income Doctor tab
        $revisit = $this->actingAs($this->admin)->get(route('settings.general', ['tab' => 'except_doctor']));
        $revisit->assertStatus(200);
        $revisit->assertSee('value="' . $this->doctor1->id . '"', false);
        $revisit->assertSee('checked', false);

        // Update: exclude only doctor 1
        $updatePayload = [
            'active_tab' => 'except_doctor',
            'except_doctor_ids' => [
                $this->doctor1->id,
            ],
        ];
        $this->actingAs($this->admin)->put(route('settings.general.update'), $updatePayload);

        $setting->refresh();
        $this->assertTrue($setting->isDoctorExcluded($this->doctor1->id));
        $this->assertFalse($setting->isDoctorExcluded($this->doctor2->id));
        $this->assertCount(1, $setting->except_doctor_ids);
    }

    public function test_validation_rejects_invalid_color_and_invalid_email(): void
    {
        $response = $this->actingAs($this->admin)->put(route('settings.general.update'), [
            'email' => 'not-an-email',
            'khmer_title_color' => 'invalid-hex',
            'facebook_page' => 'not-a-valid-url',
        ]);

        $response->assertSessionHasErrors(['email', 'khmer_title_color', 'facebook_page']);
    }

    public function test_saved_clinic_settings_appear_on_invoice_and_payment_views(): void
    {
        $setting = ClinicSetting::getSettings();
        $setting->update([
            'english_title' => 'Sunrise Smile Dental Care',
            'khmer_title' => 'គ្លីនិកធ្មេញ សាន់រ៉ាយស៍',
            'phone' => '+855 12 333 444',
        ]);

        $invoice = Invoice::where('invoice_number', 'INV-2026-001')->first();
        if ($invoice) {
            $invoiceResponse = $this->actingAs($this->admin)->get(route('invoices.show', $invoice));
            $invoiceResponse->assertStatus(200);
            $invoiceResponse->assertSee('Sunrise Smile Dental Care');
            $invoiceResponse->assertSee('គ្លីនិកធ្មេញ សាន់រ៉ាយស៍');
            $invoiceResponse->assertSee('+855 12 333 444');
        }

        $payment = Payment::where('payment_number', 'PAY-2026-001')->first();
        if ($payment) {
            $paymentResponse = $this->actingAs($this->admin)->get(route('payments.show', $payment));
            $paymentResponse->assertStatus(200);
            $paymentResponse->assertSee('Sunrise Smile Dental Care');
            $paymentResponse->assertSee('គ្លីនិកធ្មេញ សាន់រ៉ាយស៍');
            $paymentResponse->assertSee('+855 12 333 444');
        }
    }
}

