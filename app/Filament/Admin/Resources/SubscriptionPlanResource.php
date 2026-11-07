<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SubscriptionPlanResource\Pages;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Platform Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('price_monthly')
                        ->numeric()
                        ->prefix('$')
                        ->default(0.00)
                        ->required(),
                    Forms\Components\TextInput::make('price_yearly')
                        ->numeric()
                        ->prefix('$')
                        ->default(0.00)
                        ->required(),
                    Forms\Components\TextInput::make('max_users')
                        ->numeric()
                        ->default(5)
                        ->required(),
                    Forms\Components\TextInput::make('max_companies')
                        ->numeric()
                        ->default(1)
                        ->required(),
                    Forms\Components\TextInput::make('max_branches')
                        ->numeric()
                        ->default(2)
                        ->required(),
                    Forms\Components\TextInput::make('max_lcs')
                        ->numeric()
                        ->default(50)
                        ->required(),
                    Forms\Components\TextInput::make('storage_mb')
                        ->numeric()
                        ->suffix('MB')
                        ->default(5120)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                    Forms\Components\Toggle::make('is_featured')
                        ->default(false),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('price_monthly')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_users')
                    ->label('Users Limit'),
                Tables\Columns\TextColumn::make('max_lcs')
                    ->label('LCs Limit'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
