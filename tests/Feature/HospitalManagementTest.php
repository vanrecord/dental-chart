<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Hospital\Models\Category;
use Modules\Hospital\Models\Service;
use Modules\Hospital\Repositories\CategoryRepository;
use Modules\Hospital\Repositories\ServiceRepository;
use Modules\User\Database\Seeders\PermissionSeeder;
use Modules\User\Database\Seeders\RoleSeeder;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Tests\TestCase;

class HospitalManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $superAdmin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);

        $adminRole = Role::where('name', 'admin')->first();

        $this->admin = User::factory()->create([
            'username' => 'admin_user',
            'email' => 'admin_hospital@example.com',
            'is_super_addmin' => false,
        ]);
        $this->admin->roles()->attach($adminRole);

        $this->superAdmin = User::factory()->create([
            'username' => 'super_boss',
            'email' => 'superadmin@example.com',
            'is_super_addmin' => true,
        ]);

        $this->staff = User::factory()->create([
            'username' => 'simple_staff',
            'email' => 'staff@example.com',
            'is_super_addmin' => false,
        ]);
    }

    public function test_sidebar_displays_hospital_menu_and_submenus_for_authorized_users(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Hospital');
        $response->assertSee('Service Categories');
        $response->assertSee('Product Categories');
        $response->assertSee('Services');
        $response->assertSee('Products');
        $response->assertSee(route('hospital.service-categories.index'));
        $response->assertSee(route('hospital.product-categories.index'));
        $response->assertSee(route('hospital.services.index'));
        $response->assertSee(route('hospital.products.index'));
    }

    public function test_sidebar_hides_hospital_menu_when_user_has_no_permissions(): void
    {
        $response = $this->actingAs($this->staff)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee(route('hospital.service-categories.index'));
        $response->assertDontSee(route('hospital.product-categories.index'));
        $response->assertDontSee(route('hospital.services.index'));
        $response->assertDontSee(route('hospital.products.index'));
    }

    public function test_super_admin_can_access_all_hospital_pages(): void
    {
        $this->actingAs($this->superAdmin)
            ->get(route('hospital.service-categories.index'))
            ->assertStatus(200);

        $this->actingAs($this->superAdmin)
            ->get(route('hospital.product-categories.index'))
            ->assertStatus(200);

        $this->actingAs($this->superAdmin)
            ->get(route('hospital.services.index'))
            ->assertStatus(200);

        $this->actingAs($this->superAdmin)
            ->get(route('hospital.products.index'))
            ->assertStatus(200);
    }

    public function test_service_category_crud_and_independent_code_generation(): void
    {
        // 1. Create First Service Category
        $response = $this->actingAs($this->admin)->post(route('hospital.service-categories.store'), [
            'name' => 'Dental Consultation',
            'description' => 'General dental visits and checks',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('hospital.service-categories.index'));

        $cat1 = Category::where('name', 'Dental Consultation')->first();
        $this->assertNotNull($cat1);
        $this->assertEquals(Category::SERVICE, $cat1->type);
        $this->assertEquals('1', $cat1->code);
        $this->assertEquals($this->admin->id, $cat1->created_by);

        // 2. Create Second Service Category - Code must be 2
        $this->actingAs($this->admin)->post(route('hospital.service-categories.store'), [
            'name' => 'Laboratory Service',
            'description' => 'Blood and diagnostic lab tests',
            'is_active' => 1,
        ]);

        $cat2 = Category::where('name', 'Laboratory Service')->first();
        $this->assertEquals('2', $cat2->code);

        // 3. Edit Service Category - Code should remain unchanged
        $this->actingAs($this->admin)->put(route('hospital.service-categories.update', $cat1->id), [
            'name' => 'Dental Consultation Updated',
            'description' => 'Updated description',
            'is_active' => 1,
            'code' => '999', // attempt to change code
        ]);

        $cat1->refresh();
        $this->assertEquals('Dental Consultation Updated', $cat1->name);
        $this->assertEquals('1', $cat1->code); // Still 1!

        // 4. Show view
        $this->actingAs($this->admin)->get(route('hospital.service-categories.show', $cat1->id))
            ->assertStatus(200)
            ->assertSee('Dental Consultation Updated');
    }

    public function test_product_category_crud_and_independent_code_sequence(): void
    {
        // Create service category first to prove separate sequence
        Category::create([
            'name' => 'Service Cat A',
            'type' => Category::SERVICE,
            'code' => '1',
        ]);

        // 1. Create First Product Category - Should start at code 1 independently
        $this->actingAs($this->admin)->post(route('hospital.product-categories.store'), [
            'name' => 'Dental Equipment',
            'description' => 'Handpieces, lights, and tools',
            'is_active' => 1,
        ]);

        $prodCat1 = Category::where('name', 'Dental Equipment')->first();
        $this->assertNotNull($prodCat1);
        $this->assertEquals(Category::PRODUCT, $prodCat1->type);
        $this->assertEquals('1', $prodCat1->code);

        // 2. Create Second Product Category - Code must be 2
        $this->actingAs($this->admin)->post(route('hospital.product-categories.store'), [
            'name' => 'Medical Supplies',
            'description' => 'Consumables, gloves, and gauze',
            'is_active' => 1,
        ]);

        $prodCat2 = Category::where('name', 'Medical Supplies')->first();
        $this->assertEquals('2', $prodCat2->code);

        // 3. Product Categories list only displays products
        $response = $this->actingAs($this->admin)->get(route('hospital.product-categories.index'));
        $response->assertSee('Dental Equipment');
        $response->assertSee('Medical Supplies');
        $response->assertDontSee('Service Cat A');
    }

    public function test_service_crud_with_auto_code_and_strict_category_type(): void
    {
        $serviceCat = Category::create([
            'name' => 'Orthodontics',
            'type' => Category::SERVICE,
            'is_active' => true,
        ]);

        $productCat = Category::create([
            'name' => 'Dental Materials',
            'type' => Category::PRODUCT,
            'is_active' => true,
        ]);

        // 1. Attempt cross-type assignment: Assign product category to service -> Must fail validation
        $failResponse = $this->actingAs($this->admin)->post(route('hospital.services.store'), [
            'name' => 'Braces Fitting',
            'category_id' => $productCat->id,
            'price' => 500,
        ]);
        $failResponse->assertSessionHasErrors(['category_id']);

        // 2. Valid Service creation
        $response = $this->actingAs($this->admin)->post(route('hospital.services.store'), [
            'name' => 'Braces Fitting',
            'category_id' => $serviceCat->id,
            'price' => 500.00,
            'cost' => 150.00,
            'discount' => 10.00,
            'commission' => 25.00,
            'commission_type' => 'fixed',
            'status' => 'active',
        ]);
        $response->assertRedirect(route('hospital.services.index'));

        $service1 = Service::where('name', 'Braces Fitting')->first();
        $this->assertNotNull($service1);
        $this->assertEquals(Service::SERVICE, $service1->type);
        $this->assertEquals('1', $service1->code);
        $this->assertEquals($serviceCat->id, $service1->category_id);

        // 3. Create second service -> Code 2
        $this->actingAs($this->admin)->post(route('hospital.services.store'), [
            'name' => 'Teeth Whitening',
            'category_id' => $serviceCat->id,
            'price' => 200.00,
        ]);

        $service2 = Service::where('name', 'Teeth Whitening')->first();
        $this->assertEquals('2', $service2->code);

        // 4. Update service does not change code
        $this->actingAs($this->admin)->put(route('hospital.services.update', $service1->id), [
            'name' => 'Braces Fitting Premium',
            'category_id' => $serviceCat->id,
            'price' => 550.00,
            'code' => '999',
        ]);

        $service1->refresh();
        $this->assertEquals('Braces Fitting Premium', $service1->name);
        $this->assertEquals('1', $service1->code);

        // 5. Deletion of category blocked when service attached
        $deleteCatResponse = $this->actingAs($this->admin)->delete(route('hospital.service-categories.destroy', $serviceCat->id));
        $deleteCatResponse->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $serviceCat->id, 'deleted_at' => null]);
    }

    public function test_product_crud_with_auto_code_and_strict_category_type(): void
    {
        $serviceCat = Category::create([
            'name' => 'Surgical Procedures',
            'type' => Category::SERVICE,
            'is_active' => true,
        ]);

        $productCat = Category::create([
            'name' => 'Dental Instruments',
            'type' => Category::PRODUCT,
            'is_active' => true,
        ]);

        // 1. Attempt cross-type assignment: Assign service category to product -> Must fail validation
        $failResponse = $this->actingAs($this->admin)->post(route('hospital.products.store'), [
            'name' => 'Dental Explorer Probe',
            'category_id' => $serviceCat->id,
            'price' => 25.00,
        ]);
        $failResponse->assertSessionHasErrors(['category_id']);

        // 2. Valid Product creation
        $response = $this->actingAs($this->admin)->post(route('hospital.products.store'), [
            'name' => 'Dental Explorer Probe',
            'category_id' => $productCat->id,
            'price' => 25.00,
            'cost' => 10.00,
            'discount' => 2.00,
            'qty' => 50,
            'supplier_id' => 12,
            'commission' => 1.00,
            'commission_type' => 'fixed',
            'status' => 'active',
        ]);
        $response->assertRedirect(route('hospital.products.index'));

        $product1 = Service::where('name', 'Dental Explorer Probe')->first();
        $this->assertNotNull($product1);
        $this->assertEquals(Service::PRODUCT, $product1->type);
        $this->assertEquals('1', $product1->code);
        $this->assertEquals($productCat->id, $product1->category_id);
        $this->assertEquals(50, $product1->qty);
        $this->assertEquals(12, $product1->supplier_id);

        // 3. Products screen only displays products
        $serviceRecord = Service::create([
            'name' => 'Oral Surgery Procedure',
            'type' => Service::SERVICE,
            'category_id' => $serviceCat->id,
        ]);

        $listResponse = $this->actingAs($this->admin)->get(route('hospital.products.index'));
        $listResponse->assertSee('Dental Explorer Probe');
        $listResponse->assertDontSee('Oral Surgery Procedure');

        // 4. Services screen only displays services
        $serviceListResponse = $this->actingAs($this->admin)->get(route('hospital.services.index'));
        $serviceListResponse->assertSee('Oral Surgery Procedure');
        $serviceListResponse->assertDontSee('Dental Explorer Probe');
    }

    public function test_unauthorized_user_cannot_access_hospital_routes(): void
    {
        $this->actingAs($this->staff)
            ->get(route('hospital.service-categories.index'))
            ->assertStatus(403);

        $this->actingAs($this->staff)
            ->get(route('hospital.product-categories.index'))
            ->assertStatus(403);

        $this->actingAs($this->staff)
            ->get(route('hospital.services.index'))
            ->assertStatus(403);

        $this->actingAs($this->staff)
            ->get(route('hospital.products.index'))
            ->assertStatus(403);

        $this->actingAs($this->staff)
            ->post(route('hospital.services.store'), ['name' => 'Hack Attempt'])
            ->assertStatus(403);
    }

    public function test_repositories_and_model_methods(): void
    {
        $catRepo = app(CategoryRepository::class);
        $servRepo = app(ServiceRepository::class);

        $cat = Category::create(['name' => 'Rep Test Cat', 'type' => Category::SERVICE]);
        $serv = Service::create(['name' => 'Rep Test Serv', 'type' => Service::SERVICE, 'category_id' => $cat->id]);

        $this->assertTrue($cat->isService());
        $this->assertFalse($cat->isProduct());
        $this->assertTrue($serv->isService());
        $this->assertFalse($serv->isProduct());

        $this->assertEquals($cat->id, $serv->category->id);
        $this->assertTrue($cat->services->contains($serv));

        $this->assertEquals('2', $catRepo->generateNextCode(Category::SERVICE));
        $this->assertEquals('1', $catRepo->generateNextCode(Category::PRODUCT));
    }
}

