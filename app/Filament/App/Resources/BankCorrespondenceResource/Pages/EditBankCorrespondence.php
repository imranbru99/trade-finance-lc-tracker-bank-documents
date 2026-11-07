<?php

namespace App\Filament\App\Resources\BankCorrespondenceResource\Pages;

use App\Filament\App\Resources\BankCorrespondenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankCorrespondence extends EditRecord
{
    protected static string $resource = BankCorrespondenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
