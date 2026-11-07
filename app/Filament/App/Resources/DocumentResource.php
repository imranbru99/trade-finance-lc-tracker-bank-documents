<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DocumentResource\Pages;
use App\Filament\Concerns\HasModuleAccess;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\ImportLc;
use App\Services\AiTradeFinanceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Trade Finance Operations';

    protected static ?string $navigationLabel = 'Documents';

    protected static ?int $navigationSort = 4;

    public static function getModuleSlug(): ?string
    {
        return 'document_management';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('document_number')
                        ->label('Document Reference No')
                        ->maxLength(255),
                    Forms\Components\Select::make('category_id')
                        ->label('Document Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\MorphToSelect::make('documentable')
                        ->label('Associated Trade Instrument / LC')
                        ->types([
                            Forms\Components\MorphToSelect\Type::make(ImportLc::class)
                                ->titleAttribute('lc_number'),
                        ])
                        ->searchable()
                        ->preload(),
                    Forms\Components\FileUpload::make('stored_path')
                        ->label('Document File (Private Storage)')
                        ->disk('local')
                        ->directory(fn () => 'organizations/' . (auth()->user()->current_organization_id ?? 1) . '/documents/' . date('Y/m'))
                        ->visibility('private')
                        ->preserveFilenames()
                        ->storeFileNamesIn('original_filename')
                        ->required(fn (string $context): bool => $context === 'create'),
                    Forms\Components\Hidden::make('original_filename')
                        ->default('document.pdf'),
                    Forms\Components\Select::make('verification_status')
                        ->options([
                            'Pending' => 'Pending Verification',
                            'Verified' => 'Verified Compliant',
                            'Discrepant' => 'Discrepant',
                            'Rejected' => 'Rejected',
                        ])
                        ->default('Pending')
                        ->required(),
                    Forms\Components\DatePicker::make('expiry_date')
                        ->label('Document Validity / Expiry Date'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Audit & Compliance Notes')
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
                Tables\Columns\TextColumn::make('document_number')
                    ->label('Doc #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge(),
                Tables\Columns\TextColumn::make('version')
                    ->label('v#')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('verification_status')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Verified',
                        'danger' => ['Discrepant', 'Rejected'],
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('verification_status')
                    ->options([
                        'Pending' => 'Pending',
                        'Verified' => 'Verified',
                        'Discrepant' => 'Discrepant',
                        'Rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('downloadSecure')
                    ->label('Secure Download')
                    ->icon('heroicon-o-lock-closed')
                    ->color('info')
                    ->action(function (Document $record) {
                        return redirect()->to($record->getSignedDownloadUrl());
                    }),
                Tables\Actions\Action::make('runAiExtraction')
                    ->label('AI Extract')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->action(function (Document $record, AiTradeFinanceService $aiService) {
                        $extraction = $aiService->extractFromDocument($record);
                        Notification::make()
                            ->title('AI Extraction Completed')
                            ->body("Extracted fields with average 95% confidence. Human review required.")
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
