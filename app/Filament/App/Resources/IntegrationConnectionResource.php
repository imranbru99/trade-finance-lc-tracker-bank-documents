<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\IntegrationConnectionResource\Pages;
use App\Models\IntegrationConnection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IntegrationConnectionResource extends Resource
{
    protected static ?string $model = IntegrationConnection::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Organization & Settings';

    protected static ?string $navigationLabel = 'Integrations';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('config.display_name')
                        ->label('Connection name')
                        ->maxLength(255),
                    Forms\Components\Select::make('service_name')
                        ->label('Provider')
                        ->options([
                            'smtp' => 'SMTP Email',
                            's3' => 'Amazon S3',
                            'r2' => 'Cloudflare R2',
                            'google_calendar' => 'Google Calendar',
                            'outlook' => 'Microsoft Outlook',
                            'erp' => 'ERP / Accounting',
                            'whatsapp' => 'WhatsApp',
                            'sms' => 'SMS Gateway',
                            'bank_api' => 'Bank API',
                        ])
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'connected' => 'Connected',
                            'disconnected' => 'Disconnected',
                            'error' => 'Error',
                            'mock' => 'Mock / Demo',
                        ])
                        ->default('disconnected')
                        ->required(),
                    Forms\Components\DateTimePicker::make('last_synced_at')
                        ->label('Last synced at'),
                    Forms\Components\Textarea::make('last_error')
                        ->label('Last error')
                        ->columnSpanFull(),
                    Forms\Components\KeyValue::make('config.credentials')
                        ->label('Connection settings')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('config.display_name')
                    ->label('Name')
                    ->formatStateUsing(fn ($state, IntegrationConnection $record) => $state ?: $record->service_name)
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_name')
                    ->label('Provider')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'connected',
                        'gray' => 'disconnected',
                        'danger' => 'error',
                        'warning' => 'mock',
                    ]),
                Tables\Columns\TextColumn::make('last_synced_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'connected' => 'Connected',
                        'disconnected' => 'Disconnected',
                        'error' => 'Error',
                        'mock' => 'Mock',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIntegrationConnections::route('/'),
            'create' => Pages\CreateIntegrationConnection::route('/create'),
            'edit' => Pages\EditIntegrationConnection::route('/{record}/edit'),
        ];
    }
}
