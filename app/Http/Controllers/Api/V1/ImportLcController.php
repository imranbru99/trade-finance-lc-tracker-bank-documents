<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\ImportLcResource;
use App\Models\ImportLc;
use Illuminate\Http\JsonResponse;

class ImportLcController extends Controller
{
    use RespondsWithPagination;

    public function index(): JsonResponse
    {
        $paginator = ImportLc::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $this->organizationId())
            ->orderByDesc('id')
            ->paginate(min((int) request('per_page', 25), 100));

        return $this->paginatedResponse($paginator, ImportLcResource::class);
    }

    public function show(int $id): JsonResponse
    {
        $lc = ImportLc::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $this->organizationId())
            ->findOrFail($id);

        return response()->json(['data' => new ImportLcResource($lc)]);
    }
}
