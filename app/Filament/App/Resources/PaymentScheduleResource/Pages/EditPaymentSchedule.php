<?php

namespace App\Filament\App\Resources\PaymentScheduleResource\Pages;

use App\Filament\App\Resources\PaymentScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentSchedule extends EditRecord
{
    protected static string $resource = PaymentScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
