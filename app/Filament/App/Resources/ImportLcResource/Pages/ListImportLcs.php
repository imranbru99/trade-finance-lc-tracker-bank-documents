<?php

namespace App\Filament\App\Resources\ImportLcResource\Pages;

use App\Filament\App\Resources\ImportLcResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportLcs extends ListRecords
{
    protected static string $resource = ImportLcResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
