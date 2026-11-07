<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DeadlineResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\Deadline;
use App\Models\ExportLc;
use App\Models\ImportLc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeadlineResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Deadline::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Workflow & Operations';

    protected static ?string $navigationLabel = 'Deadlines';

    protected static ?int $navigationSort = 1;

    public static function getModuleSlug(): ?string
    {
        return 'deadline_tracking';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('deadline_type')
                        ->options([
                            'lc_expiry' => 'LC Expiry',
                            'latest_shipment' => 'Latest Shipment',
                            'presentation_deadline' => 'Document Presentation',
                            'payment_maturity' => 'Payment Maturity',
                            'discrepancy_resolution' => 'Discrepancy Resolution',
                            'insurance_expiry' => 'Insurance Expiry',
                            'custom' => 'Custom',
                        ])
                        ->required(),
                    Forms\Components\MorphToSelect::make('trackable')
                        ->types([
                            Forms\Components\MorphToSelect\Type::make(ImportLc::class)
                                ->titleAttribute('lc_number'),
                            Forms\Components\MorphToSelect\Type::make(ExportLc::class)
                                ->titleAttribute('lc_number'),
                        ])
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('branch_id')
                        ->relationship('branch', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\DatePicker::make('due_date')
                        ->required(),
                    Forms\Components\TimePicker::make('due_time'),
                    Forms\Components\Select::make('responsible_user_id')
                        ->label('Responsible Officer')
                        ->relationship('responsibleOfficer', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('priority')
                        ->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                            'critical' => 'Critical',
                        ])
                        ->default('high')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'completed' => 'Completed',
                            'overdue' => 'Overdue',
                            'waived' => 'Waived',
                        ])
                        ->default('pending')
                        ->required(),
                    Forms\Components\DateTimePicker::make('completed_at')
                        ->visible(fn (Forms\Get $get) => $get('status') === 'completed'),
                    Forms\Components\Select::make('completed_by_user_id')
                        ->relationship('completedByUser', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Textarea::make('notes')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('deadline_type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'overdue',
                        'secondary' => 'waived',
                    ]),
                Tables\Columns\TextColumn::make('responsibleOfficer.name')
                    ->label('Officer')
                    ->toggleable(),
            ])
            ->defaultSort('due_date')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'overdue' => 'Overdue',
                        'waived' => 'Waived',
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
            'index' => Pages\ListDeadlines::route('/'),
            'create' => Pages\CreateDeadline::route('/create'),
            'edit' => Pages\EditDeadline::route('/{record}/edit'),
        ];
    }
}
