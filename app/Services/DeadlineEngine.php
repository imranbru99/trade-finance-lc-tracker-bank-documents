<?php

namespace App\Services;

use App\Models\Deadline;
use App\Models\EscalationRule;
use App\Models\ExportLc;
use App\Models\ImportLc;
use App\Models\NotificationLog;
use App\Models\Reminder;
use Carbon\Carbon;

class DeadlineEngine
{
    /**
     * Standard reminder schedule days before deadline
     */
    public const REMINDER_DAYS = [90, 60, 30, 15, 7, 3, 1, 0, -1];

    /**
     * Synchronize deadlines for an Import LC
     */
    public function syncDeadlinesForImportLc(ImportLc $lc): void
    {
        // 1. LC Expiry Date
        if ($lc->expiry_date) {
            $deadline = Deadline::updateOrCreate(
                [
                    'organization_id' => $lc->organization_id,
                    'trackable_type' => ImportLc::class,
                    'trackable_id' => $lc->id,
                    'deadline_type' => 'lc_expiry',
                ],
                [
                    'branch_id' => $lc->branch_id,
                    'title' => "Import LC Expiry: {$lc->lc_number}",
                    'due_date' => $lc->expiry_date,
                    'responsible_user_id' => $lc->user_id,
                    'priority' => 'critical',
                    'status' => $lc->expiry_date->isPast() ? 'overdue' : 'pending',
                ]
            );
            $this->generateReminders($deadline);
        }

        // 2. Latest Shipment Date
        if ($lc->latest_shipment_date) {
            $deadline = Deadline::updateOrCreate(
                [
                    'organization_id' => $lc->organization_id,
                    'trackable_type' => ImportLc::class,
                    'trackable_id' => $lc->id,
                    'deadline_type' => 'latest_shipment',
                ],
                [
                    'branch_id' => $lc->branch_id,
                    'title' => "Latest Shipment Due: {$lc->lc_number}",
                    'due_date' => $lc->latest_shipment_date,
                    'responsible_user_id' => $lc->user_id,
                    'priority' => 'high',
                    'status' => $lc->latest_shipment_date->isPast() ? 'overdue' : 'pending',
                ]
            );
            $this->generateReminders($deadline);
        }

        // 3. Document Presentation Deadline
        $presentationDays = $lc->presentation_period_days ?: 21;
        $presentationDate = $lc->latest_shipment_date ? $lc->latest_shipment_date->copy()->addDays($presentationDays) : null;
        if ($presentationDate && $presentationDate->gt($lc->expiry_date)) {
            $presentationDate = $lc->expiry_date->copy(); // UCP 600: cannot exceed LC expiry
        }

        if ($presentationDate) {
            $deadline = Deadline::updateOrCreate(
                [
                    'organization_id' => $lc->organization_id,
                    'trackable_type' => ImportLc::class,
                    'trackable_id' => $lc->id,
                    'deadline_type' => 'presentation_deadline',
                ],
                [
                    'branch_id' => $lc->branch_id,
                    'title' => "Document Presentation Deadline: {$lc->lc_number}",
                    'due_date' => $presentationDate,
                    'responsible_user_id' => $lc->user_id,
                    'priority' => 'high',
                    'status' => $presentationDate->isPast() ? 'overdue' : 'pending',
                ]
            );
            $this->generateReminders($deadline);
        }
    }

    /**
     * Synchronize deadlines for an Export LC
     */
    public function syncDeadlinesForExportLc(ExportLc $lc): void
    {
        // 1. Expiry Date
        if ($lc->expiry_date) {
            $deadline = Deadline::updateOrCreate(
                [
                    'organization_id' => $lc->organization_id,
                    'trackable_type' => ExportLc::class,
                    'trackable_id' => $lc->id,
                    'deadline_type' => 'lc_expiry',
                ],
                [
                    'branch_id' => $lc->branch_id,
                    'title' => "Export LC Expiry: {$lc->lc_number}",
                    'due_date' => $lc->expiry_date,
                    'responsible_user_id' => $lc->user_id,
                    'priority' => 'critical',
                    'status' => $lc->expiry_date->isPast() ? 'overdue' : 'pending',
                ]
            );
            $this->generateReminders($deadline);
        }

        // 2. Latest Shipment Date
        if ($lc->latest_shipment_date) {
            $deadline = Deadline::updateOrCreate(
                [
                    'organization_id' => $lc->organization_id,
                    'trackable_type' => ExportLc::class,
                    'trackable_id' => $lc->id,
                    'deadline_type' => 'latest_shipment',
                ],
                [
                    'branch_id' => $lc->branch_id,
                    'title' => "Export Shipment Due: {$lc->lc_number}",
                    'due_date' => $lc->latest_shipment_date,
                    'responsible_user_id' => $lc->user_id,
                    'priority' => 'high',
                    'status' => $lc->latest_shipment_date->isPast() ? 'overdue' : 'pending',
                ]
            );
            $this->generateReminders($deadline);
        }

        // 3. Presentation Deadline
        if ($lc->presentation_deadline) {
            $deadline = Deadline::updateOrCreate(
                [
                    'organization_id' => $lc->organization_id,
                    'trackable_type' => ExportLc::class,
                    'trackable_id' => $lc->id,
                    'deadline_type' => 'presentation_deadline',
                ],
                [
                    'branch_id' => $lc->branch_id,
                    'title' => "Export Documents Dispatch: {$lc->lc_number}",
                    'due_date' => $lc->presentation_deadline,
                    'responsible_user_id' => $lc->user_id,
                    'priority' => 'high',
                    'status' => $lc->presentation_deadline->isPast() ? 'overdue' : 'pending',
                ]
            );
            $this->generateReminders($deadline);
        }
    }

