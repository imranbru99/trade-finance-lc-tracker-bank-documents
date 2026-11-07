<?php

namespace App\Jobs;

use App\Services\DeadlineEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessDeadlineRemindersJob implements ShouldQueue
{
    use Queueable;

    public function handle(DeadlineEngine $deadlineEngine): void
    {
        $deadlineEngine->processPendingReminders();
        $deadlineEngine->checkEscalations();
    }
}
