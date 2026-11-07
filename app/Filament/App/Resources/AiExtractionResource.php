<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AiExtractionResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\AiExtraction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AiExtractionResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = AiExtraction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Intelligence & Reports';

    protected static ?string $navigationLabel = 'AI Extractions';

    protected static ?int $navigationSort = 3;

    public static function getModuleSlug(): ?string
    {
        return 'ai_intelligence';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Review')->schema([
                    Forms\Components\Placeholder::make('document_title')
                        ->label('Document')
                        ->content(fn (?AiExtraction $record) => $record?->document?->title ?? '—'),
                    Forms\Components\KeyValue::make('extracted_data')
                        ->label('Extracted fields')
                        ->disabled()
                        ->columnSpanFull(),
                    Forms\Components\KeyValue::make('confidence_scores')
                        ->label('Confidence scores')
                        ->disabled()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('review_notes')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document.title')
                    ->label('Document')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('human_review_status')
                    ->label('Review status')
                    ->badge()
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Accepted',
                        'info' => 'Adjusted',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('reviewedByUser.name')
                    ->label('Reviewed by')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('human_review_status')
                    ->label('Review status')
                    ->options([
                        'Pending' => 'Pending',
                        'Accepted' => 'Accepted',
                        'Adjusted' => 'Adjusted',
                        'Rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiExtractions::route('/'),
            'view' => Pages\ViewAiExtraction::route('/{record}'),
        ];
    }
}
