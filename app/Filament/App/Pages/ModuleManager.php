<?php

namespace App\Filament\App\Pages;

use App\Models\Module;
use App\Services\ModuleManager as ModuleManagerService;
use Exception;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ModuleManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Organization & Settings';

    protected static ?string $navigationLabel = 'Module Manager';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.app.pages.module-manager';

    public function table(Table $table): Table
    {
        return $table
            ->query(Module::query()->orderBy('category')->orderBy('name'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(60)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('tenant_enabled')
                    ->label('Enabled')
                    ->state(function (Module $record): bool {
                        $tenant = Filament::getTenant();
                        if (! $tenant) {
                            return (bool) $record->default_enabled;
                        }

                        return app(ModuleManagerService::class)->isEnabled($record->slug, $tenant);
                    })
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('enable')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function (Module $record) {
                        $tenant = Filament::getTenant();
                        if (! $tenant) {
                            return false;
                        }

                        return ! app(ModuleManagerService::class)->isEnabled($record->slug, $tenant);
                    })
                    ->action(function (Module $record, ModuleManagerService $manager) {
                        $tenant = Filament::getTenant();
                        if (! $tenant) {
                            return;
                        }
                        try {
                            $manager->enable($record->slug, $tenant);
                            Notification::make()->title("{$record->name} enabled")->success()->send();
                        } catch (Exception $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
                Tables\Actions\Action::make('disable')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(function (Module $record) {
                        if ($record->is_core) {
                            return false;
                        }
                        $tenant = Filament::getTenant();
                        if (! $tenant) {
                            return false;
                        }

                        return app(ModuleManagerService::class)->isEnabled($record->slug, $tenant);
                    })
                    ->requiresConfirmation()
                    ->action(function (Module $record, ModuleManagerService $manager) {
                        $tenant = Filament::getTenant();
                        if (! $tenant) {
                            return;
                        }
                        try {
                            $manager->disable($record->slug, $tenant);
                            Notification::make()->title("{$record->name} disabled")->warning()->send();
                        } catch (Exception $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),
            ])
            ->paginated([25, 50, 100]);
    }
}
