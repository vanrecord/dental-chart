<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoice\Models\Invoice;
use Modules\Patient\Models\Patient;
use Modules\Payment\Models\Payment;
use Modules\Register\Models\Reception;
use Modules\User\Models\Role;
use Tests\TestCase;

class RegistrationAndBillingTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::where('email', 'doctor@example.com')->first();
    }

    public function test_sidebar_includes_register_patient_invoice_payment_menus(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        // Required 4 navigation items in sidebar
        $response->assertSee('data-tooltip="Register"', false);
        $response->assertSee('data-tooltip="Patients"', false);
        $response->assertSee('data-tooltip="Invoice"', false);
        $response->assertSee('data-tooltip="Payment"', false);

        // Verify patient menu is single and not duplicated
        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'data-tooltip="Patients"'));
        $this->assertEquals(1, substr_count($content, 'data-tooltip="Register"'));
        $this->assertEquals(1, substr_count($content, 'data-tooltip="Invoice"'));
        $this->assertEquals(1, substr_count($content, 'data-tooltip="Payment"'));
    }

    public function test_patient_list_displays_required_columns_and_invoice_count(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('patients.index'));
        $response->assertStatus(200);

        // Check required columns
        $response->assertSee('Patient ID');
        $response->assertSee('Patient Name');
        $response->assertSee('Gender');
        $response->assertSee('Date of Birth');
        $response->assertSee('Phone');
        $response->assertSee('Registered');
        $response->assertSee('Invoices');

        // Check demo patient row
        $response->assertSee('P000125');
        $response->assertSee('Demo Patient');
        $response->assertSee('1 inv');
        $response->assertSee('View');
        $response->assertSee('Edit');
    }

    public function test_patient_details_screen_shows_billing_reception_and_register_visit_action(): void
    {
        $this->actingAs($this->adminUser);

        $patient = Patient::where('patient_number', 'P000125')->first();
        $this->assertNotNull($patient);

        $response = $this->get(route('patients.show', $patient));
        $response->assertStatus(200);

        // Patient profile & register visit action
        $response->assertSee('Demo Patient');
        $response->assertSee('+ Register Visit');

        // Financial & Billing summary
        $response->assertSee('Total Invoiced');
        $response->assertSee('$80.00');
        $response->assertSee('Total Paid');
        $response->assertSee('$50.00');
        $response->assertSee('Outstanding Balance');
        $response->assertSee('$30.00');

        // Reception history
        $response->assertSee('REC-001');
        $response->assertSee('REC-002');

        // Invoices history
        $response->assertSee('INV-2026-001');
    }

    public function test_register_workflow_option_a_creates_new_invoice_and_initial_reception(): void
    {
        $this->actingAs($this->adminUser);

        $patient = Patient::where('patient_number', 'P-2026-0001')->first();
        $this->assertNotNull($patient);

        $postData = [
            'patient_id' => $patient->id,
            'workflow' => 'new_invoice',
            'total_amount' => '120.00',
            'invoice_date' => now()->toDateString(),
            'visit_type' => 'initial',
            'chief_complaint' => 'Severe toothache in lower left molar',
            'notes' => 'Exam and diagnostic x-ray billed.',
        ];

        $response = $this->post(route('register.store'), $postData);
        $newInvoice = Invoice::where('patient_id', $patient->id)->latest()->first();
        $this->assertNotNull($newInvoice);
        $response->assertRedirect(route('invoices.edit', $newInvoice));
        $response->assertSessionHas('success');

        // Assert invoice created
        $newInvoice = Invoice::where('patient_id', $patient->id)->latest()->first();
        $this->assertNotNull($newInvoice);
        $this->assertEquals(120.00, $newInvoice->total_amount);
        $this->assertEquals(120.00, $newInvoice->balance_amount);
        $this->assertEquals(0.00, $newInvoice->paid_amount);
        $this->assertEquals('unpaid', $newInvoice->status);

        // Assert reception created and linked to this invoice
        $newReception = Reception::where('patient_id', $patient->id)->latest()->first();
        $this->assertNotNull($newReception);
        $this->assertEquals($newInvoice->id, $newReception->invoice_id);
        $this->assertEquals('initial', $newReception->visit_type);
        $this->assertEquals('Severe toothache in lower left molar', $newReception->chief_complaint);
    }

    public function test_register_workflow_option_b_follow_up_links_existing_invoice_without_creating_new_invoice(): void
    {
        $this->actingAs($this->adminUser);

        // Demo Patient P000125 starts with INV-2026-001 and REC-001, REC-002
        $patient = Patient::where('patient_number', 'P000125')->first();
        $invoice = Invoice::where('invoice_number', 'INV-2026-001')->first();
        $initialInvoiceCount = Invoice::count();
        $initialReceptionCount = Reception::where('invoice_id', $invoice->id)->count();
        $this->assertEquals(2, $initialReceptionCount);

        // Perform follow-up registration
        $postData = [
            'patient_id' => $patient->id,
            'workflow' => 'follow_up',
            'invoice_id' => $invoice->id,
            'chief_complaint' => 'Follow-up inspection for scaling healing',
            'notes' => 'Treated in room 2.',
        ];

        $response = $this->post(route('register.store'), $postData);
        $response->assertRedirect(route('invoices.edit', $invoice));
        $response->assertSessionHas('success');

        // Crucial requirement: Do NOT create another invoice when reusing existing invoice
        $this->assertEquals($initialInvoiceCount, Invoice::count(), 'Invoice count must NOT increase on follow-up visit');

        // Reception count must increase by 1 (creating REC-003)
        $this->assertEquals(3, Reception::where('invoice_id', $invoice->id)->count());
        $rec003 = Reception::where('invoice_id', $invoice->id)->latest('id')->first();
        $this->assertEquals('REC-003', $rec003->reception_number);
        $this->assertEquals('follow_up', $rec003->visit_type);
        $this->assertEquals($invoice->id, $rec003->invoice_id);
    }

    public function test_register_follow_up_validates_patient_invoice_ownership(): void
    {
        $this->actingAs($this->adminUser);

        $patientA = Patient::where('patient_number', 'P-2026-0001')->first();
        $invoiceB = Invoice::where('invoice_number', 'INV-2026-001')->first(); // belongs to P000125

        $postData = [
            'patient_id' => $patientA->id,
            'workflow' => 'follow_up',
            'invoice_id' => $invoiceB->id,
        ];

        $response = $this->post(route('register.store'), $postData);
        $response->assertSessionHas('error');
    }

    public function test_register_ajax_search_patients_and_patient_invoices(): void
    {
        $this->actingAs($this->adminUser);

        // Searching with fewer than 2 characters returns empty results
        $responseEmpty = $this->getJson(route('register.search_patients', ['q' => '']));
        $responseEmpty->assertStatus(200);
        $responseEmpty->assertExactJson([]);

        $responseOneChar = $this->getJson(route('register.search_patients', ['q' => 'D']));
        $responseOneChar->assertStatus(200);
        $responseOneChar->assertExactJson([]);

        // AJAX search patients with at least 2 characters
        $responseTwoChar = $this->getJson(route('register.search_patients', ['q' => 'De']));
        $responseTwoChar->assertStatus(200);
        $responseTwoChar->assertJsonFragment(['patient_number' => 'P000125', 'full_name' => 'Demo Patient']);

        // AJAX search patients with exact case
        $response = $this->getJson(route('register.search_patients', ['q' => 'Demo']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['patient_number' => 'P000125', 'full_name' => 'Demo Patient']);

        // AJAX search patients with lowercase name
        $responseLower = $this->getJson(route('register.search_patients', ['q' => 'demo']));
        $responseLower->assertStatus(200);
        $responseLower->assertJsonFragment(['patient_number' => 'P000125', 'full_name' => 'Demo Patient']);

        // AJAX search patients with lowercase patient ID
        $responseId = $this->getJson(route('register.search_patients', ['q' => 'p000125']));
        $responseId->assertStatus(200);
        $responseId->assertJsonFragment(['patient_number' => 'P000125', 'full_name' => 'Demo Patient']);

        // AJAX patient invoices
        $patient = Patient::where('patient_number', 'P000125')->first();
        $response = $this->getJson(route('register.patient_invoices', $patient));
        $response->assertStatus(200);
        $response->assertJsonFragment(['invoice_number' => 'INV-2026-001']);
    }

    public function test_invoices_list_and_details_show_actual_records(): void
    {
        $this->actingAs($this->adminUser);

        // Index
        $response = $this->get(route('invoices.index'));
        $response->assertStatus(200);
        $response->assertSee('INV-2026-001');
        $response->assertSee('Demo Patient');
        $response->assertSee('$80.00');
        $response->assertSee('$50.00');
        $response->assertSee('$30.00');
        $invoice = Invoice::where('invoice_number', 'INV-2026-001')->first();
        $response->assertSee(route('invoices.print', $invoice));

        // Show
        $response = $this->get(route('invoices.show', $invoice));
        $response->assertStatus(200);
        $response->assertSee('INV-2026-001');
        $response->assertSee('Outstanding Balance');
        $response->assertSee('$30.00');
        $response->assertSee('REC-001');
        $response->assertSee('PAY-2026-001');
        $response->assertSee(route('invoices.print', $invoice));
        $response->assertDontSee('+ Link Follow-up Visit');
        $response->assertSee(route('register.index', ['patient_id' => $invoice->patient_id, 'invoice_id' => $invoice->id, 'workflow' => 'follow_up']));

        // Print Invoice view
        $printResponse = $this->get(route('invoices.print', $invoice));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('INV-2026-001');
        $printResponse->assertSee('Print Invoice');
        $printResponse->assertSee('$80.00');
        $printResponse->assertSee('$30.00');

        // Test clicking follow-up visit auto-selects Option B and the specific invoice
        $followUpPage = $this->get(route('register.index', [
            'patient_id' => $invoice->patient_id,
            'invoice_id' => $invoice->id,
            'workflow' => 'follow_up',
        ]));
        $followUpPage->assertStatus(200);
        $followUpPage->assertSee('value="follow_up" id="workflowFollowUp" checked', false);
        $followUpPage->assertSee('value="' . $invoice->id . '" checked', false);
        $followUpPage->assertSee('Option B: Follow-up Visit');
        $followUpPage->assertSee($invoice->invoice_number . ' (Reused)');
    }

    public function test_payments_list_and_details_show_actual_records(): void
    {
        $this->actingAs($this->adminUser);

        // Index
        $response = $this->get(route('payments.index'));
        $response->assertStatus(200);
        $response->assertSee('PAY-2026-001');
        $response->assertSee('$50.00');
        $response->assertSee('INV-2026-001');
        $response->assertDontSee('+ Record Payment');
        $payment = Payment::where('payment_number', 'PAY-2026-001')->first();
        $response->assertSee(route('payments.print', $payment));

        // Show
        $response = $this->get(route('payments.show', $payment));
        $response->assertStatus(200);
        $response->assertSee('PAY-2026-001');
        $response->assertSee('$50.00');
        $response->assertSee('INV-2026-001');
        $response->assertSee(route('payments.print', $payment));

        // Print Payment view
        $printResponse = $this->get(route('payments.print', $payment));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('PAY-2026-001');
        $printResponse->assertSee('Print Receipt');
        $printResponse->assertSee('$50.00');
    }

    public function test_recording_payment_updates_invoice_balance_and_status(): void
    {
        $this->actingAs($this->adminUser);

        $invoice = Invoice::where('invoice_number', 'INV-2026-001')->first();
        $this->assertEquals(30.00, (float) $invoice->balance_amount);
        $this->assertEquals('partial', $invoice->status);

        // Record payment for remaining $30.00
        $postData = [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => '30.00',
            'payment_method' => 'credit_card',
            'transaction_reference' => 'TXN-FINAL-002',
            'notes' => 'Settled remaining balance.',
        ];

        $response = $this->post(route('payments.store'), $postData);
        $response->assertRedirect();

        $invoice->refresh();
        $this->assertEquals(80.00, (float) $invoice->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->balance_amount);
        $this->assertEquals('paid', $invoice->status);
    }

    public function test_payment_recording_prevents_overpayment(): void
    {
        $this->actingAs($this->adminUser);

        $invoice = Invoice::where('invoice_number', 'INV-2026-001')->first();
        $this->assertEquals(30.00, (float) $invoice->balance_amount);

        // Attempt to pay $999.00 on a $30.00 balance
        $postData = [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => '999.00',
            'payment_method' => 'cash',
        ];

        $response = $this->post(route('payments.store'), $postData);
        $response->assertSessionHas('error');
    }

    public function test_unauthorized_user_without_permissions_is_forbidden_403(): void
    {
        // User with no roles or permissions
        $guestUser = User::factory()->create();

        $this->actingAs($guestUser);

        $this->get(route('register.index'))->assertStatus(403);
        $this->post(route('register.store'), [])->assertStatus(403);
        $this->get(route('invoices.index'))->assertStatus(403);
        $this->get(route('payments.index'))->assertStatus(403);
    }

    public function test_receptionist_role_can_access_registration_patients_invoices_and_payments(): void
    {
        $receptionistRole = Role::where('name', 'receptionist')->first();
        $receptionist = User::factory()->create();
        $receptionist->assignRole($receptionistRole);

        $this->actingAs($receptionist);

        // Receptionist can access Register, Patients, Invoices, Payments
        $this->get(route('register.index'))->assertStatus(200);
        $this->get(route('patients.index'))->assertStatus(200);
        $this->get(route('invoices.index'))->assertStatus(200);
        $this->get(route('payments.index'))->assertStatus(200);

        // Receptionist CANNOT access users or dental charts management
        $this->get(route('users.index'))->assertStatus(403);
        $this->get(route('roles.index'))->assertStatus(403);
    }
}

