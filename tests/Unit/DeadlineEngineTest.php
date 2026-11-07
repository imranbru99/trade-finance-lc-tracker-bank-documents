<?php

namespace Tests\Unit;

use App\Models\Deadline;
use App\Models\ImportLc;
use App\Models\Organization;
use App\Models\Reminder;
use App\Services\DeadlineEngine;
use Tests\TestCase;

class DeadlineEngineTest extends TestCase
{
    public function test_sync_deadlines_for_import_lc_creates_expiry_and_latest_shipment(): void
    {
        $organization = Organization::factory()->create();

        $lc = ImportLc::withoutGlobalScopes()->create([
            'organization_id' => $organization->id,
            'lc_number' => 'DL-ENGINE-001',
            'applicant_name' => 'Applicant',
            'beneficiary_name' => 'Beneficiary',
            'lc_amount' => 50_000,
            'expiry_date' => now()->addMonths(2),
            'latest_shipment_date' => now()->addMonth(),
        ]);

        app(DeadlineEngine::class)->syncDeadlinesForImportLc($lc);

        $this->assertDatabaseHas('deadlines', [
            'trackable_type' => ImportLc::class,
            'trackable_id' => $lc->id,
            'deadline_type' => 'lc_expiry',
        ]);

        $this->assertDatabaseHas('deadlines', [
            'trackable_type' => ImportLc::class,
            'trackable_id' => $lc->id,
            'deadline_type' => 'latest_shipment',
        ]);
    }

    public function test_generate_reminders_is_idempotent(): void
    {
        $organization = Organization::factory()->create();

        $lc = ImportLc::withoutGlobalScopes()->create([
            'organization_id' => $organization->id,
            'lc_number' => 'DL-REM-001',
            'applicant_name' => 'Applicant',
            'beneficiary_name' => 'Beneficiary',
            'lc_amount' => 25_000,
            'expiry_date' => now()->addMonths(2),
        ]);

        $engine = app(DeadlineEngine::class);
        $engine->syncDeadlinesForImportLc($lc);

        $deadline = Deadline::withoutGlobalScopes()
            ->where('trackable_id', $lc->id)
            ->where('deadline_type', 'lc_expiry')
            ->firstOrFail();

        $countAfterFirst = Reminder::where('deadline_id', $deadline->id)->count();

        $engine->generateReminders($deadline);
        $countAfterSecond = Reminder::where('deadline_id', $deadline->id)->count();

        $this->assertGreaterThan(0, $countAfterFirst);
        $this->assertSame($countAfterFirst, $countAfterSecond);
    }
}
