<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Database\Seeders\SuperAdminSeeder;

class seedSuperAdmin extends Seeder
{
    /**
     * Run the database seeds.
     * Alias seeder allowing `php artisan db:seed --class=seedSuperAdmin`.
     */
    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);
    }
}

