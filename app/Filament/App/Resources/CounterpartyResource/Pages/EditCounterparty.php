<?php

namespace App\Filament\App\Resources\CounterpartyResource\Pages;

use App\Filament\App\Resources\CounterpartyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCounterparty extends EditRecord
{
    protected static string $resource = CounterpartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
