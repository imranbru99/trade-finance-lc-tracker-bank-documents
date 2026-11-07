<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ExportLcResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\ExportLc;
use App\Services\DeadlineEngine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExportLcResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = ExportLc::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationGroup = 'Trade Finance Operations';

    protected static ?string $navigationLabel = 'Export LCs';

    protected static ?int $navigationSort = 2;

    public static function getModuleSlug(): ?string
    {
        return 'export_lc';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Export LC Details')->tabs([
                    Forms\Components\Tabs\Tab::make('General & Contract')->schema([
                        Forms\Components\TextInput::make('lc_number')
                            ->label('Export LC Number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('contract_reference')
                            ->label('Export Contract / Sales Order Ref')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('buyer_name')
                            ->label('Foreign Buyer Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('counterparty_id')
                            ->label('Linked Buyer Counterparty')
                            ->relationship('buyer', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Registered' => 'Registered',
                                'Advised' => 'Advised',
                                'Active' => 'Active',
                                'Partially Shipped' => 'Partially Shipped',
                                'Fully Shipped' => 'Fully Shipped',
                                'Documents Dispatched' => 'Documents Dispatched',
                                'Negotiated' => 'Negotiated',
                                'Matured' => 'Matured',
                                'Realized' => 'Realized (Payment Received)',
                                'Expired' => 'Expired',
                                'Closed' => 'Closed',
                            ])
                            ->default('Registered')
                            ->required(),
                        Forms\Components\Textarea::make('buyer_address')
                            ->label('Buyer Address')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Financials & Dates')->schema([
                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD ($)',
                                'EUR' => 'EUR (€)',
                                'GBP' => 'GBP (£)',
                                'BDT' => 'BDT (৳)',
                            ])
                            ->default('USD')
                            ->required(),
                        Forms\Components\TextInput::make('export_value')
                            ->label('Total Export Value')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('realized_amount')
                            ->label('Realized Amount (Received)')
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('pending_amount')
                            ->label('Pending Realization')
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('LC Advice / Issue Date'),
                        Forms\Components\DatePicker::make('latest_shipment_date')
                            ->label('Latest Shipment Deadline')
                            ->required(),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('LC Expiry Date')
                            ->required(),
                        Forms\Components\DatePicker::make('presentation_deadline')
                            ->label('Documents Presentation Deadline')
                            ->required(),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Banks & Shipping')->schema([
                        Forms\Components\TextInput::make('issuing_bank_name')
                            ->label('Overseas Issuing Bank Name'),
                        Forms\Components\TextInput::make('issuing_bank_swift')
                            ->label('Issuing Bank SWIFT/BIC'),
                        Forms\Components\Select::make('advising_bank_id')
                            ->label('Local Advising / Negotiating Bank')
                            ->relationship('advisingBank', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('incoterms')
                            ->options([
                                'FOB' => 'FOB - Free on Board',
                                'CIF' => 'CIF - Cost, Insurance & Freight',
                                'CFR' => 'CFR - Cost and Freight',
                            ])
                            ->default('FOB'),
                        Forms\Components\Textarea::make('goods_description')
                            ->label('Export Commodity Description')
                            ->columnSpanFull(),
                    ])->columns(2),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lc_number')
                    ->label('Export LC No')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('buyer_name')
                    ->label('Buyer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('export_value')
                    ->label('Value')
                    ->formatStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->export_value, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('realized_amount')
                    ->label('Realized')
                    ->formatStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->realized_amount, 2)),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'Draft',
                        'info' => ['Registered', 'Advised'],
                        'primary' => ['Active', 'Documents Dispatched'],
                        'success' => ['Negotiated', 'Realized', 'Closed'],
                        'danger' => 'Expired',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListExportLcs::route('/'),
            'create' => Pages\CreateExportLc::route('/create'),
            'edit' => Pages\EditExportLc::route('/{record}/edit'),
        ];
    }
}
