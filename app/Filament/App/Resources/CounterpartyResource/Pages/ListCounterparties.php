<?php

namespace App\Filament\App\Resources\CounterpartyResource\Pages;

use App\Filament\App\Resources\CounterpartyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCounterparties extends ListRecords
{
    protected static string $resource = CounterpartyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
