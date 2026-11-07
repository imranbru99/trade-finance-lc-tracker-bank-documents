<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ModuleCatalogResource\Pages;
use App\Models\Module;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ModuleCatalogResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Platform Management';

    protected static ?string $navigationLabel = 'Modules Catalog';

    protected static ?int $navigationSort = 3;

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
                    Forms\Components\Select::make('category')
                        ->options([
                            'core' => 'Core Module',
                            'trade_finance' => 'Trade Finance Module',
                            'advanced' => 'Advanced Module',
                            'optional' => 'Optional Business Module',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('version')
                        ->default('1.0.0')
                        ->required(),
                    Forms\Components\TextInput::make('icon')
                        ->default('heroicon-o-cube'),
                    Forms\Components\Toggle::make('is_core')
                        ->label('Is Core Module (Cannot be disabled)'),
                    Forms\Components\Toggle::make('default_enabled')
                        ->label('Default Enabled for New Tenants')
                        ->default(true),
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
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'primary' => 'core',
                        'success' => 'trade_finance',
                        'warning' => 'advanced',
                        'secondary' => 'optional',
                    ]),
                Tables\Columns\IconColumn::make('is_core')
                    ->boolean(),
                Tables\Columns\IconColumn::make('default_enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('version'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModuleCatalogs::route('/'),
            'create' => Pages\CreateModuleCatalog::route('/create'),
            'edit' => Pages\EditModuleCatalog::route('/{record}/edit'),
        ];
    }
}
