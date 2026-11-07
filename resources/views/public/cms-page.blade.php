@extends('layouts.public')
@section('title', ($page->meta_title ?: $page->title).' — '.__('messages.brand'))
@section('meta_description', $page->meta_description)
@section('content')
@include('public.partials.page-hero', ['title' => $page->title])
<section class="mx-auto max-w-3xl px-4 py-16 prose-public dark:text-slate-300 lg:px-8">
    {!! $page->content !!}
</section>
@endsection
