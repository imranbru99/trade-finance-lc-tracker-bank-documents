<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public const PERMISSIONS = [
        ['slug' => 'view_lc', 'name' => 'View Letters of Credit', 'module' => 'import_lc'],
        ['slug' => 'create_lc', 'name' => 'Create Letters of Credit', 'module' => 'import_lc'],
        ['slug' => 'edit_lc', 'name' => 'Edit Letters of Credit', 'module' => 'import_lc'],
        ['slug' => 'approve_lc', 'name' => 'Approve Letters of Credit', 'module' => 'import_lc'],
        ['slug' => 'upload_documents', 'name' => 'Upload Documents', 'module' => 'document_management'],
        ['slug' => 'delete_documents', 'name' => 'Delete Documents', 'module' => 'document_management'],
        ['slug' => 'view_financials', 'name' => 'View Financial Data', 'module' => 'payment_tracking'],
        ['slug' => 'manage_banks', 'name' => 'Manage Banks', 'module' => 'bank_management'],
        ['slug' => 'manage_branches', 'name' => 'Manage Branches', 'module' => 'branch_management'],
        ['slug' => 'export_reports', 'name' => 'Export Reports', 'module' => 'reporting'],
        ['slug' => 'configure_reminders', 'name' => 'Configure Reminders', 'module' => 'deadline_tracking'],
        ['slug' => 'manage_integrations', 'name' => 'Manage Integrations', 'module' => 'general'],
        ['slug' => 'manage_users', 'name' => 'Manage Users', 'module' => 'general'],
        ['slug' => 'configure_modules', 'name' => 'Configure Modules', 'module' => 'general'],
        ['slug' => 'manage_approvals', 'name' => 'Manage Approvals', 'module' => 'approval_workflows'],
        ['slug' => 'view_audit', 'name' => 'View Audit Logs', 'module' => 'general'],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                    'description' => $permission['name'],
                ]
            );
        }
    }
}
