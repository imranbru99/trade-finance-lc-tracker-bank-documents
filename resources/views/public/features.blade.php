@extends('layouts.public')
@section('title', 'Features — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Features', 'subtitle' => 'Modular capabilities for documentary credit lifecycle management.'])
<section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($features as $slug => $feature)
            <a href="{{ route('public.features.show', $slug) }}" class="rounded-xl border border-slate-200 bg-white p-6 hover:border-accent dark:border-slate-700 dark:bg-slate-900">
                <h2 class="text-lg font-semibold text-navy dark:text-white">{{ $feature['title'] }}</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $feature['summary'] }}</p>
            </a>
        @endforeach
    </div>
</section>
@endsection
