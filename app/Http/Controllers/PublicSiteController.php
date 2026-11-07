<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\KnowledgeArticle;
use App\Support\MarketingCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PublicSiteController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'features' => MarketingCatalog::features(),
            'plans' => MarketingCatalog::pricingPlans(),
        ]);
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function features(): View
    {
        return view('public.features', ['features' => MarketingCatalog::features()]);
    }

    public function featureShow(string $slug): View
    {
        $features = MarketingCatalog::features();
        abort_unless(isset($features[$slug]), 404);

        return view('public.feature-show', [
            'slug' => $slug,
            'feature' => $features[$slug],
            'allFeatures' => $features,
        ]);
    }

    public function modules(): View
    {
        return view('public.modules');
    }

    public function solutions(): View
    {
        return view('public.solutions');
    }

    public function pricing(): View
    {
        return view('public.pricing', ['plans' => MarketingCatalog::pricingPlans()]);
    }

    public function demo(): View
    {
        return view('public.demo', [
            'accounts' => MarketingCatalog::demoAccounts(),
            'password' => MarketingCatalog::DEMO_PASSWORD,
        ]);
    }

    public function demoLogin(): View
    {
        return view('public.demo-login', [
            'accounts' => MarketingCatalog::demoAccounts(),
            'password' => MarketingCatalog::DEMO_PASSWORD,
        ]);
    }

    public function security(): View
    {
        return view('public.security');
    }

    public function integrations(): View
    {
        return view('public.integrations');
    }

    public function documentation(): View
    {
        return view('public.documentation');
    }

    public function knowledgeBase(): View
    {
        $articles = KnowledgeArticle::query()
            ->where('is_published', true)
            ->orderByDesc('updated_at')
            ->get();

        return view('public.knowledge-base', compact('articles'));
    }

    public function knowledgeArticle(string $slug): View
    {
        $article = KnowledgeArticle::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $article->increment('views_count');

        return view('public.knowledge-article', compact('article'));
    }

    public function faq(): View
    {
        return view('public.faq');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function contactStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Log::info('Public contact form submission', $validated);

        return redirect()
            ->route('public.contact')
            ->with('contact_success', true);
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }

    public function terms(): View
    {
        return view('public.terms');
    }

    public function dataProtection(): View
    {
        return view('public.data-protection');
    }

    public function cmsPage(string $slug): View
    {
        $page = CmsPage::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('public.cms-page', compact('page'));
    }

    public function sitemap(): Response
    {
        $base = config('app.url');
        $urls = [
            '', 'about', 'features', 'modules', 'solutions', 'pricing', 'demo', 'demo/login',
            'security', 'integrations', 'documentation', 'knowledge-base', 'faq', 'contact',
            'privacy', 'terms', 'data-protection',
        ];

        foreach (MarketingCatalog::featureSlugs() as $slug) {
            $urls[] = 'features/'.$slug;
        }

        KnowledgeArticle::query()->where('is_published', true)->pluck('slug')->each(function ($slug) use (&$urls) {
            $urls[] = 'knowledge-base/'.$slug;
        });

        CmsPage::query()->where('is_published', true)->pluck('slug')->each(function ($slug) use (&$urls) {
            $urls[] = 'pages/'.$slug;
        });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $path) {
            $loc = rtrim($base, '/').'/'.ltrim($path, '/');
            $xml .= '<url><loc>'.e($loc).'</loc></url>';
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        $body = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /app\nSitemap: ".rtrim(config('app.url'), '/')."/sitemap.xml\n";

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }
}
