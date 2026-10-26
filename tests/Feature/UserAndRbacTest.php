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
            'email' => 'robert.brown@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'roles' => [$dentistRole->id],
        ];

        $response = $this->post(route('users.store'), $postData);
        $response->assertRedirect(route('users.index'));

        $newUser = User::where('email', 'robert.brown@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->hasRole('dentist'));

        // 4. Show user
        $response = $this->get(route('users.show', $newUser));
        $response->assertStatus(200);
        $response->assertSee('Dr. Robert Brown');
        $response->assertSee('Dentist');

        // 5. Edit user
        $response = $this->get(route('users.edit', $newUser));
        $response->assertStatus(200);
        $response->assertSee('Edit User: Dr. Robert Brown');

        // 6. Update user
        $updateData = [
            'name' => 'Dr. Robert C. Brown',
            'email' => 'robert.brown@example.com',
            'roles' => [$dentistRole->id],
        ];
        $response = $this->put(route('users.update', $newUser), $updateData);
        $response->assertRedirect(route('users.index'));
        $this->assertEquals('Dr. Robert C. Brown', $newUser->fresh()->name);

        // 7. Delete user
        $response = $this->delete(route('users.destroy', $newUser));
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $newUser->id]);
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
}
