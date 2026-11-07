<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    public function __invoke(Request $request, Document $document): StreamedResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired download link.');
        }

        $document = Document::withoutGlobalScopes()->findOrFail($document->id);

        if (! Storage::disk($document->disk)->exists($document->stored_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk($document->disk)->download(
            $document->stored_path,
            $document->original_filename
        );
    }
}
