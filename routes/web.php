<?php

use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\PublicSiteController;
use App\Support\MarketingCatalog;
use Illuminate\Support\Facades\Route;

Route::get('/documents/download/{document}', DocumentDownloadController::class)
    ->name('documents.download.signed')
    ->middleware('signed');

Route::get('/sitemap.xml', [PublicSiteController::class, 'sitemap'])->name('public.sitemap');
Route::get('/robots.txt', [PublicSiteController::class, 'robots'])->name('public.robots');

Route::controller(PublicSiteController::class)->group(function () {
    Route::get('/', 'home')->name('public.home');
    Route::get('/about', 'about')->name('public.about');
    Route::get('/features', 'features')->name('public.features');
    Route::get('/modules', 'modules')->name('public.modules');
    Route::get('/solutions', 'solutions')->name('public.solutions');
    Route::get('/pricing', 'pricing')->name('public.pricing');
    Route::get('/demo', 'demo')->name('public.demo');
    Route::get('/demo/login', 'demoLogin')->name('public.demo.login');
    Route::get('/security', 'security')->name('public.security');
    Route::get('/integrations', 'integrations')->name('public.integrations');
    Route::get('/documentation', 'documentation')->name('public.documentation');
    Route::get('/knowledge-base', 'knowledgeBase')->name('public.knowledge.index');
    Route::get('/knowledge-base/{slug}', 'knowledgeArticle')->name('public.knowledge.show');
    Route::get('/faq', 'faq')->name('public.faq');
    Route::get('/contact', 'contact')->name('public.contact');
    Route::post('/contact', 'contactStore')->name('public.contact.store');
    Route::get('/privacy', 'privacy')->name('public.privacy');
    Route::get('/terms', 'terms')->name('public.terms');
    Route::get('/data-protection', 'dataProtection')->name('public.data-protection');
    Route::get('/pages/{slug}', 'cmsPage')->name('public.cms.show');
});

Route::get('/features/{slug}', [PublicSiteController::class, 'featureShow'])
    ->whereIn('slug', MarketingCatalog::featureSlugs())
    ->name('public.features.show');
