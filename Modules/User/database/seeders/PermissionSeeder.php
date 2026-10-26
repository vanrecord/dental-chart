<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\Permission;
use Modules\User\Support\PermissionRegistry;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Idempotent and preserves existing permissions without duplicates.
     */
    public function run(): void
    {
        $permissions = PermissionRegistry::defaultPermissions();

        foreach ($permissions as $permName) {
            Permission::findOrCreate($permName);
        }
    }
}

