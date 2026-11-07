<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PaymentScheduleResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\ImportLc;
use App\Models\PaymentSchedule;
use App\Services\TradeFinancePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentScheduleResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = PaymentSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Trade Finance Operations';

    protected static ?string $navigationLabel = 'Payment Schedules';

    protected static ?int $navigationSort = 7;

    public static function getModuleSlug(): ?string
    {
        return 'payment_tracking';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('schedule_number')
                        ->label('Schedule Ref No')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\MorphToSelect::make('trackable')
                        ->label('Related Letter of Credit')
                        ->types([
                            Forms\Components\MorphToSelect\Type::make(ImportLc::class)
                                ->titleAttribute('lc_number'),
                        ])
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('payment_type')
                        ->options([
                            'Sight' => 'Sight Payment',
                            'Deferred' => 'Deferred Payment',
                            'Usance' => 'Usance Acceptance',
                            'Negotiation' => 'Bank Negotiation',
                            'Acceptance' => 'Banker Acceptance',
                        ])
                        ->default('Sight')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'Scheduled' => 'Scheduled',
                            'Due' => 'Payment Due',
                            'Matured' => 'Matured (Pending Settlement)',
                            'Partially Paid' => 'Partially Paid',
                            'Settled' => 'Fully Settled',
                            'Overdue' => 'Overdue',
                            'Cancelled' => 'Cancelled',
                        ])
                        ->default('Scheduled')
                        ->required(),
                    Forms\Components\Select::make('currency')
                        ->options([
                            'USD' => 'USD ($)',
                            'EUR' => 'EUR (€)',
                            'GBP' => 'GBP (£)',
                            'BDT' => 'BDT (৳)',
                        ])
                        ->default('USD')
                        ->required(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Principal Amount')
                        ->numeric()
                        ->prefix('$')
                        ->required(),
                    Forms\Components\TextInput::make('paid_amount')
                        ->label('Settled Amount')
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\TextInput::make('outstanding_amount')
                        ->label('Outstanding Balance')
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\DatePicker::make('due_date')
                        ->label('Payment Due Date')
                        ->required(),
                    Forms\Components\DatePicker::make('maturity_date')
                        ->label('Official Bank Maturity Date'),
                    Forms\Components\DatePicker::make('settlement_date')
                        ->label('Actual Settlement Date'),
                    Forms\Components\TextInput::make('payment_ref')
                        ->label('Bank Payment Reference / SWIFT MT202'),
                    Forms\Components\Textarea::make('bank_account_details')
                        ->label('Debit / Settlement Account Details')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Treasury Notes')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('schedule_number')
                    ->label('Schedule #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('payment_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->amount, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('outstanding_amount')
                    ->label('Outstanding')
                    ->formatStateUsing(fn ($record) => $record->currency . ' ' . number_format((float) $record->outstanding_amount, 2)),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'Scheduled',
                        'warning' => ['Due', 'Matured', 'Partially Paid'],
                        'success' => 'Settled',
                        'danger' => 'Overdue',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Scheduled' => 'Scheduled',
                        'Due' => 'Due',
                        'Matured' => 'Matured',
                        'Settled' => 'Settled',
                        'Overdue' => 'Overdue',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Schedule PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (PaymentSchedule $record, TradeFinancePdfService $pdfService) {
                        return $pdfService->generatePaymentSchedulePdf($record);
                    }),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentSchedules::route('/'),
            'create' => Pages\CreatePaymentSchedule::route('/create'),
            'edit' => Pages\EditPaymentSchedule::route('/{record}/edit'),
        ];
    }
}
