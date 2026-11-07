@extends('layouts.public')
@section('title', 'Integrations — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Integrations', 'subtitle' => 'REST API, webhooks, and integration connections for your ERP and treasury stack.'])
<section class="mx-auto max-w-3xl px-4 py-16 lg:px-8">
    <h2 class="text-xl font-semibold text-navy dark:text-white">REST API v1</h2>
    <p class="mt-3 text-slate-600 dark:text-slate-400">Authenticate with <code class="rounded bg-slate-100 px-1 dark:bg-slate-800">Authorization: Bearer &lt;token&gt;</code> and pass <code class="rounded bg-slate-100 px-1 dark:bg-slate-800">X-Organization-Id</code> on each request.</p>
    <h2 class="mt-10 text-xl font-semibold text-navy dark:text-white">Outbound webhooks</h2>
    <p class="mt-3 text-slate-600 dark:text-slate-400">Subscribe to LC, document, and deadline events with HMAC-signed payloads.</p>
    <a href="{{ route('public.documentation') }}" class="mt-8 inline-block font-semibold text-accent">Read API documentation →</a>
</section>
@endsection
