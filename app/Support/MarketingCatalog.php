<?php

namespace App\Support;

class MarketingCatalog
{
    public const DEMO_PASSWORD = 'DemoPass!2026';

    /**
     * @return array<int, array{email: string, role: string, organization: string}>
     */
    public static function demoAccounts(): array
    {
        return [
            ['email' => 'demo.owner@trade-finance.demo', 'role' => 'Organization Owner', 'organization' => 'Global Import Corp'],
            ['email' => 'demo.manager@trade-finance.demo', 'role' => 'Trade Finance Manager', 'organization' => 'Global Import Corp'],
            ['email' => 'demo.ops@trade-finance.demo', 'role' => 'Operations Officer', 'organization' => 'Pacific Exports Ltd'],
            ['email' => 'demo.compliance@trade-finance.demo', 'role' => 'Compliance Analyst', 'organization' => 'Pacific Exports Ltd'],
            ['email' => 'demo.viewer@trade-finance.demo', 'role' => 'Read-only Auditor', 'organization' => 'Hanseatic Trading AG'],
        ];
    }

    /**
     * @return array<string, array{title: string, headline: string, summary: string, benefits: array<int, string>}>
     */
    public static function features(): array
    {
        return [
            'import-lc' => [
                'title' => 'Import LC Management',
                'headline' => 'Control every import letter of credit from issuance to settlement.',
                'summary' => 'Centralize LC data, utilization, and bank relationships for importers and trading houses.',
                'benefits' => ['Real-time utilization tracking', 'Bank & beneficiary records', 'Amendment history', 'Document linkage'],
            ],
            'export-lc' => [
                'title' => 'Export LC Management',
                'headline' => 'Accelerate export LC realization and presentation compliance.',
                'summary' => 'Track buyer credits, presentation deadlines, and realization against export contracts.',
                'benefits' => ['Presentation deadline alerts', 'Realization vs pending amounts', 'Buyer & contract references', 'Status workflows'],
            ],
            'bank-documents' => [
                'title' => 'Bank Document Vault',
                'headline' => 'Versioned, auditable trade documents in one secure vault.',
                'summary' => 'Manage bills of lading, invoices, certificates, and compliance packs with verification status.',
                'benefits' => ['Version control', 'Verification workflow', 'Signed download links', 'LC-linked checklists'],
            ],
            'lc-amendments' => [
                'title' => 'LC Amendments',
                'headline' => 'Never miss an amendment impact on amount, dates, or clauses.',
                'summary' => 'Log bank amendments with before/after values and approval trails.',
                'benefits' => ['Structured amendment logs', 'Amount & date deltas', 'Approval integration', 'Audit history'],
            ],
            'lc-expiry' => [
                'title' => 'LC Expiry Intelligence',
                'headline' => 'Proactive expiry visibility across your LC portfolio.',
                'summary' => 'Dashboards and alerts for credits approaching expiry or shipment limits.',
                'benefits' => ['Portfolio expiry calendar', 'Risk heat maps', 'Multi-branch roll-ups', 'Export & import views'],
            ],
            'deadline-reminders' => [
                'title' => 'Deadline Reminders',
                'headline' => 'Automated reminder cadence for critical trade finance dates.',
                'summary' => 'Configurable 90/60/30/15/7/3/1-day reminders plus overdue escalation.',
                'benefits' => ['Multi-channel reminders', 'Per-deadline ownership', 'Overdue tracking', 'In-app notifications'],
            ],
            'shipment-tracking' => [
                'title' => 'Shipment Tracking',
                'headline' => 'Connect vessel milestones to LC and document requirements.',
                'summary' => 'ETD/ETA, BL references, and shipment status tied to underlying credits.',
                'benefits' => ['Milestone timelines', 'LC linkage', 'Delay risk flags', 'Branch reporting'],
            ],
            'discrepancies' => [
                'title' => 'Discrepancy Management',
                'headline' => 'Resolve documentary discrepancies before they block payment.',
                'summary' => 'Track bank refusals, waivers, and corrective actions with accountable owners.',
                'benefits' => ['Resolution deadlines', 'Bank communication refs', 'Status workflows', 'Document association'],
            ],
            'payment-maturity' => [
                'title' => 'Payment Maturity',
                'headline' => 'Cash-flow visibility for acceptance, deferred, and sight payments.',
                'summary' => 'Schedules maturity dates, settlement status, and outstanding balances by LC.',
                'benefits' => ['Maturity calendar', 'Outstanding balances', 'Settlement tracking', 'Exposure snapshots'],
            ],
            'ai-intelligence' => [
                'title' => 'AI Trade Intelligence',
                'headline' => 'Extract clauses and flag risks from LC and document uploads.',
                'summary' => 'AI-assisted parsing, discrepancy hints, and assistant sessions for your team.',
                'benefits' => ['Document extraction jobs', 'Clause highlighting', 'Assistant sessions', 'Human-in-the-loop review'],
            ],
            'bank-correspondence' => [
                'title' => 'Bank Correspondence',
                'headline' => 'Structured log of SWIFT, email, and branch communications.',
                'summary' => 'Attach correspondence to LCs with references for audits and handoffs.',
                'benefits' => ['Thread by LC', 'Reference indexing', 'Officer assignment', 'Export for audits'],
            ],
            'multi-branch' => [
                'title' => 'Multi-Branch Operations',
                'headline' => 'Tenant-grade isolation with branch-level reporting.',
                'summary' => 'Operate multiple companies and branches under one organization subscription.',
                'benefits' => ['Branch scoping', 'Role-based access', 'Consolidated dashboards', 'Per-branch modules'],
            ],
            'financial-analytics' => [
                'title' => 'Financial Analytics',
                'headline' => 'Exposure, utilization, and realization analytics for treasurers.',
                'summary' => 'Snapshots and trends across import utilization and export realization.',
                'benefits' => ['Exposure snapshots', 'Currency breakdowns', 'Trend views', 'Leadership KPIs'],
            ],
            'approval-workflow' => [
                'title' => 'Approval Workflows',
                'headline' => 'Policy-driven approvals for amendments, documents, and settlements.',
                'summary' => 'Configurable workflows with history for SOX-friendly trade operations.',
                'benefits' => ['Multi-step approvals', 'Delegation', 'Immutable history', 'Module-aware rules'],
            ],
            'trade-finance-reporting' => [
                'title' => 'Trade Finance Reporting',
                'headline' => 'Board-ready PDF and operational summaries on demand.',
                'summary' => 'LC summaries, discrepancy reports, checklists, and payment schedules exportable for banks.',
                'benefits' => ['PDF exports', 'Scheduled snapshots', 'API summary endpoint', 'Audit-ready outputs'],
            ],
        ];
    }

