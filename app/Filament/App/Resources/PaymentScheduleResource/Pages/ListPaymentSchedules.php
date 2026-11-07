<?php

namespace App\Filament\App\Resources\PaymentScheduleResource\Pages;

use App\Filament\App\Resources\PaymentScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentSchedules extends ListRecords
{
    protected static string $resource = PaymentScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
