@extends('layouts.public')
@section('title', 'Security — '.__('messages.brand'))
@section('content')
@include('public.partials.page-hero', ['title' => 'Security', 'subtitle' => 'Tenant isolation, encryption in transit, and auditable access for regulated trade operations.'])
<section class="mx-auto max-w-3xl space-y-6 px-4 py-16 text-slate-700 dark:text-slate-300 lg:px-8">
    <p><strong class="text-navy dark:text-white">Multi-tenant isolation:</strong> Organization-scoped data with RBAC and branch-level scoping.</p>
    <p><strong class="text-navy dark:text-white">Document access:</strong> Private storage disks with time-limited signed download URLs.</p>
    <p><strong class="text-navy dark:text-white">API security:</strong> Bearer tokens stored as SHA-256 hashes; organization header required on API v1.</p>
    <p><strong class="text-navy dark:text-white">Auditability:</strong> Immutable-style audit logs for sensitive events and approval history.</p>
</section>
@endsection
