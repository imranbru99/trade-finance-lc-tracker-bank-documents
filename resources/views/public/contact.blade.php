@extends('layouts.public')
@section('title', 'Contact — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Contact', 'subtitle' => 'Sales, partnerships, and product questions.'])
<section class="mx-auto max-w-xl px-4 py-16 lg:px-8">
    @if (session('contact_success'))
        <div class="mb-8 rounded-lg border border-accent/30 bg-accent/10 p-4 text-accent" role="alert">
            Thank you—we received your message and will respond shortly.
        </div>
    @endif
    <form method="post" action="{{ route('public.contact.store') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium" for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 dark:border-slate-600 dark:bg-slate-900">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium" for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 dark:border-slate-600 dark:bg-slate-900">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium" for="company">Company</label>
                <input type="text" name="company" id="company" value="{{ old('company') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 dark:border-slate-600 dark:bg-slate-900">
            </div>
            <div>
                <label class="block text-sm font-medium" for="phone">Phone</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 dark:border-slate-600 dark:bg-slate-900">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium" for="subject">Subject</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 dark:border-slate-600 dark:bg-slate-900">
            @error('subject')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium" for="message">Message</label>
            <textarea name="message" id="message" rows="5" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 dark:border-slate-600 dark:bg-slate-900">{{ old('message') }}</textarea>
            @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full rounded-lg bg-navy py-3 font-semibold text-white hover:bg-navy-600">Send message</button>
    </form>
</section>
@endsection
