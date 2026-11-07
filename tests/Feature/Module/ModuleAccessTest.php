<?php

namespace Tests\Feature\Module;

use App\Models\ImportLc;
use App\Models\Organization;
use App\Services\ModuleManager;
use Tests\TestCase;

class ModuleAccessTest extends TestCase
{
    public function test_disabling_import_lc_module_hides_feature_but_retains_data(): void
    {
        $manager = new ModuleManager;
        $manager->syncCatalog();

        $organization = Organization::factory()->create();

        $lc = ImportLc::withoutGlobalScopes()->create([
            'organization_id' => $organization->id,
            'lc_number' => 'MOD-001',
            'applicant_name' => 'Applicant',
            'beneficiary_name' => 'Beneficiary',
            'lc_amount' => 30_000,
            'expiry_date' => now()->addMonth(),
        ]);

        $manager->disable('lc_amendments', $organization);
        $manager->disable('import_lc', $organization);

        $this->assertFalse($manager->isEnabled('import_lc', $organization));
        $this->assertDatabaseHas('import_lcs', ['id' => $lc->id]);
    }
}
