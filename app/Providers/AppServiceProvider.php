<?php

namespace App\Providers;

use App\Services\AiTradeFinanceService;
use App\Services\ApprovalEngine;
use App\Services\DeadlineEngine;
use App\Services\DocumentManager;
use App\Services\FinancialExposureService;
use App\Services\ModuleManager;
use App\Services\TradeFinancePdfService;
use App\Services\WebhookDispatcher;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class);
        $this->app->singleton(DeadlineEngine::class);
        $this->app->singleton(DocumentManager::class);
        $this->app->singleton(ApprovalEngine::class);
        $this->app->singleton(AiTradeFinanceService::class);
        $this->app->singleton(FinancialExposureService::class);
        $this->app->singleton(TradeFinancePdfService::class);
        $this->app->singleton(WebhookDispatcher::class);
    }

    public function boot(): void
    {
        //
    }
}
