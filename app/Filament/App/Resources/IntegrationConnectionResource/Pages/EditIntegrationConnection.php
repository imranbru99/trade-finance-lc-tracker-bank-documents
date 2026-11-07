<?php

namespace App\Filament\App\Resources\IntegrationConnectionResource\Pages;

use App\Filament\App\Resources\IntegrationConnectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntegrationConnection extends EditRecord
{
    protected static string $resource = IntegrationConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
