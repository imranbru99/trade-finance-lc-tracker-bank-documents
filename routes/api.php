<?php

use App\Http\Controllers\Api\V1\DeadlineController;
use App\Http\Controllers\Api\V1\DiscrepancyController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\ExportLcController;
use App\Http\Controllers\Api\V1\ImportLcController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\ShipmentController;
use App\Http\Middleware\ResolveApiOrganization;
use App\Http\Middleware\VerifyApiToken;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware([VerifyApiToken::class, ResolveApiOrganization::class])
    ->group(function () {
        Route::get('import-lcs', [ImportLcController::class, 'index']);
        Route::get('import-lcs/{id}', [ImportLcController::class, 'show'])->whereNumber('id');
        Route::get('export-lcs', [ExportLcController::class, 'index']);
        Route::get('documents', [DocumentController::class, 'index']);
        Route::get('shipments', [ShipmentController::class, 'index']);
        Route::get('deadlines', [DeadlineController::class, 'index']);
        Route::get('discrepancies', [DiscrepancyController::class, 'index']);
        Route::get('payments', [PaymentController::class, 'index']);
        Route::get('reports/summary', [ReportController::class, 'summary']);
    });
