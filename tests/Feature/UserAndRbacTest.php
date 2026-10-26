<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Database\Seeders\UserDatabaseSeeder;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;
use Modules\User\Support\PermissionRegistry;
use Tests\TestCase;

class UserAndRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_permission_naming_convention_format(): void
    {
        $permissions = Permission::all();
        $this->assertNotEmpty($permissions);

        foreach ($permissions as $permission) {
            // Must contain exactly three parts: module:controller:function
            $parts = explode(':', $permission->name);
            $this->assertCount(3, $parts, "Permission [{$permission->name}] must have exactly 3 colon-separated parts.");
            $this->assertEquals($parts[0], $permission->module);
            $this->assertEquals($parts[1], $permission->controller);
            $this->assertEquals($parts[2], $permission->action);

            // Friendly display labels must not equal the raw identifier
            $this->assertNotEquals($permission->name, $permission->action_label);
            $this->assertNotEmpty($permission->action_label);
        }

        // Test specific user prompt examples
        $sample = Permission::where('name', 'users:users:view')->first();
        $this->assertNotNull($sample);
        $this->assertEquals('View', $sample->action_label);
        $this->assertEquals('User Management', $sample->module_label);
        $this->assertEquals('Users', $sample->controller_label);
    }

    public function test_permission_registry_supports_custom_functions(): void
    {
        $this->assertEquals('Approve', PermissionRegistry::actionLabel('approve'));
        $this->assertEquals('Export', PermissionRegistry::actionLabel('export'));
        $this->assertEquals('Print', PermissionRegistry::actionLabel('print'));
        $this->assertEquals('Refund', PermissionRegistry::actionLabel('refund'));
        $this->assertEquals('Cancel', PermissionRegistry::actionLabel('cancel'));
        $this->assertEquals('Custom Action', PermissionRegistry::actionLabel('custom_action'));
    }

    public function test_idempotent_permission_seeder_does_not_create_duplicates(): void
    {
        $initialCount = Permission::count();
        $this->seed(UserDatabaseSeeder::class);
        $this->assertEquals($initialCount, Permission::count());
    }

    public function test_admin_has_all_permissions_and_can_access_user_crud(): void
    {
        $adminUser = User::where('email', 'doctor@example.com')->first();
        $this->assertNotNull($adminUser);
        $this->assertTrue($adminUser->hasRole('admin'));
        $this->assertTrue($adminUser->can('users:users:view'));
        $this->assertTrue($adminUser->can('users:roles:view'));

        $this->actingAs($adminUser);

        // 1. Index
        $response = $this->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('Users');
        $response->assertSee('+ Create User');

        // 2. Create form
        $response = $this->get(route('users.create'));
        $response->assertStatus(200);
        $response->assertSee('Create User');

        // 3. Store new user
        $dentistRole = Role::where('name', 'dentist')->first();
        $postData = [
            'name' => 'Dr. Robert Brown',
            'username' => 'dr_rbrown',
            'email' => 'robert.brown@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'roles' => [$dentistRole->id],
        ];

        $response = $this->post(route('users.store'), $postData);
        $response->assertRedirect(route('users.index'));

        $newUser = User::where('email', 'robert.brown@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('dr_rbrown', $newUser->username);
        $this->assertTrue($newUser->hasRole('dentist'));

        // 4. Show user
        $response = $this->get(route('users.show', $newUser));
        $response->assertStatus(200);
        $response->assertSee('Dr. Robert Brown');
        $response->assertSee('@dr_rbrown');
        $response->assertSee('Dentist');

        // 5. Edit user
        $response = $this->get(route('users.edit', $newUser));
        $response->assertStatus(200);
        $response->assertSee('Edit User: Dr. Robert Brown');
        $response->assertSee('value="dr_rbrown"', false);

        // 6. Update user
        $updateData = [
            'name' => 'Dr. Robert C. Brown',
            'username' => 'dr_rbrown_updated',
            'email' => 'robert.brown@example.com',
            'roles' => [$dentistRole->id],
        ];
        $response = $this->put(route('users.update', $newUser), $updateData);
        $response->assertRedirect(route('users.index'));
        $this->assertEquals('Dr. Robert C. Brown', $newUser->fresh()->name);
        $this->assertEquals('dr_rbrown_updated', $newUser->fresh()->username);

        // 7. Delete user
        $response = $this->delete(route('users.destroy', $newUser));
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
    }

    public function test_user_creation_validates_username_uniqueness_and_format(): void
    {
        $adminUser = User::where('email', 'doctor@example.com')->first();
        $this->actingAs($adminUser);

        // Attempt duplicate username (doctor)
        $response = $this->from(route('users.create'))->post(route('users.store'), [
            'name' => 'Duplicate Doctor',
            'username' => 'doctor',
            'email' => 'unique_email@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('users.create'));
        $response->assertSessionHasErrors('username');

        // Attempt invalid username characters (spaces or special symbols)
        $response = $this->from(route('users.create'))->post(route('users.store'), [
            'name' => 'Invalid Username User',
            'username' => 'invalid username!',
            'email' => 'unique_email2@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('users.create'));
        $response->assertSessionHasErrors('username');
    }

    public function test_roles_crud_and_permission_matrix(): void
    {
        $adminUser = User::where('email', 'doctor@example.com')->first();
        $this->actingAs($adminUser);

        // 1. Roles Index
        $response = $this->get(route('roles.index'));
        $response->assertStatus(200);
        $response->assertSee('Roles &amp; Permissions', false);
        $response->assertSee('Administrator');
        $response->assertSee('+ Create Role');

        // 2. Roles Create Matrix Screen
        $response = $this->get(route('roles.create'));
        $response->assertStatus(200);
        $response->assertSee('Permission Matrix');
        $response->assertSee('User Management');
        // Column headers must be friendly labels, not raw strings
        $response->assertSee('View');
        $response->assertSee('Create');
        $response->assertSee('Edit');
        $response->assertSee('Delete');
        // Checkboxes should contain full identifier internally
        $response->assertSee('value="users:users:view"', false);
        $response->assertSee('value="patient:patients:view"', false);

        // 3. Store Role with specific permissions
        $postData = [
            'name' => 'hygienist',
            'label' => 'Dental Hygienist',
            'description' => 'Focuses on oral hygiene and cleaning exams.',
            'permissions' => [
                'patient:patients:view',
                'odontogram:dental_chart:view',
                'odontogram:dental_chart:print',
            ],
        ];

        $response = $this->post(route('roles.store'), $postData);
        $response->assertRedirect(route('roles.index'));

        $hygienistRole = Role::where('name', 'hygienist')->first();
        $this->assertNotNull($hygienistRole);
        $this->assertTrue($hygienistRole->hasPermission('patient:patients:view'));
        $this->assertTrue($hygienistRole->hasPermission('odontogram:dental_chart:print'));
        $this->assertFalse($hygienistRole->hasPermission('users:users:delete'));

        // 4. Edit Role Matrix Screen
        $response = $this->get(route('roles.edit', $hygienistRole));
        $response->assertStatus(200);
        $response->assertSee('Edit Role: Dental Hygienist');
        $response->assertSee('value="patient:patients:view"', false);

        // 5. Update Role
        $updateData = [
            'name' => 'hygienist',
            'label' => 'Senior Hygienist',
            'description' => 'Updated description',
            'permissions' => [
                'patient:patients:view',
                'patient:patients:create',
                'odontogram:dental_chart:view',
            ],
        ];
        $response = $this->put(route('roles.update', $hygienistRole), $updateData);
        $response->assertRedirect(route('roles.index'));

        $hygienistRole->refresh();
        $this->assertEquals('Senior Hygienist', $hygienistRole->label);
        $this->assertTrue($hygienistRole->hasPermission('patient:patients:create'));
        $this->assertFalse($hygienistRole->hasPermission('odontogram:dental_chart:print'));

        // 6. Delete Role
        $response = $this->delete(route('roles.destroy', $hygienistRole));
        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseMissing('roles', ['id' => $hygienistRole->id]);

        // 7. System admin role cannot be deleted
        $adminRole = Role::where('name', 'admin')->first();
        $response = $this->delete(route('roles.destroy', $adminRole));
        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['id' => $adminRole->id]);
    }

    public function test_backend_permission_enforcement_returns_403_for_unauthorized_user(): void
    {
        // Create user with only view permissions
        $restrictedRole = Role::create([
            'name' => 'view_only',
            'label' => 'View Only User',
        ]);
        $restrictedRole->syncPermissions([
            'users:users:view',
            'users:roles:view',
        ]);

        $restrictedUser = User::create([
            'name' => 'Restricted Viewer',
            'email' => 'viewer@example.com',
            'password' => bcrypt('secret'),
        ]);
        $restrictedUser->assignRole($restrictedRole);

        $this->actingAs($restrictedUser);

        // Can view users list
        $response = $this->get(route('users.index'));
        $response->assertStatus(200);
        // Does NOT see "+ Create User" button due to @can('users:users:create')
        $response->assertDontSee('+ Create User');

        // Direct POST to store user without 'users:users:create' must return 403 Forbidden
        $response = $this->post(route('users.store'), [
            'name' => 'Hacker User',
            'username' => 'hacker',
            'email' => 'hacker@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);
        $response->assertStatus(403);

        // Direct DELETE on role without 'users:roles:delete' must return 403 Forbidden
        $someRole = Role::where('name', 'dentist')->first();
        $response = $this->delete(route('roles.destroy', $someRole));
        $response->assertStatus(403);
    }

    public function test_seed_super_admin_can_see_all_menus_and_manage_all(): void
    {
        // 1. Run seedSuperAdmin seeder
        $this->seed(\Database\Seeders\seedSuperAdmin::class);

        // 2. Verify Super Admin user and role exist
        $superAdmin = User::where('email', 'superadmin@example.com')->first();
        $this->assertNotNull($superAdmin, 'Super Admin user should exist.');
        $this->assertTrue($superAdmin->hasRole('super-admin'));
        $this->assertTrue($superAdmin->isSuperAdmin());

        // 3. Super Admin has unrestricted permissions via Gate
        $this->assertTrue($superAdmin->can('users:users:view'));
        $this->assertTrue($superAdmin->can('users:users:create'));
        $this->assertTrue($superAdmin->can('users:roles:delete'));
        $this->assertTrue($superAdmin->can('patient:patients:delete'));
        $this->assertTrue($superAdmin->can('odontogram:dental_chart:delete'));
        $this->assertTrue($superAdmin->can('custom:nonexistent:permission')); // Universal super admin bypass

        // 4. Super Admin can see all menus across views
        $this->actingAs($superAdmin);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('data-tooltip="Dashboard"', false);
        $response->assertSee('data-tooltip="Patients"', false);
        $response->assertSee('data-tooltip="Dental Charts"', false);
        $response->assertSee('data-tooltip="Interactive Odontogram"', false);
        $response->assertSee('data-tooltip="Conditions & Legend"', false);
        $response->assertSee('data-tooltip="Users"', false);
        $response->assertSee('data-tooltip="Roles & Permissions"', false);
        $response->assertSee('data-tooltip="API Health"', false);
        $response->assertSee('Super Administrator');

        // 5. Super Admin can manage all modules
        $this->get(route('users.index'))->assertStatus(200)->assertSee('+ Create User');
        $this->get(route('roles.index'))->assertStatus(200)->assertSee('+ Create Role');
        $this->get(route('patients.index'))->assertStatus(200);
        $this->get(route('dental-charts.index'))->assertStatus(200);
        $this->get(route('dental-conditions.index'))->assertStatus(200);

        // 6. Super Admin role cannot be deleted
        $superRole = Role::where('name', 'super-admin')->first();
        $response = $this->delete(route('roles.destroy', $superRole));
        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['id' => $superRole->id]);
    }

    public function test_create_and_edit_user_screen_has_is_super_addmin_boolean_input(): void
    {
        $adminUser = User::where('email', 'doctor@example.com')->first();
        $this->actingAs($adminUser);

        // 1. Create user screen displays is_super_addmin boolean input
        $response = $this->get(route('users.create'));
        $response->assertStatus(200);
        $response->assertSee('name="is_super_addmin"', false);
        $response->assertSee('Super Administrator (is_super_addmin)');
        $response->assertSee('type="checkbox"', false);

        // 2. Create user with is_super_addmin set to true (1)
        $postData = [
            'name' => 'Custom Super Admin User',
            'username' => 'custom_super',
            'email' => 'custom_super@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'is_super_addmin' => '1',
            'roles' => [],
        ];
        $response = $this->post(route('users.store'), $postData);
        $response->assertRedirect(route('users.index'));

        $createdUser = User::where('username', 'custom_super')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->is_super_addmin);
        $this->assertTrue($createdUser->isSuperAdmin());

        // 3. Edit user screen displays is_super_addmin checked for this user
        $response = $this->get(route('users.edit', $createdUser));
        $response->assertStatus(200);
        $response->assertSee('name="is_super_addmin"', false);
        $response->assertSee('checked', false);

        // 4. Update user to uncheck is_super_addmin (set to 0)
        $updateData = [
            'name' => 'Custom Super Admin User',
            'username' => 'custom_super',
            'email' => 'custom_super@example.com',
            'is_super_addmin' => '0',
            'roles' => [],
        ];
        $response = $this->put(route('users.update', $createdUser), $updateData);
        $response->assertRedirect(route('users.index'));

        $createdUser->refresh();
        $this->assertFalse($createdUser->is_super_addmin);
        $this->assertFalse($createdUser->isSuperAdmin());
    }

    public function test_user_with_is_super_addmin_true_can_manage_all_menus_and_functions(): void
    {
        // Create user with is_super_addmin = true and ZERO roles assigned
        $standaloneSuper = User::factory()->create([
            'name' => 'Standalone Super User',
            'username' => 'standalone_super',
            'email' => 'standalone_super@example.com',
            'is_super_addmin' => true,
        ]);

        $this->assertTrue($standaloneSuper->is_super_addmin);
        $this->assertTrue($standaloneSuper->isSuperAdmin());
        $this->assertCount(0, $standaloneSuper->roles);

        // 1. Permission checks via Gate allow arbitrary permissions
        $this->assertTrue($standaloneSuper->can('users:users:view'));
        $this->assertTrue($standaloneSuper->can('users:users:create'));
        $this->assertTrue($standaloneSuper->can('users:roles:delete'));
        $this->assertTrue($standaloneSuper->can('patient:patients:delete'));
        $this->assertTrue($standaloneSuper->can('odontogram:dental_chart:delete'));
        $this->assertTrue($standaloneSuper->can('conditions:dental_condition:delete'));
        $this->assertTrue($standaloneSuper->can('any:future:permission'));

        // 2. All permissions collection returned
        $this->assertNotEmpty($standaloneSuper->getAllPermissions());

        // 3. Acting as this user, can see and access ALL menus and screens
        $this->actingAs($standaloneSuper);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('data-tooltip="Dashboard"', false);
        $response->assertSee('data-tooltip="Patients"', false);
        $response->assertSee('data-tooltip="Dental Charts"', false);
        $response->assertSee('data-tooltip="Interactive Odontogram"', false);
        $response->assertSee('data-tooltip="Conditions & Legend"', false);
        $response->assertSee('data-tooltip="Users"', false);
        $response->assertSee('data-tooltip="Roles & Permissions"', false);
        $response->assertSee('data-tooltip="API Health"', false);

        // 4. Can access all management controllers
        $this->get(route('patients.index'))->assertStatus(200);
        $this->get(route('dental-charts.index'))->assertStatus(200);
        $this->get(route('dental-conditions.index'))->assertStatus(200);
        $this->get(route('users.index'))->assertStatus(200)->assertSee('+ Create User');
        $this->get(route('roles.index'))->assertStatus(200)->assertSee('+ Create Role');
    }
}
