@extends('layouts.public')
@section('title', 'Privacy — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Privacy policy', 'subtitle' => 'How we collect and process information on the marketing site and SaaS application.'])
<section class="mx-auto max-w-3xl px-4 py-16 text-slate-700 dark:text-slate-300 lg:px-8">
    <p>We process account, usage, and contact data to provide the Trade Finance & LC Tracker service. Operational data remains within your tenant boundary and is not sold to third parties.</p>
    <p class="mt-4">Contact form submissions are logged securely for follow-up. For data subject requests, email via the contact page.</p>
</section>
@endsection
