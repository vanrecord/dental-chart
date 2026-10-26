<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\User\Models\Permission;
use Modules\User\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Idempotently seeds default roles and assigns permissions.
     */
    public function run(): void
    {
        // 0. Super Admin Role (Protected System Role)
        Role::findOrCreate(
            'super-admin',
            'Super Administrator',
            'Full unrestricted system access: can see all menus and manage all application resources, settings, users, and roles.'
        );

        // 1. Admin Role (Full Access)
        $admin = Role::findOrCreate(
            'admin',
            'Administrator',
            'Full administrative access to all modules, users, roles, and settings.'
        );
        $admin->syncPermissions(Permission::all());

        // 2. Dentist Role (Clinical Access)
        $dentist = Role::findOrCreate(
            'dentist',
            'Dentist',
            'Clinical practitioner access: patient records, dental charts, odontogram findings, and conditions.'
        );
        $dentistPermissions = Permission::whereIn('name', [
            'patient:patients:view',
            'patient:patients:create',
            'patient:patients:edit',
            'patient:patients:update',
            'patient:patients:export',
            'odontogram:dental_chart:view',
            'odontogram:dental_chart:create',
            'odontogram:dental_chart:edit',
            'odontogram:dental_chart:update',
            'odontogram:dental_chart:export',
            'odontogram:dental_chart:print',
            'conditions:dental_condition:view',
        ])->get();
        $dentist->syncPermissions($dentistPermissions);

        // 3. Dental Assistant Role
        $assistant = Role::findOrCreate(
            'assistant',
            'Dental Assistant',
            'Clinical support: view patients and dental charts.'
        );
        $assistantPermissions = Permission::whereIn('name', [
            'patient:patients:view',
            'odontogram:dental_chart:view',
            'odontogram:dental_chart:print',
            'conditions:dental_condition:view',
        ])->get();
        $assistant->syncPermissions($assistantPermissions);

        // 4. Receptionist Role
        $receptionist = Role::findOrCreate(
            'receptionist',
            'Receptionist / Front Desk',
            'Front desk operations: register and manage patient profiles.'
        );
        $receptionistPermissions = Permission::whereIn('name', [
            'patient:patients:view',
            'patient:patients:create',
            'patient:patients:edit',
            'patient:patients:update',
        ])->get();
        $receptionist->syncPermissions($receptionistPermissions);

        // Assign 'admin' role to initial default user if exists
        $user = User::where('email', 'doctor@example.com')->first();
        if ($user && ! $user->hasRole('admin')) {
            $user->assignRole($admin);
        }
    }
}

