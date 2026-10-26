<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\DentalCondition\Database\Seeders\DentalConditionDatabaseSeeder;

class DentalConditionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DentalConditionDatabaseSeeder::class);
    }
}
