<?php

namespace App\Console\Commands;

use Database\Seeders\DemoSeeder;
use Illuminate\Console\Command;

class DemoResetCommand extends Command
{
    protected $signature = 'trade-finance:demo-reset';

    protected $description = 'Re-seed the trade finance demo tenant data';

    public function handle(): int
    {
        $this->info('Resetting demo data...');
        $this->call(DemoSeeder::class);
        $this->info('Demo data seeded successfully.');

        return self::SUCCESS;
    }
}
