<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\FinancialExposure;
use App\Models\ImportLc;
use App\Models\Organization;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialExposureService
{
    /**
     * Compute comprehensive financial statistics for an organization
     */
    public function getSummary(Organization $org): array
    {
        // 1. Total Active Import LCs
        $activeImportLcs = ImportLc::where('organization_id', $org->id)
            ->whereNotIn('status', ['Settled', 'Expired', 'Cancelled', 'Closed'])
            ->get();

        $totalImportExposureUsd = 0;
        $currencyWiseExposure = [];

        foreach ($activeImportLcs as $lc) {
            $curr = $lc->currency ?: 'USD';
            $amt = (float) $lc->outstanding_amount ?: (float) $lc->lc_amount;

            $currencyWiseExposure[$curr] = ($currencyWiseExposure[$curr] ?? 0) + $amt;
            // Approximate conversion or direct USD
            $totalImportExposureUsd += ($curr === 'USD') ? $amt : ($amt * 1.0);
        }

        // 2. Bank-wise Exposure
        $bankWiseExposure = ImportLc::where('organization_id', $org->id)
            ->whereNotIn('status', ['Settled', 'Expired', 'Cancelled', 'Closed'])
            ->whereNotNull('issuing_bank_id')
            ->select('issuing_bank_id', 'currency', DB::raw('SUM(lc_amount) as total_amount'))
            ->groupBy('issuing_bank_id', 'currency')
            ->with('issuingBank')
            ->get()
            ->map(function ($row) {
                return [
                    'bank_name' => $row->issuingBank?->name ?? 'Unknown Bank',
                    'currency' => $row->currency,
                    'amount' => (float) $row->total_amount,
                ];
            });

        // 3. Upcoming Maturities in next 30 days
        $upcomingMaturities = PaymentSchedule::where('organization_id', $org->id)
            ->whereIn('status', ['Scheduled', 'Due', 'Partially Paid'])
            ->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays(30)])
            ->sum('outstanding_amount');

        // 4. Overdue Payments
        $overduePayments = PaymentSchedule::where('organization_id', $org->id)
            ->whereIn('status', ['Scheduled', 'Due', 'Partially Paid', 'Overdue'])
            ->where('due_date', '<', Carbon::today())
            ->sum('outstanding_amount');

        return [
            'total_active_lcs' => $activeImportLcs->count(),
            'total_import_exposure' => $totalImportExposureUsd,
            'currency_wise_exposure' => $currencyWiseExposure,
            'bank_wise_exposure' => $bankWiseExposure,
            'upcoming_maturities_30d' => (float) $upcomingMaturities,
            'overdue_payments' => (float) $overduePayments,
        ];
    }

    /**
     * Store daily exposure snapshot per bank
     */
    public function recordDailySnapshot(Organization $org): void
    {
        $banks = Bank::where(function ($q) use ($org) {
            $q->where('organization_id', $org->id)->orWhere('is_global', true);
        })->get();

        foreach ($banks as $bank) {
            $exposure = ImportLc::where('organization_id', $org->id)
                ->where('issuing_bank_id', $bank->id)
                ->whereNotIn('status', ['Settled', 'Expired', 'Cancelled', 'Closed'])
                ->sum('lc_amount');

            if ($exposure > 0) {
                FinancialExposure::updateOrCreate(
                    [
                        'organization_id' => $org->id,
                        'bank_id' => $bank->id,
                        'currency' => 'USD',
                        'snapshot_date' => Carbon::today(),
                    ],
                    [
                        'total_lc_exposure' => $exposure,
                        'utilized_exposure' => $exposure,
                        'available_limit' => max(0, 5000000.00 - $exposure),
                    ]
                );
            }
        }
    }
}