    public static function featureSlugs(): array
    {
        return array_keys(self::features());
    }

    /**
     * @return array<int, array{name: string, price: string, description: string, cta: string, href: string, highlighted?: bool}>
     */
    public static function pricingPlans(): array
    {
        return [
            [
                'name' => 'Free Demo',
                'price' => '$0',
                'description' => 'Full sandbox with sample LCs, documents, and workflows.',
                'cta' => 'Start demo',
                'href' => '/demo',
            ],
            [
                'name' => 'Starter',
                'price' => '$149',
                'description' => 'Single company, core LC tracking and document vault.',
                'cta' => 'Talk to sales',
                'href' => '/contact',
            ],
            [
                'name' => 'Professional',
                'price' => '$399',
                'description' => 'Multi-branch, deadlines, discrepancies, and API access.',
                'cta' => 'Request demo',
                'href' => '/demo',
                'highlighted' => true,
            ],
            [
                'name' => 'Business',
                'price' => '$899',
                'description' => 'Approvals, analytics, webhooks, and priority support.',
                'cta' => 'Contact us',
                'href' => '/contact',
            ],
            [
                'name' => 'Enterprise',
                'price' => 'Custom',
                'description' => 'SSO, custom integrations, dedicated success, and SLA.',
                'cta' => 'Enterprise inquiry',
                'href' => '/contact',
            ],
        ];
    }
}
