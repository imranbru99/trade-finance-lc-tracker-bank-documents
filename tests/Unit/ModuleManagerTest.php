<?php

namespace Tests\Unit;

use App\Models\ImportLc;
use App\Models\Module;
use App\Models\Organization;
use App\Services\ModuleManager;
use Exception;
use Tests\TestCase;

class ModuleManagerTest extends TestCase
{
    public function test_sync_catalog_creates_modules(): void
    {
        $manager = new ModuleManager;
        $manager->syncCatalog();

        $this->assertSame(
            count(ModuleManager::MODULE_CATALOG),
            Module::query()->count()
        );
    }

    public function test_enable_respects_dependencies(): void
    {
        $manager = new ModuleManager;
        $manager->syncCatalog();

        $organization = Organization::factory()->create();

        $manager->disable('lc_amendments', $organization);
        $manager->disable('import_lc', $organization);

        $this->expectException(Exception::class);
        $manager->enable('lc_amendments', $organization);
    }

    public function test_disable_preserves_existing_data(): void
    {
        $manager = new ModuleManager;
        $manager->syncCatalog();

        $organization = Organization::factory()->create();

        $lc = ImportLc::withoutGlobalScopes()->create([
            'organization_id' => $organization->id,
            'lc_number' => 'TEST-LC-001',
            'applicant_name' => 'Applicant Co',
            'beneficiary_name' => 'Beneficiary Co',
            'lc_amount' => 100_000,
            'expiry_date' => now()->addMonth(),
        ]);

        $manager->disable('lc_amendments', $organization);
        $manager->disable('import_lc', $organization);

        $this->assertDatabaseHas('import_lcs', ['id' => $lc->id]);
        $this->assertFalse($manager->isEnabled('import_lc', $organization));
    }

    public function test_core_modules_cannot_be_disabled(): void
    {
        $manager = new ModuleManager;
        $manager->syncCatalog();

        $organization = Organization::factory()->create();

        $this->expectException(Exception::class);
        $manager->disable('dashboard', $organization);
    }
}
