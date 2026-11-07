@extends('layouts.public')
@section('title', 'Pricing — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Pricing', 'subtitle' => 'Start free, scale to enterprise—with modules that match your documentary credit volume.'])
<section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
    <div class="grid gap-6 lg:grid-cols-5">
        @foreach ($plans as $plan)
            <div class="flex flex-col rounded-2xl border p-6 {{ !empty($plan['highlighted']) ? 'border-accent shadow-xl ring-2 ring-accent/20' : 'border-slate-200 dark:border-slate-700' }} bg-white dark:bg-slate-900">
                <h2 class="text-lg font-semibold">{{ $plan['name'] }}</h2>
                <p class="mt-3 font-display text-4xl text-navy dark:text-white">{{ $plan['price'] }}</p>
                <p class="mt-4 flex-1 text-sm text-slate-600 dark:text-slate-400">{{ $plan['description'] }}</p>
                <a href="{{ url($plan['href']) }}" class="mt-6 block rounded-lg bg-navy py-2.5 text-center text-sm font-semibold text-white hover:bg-navy-600">{{ $plan['cta'] }}</a>
            </div>
        @endforeach
    </div>
    <p class="mt-10 text-center text-sm text-slate-500">Existing customers: <a href="{{ url('/app/login') }}" class="text-accent underline">sign in to the app</a>.</p>
</section>
@endsection
