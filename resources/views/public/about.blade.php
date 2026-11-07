@extends('layouts.public')
@section('title', 'About — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'About us', 'subtitle' => 'We help trade finance teams ship faster compliance with fewer missed dates.'])
<section class="mx-auto max-w-3xl px-4 py-16 prose-public text-slate-700 dark:text-slate-300 lg:px-8">
    <p>Trade Finance & LC Tracker is a multi-tenant SaaS platform for importers, exporters, and trading organizations that manage documentary credits at scale.</p>
    <p>Our mission is to give operations, compliance, and treasury a shared source of truth—from LC issuance through document presentation, discrepancy resolution, and payment maturity.</p>
</section>
@endsection
