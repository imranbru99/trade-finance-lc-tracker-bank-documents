<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CmsPageResource\Pages;
use App\Models\CmsPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CmsPageResource extends Resource
{
    protected static ?string $model = CmsPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'CMS Pages';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->maxLength(255)->helperText('Leave blank to auto-generate from title.'),
            Forms\Components\Select::make('locale')
                ->options(['en' => 'English', 'bn' => 'Bengali', 'ar' => 'Arabic', 'hi' => 'Hindi', 'zh' => 'Chinese'])
                ->default('en')
                ->required(),
            Forms\Components\Toggle::make('is_published')->default(true),
            Forms\Components\TextInput::make('meta_title')->maxLength(255),
            Forms\Components\Textarea::make('meta_description')->rows(2)->columnSpanFull(),
            Forms\Components\RichEditor::make('content')->columnSpanFull(),
            Forms\Components\DateTimePicker::make('published_at'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('locale')->badge(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
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
            'index' => Pages\ListCmsPages::route('/'),
            'create' => Pages\CreateCmsPage::route('/create'),
            'edit' => Pages\EditCmsPage::route('/{record}/edit'),
        ];
    }
}
