@extends('layouts.public')
@section('title', 'Knowledge Base — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Knowledge Base', 'subtitle' => 'Guides and best practices for documentary credit operations.'])
<section class="mx-auto max-w-4xl px-4 py-16 lg:px-8">
    @forelse ($articles as $article)
        <a href="{{ route('public.knowledge.show', $article->slug) }}" class="mb-4 block rounded-lg border border-slate-200 p-5 hover:border-accent dark:border-slate-700">
            <span class="text-xs font-semibold uppercase text-accent">{{ $article->category }}</span>
            <h2 class="mt-1 text-lg font-semibold text-navy dark:text-white">{{ $article->title }}</h2>
            @if ($article->summary)
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ $article->summary }}</p>
            @endif
        </a>
    @empty
        <p class="text-slate-600 dark:text-slate-400">Articles will appear here once published in the admin CMS.</p>
    @endforelse
</section>
@endsection
