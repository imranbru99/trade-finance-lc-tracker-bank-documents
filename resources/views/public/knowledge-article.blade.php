@extends('layouts.public')
@section('title', $article->title.' — '.__('messages.brand'))
@section('meta_description', $article->summary)
@section('content')
@include('public.partials.page-hero', ['title' => $article->title, 'subtitle' => $article->summary])
<section class="mx-auto max-w-3xl px-4 py-16 prose-public dark:text-slate-300 lg:px-8">
    {!! nl2br(e($article->content)) !!}
</section>
@endsection
