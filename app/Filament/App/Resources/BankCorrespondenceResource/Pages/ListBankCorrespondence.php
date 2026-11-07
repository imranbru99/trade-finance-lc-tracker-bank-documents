<?php

namespace App\Filament\App\Resources\BankCorrespondenceResource\Pages;

use App\Filament\App\Resources\BankCorrespondenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBankCorrespondence extends ListRecords
{
    protected static string $resource = BankCorrespondenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
