@extends('layouts.public')

@section('title', __('messages.brand').' — '.__('messages.tagline'))

@section('content')
    {{-- Full-bleed hero --}}
    <section class="hero-mesh relative overflow-hidden text-white">
        <div class="mx-auto flex min-h-[88vh] max-w-7xl flex-col justify-center px-4 py-24 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-accent-light">Premium trade finance SaaS</p>
            <h1 class="mt-4 max-w-4xl font-display text-5xl leading-tight md:text-6xl lg:text-7xl">
                {{ __('messages.hero.title') }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg text-slate-200 md:text-xl">
                {{ __('messages.hero.subtitle') }}
            </p>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('public.demo') }}" class="rounded-lg bg-accent px-6 py-3 font-semibold text-white shadow-xl hover:bg-accent-light">
                    {{ __('messages.hero.cta_primary') }}
                </a>
                <a href="{{ route('public.pricing') }}" class="rounded-lg border border-white/30 px-6 py-3 font-semibold text-white hover:bg-white/10">
                    {{ __('messages.hero.cta_secondary') }}
                </a>
            </div>
        </div>
    </section>

    {{-- Product intro --}}
    <section class="bg-white py-20 dark:bg-slate-900">
        <div class="mx-auto grid max-w-7xl gap-12 px-4 lg:grid-cols-2 lg:items-center lg:px-8">
            <div>
                <h2 class="font-display text-4xl text-navy dark:text-white">Built for documentary credit operations</h2>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">
                    Replace fragmented spreadsheets and email threads with a single system of record for LCs, bank documents, discrepancies, and payment maturity—accessible to operations, compliance, and treasury.
                </p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 dark:border-slate-700 dark:bg-slate-800">
                <ul class="space-y-4 text-slate-700 dark:text-slate-300">
                    <li class="flex gap-3"><span class="text-accent">✓</span> Import & export LC registers with utilization</li>
                    <li class="flex gap-3"><span class="text-accent">✓</span> Private document vault with signed downloads</li>
                    <li class="flex gap-3"><span class="text-accent">✓</span> Deadline engine with reminder cadence</li>
                    <li class="flex gap-3"><span class="text-accent">✓</span> REST API & webhooks for integrations</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Feature overview --}}
    <section class="border-y border-slate-200 bg-slate-50 py-20 dark:border-slate-800 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <h2 class="font-display text-4xl text-navy dark:text-white">Everything your trade desk needs</h2>
            <p class="mt-3 max-w-2xl text-slate-600 dark:text-slate-400">Modular capabilities you can enable per subscription—no bloated bundles.</p>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (array_slice($features, 0, 6, true) as $slug => $feature)
                    <a href="{{ route('public.features.show', $slug) }}" class="group rounded-xl border border-slate-200 bg-white p-6 transition hover:border-accent/40 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="text-lg font-semibold text-navy group-hover:text-accent dark:text-white">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $feature['summary'] }}</p>
                    </a>
                @endforeach
            </div>
            <div class="mt-10 text-center">
                <a href="{{ route('public.features') }}" class="font-semibold text-pro hover:text-accent">View all features →</a>
            </div>
        </div>
    </section>

    {{-- Workflow --}}
    <section class="bg-white py-20 dark:bg-slate-900">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <h2 class="font-display text-4xl text-navy dark:text-white">From issuance to settlement</h2>
            <ol class="mt-12 grid gap-8 md:grid-cols-4">
                @foreach (['Capture LC & bank terms', 'Collect & verify documents', 'Track shipments & discrepancies', 'Mature payments & report'] as $i => $step)
                    <li class="relative border-l-4 border-accent pl-6">
                        <span class="text-sm font-bold text-accent">Step {{ $i + 1 }}</span>
                        <p class="mt-2 font-semibold text-navy dark:text-white">{{ $step }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Deadline tracking --}}
    <section class="bg-navy py-20 text-white">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                <div>
                    <h2 class="font-display text-4xl">Never miss an LC expiry or presentation date</h2>
                    <p class="mt-4 text-slate-300">Deadlines roll up from LCs, shipments, discrepancies, and payment schedules—with accountable owners and escalating reminders.</p>
                </div>
                <div class="rounded-2xl bg-white/10 p-8 backdrop-blur">
                    <p class="text-sm uppercase tracking-wide text-accent-light">Reminder cadence</p>
                    <p class="mt-2 text-2xl font-semibold">90 · 60 · 30 · 15 · 7 · 3 · 1 · Due · Overdue</p>
                </div>
            </div>
        </div>
    </section>

    {{-- AI capabilities --}}
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <h2 class="font-display text-4xl text-navy dark:text-white">AI-assisted trade intelligence</h2>
            <p class="mt-4 max-w-2xl text-slate-600 dark:text-slate-400">Upload LC PDFs and document packs for extraction jobs, clause surfacing, and guided review—always with human approval.</p>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <div class="rounded-xl border border-slate-200 p-6 dark:border-slate-700">
                    <h3 class="font-semibold text-navy dark:text-white">Document extraction</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Structured fields from unstructured bank docs.</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-6 dark:border-slate-700">
                    <h3 class="font-semibold text-navy dark:text-white">Risk hints</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Flag unusual clauses and mismatches early.</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-6 dark:border-slate-700">
                    <h3 class="font-semibold text-navy dark:text-white">Assistant sessions</h3>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Context-aware Q&A for officers and auditors.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Business types --}}
    <section class="border-t border-slate-200 bg-slate-50 py-20 dark:border-slate-800 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <h2 class="font-display text-4xl text-navy dark:text-white">Trusted across trade finance teams</h2>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (['Importers & trading houses', 'Export manufacturers', 'Commodity traders', 'Multi-branch conglomerates'] as $type)
                    <div class="rounded-lg bg-white p-6 text-center font-medium text-navy shadow-sm dark:bg-slate-900 dark:text-white">{{ $type }}</div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Security --}}
    <section class="bg-white py-20 dark:bg-slate-900">
        <div class="mx-auto max-w-7xl px-4 text-center lg:px-8">
            <h2 class="font-display text-4xl text-navy dark:text-white">Enterprise-grade security</h2>
            <p class="mx-auto mt-4 max-w-2xl text-slate-600 dark:text-slate-400">Tenant isolation, role-based access, audit logs, signed document URLs, and API token authentication.</p>
            <a href="{{ route('public.security') }}" class="mt-8 inline-block font-semibold text-accent hover:underline">Read our security overview</a>
        </div>
    </section>

    {{-- Pricing preview --}}
    <section class="py-20">
        <div class="mx-auto max-w-7xl px-4 lg:px-8">
            <h2 class="font-display text-4xl text-navy dark:text-white">Transparent plans</h2>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach (array_slice($plans, 0, 3) as $plan)
                    <div class="rounded-xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900">
                        <h3 class="text-lg font-semibold">{{ $plan['name'] }}</h3>
                        <p class="mt-2 text-3xl font-display text-navy dark:text-white">{{ $plan['price'] }}<span class="text-base font-sans text-slate-500">/mo</span></p>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">{{ $plan['description'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('public.pricing') }}" class="rounded-lg bg-navy px-6 py-3 font-semibold text-white hover:bg-navy-600">Compare all plans</a>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="border-t border-slate-200 bg-slate-50 py-20 dark:border-slate-800 dark:bg-slate-950">
        <div class="mx-auto max-w-3xl px-4 lg:px-8">
            <h2 class="font-display text-4xl text-navy dark:text-white text-center">Frequently asked questions</h2>
            <dl class="mt-10 space-y-6">
                <div>
                    <dt class="font-semibold text-navy dark:text-white">Is there a live demo?</dt>
                    <dd class="mt-2 text-slate-600 dark:text-slate-400">Yes—use our sandbox tenants with pre-loaded LCs and documents. See the <a href="{{ route('public.demo') }}" class="text-accent underline">demo page</a> for credentials.</dd>
                </div>
                <div>
                    <dt class="font-semibold text-navy dark:text-white">Do you support API integrations?</dt>
                    <dd class="mt-2 text-slate-600 dark:text-slate-400">REST API v1 with Bearer tokens, organization scoping, and outbound webhooks for event-driven ERP connectivity.</dd>
                </div>
                <div>
                    <dt class="font-semibold text-navy dark:text-white">Can we operate multiple branches?</dt>
                    <dd class="mt-2 text-slate-600 dark:text-slate-400">Business and Enterprise plans include multi-branch RBAC with consolidated reporting.</dd>
                </div>
            </dl>
            <p class="mt-8 text-center"><a href="{{ route('public.faq') }}" class="font-semibold text-pro">More answers →</a></p>
        </div>
    </section>

    {{-- CTA --}}
    <section class="hero-mesh py-24 text-center text-white">
        <div class="mx-auto max-w-2xl px-4">
            <h2 class="font-display text-4xl">Ready to modernize your LC operations?</h2>
            <p class="mt-4 text-slate-200">Launch the demo in minutes or speak with our team about enterprise rollout.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ url('/app/login') }}" class="rounded-lg bg-white px-6 py-3 font-semibold text-navy">Sign in to app</a>
                <a href="{{ route('public.contact') }}" class="rounded-lg border border-white/40 px-6 py-3 font-semibold text-white hover:bg-white/10">Contact sales</a>
            </div>
        </div>
    </section>
@endsection
