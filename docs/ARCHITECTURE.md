# Architecture

## Overview

Trade Finance and LC Tracker is a multi-tenant Laravel application for importers, exporters, and trading houses to manage letters of credit, bank documents, deadlines, shipments, discrepancies, and related workflows. The product combines a public marketing site, a platform administration panel, and a tenant-facing operations panel built on Filament 3.

## Tenancy model

- **Organization** is the tenant boundary. Users belong to one or more organizations through `tenant_users` with role slugs and optional branch scope.
- **Companies** and **branches** model corporate structure inside an organization.
- **`BelongsToOrganization`** is applied to operational models (LCs, documents, deadlines, tasks, webhooks, etc.). It:
  - Auto-fills `organization_id` on create from the Filament tenant or the authenticated user's `current_organization_id`.
  - Applies a global query scope so non–platform-admin users only see rows for their active organization (Filament tenant context or `current_organization_id`).
- **Filament multi-tenancy**: the `/app` panel uses `Organization` as the tenant. Platform admins may switch across all organizations; tenant users only access organizations they are linked to (`User::canAccessTenant`).

## Dual Filament panels

| Panel | Path | Audience |
|-------|------|----------|
| `admin` | `/admin` | Platform administrators (`is_platform_admin`) — subscriptions, platform stats, CMS oversight |
| `app` | `/app` | Tenant users with organization membership — day-to-day trade finance operations |

Panel access is enforced on `User::canAccessPanel`. Navigation items and custom pages can be gated with `HasModuleGatedPage` and `ModuleManager::isEnabled`.

## Module system

`App\Services\ModuleManager` maintains a catalog of **20 modules** (core, trade finance, advanced, optional). `syncCatalog()` upserts definitions into the `modules` table. Per-tenant activation is stored in `tenant_modules` (`is_enabled`, settings, timestamps).

- **Core modules** (`is_core = true`) cannot be disabled.
- **Dependencies** are enforced on enable (required modules must be active) and on disable (dependents must be disabled first).
- **Disabling never deletes data** — records remain in the database; UI and gates hide disabled capabilities.

Optional modules (e.g. `attendance_hr`, `commodity_catalog`) default to off.

## Deadline engine

`App\Services\DeadlineEngine` derives structured `deadlines` from LC date fields (expiry, latest shipment, presentation window) for import and export LCs. For each deadline it generates an **idempotent** reminder schedule (`REMINDER_DAYS`: 90, 60, 30, 15, 7, 3, 1, 0, -1) across `in_app` and `email` channels.

- **Sync** is invoked after LC create/update in Filament import LC pages (and similarly for export).
- **`processPendingReminders`** creates `notification_logs` and marks reminders sent; overdue deadlines are flagged.
- **`checkEscalations`** applies per-organization `escalation_rules` for overdue items.

Scheduled jobs: `ProcessDeadlineRemindersJob` (hourly), `SnapshotFinancialExposuresJob` (daily). Artisan: `trade-finance:process-reminders`.

## API layer

REST endpoints under `/api/v1/*` require:

1. **Bearer token** — SHA-256 hash stored in `users.api_token`
2. **`X-Organization-Id` header** — must be an organization the user can access

Controllers scope data by resolved organization id (explicit `withoutGlobalScopes()` where needed).

## Documents & downloads

Trade documents live on a private disk. **Signed URLs** (`documents.download.signed`, 15-minute TTL) gate downloads; unsigned requests receive 403.

## AI & human review

`AiTradeFinanceService` and Filament **AI Extractions** persist machine-generated field suggestions with `human_review_status` defaulting to **Pending**. Accepted, adjusted, or rejected outcomes are recorded before changes affect authoritative LC/document data. The in-app AI assistant surfaces an explicit human-review disclaimer.

There is **no** live SWIFT network or bank host connectivity — bank correspondence fields are operational records only.

## Integrations (packages)

| Package | Role in this app |
|---------|------------------|
| `filament/filament` | Primary admin and tenant UI |
| `laravel-ai-hub` | Provider abstraction for AI assistant / extractions |
| `laravel-unicode-pdf` | PDF report generation (`TradeFinancePdfService`) |
| `laravel-universal-slug` | CMS pages and knowledge base slugs |
| `drutogoti` | Optional request/query diagnostics tables |
| `laravel-attendance-hub` / `laravel-ai-catalog` | Optional modules, off by default |
| `laravel-admin-panel` | Installed; Filament remains the primary UI |

## Public site

Blade marketing pages (`PublicSiteController`) expose product positioning, pricing, security, FAQ, contact, and feature detail routes. CMS (`CmsPage`) and knowledge articles support published content with slug routing.

## Webhooks

Tenants configure `webhooks` (URL, secret, event list) in the app panel. `WebhookDispatcher` signs JSON payloads (`X-Webhook-Signature`) and dispatches via queue when enabled. Event subscription is configuration-driven; wire domain events to the dispatcher as integrations mature.

## Data layering (simplified)

```
organizations
├── companies → branches → departments
├── tenant_users → users (roles/permissions)
├── tenant_modules → modules (catalog)
├── import_lcs / export_lcs → amendments, documents, deadlines, shipments, …
├── documents (morph) → versions, approvals
├── deadlines → reminders → notification_logs
└── subscriptions → subscription_plans
```

Platform tables (modules, permissions, roles, document_categories, currency_rates) support seeding and cross-tenant defaults.
