<?php

namespace Tests\Feature\Tenancy;

use App\Models\ImportLc;
use App\Models\Organization;
use App\Models\TenantUser;
use App\Models\User;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    public function test_tenant_user_cannot_see_other_organization_import_lcs(): void
    {
        $orgA = Organization::factory()->create(['name' => 'Org A']);
        $orgB = Organization::factory()->create(['name' => 'Org B']);

        $lcA = ImportLc::withoutGlobalScopes()->create([
            'organization_id' => $orgA->id,
            'lc_number' => 'ISO-A-001',
            'applicant_name' => 'A Applicant',
            'beneficiary_name' => 'A Beneficiary',
            'lc_amount' => 10_000,
            'expiry_date' => now()->addMonth(),
        ]);

        $lcB = ImportLc::withoutGlobalScopes()->create([
            'organization_id' => $orgB->id,
            'lc_number' => 'ISO-B-001',
            'applicant_name' => 'B Applicant',
            'beneficiary_name' => 'B Beneficiary',
            'lc_amount' => 20_000,
            'expiry_date' => now()->addMonth(),
        ]);

        $user = User::factory()->create([
            'current_organization_id' => $orgA->id,
            'is_platform_admin' => false,
        ]);

        TenantUser::create([
            'organization_id' => $orgA->id,
            'user_id' => $user->id,
            'role_slug' => 'import_manager',
            'is_owner' => false,
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $visibleIds = ImportLc::query()->pluck('id')->all();

        $this->assertContains($lcA->id, $visibleIds);
        $this->assertNotContains($lcB->id, $visibleIds);
        $this->assertNull(ImportLc::query()->find($lcB->id));

        $this->assertTrue($user->canAccessTenant($orgA));
        $this->assertFalse($user->canAccessTenant($orgB));
    }
}
