<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPagination;
use App\Http\Controllers\Controller;
use App\Models\Discrepancy;
use Illuminate\Http\JsonResponse;

class DiscrepancyController extends Controller
{
    use RespondsWithPagination;

    public function index(): JsonResponse
    {
        $paginator = Discrepancy::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $this->organizationId())
            ->orderByDesc('id')
            ->paginate(min((int) request('per_page', 25), 100));

        $paginator->getCollection()->transform(fn (Discrepancy $d) => [
            'id' => $d->id,
            'reference' => $d->discrepancy_reference,
            'category' => $d->category,
            'description' => $d->description,
            'status' => $d->status,
            'identified_date' => $d->identified_date?->toDateString(),
        ]);

        return $this->paginatedResponse($paginator);
    }
}
