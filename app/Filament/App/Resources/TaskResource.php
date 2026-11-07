<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TaskResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\ExportLc;
use App\Models\ImportLc;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Workflow & Operations';

    protected static ?string $navigationLabel = 'Tasks';

    protected static ?int $navigationSort = 2;

    public static function getModuleSlug(): ?string
    {
        return 'task_management';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                    Forms\Components\Select::make('task_type')
                        ->options([
                            'General Follow-up' => 'General Follow-up',
                            'LC Preparation' => 'LC Preparation',
                            'Document Collection' => 'Document Collection',
                            'Bank Follow-up' => 'Bank Follow-up',
                            'Shipment Follow-up' => 'Shipment Follow-up',
                            'Amendment Processing' => 'Amendment Processing',
                            'Discrepancy Resolution' => 'Discrepancy Resolution',
                            'Payment Follow-up' => 'Payment Follow-up',
                            'Compliance Review' => 'Compliance Review',
                        ])
                        ->default('General Follow-up')
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
                    Forms\Components\Select::make('assigned_to_user_id')
                        ->label('Assigned To')
                        ->relationship('assignedTo', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('priority')
                        ->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                            'urgent' => 'Critical',
                        ])
                        ->default('medium')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options([
                            'todo' => 'Pending',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                            'overdue' => 'Overdue',
                        ])
                        ->default('todo')
                        ->required(),
                    Forms\Components\DatePicker::make('due_date'),
                    Forms\Components\DateTimePicker::make('completed_at')
                        ->visible(fn (Forms\Get $get) => $get('status') === 'completed'),
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
                Tables\Columns\TextColumn::make('task_type')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assignee')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'urgent' => 'Critical',
                        default => ucfirst($state),
                    }),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'todo' => 'Pending',
                        'in_progress' => 'In Progress',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->colors([
                        'secondary' => 'todo',
                        'info' => 'in_progress',
                        'success' => 'completed',
                        'danger' => ['overdue', 'cancelled'],
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'todo' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'overdue' => 'Overdue',
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
