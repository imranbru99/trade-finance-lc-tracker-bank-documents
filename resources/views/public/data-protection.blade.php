@extends('layouts.public')
@section('title', 'Data protection — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Data protection', 'subtitle' => 'Retention, subprocessors, and tenant responsibilities.'])
<section class="mx-auto max-w-3xl px-4 py-16 text-slate-700 dark:text-slate-300 lg:px-8">
    <p>Tenants control trade finance data uploaded to the platform. We apply encryption in transit, access controls, and backup procedures aligned with SaaS best practices.</p>
    <p class="mt-4">Enterprise customers may request a DPA and details on subprocessors during onboarding.</p>
</section>
@endsection
