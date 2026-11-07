<?php

namespace App\Filament\Concerns;

trait HasModuleAccess
{
    public static function getModuleSlug(): ?string
    {
        return null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $slug = static::getModuleSlug();
        if (! $slug) {
            return parent::shouldRegisterNavigation();
        }

        return app(\App\Services\ModuleManager::class)->isEnabled($slug) && parent::shouldRegisterNavigation();
    }

    public static function canAccess(): bool
    {
        $slug = static::getModuleSlug();
        if (! $slug) {
            return parent::canAccess();
        }

        return app(\App\Services\ModuleManager::class)->isEnabled($slug);
    }
}
