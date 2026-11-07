<?php

namespace App\Filament\App\Resources\BankResource\Pages;

use App\Filament\App\Resources\BankResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateBank extends CreateRecord
{
    protected static string $resource = BankResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $tenant = Filament::getTenant();
        if ($tenant && empty($data['organization_id'])) {
            $data['organization_id'] = $tenant->getKey();
        }

        return $data;
    }
}
