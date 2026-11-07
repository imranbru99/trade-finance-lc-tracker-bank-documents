<?php

namespace App\Filament\App\Pages;

use App\Filament\Concerns\HasModuleGatedPage;
use App\Models\Deadline;
use App\Models\Discrepancy;
use App\Models\ExportLc;
use App\Models\ImportLc;
use App\Services\FinancialExposureService;
use App\Services\TradeFinancePdfService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;

class TradeFinanceReports extends Page
{
    use HasModuleGatedPage;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Intelligence & Reports';

    protected static ?string $navigationLabel = 'Trade Finance Reports';

    protected static ?string $slug = 'trade-finance-reports';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.app.pages.trade-finance-reports';

    /** @var array<string, mixed> */
    public array $stats = [];

    public static function getModuleSlug(): ?string
    {
        return 'reporting';
    }

    public function mount(FinancialExposureService $exposureService): void
    {
        $this->loadStats($exposureService);
    }

    protected function loadStats(FinancialExposureService $exposureService): void
    {
        $tenant = Filament::getTenant();
        if (! $tenant) {
            $this->stats = [];

            return;
        }

        $closedStatuses = ['Settled', 'Expired', 'Cancelled', 'Closed'];

        $activeImport = ImportLc::query()
            ->where('organization_id', $tenant->getKey())
            ->whereNotIn('status', $closedStatuses)
            ->count();

        $activeExport = ExportLc::query()
            ->where('organization_id', $tenant->getKey())
            ->whereNotIn('status', $closedStatuses)
            ->count();

        $expiring30 = Deadline::query()
            ->where('organization_id', $tenant->getKey())
            ->where('status', 'pending')
            ->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays(30)])
            ->count();

        $openDiscrepancies = Discrepancy::query()
            ->where('organization_id', $tenant->getKey())
            ->whereNotIn('status', ['Resolved', 'Closed', 'Rejected'])
            ->count();

        $summary = $exposureService->getSummary($tenant);

        $this->stats = [
            'active_import_lcs' => $activeImport,
            'active_export_lcs' => $activeExport,
            'outstanding_exposure' => $summary['total_import_exposure'] ?? 0,
            'expiring_30_days' => $expiring30,
            'open_discrepancies' => $openDiscrepancies,
            'overdue_payments' => $summary['overdue_payments'] ?? 0,
            'currency_wise_exposure' => $summary['currency_wise_exposure'] ?? [],
            'upcoming_maturities_30d' => $summary['upcoming_maturities_30d'] ?? 0,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadImportLcRegister')
                ->label('Import LC Register (PDF)')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (TradeFinancePdfService $pdf) {
                    $tenant = Filament::getTenant();
                    if (! $tenant) {
                        return;
                    }

                    return $pdf->generateImportLcRegisterPdf($tenant);
                }),
        ];
    }
}
