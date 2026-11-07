<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\LcAmendmentResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\ImportLc;
use App\Models\LcAmendment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LcAmendmentResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = LcAmendment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Trade Finance Operations';

    protected static ?string $navigationLabel = 'LC Amendments';

    protected static ?int $navigationSort = 3;

    public static function getModuleSlug(): ?string
    {
        return 'lc_amendments';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\MorphToSelect::make('amendable')
                        ->label('Letter of Credit')
                        ->types([
                            Forms\Components\MorphToSelect\Type::make(ImportLc::class)
                                ->titleAttribute('lc_number'),
                        ])
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('amendment_number')
                        ->label('Amendment Sequence No')
                        ->numeric()
                        ->default(1)
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'Requested' => 'Requested',
                            'Pending Bank' => 'Submitted to Bank',
                            'Approved' => 'Approved by Bank',
                            'Rejected' => 'Rejected',
                            'Cancelled' => 'Cancelled',
                        ])
                        ->default('Requested')
                        ->required(),
                    Forms\Components\DatePicker::make('request_date')
                        ->label('Request Date')
                        ->default(now())
                        ->required(),
                    Forms\Components\DatePicker::make('bank_submission_date')
                        ->label('Bank Submission Date'),
                    Forms\Components\DatePicker::make('bank_approval_date')
                        ->label('Bank Approval Date'),
                    Forms\Components\TextInput::make('amount_change')
                        ->label('Amount Change (+ / -)')
                        ->numeric()
                        ->default(0.00),
                    Forms\Components\DatePicker::make('new_expiry_date')
                        ->label('Amended Expiry Date'),
                    Forms\Components\DatePicker::make('new_latest_shipment_date')
                        ->label('Amended Latest Shipment Date'),
                    Forms\Components\Textarea::make('reason')
                        ->label('Justification / Reason for Amendment')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Internal Notes')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amendment_number')
                    ->label('Amd #')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('amendable.lc_number')
                    ->label('LC Reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('request_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_change')
                    ->label('Value Delta')
                    ->formatStateUsing(fn ($state) => ($state >= 0 ? '+' : '') . number_format((float) $state, 2)),
                Tables\Columns\TextColumn::make('new_expiry_date')
                    ->label('New Expiry')
                    ->date(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'Requested',
                        'warning' => 'Pending Bank',
                        'success' => 'Approved',
                        'danger' => ['Rejected', 'Cancelled'],
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLcAmendments::route('/'),
            'create' => Pages\CreateLcAmendment::route('/create'),
            'edit' => Pages\EditLcAmendment::route('/{record}/edit'),
        ];
    }
}
