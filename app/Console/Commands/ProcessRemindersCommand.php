<?php

namespace App\Console\Commands;

use App\Services\DeadlineEngine;
use Illuminate\Console\Command;

class ProcessRemindersCommand extends Command
{
    protected $signature = 'trade-finance:process-reminders';

    protected $description = 'Process pending deadline reminders and escalation rules';

    public function handle(DeadlineEngine $deadlineEngine): int
    {
        $processed = $deadlineEngine->processPendingReminders();
        $escalated = $deadlineEngine->checkEscalations();

        $this->info("Processed {$processed} reminder(s); escalated {$escalated} deadline(s).");

        return self::SUCCESS;
    }
}
