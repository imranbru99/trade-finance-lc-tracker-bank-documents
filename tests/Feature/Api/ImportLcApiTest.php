<?php

namespace Tests\Feature\Api;

use App\Models\ImportLc;
use App\Models\Organization;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImportLcApiTest extends TestCase
{
    public function test_import_lc_list_requires_authentication(): void
    {
        $this->getJson('/api/v1/import-lcs')
            ->assertUnauthorized();
    }

    public function test_import_lc_list_returns_data_with_valid_token_and_organization_header(): void
    {
        $organization = Organization::factory()->create();
        $plainToken = Str::random(40);

        $user = User::factory()->create([
            'api_token' => hash('sha256', $plainToken),
            'current_organization_id' => $organization->id,
        ]);

        TenantUser::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_slug' => 'import_manager',
            'is_owner' => false,
            'status' => 'active',
        ]);

        ImportLc::withoutGlobalScopes()->create([
            'organization_id' => $organization->id,
            'lc_number' => 'API-001',
            'applicant_name' => 'Applicant',
            'beneficiary_name' => 'Beneficiary',
            'lc_amount' => 40_000,
            'expiry_date' => now()->addMonth(),
        ]);

        $response = $this->withToken($plainToken)
            ->withHeader('X-Organization-Id', (string) $organization->id)
            ->getJson('/api/v1/import-lcs');

        $response->assertOk();
        $response->assertJsonStructure(['data']);
    }
}
