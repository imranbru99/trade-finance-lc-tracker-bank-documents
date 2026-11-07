@extends('layouts.public')
@section('title', 'FAQ — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'FAQ', 'subtitle' => 'Common questions about deployment, pricing, and data handling.'])
<section class="mx-auto max-w-3xl space-y-8 px-4 py-16 lg:px-8">
    @foreach ([
        ['How do I start the demo?', 'Visit the demo page for shared credentials, then sign in at /app/login.'],
        ['Can we use our own subdomain?', 'Enterprise plans support custom branding and dedicated environments.'],
        ['Where is data hosted?', 'Contact us for current region availability and data residency options.'],
        ['How are API tokens issued?', 'Platform admins or owners can set a user API token (stored hashed) for integrations.'],
    ] as [$q, $a])
        <div>
            <h2 class="font-semibold text-navy dark:text-white">{{ $q }}</h2>
            <p class="mt-2 text-slate-600 dark:text-slate-400">{{ $a }}</p>
        </div>
    @endforeach
</section>
@endsection
