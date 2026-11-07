@extends('layouts.public')
@section('title', 'Demo — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Live sandbox demo', 'subtitle' => 'Explore import/export LCs, documents, deadlines, and approvals with pre-seeded data.'])
<section class="mx-auto max-w-4xl px-4 py-16 lg:px-8">
    <p class="text-slate-600 dark:text-slate-400">Use any demo account below with the shared password. Tenant data is reset periodically in shared environments.</p>
    <p class="mt-4 rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900 dark:bg-amber-950/40 dark:border-amber-800 dark:text-amber-100">
        Shared password: <strong class="font-mono">{{ $password }}</strong>
    </p>
    <div class="mt-8 overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-100 dark:bg-slate-800">
                <tr>
                    <th class="px-4 py-3 font-semibold">Email</th>
                    <th class="px-4 py-3 font-semibold">Role</th>
                    <th class="px-4 py-3 font-semibold">Organization</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                @foreach ($accounts as $row)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs sm:text-sm">{{ $row['email'] }}</td>
                        <td class="px-4 py-3">{{ $row['role'] }}</td>
                        <td class="px-4 py-3">{{ $row['organization'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-10 flex flex-wrap gap-4">
        <a href="{{ url('/app/login') }}" class="rounded-lg bg-accent px-6 py-3 font-semibold text-white">Open app login</a>
        <a href="{{ url('/admin/login') }}" class="rounded-lg border border-slate-300 px-6 py-3 font-semibold dark:border-slate-600">Platform admin login</a>
        <a href="{{ route('public.demo.login') }}" class="text-pro font-semibold underline">Demo account cards →</a>
    </div>
</section>
@endsection
