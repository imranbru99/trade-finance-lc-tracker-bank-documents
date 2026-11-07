<?php

namespace Tests\Feature\Auth;

use App\Models\ImportLc;
use App\Models\Module;
use App\Models\Organization;
use App\Models\User;
use App\Services\ModuleManager;
use Database\Seeders\DemoSeeder;
use Database\Seeders\DocumentCategorySeeder;
use Database\Seeders\ModuleSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Tests\TestCase;

class DemoEnvironmentTest extends TestCase
{
    public function test_full_demo_seed_produces_expected_catalog_and_demo_records(): void
    {
        $this->seed([
            ModuleSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            DocumentCategorySeeder::class,
            SubscriptionPlanSeeder::class,
            DemoSeeder::class,
        ]);

        $this->assertSame(count(ModuleManager::MODULE_CATALOG), Module::count());
        $this->assertGreaterThanOrEqual(10, User::where('email', 'like', '%@tradefinance.demo')->count());
        $this->assertGreaterThanOrEqual(1, Organization::count());
        $this->assertGreaterThan(0, ImportLc::withoutGlobalScopes()->count());
    }
}
