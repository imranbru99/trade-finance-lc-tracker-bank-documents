<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('messages.brand'))</title>
    <meta name="description" content="@yield('meta_description', __('messages.tagline'))">
    @stack('meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"DM Serif Display"', 'Georgia', 'serif'],
                        sans: ['"Source Sans 3"', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            DEFAULT: '#0B1F3A',
                            800: '#0B1F3A',
                            700: '#123052',
                            600: '#1A3D66',
                        },
                        pro: {
                            DEFAULT: '#1D4ED8',
                            light: '#3B82F6',
                        },
                        accent: {
                            DEFAULT: '#0D9488',
                            light: '#14B8A6',
                        },
                    },
                },
            },
        };
    </script>
    <style>
        :root {
            --color-navy: #0B1F3A;
            --color-pro: #1D4ED8;
            --color-accent: #0D9488;
        }
        .hero-mesh {
            background:
                radial-gradient(ellipse 80% 60% at 70% 20%, rgba(13, 148, 136, 0.18), transparent),
                radial-gradient(ellipse 60% 50% at 20% 80%, rgba(29, 78, 216, 0.15), transparent),
                linear-gradient(165deg, #0B1F3A 0%, #123052 45%, #0B1F3A 100%);
        }
        .prose-public a { color: var(--color-accent); text-decoration: underline; }
    </style>
    @stack('head')
</head>
<body class="font-sans text-slate-800 bg-slate-50 antialiased dark:bg-slate-950 dark:text-slate-100">
    <header class="sticky top-0 z-50 border-b border-white/10 bg-navy/95 backdrop-blur-md dark:bg-navy">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 lg:px-8">
            <a href="{{ route('public.home') }}" class="font-display text-xl text-white lg:text-2xl">
                {{ __('messages.brand') }}
            </a>
            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-200 lg:flex" aria-label="Primary">
                <a href="{{ route('public.features') }}" class="hover:text-white">{{ __('messages.nav.features') }}</a>
                <a href="{{ route('public.solutions') }}" class="hover:text-white">{{ __('messages.nav.solutions') }}</a>
                <a href="{{ route('public.pricing') }}" class="hover:text-white">{{ __('messages.nav.pricing') }}</a>
                <a href="{{ route('public.demo') }}" class="hover:text-white">{{ __('messages.nav.demo') }}</a>
                <a href="{{ route('public.documentation') }}" class="hover:text-white">{{ __('messages.nav.docs') }}</a>
                <a href="{{ route('public.contact') }}" class="hover:text-white">{{ __('messages.nav.contact') }}</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ url('/app/login') }}" class="hidden rounded-lg border border-white/25 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10 sm:inline-block">
                    {{ __('messages.nav.login') }}
                </a>
                <a href="{{ route('public.demo') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-accent/30 hover:bg-accent-light">
                    {{ __('messages.hero.cta_primary') }}
                </a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 lg:grid-cols-4 lg:px-8">
            <div class="lg:col-span-2">
                <p class="font-display text-2xl text-navy dark:text-white">{{ __('messages.brand') }}</p>
                <p class="mt-3 max-w-md text-slate-600 dark:text-slate-400">{{ __('messages.tagline') }}</p>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Product</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('public.features') }}" class="text-slate-700 hover:text-accent dark:text-slate-300">Features</a></li>
                    <li><a href="{{ route('public.modules') }}" class="text-slate-700 hover:text-accent dark:text-slate-300">Modules</a></li>
                    <li><a href="{{ route('public.integrations') }}" class="text-slate-700 hover:text-accent dark:text-slate-300">Integrations</a></li>
                    <li><a href="{{ route('public.security') }}" class="text-slate-700 hover:text-accent dark:text-slate-300">Security</a></li>
                </ul>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Company</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('public.about') }}" class="text-slate-700 hover:text-accent dark:text-slate-300">About</a></li>
                    <li><a href="{{ route('public.knowledge.index') }}" class="text-slate-700 hover:text-accent dark:text-slate-300">Knowledge Base</a></li>
                    <li><a href="{{ route('public.faq') }}" class="text-slate-700 hover:text-accent dark:text-slate-300">FAQ</a></li>
                    <li><a href="{{ route('public.contact') }}" class="text-slate-700 hover:text-accent dark:text-slate-300">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-200 dark:border-slate-800">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <span>&copy; {{ date('Y') }} {{ __('messages.brand') }}. {{ __('messages.footer.rights') }}</span>
                <div class="flex gap-4">
                    <a href="{{ route('public.privacy') }}" class="hover:text-accent">{{ __('messages.footer.privacy') }}</a>
                    <a href="{{ route('public.terms') }}" class="hover:text-accent">{{ __('messages.footer.terms') }}</a>
                    <a href="{{ route('public.data-protection') }}" class="hover:text-accent">{{ __('messages.footer.data_protection') }}</a>
                </div>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
