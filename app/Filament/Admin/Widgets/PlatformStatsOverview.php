<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeSubscriptions = Subscription::query()
            ->withoutGlobalScopes()
            ->whereIn('status', ['active', 'trialing'])
            ->count();

        return [
            Stat::make('Organizations', (string) Organization::query()->count())
                ->description('Registered tenants')
                ->color('primary'),
            Stat::make('Users', (string) User::query()->count())
                ->description('Platform & tenant users')
                ->color('success'),
            Stat::make('Active subscriptions', (string) $activeSubscriptions)
                ->description('Active or trialing plans')
                ->color('warning'),
            Stat::make('Platform admins', (string) User::query()->where('is_platform_admin', true)->count())
                ->description('Super admin accounts')
                ->color('gray'),
        ];
    }
}
