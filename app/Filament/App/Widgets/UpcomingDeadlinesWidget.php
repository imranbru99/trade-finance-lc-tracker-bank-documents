<?php

namespace App\Filament\App\Widgets;

use App\Models\Deadline;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingDeadlinesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Upcoming Deadlines';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Deadline::query()
                    ->where('status', 'pending')
                    ->where('due_date', '>=', now()->toDateString())
                    ->orderBy('due_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('deadline_type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge(),
                Tables\Columns\TextColumn::make('responsibleOfficer.name')
                    ->label('Officer'),
            ])
            ->paginated(false);
    }
}
