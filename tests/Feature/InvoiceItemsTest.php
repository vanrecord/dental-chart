<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hospital\Models\Category;
use Modules\Hospital\Models\Service;
use Modules\Invoice\Models\Invoice;
use Modules\Invoice\Models\InvoiceItem;
use Modules\Patient\Models\Patient;
use Modules\Register\Models\Reception;
use Modules\User\Database\Seeders\PermissionSeeder;
use Modules\User\Database\Seeders\RoleSeeder;
use Modules\User\Models\Role;
use Tests\TestCase;

class InvoiceItemsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Patient $patient;
    protected Service $dentalCleaning;
    protected Service $rootCanal;
    protected Service $compositeResin;
    protected Service $dentalFloss;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $adminRole = Role::where('name', 'admin')->first();
        $this->admin = User::factory()->create([
            'username' => 'doctor_alex',
            'is_super_addmin' => false,
        ]);
        $this->admin->roles()->attach($adminRole);

        $this->patient = Patient::create([
            'patient_number' => 'P-2026-9001',
            'full_name' => 'Johnathan Dental',
            'sex' => 'male',
            'date_of_birth' => '1990-05-15',
            'phone' => '012999888',
        ]);

        $serviceCat = Category::create([
            'name' => 'General Dentistry',
            'type' => Category::SERVICE,
            'is_active' => true,
        ]);

        $productCat = Category::create([
            'name' => 'Dental Consumables',
            'type' => Category::PRODUCT,
            'is_active' => true,
        ]);

        $this->dentalCleaning = Service::create([
            'name' => 'Dental Cleaning (Scaling)',
            'type' => Service::SERVICE,
            'category_id' => $serviceCat->id,
            'price' => 60.00,
            'status' => 'active',
        ]);

        $this->rootCanal = Service::create([
            'name' => 'Root Canal Treatment',
            'type' => Service::SERVICE,
            'category_id' => $serviceCat->id,
            'price' => 250.00,
            'status' => 'active',
        ]);

        $this->compositeResin = Service::create([
            'name' => 'Composite Resin Syringe',
            'type' => Service::PRODUCT,
            'category_id' => $productCat->id,
            'price' => 35.00,
            'status' => 'active',
        ]);

        $this->dentalFloss = Service::create([
            'name' => 'Dental Floss Waxed',
            'type' => Service::PRODUCT,
            'category_id' => $productCat->id,
            'price' => 5.00,
            'status' => 'active',
        ]);
    }

    public function test_create_invoice_page_renders_service_and_product_buttons_and_items_table(): void
    {
        $response = $this->actingAs($this->admin)->get(route('invoices.create'));

        $response->assertStatus(200);
        $response->assertSee('+ Service');
        $response->assertSee('+ Product');
        $response->assertSee('Create Dental Charts');
        $response->assertSee('Operate Date');
        $response->assertSee('Service / Product');
        $response->assertSee('Treatment Teeth');
        $response->assertSee('Quantity');
        $response->assertSee('Unit Price');
        $response->assertSee('Invoice Summary');
    }

    public function test_store_invoice_with_multiple_services_and_products(): void
    {
        $payload = [
            'patient_id' => $this->patient->id,
            'invoice_date' => now()->toDateString(),
            'notes' => 'Dental treatment and post-op supplies',
            'discount' => '20.00',
            'tax' => '10.00',
            'items' => [
                // Service 1: Cleaning
                [
                    'type' => 'service',
                    'service_id' => $this->dentalCleaning->id,
                    'operate_date' => now()->toDateString(),
                    'tooth_numbers' => 'All teeth',
                    'quantity' => 1,
                    'unit_price' => 60.00,
                    'note' => 'Routine hygiene cleaning',
                ],
                // Service 2: Root canal on tooth 16
                [
                    'type' => 'service',
                    'service_id' => $this->rootCanal->id,
                    'operate_date' => now()->toDateString(),
                    'tooth_numbers' => '16',
                    'quantity' => 1,
                    'unit_price' => 250.00,
                    'note' => 'First stage pulpectomy',
                ],
                // Product 1: Resin 2 syringes
                [
                    'type' => 'product',
                    'service_id' => $this->compositeResin->id,
                    'operate_date' => now()->toDateString(),
                    'quantity' => 2,
                    'unit_price' => 35.00,
                    'note' => 'Shade A2',
                ],
                // Product 2: Floss 3 packs
                [
                    'type' => 'product',
                    'service_id' => $this->dentalFloss->id,
                    'operate_date' => now()->toDateString(),
                    'quantity' => 3,
                    'unit_price' => 5.00,
                    'note' => 'Patient home hygiene kit',
                ],
            ],
        ];

        // Subtotal calculation:
        // 60*1 + 250*1 + 35*2 + 5*3 = 60 + 250 + 70 + 15 = 395.00
        // Grand total: 395.00 - 20.00 (discount) + 10.00 (tax) = 385.00

        $response = $this->actingAs($this->admin)->post(route('invoices.store'), $payload);

        $invoice = Invoice::where('patient_id', $this->patient->id)->latest()->first();
        $this->assertNotNull($invoice);
        $response->assertRedirect(route('invoices.show', $invoice));

        $this->assertEquals(395.00, $invoice->subtotal);
        $this->assertEquals(20.00, $invoice->discount);
        $this->assertEquals(10.00, $invoice->tax);
        $this->assertEquals(385.00, $invoice->total_amount);
        $this->assertEquals(385.00, $invoice->balance_amount);
        $this->assertEquals('unpaid', $invoice->status);

        // Verify items persisted
        $this->assertCount(4, $invoice->items);
        $serviceItems = $invoice->items()->where('type', 'service')->get();
        $productItems = $invoice->items()->where('type', 'product')->get();
        $this->assertCount(2, $serviceItems);
        $this->assertCount(2, $productItems);

        $rootCanalItem = $invoice->items()->where('service_id', $this->rootCanal->id)->first();
        $this->assertEquals('16', $rootCanalItem->tooth_numbers);
        $this->assertEquals(250.00, $rootCanalItem->amount);

        $resinItem = $invoice->items()->where('service_id', $this->compositeResin->id)->first();
        $this->assertEquals(2, $resinItem->quantity);
        $this->assertEquals(70.00, $resinItem->amount);
    }

    public function test_edit_invoice_loads_items_and_supports_add_update_remove(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'patient_id' => $this->patient->id,
            'invoice_date' => now()->toDateString(),
            'subtotal' => 60.00,
            'total_amount' => 60.00,
            'paid_amount' => 0.00,
            'balance_amount' => 60.00,
            'status' => 'unpaid',
        ]);

        $item1 = $invoice->items()->create([
            'type' => 'service',
            'service_id' => $this->dentalCleaning->id,
            'item_name' => 'Dental Cleaning (Scaling)',
            'operate_date' => now()->toDateString(),
            'quantity' => 1,
            'unit_price' => 60.00,
            'amount' => 60.00,
        ]);

        // Visit Edit Page
        $editResponse = $this->actingAs($this->admin)->get(route('invoices.edit', $invoice));
        $editResponse->assertStatus(200);
        $editResponse->assertSee($this->dentalCleaning->name);
        $editResponse->assertSee('Create Dental Charts');
        $editResponse->assertSee(route('dental-charts.create', ['patient_id' => $invoice->patient_id, 'invoice_id' => $invoice->id]));
        $editResponse->assertDontSee('+ Interactive Odontogram');
        $editResponse->assertDontSee('Create Interactive Odontogram Chart');

        // Update: Keep item1 with price 50, remove nothing, add Product Floss
        $updatePayload = [
            'patient_id' => $this->patient->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'discount' => '0.00',
            'tax' => '0.00',
            'items' => [
                [
                    'id' => $item1->id,
                    'type' => 'service',
                    'service_id' => $this->dentalCleaning->id,
                    'operate_date' => now()->toDateString(),
                    'tooth_numbers' => 'All',
                    'quantity' => 1,
                    'unit_price' => 50.00, // Price updated
                ],
                [
                    'type' => 'product',
                    'service_id' => $this->dentalFloss->id,
                    'operate_date' => now()->toDateString(),
                    'quantity' => 2,
                    'unit_price' => 5.00,
                ],
            ],
        ];

        $putResponse = $this->actingAs($this->admin)->put(route('invoices.update', $invoice), $updatePayload);
        $putResponse->assertRedirect(route('invoices.show', $invoice));

        $invoice->refresh();
        $this->assertEquals(60.00, $invoice->subtotal); // 50 + (2*5) = 60
        $this->assertEquals(60.00, $invoice->total_amount);
        $this->assertCount(2, $invoice->items);

        $item1->refresh();
        $this->assertEquals(50.00, $item1->amount);
    }

    public function test_server_side_calculation_ignores_tampered_totals(): void
    {
        $payload = [
            'patient_id' => $this->patient->id,
            'invoice_date' => now()->toDateString(),
            'total_amount' => '1.00', // Client tried to submit $1.00 total
            'subtotal' => '1.00',
            'discount' => '0.00',
            'tax' => '0.00',
            'items' => [
                [
                    'type' => 'service',
                    'service_id' => $this->rootCanal->id,
                    'operate_date' => now()->toDateString(),
                    'quantity' => 2,
                    'unit_price' => 250.00, // Server must calculate 2 * 250 = 500
                ],
            ],
        ];

        $this->actingAs($this->admin)->post(route('invoices.store'), $payload);

        $invoice = Invoice::where('patient_id', $this->patient->id)->latest()->first();
        $this->assertEquals(500.00, $invoice->subtotal);
        $this->assertEquals(500.00, $invoice->total_amount);
    }

    public function test_create_dental_chart_from_edit_invoice_supports_roundtrip_redirect(): void
    {
        $invoice = Invoice::create([
            'patient_id' => $this->patient->id,
            'invoice_number' => 'INV-TEST-FLOW',
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'total_amount' => 100.00,
            'subtotal' => 100.00,
        ]);

        // 1. User is on Edit Invoice and clicks Create Dental Charts
        $editResp = $this->actingAs($this->admin)->get(route('invoices.edit', $invoice));
        $editResp->assertSee(route('dental-charts.create', ['patient_id' => $invoice->patient_id, 'invoice_id' => $invoice->id]));

        // 2. Navigates to Create Dental Chart
        $createChartResp = $this->actingAs($this->admin)->get(route('dental-charts.create', [
            'patient_id' => $invoice->patient_id,
            'invoice_id' => $invoice->id,
        ]));
        $createChartResp->assertStatus(200);
        $createChartResp->assertSee(route('invoices.edit', $invoice->id));
        $createChartResp->assertSee('Back to Invoice');
        $createChartResp->assertSee('Locked for Invoice');
        $createChartResp->assertSee($this->patient->full_name);
        $createChartResp->assertDontSee('<select id="patient_id"', false);
        $createChartResp->assertDontSee('<select name="patient_id"', false);

        // 3. User submits "Create Chart & Open Editor"
        $storeResp = $this->actingAs($this->admin)->post(route('dental-charts.store'), [
            'patient_id' => $this->patient->id,
            'chart_date' => now()->toDateString(),
            'dentition_type' => 'permanent',
            'invoice_id' => $invoice->id,
        ]);

        $createdChart = \Modules\DentalChart\Models\DentalChart::where('patient_id', $this->patient->id)->latest()->first();
        $this->assertNotNull($createdChart);
        $storeResp->assertRedirect(route('dental.show', ['dentalChart' => $createdChart, 'invoice_id' => $invoice->id]));

        // 4. User lands on dental chart editor
        $editorResp = $this->actingAs($this->admin)->get(route('dental.show', [
            'dentalChart' => $createdChart,
            'invoice_id' => $invoice->id,
        ]));
        $editorResp->assertStatus(200);
        $editorResp->assertSee(route('invoices.edit', $invoice->id));
        $editorResp->assertSee('Save & Return to Invoice');

        // 5. When returning to Edit Invoice now that chart exists, button changes to "Edit Dental Chart"
        $editAfterChartResp = $this->actingAs($this->admin)->get(route('invoices.edit', $invoice));
        $editAfterChartResp->assertStatus(200);
        $editAfterChartResp->assertSee('Edit Dental Chart');
        $editAfterChartResp->assertSee(route('dental.show', ['dentalChart' => $createdChart, 'invoice_id' => $invoice->id]));
        $editAfterChartResp->assertDontSee('<span id="btnDentalChartText">Create Dental Charts</span>', false);
        $editAfterChartResp->assertSee('<span id="btnDentalChartText">Edit Dental Chart</span>', false);
    }

    public function test_edit_invoice_autosaves_unsaved_items_when_clicking_dental_chart_button(): void
    {
        $invoice = Invoice::create([
            'patient_id' => $this->patient->id,
            'invoice_number' => 'INV-TEST-AUTOSAVE',
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'total_amount' => 0.00,
            'subtotal' => 0.00,
        ]);

        // Form submits with newly added service and product when clicking dental chart button
        $updateResp = $this->actingAs($this->admin)->put(route('invoices.update', $invoice), [
            'patient_id' => $this->patient->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'redirect_to_dental_chart' => '1',
            'items' => [
                [
                    'type' => 'service',
                    'service_id' => $this->dentalCleaning->id,
                    'operate_date' => now()->toDateString(),
                    'quantity' => 1,
                    'unit_price' => 60.00,
                    'tooth_numbers' => '16, 17',
                    'note' => 'Cleaning before examination',
                ],
                [
                    'type' => 'product',
                    'service_id' => $this->dentalFloss->id,
                    'operate_date' => now()->toDateString(),
                    'quantity' => 2,
                    'unit_price' => 5.00,
                    'note' => 'Patient home care kit',
                ],
            ],
        ]);

        // Invoice should be saved and redirected to create dental chart
        $invoice->refresh();
        $this->assertEquals(70.00, $invoice->subtotal);
        $this->assertEquals(70.00, $invoice->total_amount);
        $this->assertEquals(2, $invoice->items()->count());

        $updateResp->assertRedirect(route('dental-charts.create', [
            'patient_id' => $this->patient->id,
            'invoice_id' => $invoice->id,
        ]));

        // Re-visiting edit invoice loads both items so nothing is reset
        $reloadResp = $this->actingAs($this->admin)->get(route('invoices.edit', $invoice));
        $reloadResp->assertStatus(200);
        $reloadResp->assertSee('Dental Cleaning (Scaling)');
        $reloadResp->assertSee('Dental Floss Waxed');
        $reloadResp->assertSee('16, 17');
    }

    public function test_invoice_items_store_custom_operate_date_and_format_for_html_inputs(): void
    {
        $customDate = '2026-05-15';

        $response = $this->actingAs($this->admin)->post(route('invoices.store'), [
            'patient_id' => $this->patient->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'items' => [
                [
                    'type' => 'service',
                    'service_id' => $this->dentalCleaning->id,
                    'operate_date' => $customDate,
                    'quantity' => 1,
                    'unit_price' => 60.00,
                ],
            ],
        ]);

        $invoice = Invoice::latest('id')->first();
        $this->assertNotNull($invoice);
        $response->assertRedirect(route('invoices.show', $invoice));

        $item = $invoice->items()->first();
        $this->assertEquals($customDate, $item->operate_date->format('Y-m-d'));

        // Verify JSON serialization outputs Y-m-d format so HTML5 date input accepts it
        $editResp = $this->actingAs($this->admin)->get(route('invoices.edit', $invoice));
        $editResp->assertStatus(200);
        $editResp->assertSee('"operate_date":"2026-05-15"', false);
        $editResp->assertDontSee('2026-05-15T', false);

        // Update item with another custom date
        $updatedDate = '2026-07-22';
        $updateResp = $this->actingAs($this->admin)->put(route('invoices.update', $invoice), [
            'patient_id' => $this->patient->id,
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'items' => [
                [
                    'id' => $item->id,
                    'type' => 'service',
                    'service_id' => $this->dentalCleaning->id,
                    'operate_date' => $updatedDate,
                    'quantity' => 1,
                    'unit_price' => 60.00,
                ],
            ],
        ]);
        $updateResp->assertRedirect(route('invoices.show', $invoice));

        $item->refresh();
        $this->assertEquals($updatedDate, $item->operate_date->format('Y-m-d'));

        $reloadEdit = $this->actingAs($this->admin)->get(route('invoices.edit', $invoice));
        $reloadEdit->assertSee('"operate_date":"2026-07-22"', false);
    }
}

