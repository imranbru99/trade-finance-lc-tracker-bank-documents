<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DiscrepancyResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\Discrepancy;
use App\Models\ImportLc;
use App\Services\TradeFinancePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DiscrepancyResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Discrepancy::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Trade Finance Operations';

    protected static ?string $navigationLabel = 'Discrepancies';

    protected static ?int $navigationSort = 6;

    public static function getModuleSlug(): ?string
    {
        return 'discrepancy_management';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('discrepancy_reference')
                        ->label('Discrepancy Ref')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\MorphToSelect::make('trackable')
                        ->label('Letter of Credit')
                        ->types([
                            Forms\Components\MorphToSelect\Type::make(ImportLc::class)
                                ->titleAttribute('lc_number'),
                        ])
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('category')
                        ->options([
                            'Documentary' => 'Documentary Inconsistency',
                            'Technical' => 'Technical Clause Deviation',
                            'Shipment Delay' => 'Late Shipment Date',
                            'Missing Clause' => 'Missing Mandatory Endorsement',
                            'Value Mismatch' => 'Invoice Value Exceeds Tolerance',
                            'Other' => 'Other Banking Discrepancy',
                        ])
                        ->default('Documentary')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'Identified' => 'Identified',
                            'Under Review' => 'Under Bank / Internal Review',
                            'Awaiting Correction' => 'Awaiting Document Correction',
                            'Submitted for Waiver' => 'Submitted for Applicant Waiver',
                            'Waiver Pending' => 'Waiver Pending Issuing Bank',
                            'Resolved' => 'Resolved / Accepted',
                            'Rejected' => 'Rejected / Refused',
                            'Closed' => 'Closed',
                        ])
                        ->default('Identified')
                        ->required(),
                    Forms\Components\DatePicker::make('identified_date')
                        ->label('Date Identified')
                        ->default(now())
                        ->required(),
                    Forms\Components\DatePicker::make('resolution_deadline')
                        ->label('Resolution Deadline Date')
                        ->required(),
                    Forms\Components\TextInput::make('bank_communication_ref')
                        ->label('Bank Discrepancy Advice Ref / MT734'),
                    Forms\Components\Textarea::make('description')
                        ->label('Discrepancy Description as Notified by Bank')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('corrective_action')
                        ->label('Required Corrective Action / Resolution Strategy')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('resolution_notes')
                        ->label('Resolution Notes & Confirmation')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('discrepancy_reference')
                    ->label('Ref')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('identified_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resolution_deadline')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => ['Identified', 'Rejected'],
                        'warning' => ['Under Review', 'Awaiting Correction', 'Submitted for Waiver', 'Waiver Pending'],
                        'success' => ['Resolved', 'Closed'],
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Identified' => 'Identified',
                        'Awaiting Correction' => 'Awaiting Correction',
                        'Submitted for Waiver' => 'Submitted for Waiver',
                        'Resolved' => 'Resolved',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Notice PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('danger')
                    ->action(function (Discrepancy $record, TradeFinancePdfService $pdfService) {
                        return $pdfService->generateDiscrepancyReportPdf($record);
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscrepancies::route('/'),
            'create' => Pages\CreateDiscrepancy::route('/create'),
            'edit' => Pages\EditDiscrepancy::route('/{record}/edit'),
        ];
    }
}
