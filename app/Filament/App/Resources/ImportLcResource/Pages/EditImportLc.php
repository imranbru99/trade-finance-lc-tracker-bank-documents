<?php

namespace App\Filament\App\Resources\ImportLcResource\Pages;

use App\Filament\App\Resources\ImportLcResource;
use App\Services\DeadlineEngine;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportLc extends EditRecord
{
    protected static string $resource = ImportLcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Re-sync deadlines if dates changed
        app(DeadlineEngine::class)->syncDeadlinesForImportLc($this->record);
    }
}
