<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExportLcResource;
use App\Models\ExportLc;
use Illuminate\Http\JsonResponse;

class ExportLcController extends Controller
{
    use RespondsWithPagination;

    public function index(): JsonResponse
    {
        $paginator = ExportLc::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $this->organizationId())
            ->orderByDesc('id')
            ->paginate(min((int) request('per_page', 25), 100));

        return $this->paginatedResponse($paginator, ExportLcResource::class);
    }
}
