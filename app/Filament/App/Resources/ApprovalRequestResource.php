<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApprovalRequestResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\ApprovalRequest;
use App\Services\ApprovalEngine;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApprovalRequestResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = ApprovalRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationGroup = 'Workflow & Operations';

    protected static ?string $navigationLabel = 'Approval Requests';

    protected static ?int $navigationSort = 3;

    public static function getModuleSlug(): ?string
    {
        return 'approval_workflows';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Request Details')->schema([
                    Infolists\Components\TextEntry::make('status')->badge(),
                    Infolists\Components\TextEntry::make('workflow.name')
                        ->label('Workflow'),
                    Infolists\Components\TextEntry::make('requestedByUser.name')
                        ->label('Requested By'),
                    Infolists\Components\TextEntry::make('current_step')
                        ->label('Current Step'),
                    Infolists\Components\TextEntry::make('total_steps'),
                    Infolists\Components\TextEntry::make('decision_notes')
                        ->columnSpanFull(),
                    Infolists\Components\TextEntry::make('completed_at')
                        ->dateTime(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approvable_type')
                    ->label('Item Type')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                Tables\Columns\TextColumn::make('workflow.name')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('requestedByUser.name')
                    ->label('Requester'),
                Tables\Columns\TextColumn::make('current_step')
                    ->label('Step')
                    ->formatStateUsing(fn ($record) => "{$record->current_step} / {$record->total_steps}"),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Approved',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                        'Delegated' => 'Delegated',
                        'Cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ApprovalRequest $record) => $record->status === 'Pending')
                    ->form([
                        Forms\Components\Textarea::make('comments')
                            ->label('Approval Comments'),
                    ])
                    ->action(function (ApprovalRequest $record, array $data, ApprovalEngine $engine) {
                        $user = Filament::auth()->user();
                        try {
                            $engine->approve($record, $user, $data['comments'] ?? null);
                            Notification::make()->title('Approval recorded')->success()->send();
                        } catch (Exception $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ApprovalRequest $record) => $record->status === 'Pending')
                    ->form([
                        Forms\Components\Textarea::make('comments')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function (ApprovalRequest $record, array $data, ApprovalEngine $engine) {
                        $user = Filament::auth()->user();
                        try {
                            $engine->reject($record, $user, $data['comments'] ?? null);
                            Notification::make()->title('Request rejected')->warning()->send();
                        } catch (Exception $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalRequests::route('/'),
            'view' => Pages\ViewApprovalRequest::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
