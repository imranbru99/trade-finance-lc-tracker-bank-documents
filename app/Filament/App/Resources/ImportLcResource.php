<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ImportLcResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\Bank;
use App\Models\Counterparty;
use App\Models\ImportLc;
use App\Services\DeadlineEngine;
use App\Services\DocumentManager;
use App\Services\TradeFinancePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ImportLcResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = ImportLc::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationGroup = 'Trade Finance Operations';

    protected static ?string $navigationLabel = 'Import LCs';

    protected static ?int $navigationSort = 1;

    public static function getModuleSlug(): ?string
    {
        return 'import_lc';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Import LC Details')->tabs([
                    Forms\Components\Tabs\Tab::make('General & Parties')->schema([
                        Forms\Components\TextInput::make('lc_number')
                            ->label('LC Number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('internal_reference')
                            ->label('Internal File Reference')
                            ->maxLength(255),
                        Forms\Components\Select::make('lc_type')
                            ->label('LC Type')
                            ->options([
                                'Irrevocable' => 'Irrevocable',
                                'Irrevocable Confirmed' => 'Irrevocable Confirmed',
                                'Transferable' => 'Transferable',
                                'Revolving' => 'Revolving',
                                'Standby' => 'Standby Letter of Credit (SBLC)',
                                'Back-to-Back' => 'Back-to-Back LC',
                                'Red Clause' => 'Red Clause LC',
                            ])
                            ->default('Irrevocable')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Operational Status')
                            ->options([
                                'Draft' => 'Draft',
                                'Pending Internal Review' => 'Pending Internal Review',
                                'Pending Approval' => 'Pending Approval',
                                'Submitted to Bank' => 'Submitted to Bank',
                                'Issued' => 'Issued',
                                'Advised' => 'Advised',
                                'Active' => 'Active',
                                'Partially Shipped' => 'Partially Shipped',
                                'Fully Shipped' => 'Fully Shipped',
                                'Documents Submitted' => 'Documents Submitted',
                                'Under Bank Examination' => 'Under Bank Examination',
                                'Discrepancy Found' => 'Discrepancy Found',
                                'Accepted' => 'Accepted',
                                'Payment Pending' => 'Payment Pending',
                                'Matured' => 'Matured',
                                'Settled' => 'Settled',
                                'Expired' => 'Expired',
                                'Cancelled' => 'Cancelled',
                                'Closed' => 'Closed',
                            ])
                            ->default('Draft')
                            ->required(),
                        Forms\Components\TextInput::make('applicant_name')
                            ->label('Applicant Name (Importer)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('applicant_address')
                            ->label('Applicant Address'),
                        Forms\Components\TextInput::make('beneficiary_name')
                            ->label('Beneficiary Name (Exporter)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('beneficiary_address')
                            ->label('Beneficiary Address'),
                        Forms\Components\Select::make('counterparty_id')
                            ->label('Linked Supplier Counterparty')
                            ->relationship('counterparty', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Financials & Dates')->schema([
                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD - US Dollar',
                                'EUR' => 'EUR - Euro',
                                'GBP' => 'GBP - British Pound',
                                'BDT' => 'BDT - Bangladeshi Taka',
                                'CNY' => 'CNY - Chinese Yuan',
                                'JPY' => 'JPY - Japanese Yen',
                                'INR' => 'INR - Indian Rupee',
                                'AED' => 'AED - UAE Dirham',
                                'SGD' => 'SGD - Singapore Dollar',
                            ])
                            ->default('USD')
                            ->required(),
                        Forms\Components\TextInput::make('lc_amount')
                            ->label('LC Value')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('tolerance_percentage')
                            ->label('Tolerance (±%)')
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\Select::make('payment_terms')
                            ->options([
                                'Sight' => 'Sight Payment',
                                'Deferred' => 'Deferred Payment',
                                'Usance' => 'Usance Payment',
                                'Mixed' => 'Mixed Payment Terms',
                            ])
                            ->default('Sight')
                            ->required(),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('Issuance Date'),
                        Forms\Components\DatePicker::make('latest_shipment_date')
                            ->label('Latest Shipment Date')
                            ->required(),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('Expiry Date')
                            ->required(),
                        Forms\Components\TextInput::make('presentation_period_days')
                            ->label('Presentation Period (Days)')
                            ->numeric()
                            ->default(21)
                            ->required(),
                        Forms\Components\TextInput::make('expiry_place')
                            ->label('Place of Expiry')
                            ->default('Beneficiary Country'),
                        Forms\Components\Textarea::make('payment_details')
                            ->label('Payment Tenor & Details')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Banking Institutions')->schema([
                        Forms\Components\Select::make('issuing_bank_id')
                            ->label('Issuing Bank')
                            ->relationship('issuingBank', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('advising_bank_id')
                            ->label('Advising Bank')
                            ->relationship('advisingBank', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('confirming_bank_id')
                            ->label('Confirming Bank')
                            ->relationship('confirmingBank', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('negotiating_bank_id')
                            ->label('Negotiating Bank')
                            ->relationship('negotiatingBank', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Logistics & Goods')->schema([
                        Forms\Components\TextInput::make('port_of_loading')
                            ->label('Port of Loading'),
                        Forms\Components\TextInput::make('port_of_discharge')
                            ->label('Port of Discharge'),
                        Forms\Components\Select::make('incoterms')
                            ->options([
                                'FOB' => 'FOB - Free on Board',
                                'CIF' => 'CIF - Cost, Insurance & Freight',
                                'CFR' => 'CFR - Cost and Freight',
                                'EXW' => 'EXW - Ex Works',
                                'FCA' => 'FCA - Free Carrier',
                                'CPT' => 'CPT - Carriage Paid To',
                                'CIP' => 'CIP - Carriage & Insurance Paid',
                                'DAP' => 'DAP - Delivered at Place',
                                'DPU' => 'DPU - Delivered at Place Unloaded',
                                'DDP' => 'DDP - Delivered Duty Paid',
                            ])
                            ->default('FOB')
                            ->required(),
                        Forms\Components\TextInput::make('hs_code')
                            ->label('Harmonized System (HS) Code'),
                        Forms\Components\Toggle::make('partial_shipment_allowed')
                            ->label('Partial Shipment Allowed')
                            ->default(true),
                        Forms\Components\Toggle::make('transshipment_allowed')
                            ->label('Transshipment Allowed')
                            ->default(false),
                        Forms\Components\Textarea::make('goods_description')
                            ->label('Full Goods Description')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Special Conditions')->schema([
                        Forms\Components\Textarea::make('special_conditions')
                            ->label('Special Clauses & Instructions')
                            ->rows(4),
                        Forms\Components\Textarea::make('internal_notes')
                            ->label('Internal Operation Notes')
                            ->rows(4),
                    ]),
                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lc_number')
                    ->label('LC Number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('applicant_name')
                    ->label('Applicant')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('beneficiary_name')
                    ->label('Beneficiary')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('issuingBank.name')
                    ->label('Issuing Bank')
                    ->limit(18)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lc_amount')
                    ->label('LC Amount')
                    ->formatStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->lc_amount, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'Draft',
                        'warning' => ['Pending Internal Review', 'Pending Approval', 'Discrepancy Found'],
                        'info' => ['Submitted to Bank', 'Issued', 'Advised'],
                        'success' => ['Active', 'Accepted', 'Settled', 'Closed'],
                        'danger' => ['Expired', 'Cancelled'],
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Active' => 'Active',
                        'Pending Approval' => 'Pending Approval',
                        'Discrepancy Found' => 'Discrepancy Found',
                        'Under Bank Examination' => 'Under Bank Examination',
                        'Settled' => 'Settled',
                        'Expired' => 'Expired',
                    ]),
                Tables\Filters\SelectFilter::make('currency')
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'BDT' => 'BDT',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('LC Summary PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (ImportLc $record, TradeFinancePdfService $pdfService) {
                        return $pdfService->generateLcSummaryPdf($record);
                    }),
                Tables\Actions\Action::make('downloadChecklist')
                    ->label('Checklist PDF')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->action(function (ImportLc $record, TradeFinancePdfService $pdfService) {
                        return $pdfService->generateDocumentChecklistPdf($record);
                    }),
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
            'index' => Pages\ListImportLcs::route('/'),
            'create' => Pages\CreateImportLc::route('/create'),
            'edit' => Pages\EditImportLc::route('/{record}/edit'),
        ];
    }
}
