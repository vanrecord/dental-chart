<?php

namespace Modules\User\Console;

use Illuminate\Console\Command;
use Modules\User\Database\Seeders\SuperAdminSeeder;

class SeedSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the Super Administrator role and user with full system privileges';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Seeding Super Administrator...');

        $seeder = new SuperAdminSeeder();
        $seeder->run();

        $this->info('Super Administrator seeded successfully.');
        $this->line('Email: ' . env('SUPER_ADMIN_EMAIL', 'superadmin@example.com'));
        $this->line('Password: ' . env('SUPER_ADMIN_PASSWORD', 'password'));

        return Command::SUCCESS;
    }
}
