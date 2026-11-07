<?php

namespace App\Filament\Admin\Resources\ModuleCatalogResource\Pages;

use App\Filament\Admin\Resources\ModuleCatalogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModuleCatalogs extends ListRecords
{
    protected static string $resource = ModuleCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