    /**
     * Generate idempotent reminder schedules for a deadline
     */
    public function generateReminders(Deadline $deadline): void
    {
        foreach (self::REMINDER_DAYS as $days) {
            $scheduledDate = $days >= 0
                ? $deadline->due_date->copy()->subDays($days)
                : $deadline->due_date->copy()->addDays(abs($days)); // overdue reminder

            // Default channels: in_app and email
            foreach (['in_app', 'email'] as $channel) {
                Reminder::firstOrCreate(
                    [
                        'deadline_id' => $deadline->id,
                        'days_before' => $days,
                        'channel' => $channel,
                    ],
                    [
                        'scheduled_for' => $scheduledDate,
                        'status' => $scheduledDate->isPast() && $days > 0 ? 'sent' : 'pending',
                    ]
                );
            }
        }
    }

    /**
     * Process all reminders that are due today
     */
    public function processPendingReminders(?Carbon $today = null): int
    {
        $today = $today ?? Carbon::today();
        $processed = 0;

        $reminders = Reminder::with('deadline.responsibleOfficer')
            ->where('status', 'pending')
            ->where('scheduled_for', '<=', $today->toDateString())
            ->get();

        foreach ($reminders as $reminder) {
            $deadline = $reminder->deadline;
            if (! $deadline || $deadline->status === 'completed' || $deadline->status === 'waived') {
                $reminder->update(['status' => 'cancelled']);
                continue;
            }

            // Create notification log
            $daysLabel = $reminder->days_before > 0
                ? "in {$reminder->days_before} days"
                : ($reminder->days_before === 0 ? "TODAY" : "OVERDUE by " . abs($reminder->days_before) . " day(s)");

            $subject = "[Urgent Deadline Alert] {$deadline->title} is due {$daysLabel}";
            $message = "Notice: Deadline '{$deadline->title}' is scheduled for {$deadline->due_date->toFormattedDateString()}. Priority: {$deadline->priority}. Please review actions.";

            NotificationLog::create([
                'organization_id' => $deadline->organization_id,
                'user_id' => $deadline->responsible_user_id,
                'channel' => $reminder->channel,
                'subject' => $subject,
                'message' => $message,
                'metadata' => [
                    'deadline_id' => $deadline->id,
                    'days_before' => $reminder->days_before,
                    'due_date' => $deadline->due_date->toDateString(),
                ],
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $reminder->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $processed++;
        }

        // Update overdue status for pending deadlines
        Deadline::where('status', 'pending')
            ->where('due_date', '<', $today->toDateString())
            ->update(['status' => 'overdue']);

        return $processed;
    }

    /**
     * Check escalation rules and notify senior managers
     */
    public function checkEscalations(): int
    {
        $escalatedCount = 0;
        $rules = EscalationRule::where('is_active', true)->get();

        foreach ($rules as $rule) {
            $overdueDate = Carbon::today()->subDays($rule->days_overdue);

            $deadlines = Deadline::where('organization_id', $rule->organization_id)
                ->where('status', 'overdue')
                ->where('deadline_type', $rule->deadline_type)
                ->where('due_date', '<=', $overdueDate)
                ->get();

            foreach ($deadlines as $deadline) {
                // Log escalation notification
                NotificationLog::create([
                    'organization_id' => $deadline->organization_id,
                    'channel' => 'email',
                    'subject' => "[ESCALATION] Overdue Deadline: {$deadline->title}",
                    'message' => "Escalation: {$deadline->title} has been overdue for more than {$rule->days_overdue} days. Escalated to {$rule->escalate_to_role}.",
                    'metadata' => [
                        'deadline_id' => $deadline->id,
                        'escalated_to' => $rule->escalate_to_role,
                        'days_overdue' => $rule->days_overdue,
                    ],
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $escalatedCount++;
            }
        }

        return $escalatedCount;
    }
}
