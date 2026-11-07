<?php

namespace Tests\Feature\Filament;

use App\Models\Organization;
use App\Models\TenantUser;
use App\Models\User;
use Filament\Facades\Filament;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    public function test_platform_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->create(['is_platform_admin' => true]);
        $panel = Filament::getPanel('admin');

        $this->assertTrue($admin->canAccessPanel($panel));
    }

    public function test_tenant_user_cannot_access_admin_panel(): void
    {
        $organization = Organization::factory()->create();

        $user = User::factory()->create([
            'is_platform_admin' => false,
            'current_organization_id' => $organization->id,
        ]);

        TenantUser::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_slug' => 'import_manager',
            'is_owner' => false,
            'status' => 'active',
        ]);

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_tenant_user_can_access_app_panel(): void
    {
        $organization = Organization::factory()->create();

        $user = User::factory()->create([
            'is_platform_admin' => false,
            'current_organization_id' => $organization->id,
        ]);

        TenantUser::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_slug' => 'import_manager',
            'is_owner' => false,
            'status' => 'active',
        ]);

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('app')));
    }
}
