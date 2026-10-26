<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the Super Administrator role and user with full system privileges.
     */
    public function run(): void
    {
        // 1. Ensure permissions exist
        $this->call(PermissionSeeder::class);

        // 2. Create or update Super Admin Role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super-admin'],
            [
                'label' => 'Super Administrator',
                'description' => 'Full unrestricted system access: can see all menus and manage all application resources, settings, users, and roles.',
            ]
        );

        // Super Admin gets all system permissions directly
        $superAdminRole->syncPermissions(Permission::all());

        // 3. Create or update the Super Admin User
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@example.com');
        $username = env('SUPER_ADMIN_USERNAME', 'superadmin');
        $superAdmin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Super Administrator'),
                'username' => $username,
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'password')),
                'is_super_addmin' => true,
            ]
        );

        $superAdmin->update([
            'username' => $username,
            'is_super_addmin' => true,
        ]);

        // Ensure super-admin role is assigned
        if (! $superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Also assign 'admin' role if exists for compatibility
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && ! $superAdmin->hasRole('admin')) {
            $superAdmin->assignRole($adminRole);
        }
    }
}

