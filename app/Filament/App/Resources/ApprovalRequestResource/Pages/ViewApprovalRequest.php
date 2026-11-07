<?php

namespace App\Filament\App\Resources\ApprovalRequestResource\Pages;

use App\Filament\App\Resources\ApprovalRequestResource;
use App\Models\ApprovalRequest;
use App\Services\ApprovalEngine;
use Exception;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewApprovalRequest extends ViewRecord
{
    protected static string $resource = ApprovalRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (ApprovalRequest $record) => $record->status === 'Pending')
                ->form([
                    \Filament\Forms\Components\Textarea::make('comments')
                        ->label('Approval Comments'),
                ])
                ->action(function (array $data, ApprovalEngine $engine) {
                    try {
                        $engine->approve($this->record, Filament::auth()->user(), $data['comments'] ?? null);
                        Notification::make()->title('Approval recorded')->success()->send();
                    } catch (Exception $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
            Actions\Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (ApprovalRequest $record) => $record->status === 'Pending')
                ->form([
                    \Filament\Forms\Components\Textarea::make('comments')
                        ->label('Rejection Reason')
                        ->required(),
                ])
                ->action(function (array $data, ApprovalEngine $engine) {
                    try {
                        $engine->reject($this->record, Filament::auth()->user(), $data['comments'] ?? null);
                        Notification::make()->title('Request rejected')->warning()->send();
                    } catch (Exception $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}
