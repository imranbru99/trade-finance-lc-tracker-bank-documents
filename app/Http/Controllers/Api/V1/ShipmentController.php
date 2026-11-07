<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPagination;
use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;

class ShipmentController extends Controller
{
    use RespondsWithPagination;

    public function index(): JsonResponse
    {
        $paginator = Shipment::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $this->organizationId())
            ->orderByDesc('id')
            ->paginate(min((int) request('per_page', 25), 100));

        $paginator->getCollection()->transform(fn (Shipment $s) => [
            'id' => $s->id,
            'reference' => $s->shipment_reference,
            'status' => $s->status,
            'vessel_name' => $s->vessel_name ?? null,
            'etd' => $s->etd?->toDateString(),
            'eta' => $s->eta?->toDateString(),
            'updated_at' => $s->updated_at?->toIso8601String(),
        ]);

        return $this->paginatedResponse($paginator);
    }
}
