<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    public function test_admin_demo_user_can_authenticate(): void
    {
        User::factory()->create([
            'email' => 'admin@tradefinance.demo',
            'password' => Hash::make('DemoPass!2026'),
            'is_platform_admin' => true,
        ]);

        $this->assertTrue(
            auth()->attempt([
                'email' => 'admin@tradefinance.demo',
                'password' => 'DemoPass!2026',
            ])
        );
    }
}
