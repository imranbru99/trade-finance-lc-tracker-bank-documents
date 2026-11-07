@extends('layouts.public')
@section('title', 'Modules — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Modules', 'subtitle' => 'Subscribe only to the trade finance modules your organization needs.'])
<section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
    <div class="grid gap-6 md:grid-cols-2">
        @foreach (['Import LC', 'Export LC', 'Documents', 'Deadlines', 'Shipments', 'Discrepancies', 'Payments', 'Approvals', 'AI Assistant', 'Reporting', 'Webhooks', 'API Access'] as $mod)
            <div class="rounded-xl border border-slate-200 p-5 dark:border-slate-700">
                <h2 class="font-semibold text-navy dark:text-white">{{ $mod }}</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Enable per tenant via the module catalog in your subscription plan.</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
