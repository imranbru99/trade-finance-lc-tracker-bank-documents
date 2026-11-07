@extends('layouts.public')
@section('title', 'Documentation — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Documentation', 'subtitle' => 'API reference and integration guides for technical teams.'])
<section class="mx-auto max-w-3xl px-4 py-16 font-mono text-sm text-slate-700 dark:text-slate-300 lg:px-8">
    <p class="font-sans text-base text-slate-600 dark:text-slate-400">Base URL: <strong>{{ url('/api/v1') }}</strong></p>
    <ul class="mt-8 list-disc space-y-2 pl-5 font-sans text-base">
        <li>GET /import-lcs — paginated import LC list</li>
        <li>GET /import-lcs/{id} — single import LC</li>
        <li>GET /export-lcs — paginated export LC list</li>
        <li>GET /documents — tenant documents</li>
        <li>GET /shipments — shipments</li>
        <li>GET /deadlines — deadlines</li>
        <li>GET /discrepancies — discrepancies</li>
        <li>GET /payments — payment schedules</li>
        <li>GET /reports/summary — portfolio summary</li>
    </ul>
    <p class="mt-8 font-sans">Generate an API token for a user (SHA-256 hash stored in <code>users.api_token</code>) and pass the raw token in the Authorization header.</p>
</section>
@endsection
