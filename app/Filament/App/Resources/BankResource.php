<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\BankResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\Bank;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BankResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Bank::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Organization & Settings';

    protected static ?string $navigationLabel = 'Banks';

    protected static ?int $navigationSort = 3;

    public static function getModuleSlug(): ?string
    {
        return 'bank_management';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('category')
                        ->label('Institution Category')
                        ->options([
                            'commercial' => 'Commercial',
                            'central' => 'Central',
                            'development' => 'Development',
                            'islamic' => 'Islamic',
                            'foreign' => 'Foreign',
                            'offshore' => 'Offshore',
                        ])
                        ->default('commercial')
                        ->required(),
                    Forms\Components\Select::make('operational_lc_role')
                        ->label('LC Bank Role Profile')
                        ->options([
                            'Issuing' => 'Issuing',
                            'Advising' => 'Advising',
                            'Confirming' => 'Confirming',
                            'Negotiating' => 'Negotiating',
                            'Nominated' => 'Nominated',
                            'Reimbursing' => 'Reimbursing',
                        ])
                        ->helperText('Operational reference when assigning this bank on LCs; record details in Internal Notes if needed.')
                        ->dehydrated(false)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('swift_bic')
                        ->label('SWIFT / BIC')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('routing_number')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('country')
                        ->default('Bangladesh')
                        ->maxLength(100),
                    Forms\Components\Textarea::make('address')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('website')
                        ->url()
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_global')
                        ->label('Global Bank Directory Entry'),
                    Forms\Components\Textarea::make('internal_notes')
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
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('swift_bic')
                    ->label('SWIFT')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('country')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_global')
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'commercial' => 'Commercial',
                        'central' => 'Central',
                        'development' => 'Development',
                        'islamic' => 'Islamic',
                        'foreign' => 'Foreign',
                        'offshore' => 'Offshore',
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $tenant = Filament::getTenant();

        if ($tenant) {
            $query->where(function (Builder $builder) use ($tenant) {
                $builder->where('organization_id', $tenant->getKey())
                    ->orWhere('is_global', true);
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanks::route('/'),
            'create' => Pages\CreateBank::route('/create'),
            'edit' => Pages\EditBank::route('/{record}/edit'),
        ];
    }
}
