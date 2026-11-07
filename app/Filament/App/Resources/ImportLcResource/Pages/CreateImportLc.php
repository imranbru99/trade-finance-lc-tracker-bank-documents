<?php

namespace App\Filament\App\Resources\ImportLcResource\Pages;

use App\Filament\App\Resources\ImportLcResource;
use App\Services\DeadlineEngine;
use App\Services\DocumentManager;
use Filament\Resources\Pages\CreateRecord;

class CreateImportLc extends CreateRecord
{
    protected static string $resource = ImportLcResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Auto sync deadlines
        app(DeadlineEngine::class)->syncDeadlinesForImportLc($record);

        // Auto generate initial document checklist
        app(DocumentManager::class)->generateChecklistForLc($record);
    }
}
