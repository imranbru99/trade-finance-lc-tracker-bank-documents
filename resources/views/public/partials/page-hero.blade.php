@props(['title', 'subtitle' => null])
<section class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
    <div class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl text-navy dark:text-white md:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-lg text-slate-600 dark:text-slate-400">{{ $subtitle }}</p>
        @endif
    </div>
</section>
