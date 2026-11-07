<?php

namespace Database\Seeders;

use App\Services\ModuleManager;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        (new ModuleManager)->syncCatalog();
    }
}
