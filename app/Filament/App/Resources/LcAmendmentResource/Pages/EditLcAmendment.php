<?php

namespace App\Filament\App\Resources\LcAmendmentResource\Pages;

use App\Filament\App\Resources\LcAmendmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLcAmendment extends EditRecord
{
    protected static string $resource = LcAmendmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
