<?php

namespace App\Jobs;

use App\Models\Organization;
use App\Services\FinancialExposureService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SnapshotFinancialExposuresJob implements ShouldQueue
{
    use Queueable;

    public function handle(FinancialExposureService $financialExposureService): void
    {
        if (! method_exists($financialExposureService, 'recordDailySnapshot')) {
            return;
        }

        Organization::query()
            ->where('status', 'active')
            ->each(fn (Organization $org) => $financialExposureService->recordDailySnapshot($org));
    }
}
