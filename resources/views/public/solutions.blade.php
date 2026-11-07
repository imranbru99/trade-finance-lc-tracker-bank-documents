@extends('layouts.public')
@section('title', 'Solutions — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Solutions', 'subtitle' => 'Purpose-built workflows for importers, exporters, and centralized trade desks.'])
<section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-3">
        <article class="rounded-2xl bg-navy p-8 text-white">
            <h2 class="font-display text-2xl">Import operations</h2>
            <p class="mt-3 text-slate-300">Utilization tracking, supplier LCs, and document checklists aligned to issuing banks.</p>
        </article>
        <article class="rounded-2xl border border-slate-200 p-8 dark:border-slate-700">
            <h2 class="font-display text-2xl text-navy dark:text-white">Export realization</h2>
            <p class="mt-3 text-slate-600 dark:text-slate-400">Presentation deadlines, buyer credits, and realization against export value.</p>
        </article>
        <article class="rounded-2xl border border-accent/30 bg-accent/5 p-8">
            <h2 class="font-display text-2xl text-navy dark:text-white">Shared compliance</h2>
            <p class="mt-3 text-slate-600 dark:text-slate-400">Approvals, audit logs, and discrepancy resolution across branches.</p>
        </article>
    </div>
</section>
@endsection
