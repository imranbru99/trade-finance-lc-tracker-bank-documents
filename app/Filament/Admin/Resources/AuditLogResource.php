<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Security';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?int $navigationSort = 20;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('event')->disabled(),
            Forms\Components\TextInput::make('ip_address')->disabled(),
            Forms\Components\Textarea::make('user_agent')->disabled()->columnSpanFull(),
            Forms\Components\KeyValue::make('old_values')->disabled()->columnSpanFull(),
            Forms\Components\KeyValue::make('new_values')->disabled()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('event')->searchable()->badge(),
                Tables\Columns\TextColumn::make('organization.name')->label('Organization')->toggleable(),
                Tables\Columns\TextColumn::make('user.name')->label('User')->toggleable(),
                Tables\Columns\TextColumn::make('auditable_type')->label('Entity')->toggleable(),
                Tables\Columns\TextColumn::make('ip_address')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options(fn () => AuditLog::query()->distinct()->orderBy('event')->pluck('event', 'event')->all()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
