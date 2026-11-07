@extends('layouts.public')
@section('title', 'Demo login — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Choose a demo account', 'subtitle' => 'Each card links to the tenant app login. Use password '.$password.'.'])
<section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($accounts as $row)
            <a href="{{ url('/app/login') }}" class="block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-accent hover:shadow-lg dark:border-slate-700 dark:bg-slate-900">
                <p class="text-xs font-semibold uppercase tracking-wide text-accent">{{ $row['organization'] }}</p>
                <h2 class="mt-2 text-lg font-semibold text-navy dark:text-white">{{ $row['role'] }}</h2>
                <p class="mt-3 font-mono text-sm text-slate-600 dark:text-slate-400">{{ $row['email'] }}</p>
                <p class="mt-4 text-sm text-slate-500">Password: <span class="font-mono">{{ $password }}</span></p>
                <p class="mt-4 text-sm font-semibold text-pro">Continue to login →</p>
            </a>
        @endforeach
    </div>
    <p class="mt-10 text-center text-sm text-slate-500">Credentials are for demonstration only. <a href="{{ route('public.demo') }}" class="text-accent underline">Full demo details</a></p>
</section>
@endsection
