<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPagination;
use App\Http\Controllers\Controller;
use App\Models\Deadline;
use App\Models\Discrepancy;
use App\Models\Document;
use App\Models\ExportLc;
use App\Models\ImportLc;
use App\Models\PaymentSchedule;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use RespondsWithPagination;

    public function summary(): JsonResponse
    {
        $orgId = $this->organizationId();

        $importQuery = fn () => ImportLc::query()->withoutGlobalScopes()->where('organization_id', $orgId);
        $exportQuery = fn () => ExportLc::query()->withoutGlobalScopes()->where('organization_id', $orgId);

        return response()->json([
            'data' => [
                'import_lcs' => [
                    'total' => $importQuery()->count(),
                    'active' => $importQuery()->whereNotIn('status', ['Closed', 'Cancelled'])->count(),
                    'total_lc_amount' => (float) $importQuery()->sum('lc_amount'),
                    'outstanding_amount' => (float) $importQuery()->sum('outstanding_amount'),
                ],
                'export_lcs' => [
                    'total' => $exportQuery()->count(),
                    'active' => $exportQuery()->whereNotIn('status', ['Closed', 'Cancelled'])->count(),
                    'total_export_value' => (float) $exportQuery()->sum('export_value'),
                ],
                'documents' => Document::query()->withoutGlobalScopes()->where('organization_id', $orgId)->count(),
                'shipments' => Shipment::query()->withoutGlobalScopes()->where('organization_id', $orgId)->count(),
                'open_discrepancies' => Discrepancy::query()
                    ->withoutGlobalScopes()
                    ->where('organization_id', $orgId)
                    ->whereNotIn('status', ['Resolved', 'Waived'])
                    ->count(),
                'upcoming_deadlines' => Deadline::query()
                    ->withoutGlobalScopes()
                    ->where('organization_id', $orgId)
                    ->where('due_date', '>=', now()->toDateString())
                    ->where('due_date', '<=', now()->addDays(30)->toDateString())
                    ->count(),
                'payments_due_30d' => PaymentSchedule::query()
                    ->withoutGlobalScopes()
                    ->where('organization_id', $orgId)
                    ->whereBetween('maturity_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
                    ->sum(DB::raw('COALESCE(amount, 0)')),
            ],
        ]);
    }
}
