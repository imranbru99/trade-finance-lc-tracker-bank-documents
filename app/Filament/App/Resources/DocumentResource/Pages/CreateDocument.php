<?php

namespace App\Filament\App\Resources\DocumentResource\Pages;

use App\Filament\App\Resources\DocumentResource;
use App\Models\DocumentVersion;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function afterCreate(): void
    {
        $doc = $this->record;

        DocumentVersion::firstOrCreate(
            ['document_id' => $doc->id, 'version_number' => 1],
            [
                'original_filename' => $doc->original_filename ?: 'document.pdf',
                'stored_path' => $doc->stored_path,
                'file_size' => $doc->file_size ?: 0,
                'change_notes' => 'Initial upload',
                'uploaded_by_user_id' => auth()->id(),
            ]
        );
    }
}
