<?php

namespace App\Services;

use App\Models\Module;
use App\Models\Organization;
use App\Models\TenantModule;
use Exception;
use Filament\Facades\Filament;

class ModuleManager
{
    /**
     * Default catalog of modules
     */
    public const MODULE_CATALOG = [
        // Core
        [
            'name' => 'Dashboard & Analytics',
            'slug' => 'dashboard',
            'category' => 'core',
            'description' => 'Executive trade finance KPI widgets, charts, and activity streams.',
            'icon' => 'heroicon-o-chart-bar-square',
            'is_core' => true,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        [
            'name' => 'Multi-Branch Management',
            'slug' => 'branch_management',
            'category' => 'core',
            'description' => 'Multi-company, multi-branch, and department operational structure.',
            'icon' => 'heroicon-o-building-office-2',
            'is_core' => true,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        [
            'name' => 'Bank Directory',
            'slug' => 'bank_management',
            'category' => 'core',
            'description' => 'Global & domestic bank profiles, branches, and relationship managers.',
            'icon' => 'heroicon-o-building-library',
            'is_core' => true,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        [
            'name' => 'Counterparties',
            'slug' => 'counterparties',
            'category' => 'core',
            'description' => 'Buyers, suppliers, shipping lines, and freight forwarders directory.',
            'icon' => 'heroicon-o-users',
            'is_core' => true,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        // Trade Finance
        [
            'name' => 'Import LC Management',
            'slug' => 'import_lc',
            'category' => 'trade_finance',
            'description' => 'Comprehensive Import Letter of Credit tracking, terms, and status lifecycles.',
            'icon' => 'heroicon-o-arrow-down-tray',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['bank_management', 'counterparties'],
        ],
        [
            'name' => 'Export LC Management',
            'slug' => 'export_lc',
            'category' => 'trade_finance',
            'description' => 'Export Letter of Credit registration, advice, negotiations, and proceeds.',
            'icon' => 'heroicon-o-arrow-up-tray',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['bank_management', 'counterparties'],
        ],
        [
            'name' => 'LC Amendments',
            'slug' => 'lc_amendments',
            'category' => 'trade_finance',
            'description' => 'Immutable amendment logs, change requests, bank submissions, and diffs.',
            'icon' => 'heroicon-o-pencil-square',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['import_lc'],
        ],
        [
            'name' => 'Bank Document Management',
            'slug' => 'document_management',
            'category' => 'trade_finance',
            'description' => 'Secure private document repository, auto-checklists, versions, and signed URLs.',
            'icon' => 'heroicon-o-document-duplicate',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        [
            'name' => 'Automated Deadlines & Reminders',
            'slug' => 'deadline_tracking',
            'category' => 'trade_finance',
            'description' => 'LC expiry, latest shipment, document presentation, and payment reminders.',
            'icon' => 'heroicon-o-bell-alert',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        [
            'name' => 'Shipment & Logistics Tracking',
            'slug' => 'shipment_tracking',
            'category' => 'trade_finance',
            'description' => 'Track partial shipments, vessels, containers, ETD/ETA, and customs.',
            'icon' => 'heroicon-o-truck',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['counterparties'],
        ],
        [
            'name' => 'Document Discrepancy Management',
            'slug' => 'discrepancy_management',
            'category' => 'trade_finance',
            'description' => 'Log bank discrepancies, track resolution deadlines, waivers, and root causes.',
            'icon' => 'heroicon-o-exclamation-triangle',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['document_management'],
        ],
        [
            'name' => 'Payment & Maturity Tracking',
            'slug' => 'payment_tracking',
            'category' => 'trade_finance',
            'description' => 'Sight, deferred, usance maturity schedules, bank charges, and settlements.',
            'icon' => 'heroicon-o-banknotes',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['bank_management'],
        ],
        [
            'name' => 'Bank Correspondence & SWIFT',
            'slug' => 'bank_correspondence',
            'category' => 'trade_finance',
            'description' => 'Track official communications, MT700/707 records, letters, and advice.',
            'icon' => 'heroicon-o-envelope',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['bank_management'],
        ],
        // Advanced
        [
            'name' => 'Approval Workflows',
            'slug' => 'approval_workflows',
            'category' => 'advanced',
            'description' => 'Multi-tiered hierarchical approvals, threshold rules, and audit logging.',
            'icon' => 'heroicon-o-check-badge',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        [
            'name' => 'Task Management',
            'slug' => 'task_management',
            'category' => 'advanced',
            'description' => 'Kanban and list view trade finance tasks, assignments, and escalations.',
            'icon' => 'heroicon-o-clipboard-document-check',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        [
            'name' => 'AI Document Intelligence',
            'slug' => 'ai_intelligence',
            'category' => 'advanced',
            'description' => 'AI OCR extraction, field confidence scoring, review queue, and AI assistant.',
            'icon' => 'heroicon-o-sparkles',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['document_management'],
        ],
        [
            'name' => 'Calendar & Deadline Center',
            'slug' => 'calendar',
            'category' => 'advanced',
            'description' => 'Timezone-aware visual calendar for shipments, expiries, and maturities.',
            'icon' => 'heroicon-o-calendar',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => ['deadline_tracking'],
        ],
        [
            'name' => 'Reports & Analytics',
            'slug' => 'reporting',
            'category' => 'advanced',
            'description' => 'Comprehensive registers, bank exposure, and Unicode PDF/Excel exports.',
            'icon' => 'heroicon-o-document-chart-bar',
            'is_core' => false,
            'default_enabled' => true,
            'dependencies' => [],
        ],
        // Optional Business Modules
        [
            'name' => 'HR & Attendance Management',
            'slug' => 'attendance_hr',
            'category' => 'optional',
            'description' => 'Optional staff attendance and shift tracking powered by Attendance Hub.',
            'icon' => 'heroicon-o-user-group',
            'is_core' => false,
            'default_enabled' => false,
            'dependencies' => [],
        ],
        [
            'name' => 'Commodity & AI Catalog',
            'slug' => 'commodity_catalog',
            'category' => 'optional',
            'description' => 'Trade goods descriptions and HS-code master catalog powered by AI Catalog.',
            'icon' => 'heroicon-o-tag',
            'is_core' => false,
            'default_enabled' => false,
            'dependencies' => [],
        ],
    ];

    /**
     * Synchronize the module catalog table
     */
    public function syncCatalog(): void
    {
        foreach (self::MODULE_CATALOG as $data) {
            Module::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }

    /**
     * Check if a module is enabled for an organization
     */
    public function isEnabled(string $moduleSlug, ?Organization $organization = null): bool
    {
        if (! $organization) {
            $organization = Filament::getTenant();
        }

        if (! $organization) {
            return true;
        }

        return $organization->isModuleEnabled($moduleSlug);
    }

    /**
     * Enable a module for an organization (verifies dependencies)
     */
    public function enable(string $moduleSlug, Organization $organization, array $settings = []): void
    {
        $module = Module::where('slug', $moduleSlug)->first();
        if (! $module) {
            throw new Exception("Module [{$moduleSlug}] not found.");
        }

        // Check dependencies
        if (! empty($module->dependencies)) {
            foreach ($module->dependencies as $dep) {
                if (! $this->isEnabled($dep, $organization)) {
                    $depMod = Module::where('slug', $dep)->first();
                    $depName = $depMod ? $depMod->name : $dep;
                    throw new Exception("Cannot enable [{$module->name}]. Required dependency [{$depName}] is disabled.");
                }
            }
        }

        TenantModule::updateOrCreate(
            ['organization_id' => $organization->id, 'module_slug' => $moduleSlug],
            [
                'is_enabled' => true,
                'settings' => $settings,
                'enabled_at' => now(),
            ]
        );
    }

    /**
     * Disable a module for an organization (checks if other enabled modules depend on it)
     * NOTE: Existing data is strictly preserved!
     */
    public function disable(string $moduleSlug, Organization $organization): void
    {
        $module = Module::where('slug', $moduleSlug)->first();
        if ($module && $module->is_core) {
            throw new Exception("Core module [{$module->name}] cannot be disabled.");
        }

        // Check if any currently enabled module depends on this one
        $dependents = Module::whereJsonContains('dependencies', $moduleSlug)->get();
        foreach ($dependents as $dep) {
            if ($this->isEnabled($dep->slug, $organization)) {
                throw new Exception("Cannot disable [{$module->name}]. Module [{$dep->name}] depends on it.");
            }
        }

        TenantModule::updateOrCreate(
            ['organization_id' => $organization->id, 'module_slug' => $moduleSlug],
            [
                'is_enabled' => false,
                'disabled_at' => now(),
            ]
        );
    }
}
