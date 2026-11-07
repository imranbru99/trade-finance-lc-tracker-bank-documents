<?php

namespace App\Filament\App\Resources\LcAmendmentResource\Pages;

use App\Filament\App\Resources\LcAmendmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLcAmendments extends ListRecords
{
    protected static string $resource = LcAmendmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
