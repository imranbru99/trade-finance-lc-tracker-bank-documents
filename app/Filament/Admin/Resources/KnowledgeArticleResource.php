<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\KnowledgeArticleResource\Pages;
use App\Models\KnowledgeArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KnowledgeArticleResource extends Resource
{
    protected static ?string $model = KnowledgeArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Knowledge Base';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->maxLength(255),
            Forms\Components\Select::make('category')
                ->options([
                    'Getting Started' => 'Getting Started',
                    'Import LC' => 'Import LC',
                    'Export LC' => 'Export LC',
                    'Document Management' => 'Document Management',
                    'Deadline Management' => 'Deadline Management',
                    'Bank Management' => 'Bank Management',
                    'Reports' => 'Reports',
                    'AI Features' => 'AI Features',
                    'Integrations' => 'Integrations',
                    'Account and Security' => 'Account and Security',
                    'General' => 'General',
                ])
                ->default('General')
                ->required(),
            Forms\Components\Select::make('locale')
                ->options(['en' => 'English', 'bn' => 'Bengali', 'ar' => 'Arabic'])
                ->default('en')
                ->required(),
            Forms\Components\Toggle::make('is_published')->default(true),
            Forms\Components\Textarea::make('summary')->rows(2)->columnSpanFull(),
            Forms\Components\RichEditor::make('content')->required()->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('locale')->badge(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('views_count')->label('Views')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->since(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeArticles::route('/'),
            'create' => Pages\CreateKnowledgeArticle::route('/create'),
            'edit' => Pages\EditKnowledgeArticle::route('/{record}/edit'),
        ];
    }
}
