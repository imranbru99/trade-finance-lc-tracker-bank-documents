<?php

namespace App\Filament\App\Resources\ExportLcResource\Pages;

use App\Filament\App\Resources\ExportLcResource;
use App\Services\DeadlineEngine;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExportLcs extends ListRecords
{
    protected static string $resource = ExportLcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
