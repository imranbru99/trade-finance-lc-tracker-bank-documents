<?php

use App\Jobs\ProcessDeadlineRemindersJob;
use App\Jobs\SnapshotFinancialExposuresJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new ProcessDeadlineRemindersJob)->hourly();
Schedule::job(new SnapshotFinancialExposuresJob)->dailyAt('01:00');
