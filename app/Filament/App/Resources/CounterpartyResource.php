<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CounterpartyResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\Counterparty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CounterpartyResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Counterparty::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Organization & Settings';

    protected static ?string $navigationLabel = 'Counterparties';

    protected static ?int $navigationSort = 4;

    public static function getModuleSlug(): ?string
    {
        return 'counterparties';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('type')
                        ->options([
                            'buyer' => 'Buyer',
                            'supplier' => 'Supplier',
                            'freight_forwarder' => 'Freight Forwarder',
                            'shipping_line' => 'Shipping Line',
                            'insurer' => 'Insurer',
                            'other' => 'Other',
                        ])
                        ->default('supplier')
                        ->required(),
                    Forms\Components\TextInput::make('country')
                        ->default('Bangladesh')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('tax_id')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('registration_number')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('address')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('contact_person')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('credit_limit')
                        ->numeric()
                        ->default(0),
                    Forms\Components\TextInput::make('rating')
                        ->maxLength(10),
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
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('country')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('credit_limit')
                    ->money('usd')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'buyer' => 'Buyer',
                        'supplier' => 'Supplier',
                        'freight_forwarder' => 'Freight Forwarder',
                        'shipping_line' => 'Shipping Line',
                        'insurer' => 'Insurer',
                        'other' => 'Other',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCounterparties::route('/'),
            'create' => Pages\CreateCounterparty::route('/create'),
            'edit' => Pages\EditCounterparty::route('/{record}/edit'),
        ];
    }
}
