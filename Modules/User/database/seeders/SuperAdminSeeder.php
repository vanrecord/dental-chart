<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the Super Administrator user with full system privileges (is_super_addmin).
     */
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@example.com');
        $username = env('SUPER_ADMIN_USERNAME', 'superadmin');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Super Administrator'),
                'username' => $username,
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'password')),
                'is_super_addmin' => true,
            ]
        );
    }
}
