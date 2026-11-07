<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPagination;
use App\Http\Controllers\Controller;
use App\Models\Deadline;
use Illuminate\Http\JsonResponse;

class DeadlineController extends Controller
{
    use RespondsWithPagination;

    public function index(): JsonResponse
    {
        $paginator = Deadline::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $this->organizationId())
            ->orderBy('due_date')
            ->paginate(min((int) request('per_page', 25), 100));

        $paginator->getCollection()->transform(fn (Deadline $d) => [
            'id' => $d->id,
            'title' => $d->title,
            'deadline_type' => $d->deadline_type,
            'due_date' => $d->due_date?->toDateString(),
            'status' => $d->status,
            'priority' => $d->priority ?? null,
        ]);

        return $this->paginatedResponse($paginator);
    }
}
