<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\BankCorrespondenceResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\BankCorrespondence;
use App\Models\ExportLc;
use App\Models\ImportLc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankCorrespondenceResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = BankCorrespondence::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Trade Finance Operations';

    protected static ?string $navigationLabel = 'Bank Correspondence';

    protected static ?int $navigationSort = 8;

    public static function getModuleSlug(): ?string
    {
        return 'bank_correspondence';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('correspondence_type')
                        ->options([
                            'Email' => 'Email',
                            'Bank Letter' => 'Bank Letter',
                            'Bank Advice' => 'Bank Advice',
                            'Amendment Communication' => 'Amendment Communication',
                            'Document Submission Letter' => 'Document Submission Letter',
                            'Discrepancy Notice' => 'Discrepancy Notice',
                            'Acceptance Notice' => 'Acceptance Notice',
                            'Payment Advice' => 'Payment Advice',
                            'SWIFT Record' => 'SWIFT Record',
                        ])
                        ->default('Email')
                        ->required(),
                    Forms\Components\TextInput::make('reference_number')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('subject')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('message_date')
                        ->required()
                        ->default(now()),
                    Forms\Components\Select::make('direction')
                        ->options([
                            'Incoming' => 'Incoming',
                            'Outgoing' => 'Outgoing',
                        ])
                        ->default('Incoming')
                        ->required(),
                    Forms\Components\Select::make('bank_id')
                        ->relationship('bank', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('branch_id')
                        ->relationship('branch', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\MorphToSelect::make('trackable')
                        ->types([
                            Forms\Components\MorphToSelect\Type::make(ImportLc::class)
                                ->titleAttribute('lc_number'),
                            Forms\Components\MorphToSelect\Type::make(ExportLc::class)
                                ->titleAttribute('lc_number'),
                        ])
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('sender')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('recipient')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('swift_message_type')
                        ->label('SWIFT Message Type')
                        ->maxLength(20),
                    Forms\Components\Select::make('status')
                        ->options([
                            'Logged' => 'Logged',
                            'Action Required' => 'Action Required',
                            'Resolved' => 'Resolved',
                            'Archived' => 'Archived',
                        ])
                        ->default('Logged')
                        ->required(),
                    Forms\Components\Textarea::make('summary')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('body')
                        ->rows(6)
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Ref')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('bank.name')
                    ->label('Bank')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('message_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('direction')
                    ->badge(),
                Tables\Columns\BadgeColumn::make('status'),
            ])
            ->defaultSort('message_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Logged' => 'Logged',
                        'Action Required' => 'Action Required',
                        'Resolved' => 'Resolved',
                        'Archived' => 'Archived',
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
            'index' => Pages\ListBankCorrespondence::route('/'),
            'create' => Pages\CreateBankCorrespondence::route('/create'),
            'edit' => Pages\EditBankCorrespondence::route('/{record}/edit'),
        ];
    }
}
