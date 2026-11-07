<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPagination;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    use RespondsWithPagination;

    public function index(): JsonResponse
    {
        $paginator = Document::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $this->organizationId())
            ->orderByDesc('id')
            ->paginate(min((int) request('per_page', 25), 100));

        $paginator->getCollection()->transform(fn (Document $doc) => [
            'id' => $doc->id,
            'title' => $doc->title,
            'document_number' => $doc->document_number,
            'verification_status' => $doc->verification_status,
            'mime_type' => $doc->mime_type,
            'file_size' => $doc->file_size,
            'expiry_date' => $doc->expiry_date?->toDateString(),
            'updated_at' => $doc->updated_at?->toIso8601String(),
        ]);

        return $this->paginatedResponse($paginator);
    }
}
