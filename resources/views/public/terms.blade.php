@extends('layouts.public')
@section('title', 'Terms — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Terms of service', 'subtitle' => 'Agreement governing use of the platform and demo environments.'])
<section class="mx-auto max-w-3xl px-4 py-16 text-slate-700 dark:text-slate-300 lg:px-8">
    <p>By accessing the application you agree to acceptable use policies, subscription terms, and confidentiality of bank and counterparty data entered into the system.</p>
    <p class="mt-4">Demo environments may be reset without notice; do not upload production confidential data to shared sandboxes.</p>
</section>
@endsection
