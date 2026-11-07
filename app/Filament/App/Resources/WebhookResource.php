<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WebhookResource\Pages;
use App\Models\Webhook;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Organization & Settings';

    protected static ?string $navigationLabel = 'Webhooks';

    protected static ?int $navigationSort = 21;

    public const EVENT_OPTIONS = [
        'lc.created' => 'LC created',
        'lc.updated' => 'LC updated',
        'lc.expiring' => 'LC expiring',
        'document.uploaded' => 'Document uploaded',
        'shipment.updated' => 'Shipment updated',
        'payment.due' => 'Payment due',
        'discrepancy.opened' => 'Discrepancy opened',
        '*' => 'All events',
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('target_url')
                        ->label('Webhook URL')
                        ->url()
                        ->required()
                        ->maxLength(2048)
                        ->columnSpanFull(),
                    Forms\Components\CheckboxList::make('events')
                        ->options(self::EVENT_OPTIONS)
                        ->columns(2)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                    Forms\Components\TextInput::make('secret')
                        ->password()
                        ->revealable()
                        ->maxLength(255)
                        ->helperText('Used to sign outbound payloads (HMAC SHA-256).')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('generateSecret')
                                ->icon('heroicon-m-arrow-path')
                                ->action(function (Forms\Set $set) {
                                    $set('secret', Str::random(40));
                                })
                        ),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('target_url')
                    ->label('URL')
                    ->limit(40)
                    ->tooltip(fn (Webhook $record) => $record->target_url),
                Tables\Columns\TextColumn::make('events')
                    ->badge()
                    ->limitList(3),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
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
            'index' => Pages\ListWebhooks::route('/'),
            'create' => Pages\CreateWebhook::route('/create'),
            'edit' => Pages\EditWebhook::route('/{record}/edit'),
        ];
    }
}
