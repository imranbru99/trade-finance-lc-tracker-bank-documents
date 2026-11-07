<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Demo',
                'slug' => 'free-demo',
                'description' => 'Evaluation workspace with core modules and capped LC volume.',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_users' => 3,
                'max_companies' => 1,
                'max_branches' => 1,
                'max_lcs' => 10,
                'storage_mb' => 1024,
                'features' => [
                    'enabled_modules' => ['dashboard', 'import_lc', 'document_management'],
                    'ai_quota' => 25,
                ],
                'is_featured' => false,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Small importers with a single branch and essential tracking.',
                'price_monthly' => 49,
                'price_yearly' => 490,
                'max_users' => 10,
                'max_companies' => 1,
                'max_branches' => 2,
                'max_lcs' => 50,
                'storage_mb' => 5120,
                'features' => [
                    'enabled_modules' => ['dashboard', 'import_lc', 'export_lc', 'document_management', 'deadline_tracking'],
                    'ai_quota' => 100,
                ],
                'is_featured' => false,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Growing trade teams with multi-branch operations and AI extraction.',
                'price_monthly' => 149,
                'price_yearly' => 1490,
                'max_users' => 35,
                'max_companies' => 3,
                'max_branches' => 10,
                'max_lcs' => 250,
                'storage_mb' => 20480,
                'features' => [
                    'enabled_modules' => ['dashboard', 'branch_management', 'import_lc', 'export_lc', 'document_management', 'deadline_tracking', 'shipment_tracking', 'discrepancy_management', 'payment_tracking', 'ai_intelligence', 'reporting'],
                    'ai_quota' => 500,
                ],
                'is_featured' => true,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Regional enterprises with approvals, integrations, and higher limits.',
                'price_monthly' => 349,
                'price_yearly' => 3490,
                'max_users' => 100,
                'max_companies' => 10,
                'max_branches' => 25,
                'max_lcs' => 1000,
                'storage_mb' => 102400,
                'features' => [
                    'enabled_modules' => ['dashboard', 'branch_management', 'bank_management', 'counterparties', 'import_lc', 'export_lc', 'lc_amendments', 'document_management', 'deadline_tracking', 'shipment_tracking', 'discrepancy_management', 'payment_tracking', 'bank_correspondence', 'approval_workflows', 'task_management', 'ai_intelligence', 'reporting'],
                    'ai_quota' => 2500,
                ],
                'is_featured' => false,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Unlimited-scale deployments with custom modules and premium support.',
                'price_monthly' => 899,
                'price_yearly' => 8990,
                'max_users' => 500,
                'max_companies' => 50,
                'max_branches' => 100,
                'max_lcs' => 10000,
                'storage_mb' => 512000,
                'features' => [
                    'enabled_modules' => ['*'],
                    'ai_quota' => 10000,
                ],
                'is_featured' => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                array_merge($plan, ['is_active' => true])
            );
        }
    }
}
