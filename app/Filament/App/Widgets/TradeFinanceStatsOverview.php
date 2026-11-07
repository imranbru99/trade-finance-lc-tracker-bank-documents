<?php

namespace App\Filament\App\Widgets;

use App\Models\Deadline;
use App\Models\Discrepancy;
use App\Models\ExportLc;
use App\Models\ImportLc;
use App\Models\PaymentSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TradeFinanceStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $importCount = ImportLc::count();
        $exportCount = ExportLc::count();

        $expiringDeadlines = Deadline::query()
            ->where('status', 'pending')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();

        $openDiscrepancies = Discrepancy::query()
            ->whereNotIn('status', ['Resolved', 'Closed', 'Rejected'])
            ->count();

        $upcomingPayments = PaymentSchedule::query()
            ->whereNotIn('status', ['Settled', 'Cancelled'])
            ->where('due_date', '>=', now()->toDateString())
            ->where('due_date', '<=', now()->addDays(30)->toDateString())
            ->count();

        return [
            Stat::make('Import LCs', (string) $importCount)
                ->description('Active import portfolio')
                ->icon('heroicon-o-arrow-down-tray'),
            Stat::make('Export LCs', (string) $exportCount)
                ->description('Active export portfolio')
                ->icon('heroicon-o-arrow-up-tray'),
            Stat::make('Expiring Deadlines', (string) $expiringDeadlines)
                ->description('Next 30 days')
                ->color('warning')
                ->icon('heroicon-o-bell-alert'),
            Stat::make('Open Discrepancies', (string) $openDiscrepancies)
                ->description('Awaiting resolution')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
            Stat::make('Upcoming Payments', (string) $upcomingPayments)
                ->description('Due within 30 days')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
