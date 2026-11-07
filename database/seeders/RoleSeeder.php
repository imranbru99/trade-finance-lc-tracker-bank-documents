<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public const SYSTEM_ROLES = [
        'organization_owner' => 'Organization Owner',
        'organization_administrator' => 'Organization Administrator',
        'trade_finance_manager' => 'Trade Finance Manager',
        'import_manager' => 'Import Manager',
        'export_manager' => 'Export Manager',
        'documentation_officer' => 'Documentation Officer',
        'bank_relationship_manager' => 'Bank Relationship Manager',
        'finance_manager' => 'Finance Manager',
        'accounts_officer' => 'Accounts Officer',
        'compliance_officer' => 'Compliance Officer',
        'branch_manager' => 'Branch Manager',
        'approval_manager' => 'Approval Manager',
        'internal_auditor' => 'Internal Auditor',
        'read_only_viewer' => 'Read Only Viewer',
        'external_partner' => 'External Partner',
    ];

    public function run(): void
    {
        $allPermissionIds = Permission::pluck('id', 'slug');
        $allIds = $allPermissionIds->values()->all();

        $fullAccessRoles = [
            'organization_owner',
            'organization_administrator',
            'trade_finance_manager',
        ];

        $subsets = [
            'import_manager' => [
                'view_lc', 'create_lc', 'edit_lc', 'upload_documents', 'view_financials', 'export_reports',
            ],
            'export_manager' => [
                'view_lc', 'create_lc', 'edit_lc', 'upload_documents', 'export_reports',
            ],
            'documentation_officer' => [
                'view_lc', 'upload_documents', 'delete_documents', 'export_reports',
            ],
            'bank_relationship_manager' => [
                'view_lc', 'manage_banks', 'export_reports', 'view_audit',
            ],
            'finance_manager' => [
                'view_lc', 'view_financials', 'export_reports', 'manage_approvals',
            ],
            'accounts_officer' => [
                'view_lc', 'view_financials', 'export_reports',
            ],
            'compliance_officer' => [
                'view_lc', 'view_audit', 'export_reports', 'configure_reminders',
            ],
            'branch_manager' => [
                'view_lc', 'edit_lc', 'upload_documents', 'manage_branches', 'export_reports',
            ],
            'approval_manager' => [
                'view_lc', 'approve_lc', 'manage_approvals', 'view_audit',
            ],
            'internal_auditor' => [
                'view_lc', 'view_financials', 'view_audit', 'export_reports',
            ],
            'read_only_viewer' => [
                'view_lc', 'view_financials',
            ],
            'external_partner' => [
                'view_lc', 'upload_documents',
            ],
        ];

        foreach (self::SYSTEM_ROLES as $slug => $name) {
            $role = Role::updateOrCreate(
                [
                    'organization_id' => null,
                    'slug' => $slug,
                ],
                [
                    'name' => $name,
                    'description' => $name,
                    'is_system' => true,
                ]
            );

            if (in_array($slug, $fullAccessRoles, true)) {
                $role->permissions()->sync($allIds);
                continue;
            }

            $slugs = $subsets[$slug] ?? ['view_lc'];
            $ids = collect($slugs)
                ->map(fn (string $permissionSlug) => $allPermissionIds[$permissionSlug] ?? null)
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($ids);
        }
    }
}
