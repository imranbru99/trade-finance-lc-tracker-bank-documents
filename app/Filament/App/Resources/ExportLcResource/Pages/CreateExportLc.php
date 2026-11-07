<?php

namespace App\Filament\App\Resources\ExportLcResource\Pages;

use App\Filament\App\Resources\ExportLcResource;
use App\Services\DeadlineEngine;
use Filament\Resources\Pages\CreateRecord;

class CreateExportLc extends CreateRecord
{
    protected static string $resource = ExportLcResource::class;

    protected function afterCreate(): void
    {
        app(DeadlineEngine::class)->syncDeadlinesForExportLc($this->record);
    }
}
