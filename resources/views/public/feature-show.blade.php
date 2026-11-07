@extends('layouts.public')
@section('title', $feature['title'].' — '.__('messages.brand'))
@section('meta_description', $feature['summary'])
@section('content')
@include('public.partials.page-hero', ['title' => $feature['title'], 'subtitle' => $feature['headline']])
<section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
    <p class="max-w-3xl text-lg text-slate-600 dark:text-slate-400">{{ $feature['summary'] }}</p>
    <ul class="mt-10 grid gap-4 sm:grid-cols-2">
        @foreach ($feature['benefits'] as $benefit)
            <li class="flex gap-3 rounded-lg border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                <span class="font-bold text-accent">→</span>
                <span>{{ $benefit }}</span>
            </li>
        @endforeach
    </ul>
    <div class="mt-12 flex gap-4">
        <a href="{{ route('public.demo') }}" class="rounded-lg bg-accent px-5 py-2.5 font-semibold text-white">Try demo</a>
        <a href="{{ route('public.contact') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 font-semibold dark:border-slate-600">Contact sales</a>
    </div>
</section>
@endsection
