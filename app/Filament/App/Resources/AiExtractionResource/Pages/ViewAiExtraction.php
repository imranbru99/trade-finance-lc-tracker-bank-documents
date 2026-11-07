<?php

namespace App\Filament\App\Resources\AiExtractionResource\Pages;

use App\Filament\App\Resources\AiExtractionResource;
use App\Models\AiExtraction;
use App\Services\AiTradeFinanceService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAiExtraction extends ViewRecord
{
    protected static string $resource = AiExtractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (AiExtraction $record) => $record->human_review_status === 'Pending')
                ->form([
                    \Filament\Forms\Components\Textarea::make('review_notes')
                        ->label('Review notes'),
                ])
                ->action(function (array $data, AiTradeFinanceService $ai) {
                    $record = $this->record;
                    $ai->reviewExtraction(
                        $record,
                        Filament::auth()->user(),
                        'Accepted',
                        $record->extracted_data ?? [],
                        $data['review_notes'] ?? null
                    );
                    $this->record->refresh();
                    Notification::make()->title('Extraction approved')->success()->send();
                }),
            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (AiExtraction $record) => $record->human_review_status === 'Pending')
                ->form([
                    \Filament\Forms\Components\Textarea::make('review_notes')
                        ->label('Rejection reason')
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data, AiTradeFinanceService $ai) {
                    $record = $this->record;
                    $ai->reviewExtraction(
                        $record,
                        Filament::auth()->user(),
                        'Rejected',
                        $record->extracted_data ?? [],
                        $data['review_notes'] ?? null
                    );
                    $this->record->refresh();
                    Notification::make()->title('Extraction rejected')->warning()->send();
                }),
        ];
    }
}
