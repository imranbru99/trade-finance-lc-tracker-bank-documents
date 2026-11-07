<?php

namespace App\Filament\App\Resources\ExportLcResource\Pages;

use App\Filament\App\Resources\ExportLcResource;
use App\Services\DeadlineEngine;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExportLc extends EditRecord
{
    protected static string $resource = ExportLcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        app(DeadlineEngine::class)->syncDeadlinesForExportLc($this->record);
    }
}
