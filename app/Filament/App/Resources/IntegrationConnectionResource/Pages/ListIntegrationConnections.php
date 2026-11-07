<?php

namespace App\Filament\App\Resources\IntegrationConnectionResource\Pages;

use App\Filament\App\Resources\IntegrationConnectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntegrationConnections extends ListRecords
{
    protected static string $resource = IntegrationConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
