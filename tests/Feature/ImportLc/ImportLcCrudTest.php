<?php

namespace Tests\Feature\ImportLc;

use App\Models\Deadline;
use App\Models\ImportLc;
use App\Models\Organization;
use App\Models\TenantUser;
use App\Models\User;
use App\Services\DeadlineEngine;
use Tests\TestCase;

class ImportLcCrudTest extends TestCase
{
    public function test_tenant_user_can_create_import_lc_and_sync_deadlines(): void
    {
        $organization = Organization::factory()->create();

        $user = User::factory()->create([
            'current_organization_id' => $organization->id,
            'is_platform_admin' => false,
        ]);

        TenantUser::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_slug' => 'import_manager',
            'is_owner' => false,
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $lc = ImportLc::create([
            'lc_number' => 'CRUD-001',
            'applicant_name' => 'Test Applicant',
            'beneficiary_name' => 'Test Beneficiary',
            'lc_amount' => 75_000,
            'expiry_date' => now()->addMonths(2),
            'latest_shipment_date' => now()->addMonth(),
        ]);

        app(DeadlineEngine::class)->syncDeadlinesForImportLc($lc);

        $this->assertSame($organization->id, $lc->organization_id);
        $this->assertGreaterThanOrEqual(2, Deadline::where('trackable_id', $lc->id)->count());
    }
}
