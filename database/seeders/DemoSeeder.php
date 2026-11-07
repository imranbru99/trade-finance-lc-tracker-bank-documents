<?php

namespace Database\Seeders;

use App\Models\ApprovalHistory;
use App\Models\ApprovalRequest;
use App\Models\ApprovalWorkflow;
use App\Models\Bank;
use App\Models\BankCorrespondence;
use App\Models\Branch;
use App\Models\CmsPage;
use App\Models\Company;
use App\Models\Counterparty;
use App\Models\Deadline;
use App\Models\Discrepancy;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\ExportLc;
use App\Models\ImportLc;
use App\Models\KnowledgeArticle;
use App\Models\LcAmendment;
use App\Models\LcStatusHistory;
use App\Models\Module;
use App\Models\Organization;
use App\Models\PaymentSchedule;
use App\Models\Reminder;
use App\Models\Shipment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Task;
use App\Models\TenantModule;
use App\Models\TenantUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    protected Organization $organization;

    protected Company $company;

    protected Branch $headOffice;

    protected Branch $chattogramBranch;

    protected array $users = [];

    protected array $banks = [];

    protected array $counterparties = [];

    protected array $importLcs = [];

    protected array $exportLcs = [];

    protected const DEMO_PASSWORD = 'DemoPass!2026';

    public function run(): void
    {
        $existing = Organization::withTrashed()->where('slug', 'global-trade-solutions')->first();
        if ($existing) {
            $existing->forceDelete();
        }

        $this->seedOrganizationStructure();
        $this->seedUsers();
        $this->seedTenantModules();
        $this->seedBanksAndCounterparties();
        $this->seedImportLcs();
        $this->seedExportLcs();
        $this->seedAmendments();
        $this->seedDocuments();
        $this->seedShipments();
        $this->seedDiscrepancies();
        $this->seedDeadlinesAndReminders();
        $this->seedPaymentSchedules();
        $this->seedBankCorrespondence();
        $this->seedTasks();
        $this->seedApprovals();
        $this->seedSubscription();
        $this->seedPublicContent();
    }

    protected function seedOrganizationStructure(): void
    {
        $this->organization = Organization::create([
            'name' => 'Global Trade Solutions Ltd',
            'slug' => 'global-trade-solutions',
            'code' => 'GTS-DEMO',
            'email' => 'contact@globaltrade.demo',
            'phone' => '+880-2-5500-1200',
            'address' => 'Level 12, Trade Tower, Motijheel Commercial Area, Dhaka 1000',
            'country' => 'Bangladesh',
            'currency' => 'USD',
            'timezone' => 'Asia/Dhaka',
            'status' => 'active',
        ]);

        $this->company = Company::create([
            'organization_id' => $this->organization->id,
            'name' => 'Global Trade Solutions Ltd',
            'registration_number' => 'C-198452-BD',
            'tax_number' => 'VAT-TRD-44521',
            'address' => $this->organization->address,
            'country' => 'Bangladesh',
            'phone' => $this->organization->phone,
            'email' => 'company@globaltrade.demo',
        ]);

        $this->headOffice = Branch::create([
            'organization_id' => $this->organization->id,
            'company_id' => $this->company->id,
            'name' => 'Dhaka Head Office',
            'code' => 'DHK-HQ',
            'address' => 'Motijheel, Dhaka',
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'is_head_office' => true,
        ]);

        $this->chattogramBranch = Branch::create([
            'organization_id' => $this->organization->id,
            'company_id' => $this->company->id,
            'name' => 'Chattogram Branch',
            'code' => 'CTG-01',
            'address' => 'Agrabad Commercial Area, Chattogram',
            'city' => 'Chattogram',
            'country' => 'Bangladesh',
        ]);

        Branch::create([
            'organization_id' => $this->organization->id,
            'company_id' => $this->company->id,
            'name' => 'Khulna Branch',
            'code' => 'KHL-01',
            'address' => 'Sonadanga, Khulna',
            'city' => 'Khulna',
            'country' => 'Bangladesh',
        ]);
    }

    protected function seedUsers(): void
    {
        $definitions = [
            ['email' => 'admin@tradefinance.demo', 'name' => 'Platform Admin', 'role' => null, 'is_platform_admin' => true, 'is_owner' => false, 'branch' => null],
            ['email' => 'owner@tradefinance.demo', 'name' => 'Rashid Karim', 'role' => 'organization_owner', 'is_platform_admin' => false, 'is_owner' => true, 'branch' => null],
            ['email' => 'manager@tradefinance.demo', 'name' => 'Nusrat Alam', 'role' => 'trade_finance_manager', 'is_platform_admin' => false, 'is_owner' => false, 'branch' => null],
            ['email' => 'import@tradefinance.demo', 'name' => 'Imran Hossain', 'role' => 'import_manager', 'is_platform_admin' => false, 'is_owner' => false, 'branch' => null],
            ['email' => 'export@tradefinance.demo', 'name' => 'Farhana Begum', 'role' => 'export_manager', 'is_platform_admin' => false, 'is_owner' => false, 'branch' => null],
            ['email' => 'documents@tradefinance.demo', 'name' => 'Salma Khatun', 'role' => 'documentation_officer', 'is_platform_admin' => false, 'is_owner' => false, 'branch' => null],
            ['email' => 'finance@tradefinance.demo', 'name' => 'Kabir Ahmed', 'role' => 'finance_manager', 'is_platform_admin' => false, 'is_owner' => false, 'branch' => null],
            ['email' => 'compliance@tradefinance.demo', 'name' => 'Tania Rahman', 'role' => 'compliance_officer', 'is_platform_admin' => false, 'is_owner' => false, 'branch' => null],
            ['email' => 'branch@tradefinance.demo', 'name' => 'Arif Chowdhury', 'role' => 'branch_manager', 'is_platform_admin' => false, 'is_owner' => false, 'branch' => 'chattogram'],
            ['email' => 'auditor@tradefinance.demo', 'name' => 'Mehjabin Islam', 'role' => 'internal_auditor', 'is_platform_admin' => false, 'is_owner' => false, 'branch' => null],
        ];

        foreach ($definitions as $def) {
            $user = User::updateOrCreate(
                ['email' => $def['email']],
                [
                    'name' => $def['name'],
                    'password' => Hash::make(self::DEMO_PASSWORD),
                    'is_platform_admin' => $def['is_platform_admin'],
                    'current_organization_id' => $def['is_platform_admin'] ? null : $this->organization->id,
                    'status' => 'active',
                    'designation' => $def['role'] ? Str::headline(str_replace('_', ' ', $def['role'])) : 'Platform Administrator',
                ]
            );

            $this->users[$def['email']] = $user;

            if ($def['role']) {
                $branchId = $def['branch'] === 'chattogram' ? $this->chattogramBranch->id : null;

                TenantUser::updateOrCreate(
                    [
                        'organization_id' => $this->organization->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'branch_id' => $branchId,
                        'role_slug' => $def['role'],
                        'is_owner' => $def['is_owner'],
                        'status' => 'active',
                    ]
                );
            }
        }
    }

    protected function seedTenantModules(): void
    {
        Module::query()->where('default_enabled', true)->each(function (Module $module): void {
            TenantModule::updateOrCreate(
                [
                    'organization_id' => $this->organization->id,
                    'module_slug' => $module->slug,
                ],
                [
                    'is_enabled' => true,
                    'enabled_at' => now(),
                ]
            );
        });
    }

    protected function seedBanksAndCounterparties(): void
    {
        $bankData = [
            ['name' => 'Sonali Bank PLC', 'swift_bic' => 'BSONBDDH', 'category' => 'commercial'],
            ['name' => 'HSBC Bangladesh', 'swift_bic' => 'HSBCBDDH', 'category' => 'foreign'],
            ['name' => 'Standard Chartered Bangladesh', 'swift_bic' => 'SCBLBDDX', 'category' => 'foreign'],
            ['name' => 'Agrani Bank PLC', 'swift_bic' => 'AGRNBDDH', 'category' => 'commercial'],
            ['name' => 'Janata Bank PLC', 'swift_bic' => 'JANBBDDH', 'category' => 'commercial'],
            ['name' => 'Eastern Bank Limited', 'swift_bic' => 'EBLDBDDH', 'category' => 'commercial'],
        ];

        foreach ($bankData as $row) {
            $this->banks[] = Bank::create([
                'organization_id' => $this->organization->id,
                'name' => $row['name'],
                'swift_bic' => $row['swift_bic'],
                'country' => 'Bangladesh',
                'category' => $row['category'],
                'is_global' => false,
            ]);
        }

        $counterpartyData = [
            ['name' => 'Shenzhen Apex Electronics Co.', 'type' => 'supplier', 'country' => 'China'],
            ['name' => 'Mumbai Textile Exports Pvt Ltd', 'type' => 'supplier', 'country' => 'India'],
            ['name' => 'Dubai Gulf Trading FZE', 'type' => 'buyer', 'country' => 'United Arab Emirates'],
            ['name' => 'Hamburg Maschinen GmbH', 'type' => 'supplier', 'country' => 'Germany'],
            ['name' => 'Pacific Agro Buyers LLC', 'type' => 'buyer', 'country' => 'USA'],
            ['name' => 'Guangzhou Fiber Mills', 'type' => 'supplier', 'country' => 'China'],
        ];

        foreach ($counterpartyData as $row) {
            $this->counterparties[] = Counterparty::create([
                'organization_id' => $this->organization->id,
                'name' => $row['name'],
                'type' => $row['type'],
                'country' => $row['country'],
                'contact_person' => 'Demo Contact',
                'email' => Str::slug($row['name'], '.') . '@example.demo',
                'credit_limit' => rand(100000, 500000),
            ]);
        }
    }

    protected function seedImportLcs(): void
    {
        $manager = $this->users['manager@tradefinance.demo'];
        $statuses = [
            'Active', 'Draft', 'Documents Submitted', 'Discrepancy Found', 'Settled', 'Expired',
            'Payment Pending', 'Issued', 'Fully Shipped', 'Partially Shipped', 'Under Bank Examination',
            'Amendment Pending', 'Advised', 'Submitted to Bank', 'Matured',
        ];
        $currencies = ['USD', 'EUR', 'BDT', 'USD', 'USD', 'EUR', 'BDT', 'USD', 'USD', 'EUR', 'USD', 'BDT', 'USD', 'USD', 'EUR'];

        foreach ($statuses as $index => $status) {
            $amount = rand(50000, 2500000);
            $utilized = in_array($status, ['Settled', 'Fully Shipped', 'Matured'], true) ? $amount : rand(0, (int) ($amount * 0.8));
            $issue = Carbon::now()->subDays(rand(30, 200));
            $expiry = $status === 'Expired'
                ? Carbon::now()->subDays(rand(5, 30))
                : Carbon::now()->addDays(rand($index < 3 ? 7 : 30, 180));

            $lc = ImportLc::create([
                'organization_id' => $this->organization->id,
                'company_id' => $this->company->id,
                'branch_id' => $index % 3 === 0 ? $this->chattogramBranch->id : $this->headOffice->id,
                'user_id' => $manager->id,
                'counterparty_id' => $this->counterparties[$index % count($this->counterparties)]->id,
                'issuing_bank_id' => $this->banks[$index % count($this->banks)]->id,
                'advising_bank_id' => $this->banks[($index + 1) % count($this->banks)]->id,
                'lc_number' => 'IMP-LC-' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT),
                'internal_reference' => 'GTS-IMP-' . ($index + 1001),
                'issue_date' => $issue,
                'expiry_date' => $expiry,
                'latest_shipment_date' => $expiry->copy()->subDays(15),
                'applicant_name' => $this->company->name,
                'beneficiary_name' => $this->counterparties[$index % count($this->counterparties)]->name,
                'currency' => $currencies[$index],
                'lc_amount' => $amount,
                'utilized_amount' => $utilized,
                'outstanding_amount' => max(0, $amount - $utilized),
                'country_of_origin' => 'China',
                'country_of_destination' => 'Bangladesh',
                'port_of_loading' => 'Shanghai',
                'port_of_discharge' => 'Chattogram',
                'goods_description' => 'Industrial components and textile raw materials (demo shipment ' . ($index + 1) . ')',
                'status' => $status,
            ]);

            LcStatusHistory::create([
                'trackable_type' => ImportLc::class,
                'trackable_id' => $lc->id,
                'from_status' => 'Draft',
                'to_status' => $status,
                'changed_by_user_id' => $manager->id,
                'comment' => 'Demo status transition',
            ]);

            $this->importLcs[] = $lc;
        }
    }

    protected function seedExportLcs(): void
    {
        $exportManager = $this->users['export@tradefinance.demo'];
        $statuses = [
            'Active', 'Draft', 'Registered', 'Advised', 'Partially Shipped', 'Fully Shipped',
            'Documents Dispatched', 'Negotiated', 'Realized', 'Closed',
        ];
        $currencies = ['USD', 'EUR', 'USD', 'BDT', 'USD', 'EUR', 'USD', 'USD', 'BDT', 'USD'];

        foreach ($statuses as $index => $status) {
            $value = rand(75000, 1800000);
            $realized = in_array($status, ['Realized', 'Closed'], true) ? $value : rand(0, (int) ($value * 0.6));

            $lc = ExportLc::create([
                'organization_id' => $this->organization->id,
                'company_id' => $this->company->id,
                'branch_id' => $this->headOffice->id,
                'user_id' => $exportManager->id,
                'counterparty_id' => $this->counterparties[($index + 2) % count($this->counterparties)]->id,
                'advising_bank_id' => $this->banks[$index % count($this->banks)]->id,
                'issuing_bank_name' => 'Overseas Issuing Bank ' . ($index + 1),
                'issuing_bank_swift' => 'DEMOUS33',
                'lc_number' => 'EXP-LC-' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT),
                'contract_reference' => 'EXP-CON-' . ($index + 500),
                'buyer_name' => $this->counterparties[($index + 2) % count($this->counterparties)]->name,
                'currency' => $currencies[$index],
                'export_value' => $value,
                'realized_amount' => $realized,
                'pending_amount' => max(0, $value - $realized),
                'issue_date' => Carbon::now()->subDays(rand(20, 150)),
                'expiry_date' => Carbon::now()->addDays(rand(20, 120)),
                'latest_shipment_date' => Carbon::now()->addDays(rand(10, 90)),
                'presentation_deadline' => Carbon::now()->addDays(rand(15, 100)),
                'goods_description' => 'Ready-made garments and jute goods export order #' . ($index + 1),
                'status' => $status,
            ]);

            $this->exportLcs[] = $lc;
        }
    }

    protected function seedAmendments(): void
    {
        $manager = $this->users['manager@tradefinance.demo'];
        $targets = array_slice($this->importLcs, 0, 8);

        foreach ($targets as $index => $lc) {
            LcAmendment::create([
                'organization_id' => $this->organization->id,
                'amendable_type' => ImportLc::class,
                'amendable_id' => $lc->id,
                'amendment_number' => $index + 1,
                'request_date' => Carbon::now()->subDays(rand(5, 40)),
                'bank_submission_date' => Carbon::now()->subDays(rand(2, 20)),
                'status' => ['Requested', 'Pending Bank', 'Approved', 'Approved'][$index % 4],
                'reason' => 'Extend shipment date and increase LC amount for additional lot ' . ($index + 1),
                'amount_change' => rand(5000, 50000),
                'new_expiry_date' => $lc->expiry_date->copy()->addDays(30),
                'responsible_user_id' => $manager->id,
                'document_path' => 'documents/demo/amendment-' . ($index + 1) . '.pdf',
            ]);
        }
    }

    protected function seedDocuments(): void
    {
        $uploader = $this->users['documents@tradefinance.demo'];
        $categories = DocumentCategory::pluck('id')->all();
        $verificationStatuses = ['Pending', 'Verified', 'Pending', 'Verified', 'Discrepant', 'Rejected'];

        for ($i = 1; $i <= 30; $i++) {
            $lc = $this->importLcs[($i - 1) % count($this->importLcs)];

            Document::create([
                'organization_id' => $this->organization->id,
                'company_id' => $this->company->id,
                'branch_id' => $lc->branch_id,
                'documentable_type' => ImportLc::class,
                'documentable_id' => $lc->id,
                'category_id' => $categories[($i - 1) % count($categories)],
                'title' => 'Demo Trade Document ' . $i,
                'document_number' => 'DOC-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'original_filename' => 'invoice-' . $i . '.pdf',
                'stored_path' => 'documents/demo/invoice-' . $i . '.pdf',
                'disk' => 'local',
                'file_size' => rand(120_000, 2_500_000),
                'mime_type' => 'application/pdf',
                'verification_status' => $verificationStatuses[$i % count($verificationStatuses)],
                'uploaded_by_user_id' => $uploader->id,
                'tags' => ['demo', 'import'],
            ]);
        }
    }

    protected function seedShipments(): void
    {
        $statuses = ['Planned', 'Booked', 'In Transit', 'Shipped', 'Arrived', 'Delivered', 'Delayed', 'Customs Processing'];

        for ($i = 0; $i < 10; $i++) {
            $trackable = $i % 2 === 0
                ? $this->importLcs[$i % count($this->importLcs)]
                : $this->exportLcs[$i % count($this->exportLcs)];

            Shipment::create([
                'organization_id' => $this->organization->id,
                'company_id' => $this->company->id,
                'branch_id' => $this->headOffice->id,
                'trackable_type' => $trackable::class,
                'trackable_id' => $trackable->id,
                'shipment_reference' => 'SHP-DEMO-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'shipment_number' => $i + 1,
                'vessel_name' => 'MV Bay Voyager ' . ($i + 1),
                'bill_of_lading_number' => 'BL' . rand(100000, 999999),
                'port_of_loading' => 'Shanghai',
                'port_of_discharge' => 'Chattogram',
                'etd' => Carbon::now()->subDays(rand(5, 30)),
                'eta' => Carbon::now()->addDays(rand(5, 25)),
                'shipment_value' => rand(25000, 400000),
                'currency' => 'USD',
                'status' => $statuses[$i % count($statuses)],
            ]);
        }
    }

    protected function seedDiscrepancies(): void
    {
        $officer = $this->users['documents@tradefinance.demo'];
        $statuses = ['Identified', 'Under Review', 'Awaiting Correction', 'Resolved', 'Closed', 'Waiver Pending', 'Submitted for Waiver', 'Rejected'];

        for ($i = 0; $i < 8; $i++) {
            $lc = $this->importLcs[$i];

            Discrepancy::create([
                'organization_id' => $this->organization->id,
                'branch_id' => $lc->branch_id,
                'trackable_type' => ImportLc::class,
                'trackable_id' => $lc->id,
                'discrepancy_reference' => 'DISC-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'category' => 'Documentary',
                'description' => 'Invoice amount mismatch versus LC value on presentation ' . ($i + 1),
                'identified_date' => Carbon::now()->subDays(rand(3, 20)),
                'resolution_deadline' => Carbon::now()->addDays(rand(5, 20)),
                'responsible_user_id' => $officer->id,
                'status' => $statuses[$i],
            ]);
        }
    }

    protected function seedDeadlinesAndReminders(): void
    {
        $manager = $this->users['manager@tradefinance.demo'];

        for ($i = 0; $i < 12; $i++) {
            $lc = $this->importLcs[$i % count($this->importLcs)];
            $dueDate = Carbon::now()->addDays(rand(3, 45));

            $deadline = Deadline::create([
                'organization_id' => $this->organization->id,
                'branch_id' => $lc->branch_id,
                'trackable_type' => ImportLc::class,
                'trackable_id' => $lc->id,
                'title' => 'Upcoming LC milestone #' . ($i + 1) . ' — ' . $lc->lc_number,
                'deadline_type' => ['lc_expiry', 'latest_shipment', 'presentation_deadline'][$i % 3],
                'due_date' => $dueDate,
                'responsible_user_id' => $manager->id,
                'priority' => ['high', 'critical', 'medium'][$i % 3],
                'status' => 'pending',
            ]);

            Reminder::create([
                'deadline_id' => $deadline->id,
                'days_before' => [7, 3, 1][$i % 3],
                'scheduled_for' => $dueDate->copy()->subDays([7, 3, 1][$i % 3]),
                'channel' => 'email',
                'status' => 'pending',
            ]);
        }
    }

    protected function seedPaymentSchedules(): void
    {
        $statuses = ['Scheduled', 'Due', 'Partially Paid', 'Settled', 'Overdue', 'Matured', 'Due', 'Scheduled'];

        for ($i = 0; $i < 8; $i++) {
            $lc = $this->importLcs[$i + 3];
            $amount = rand(20000, 350000);
            $paid = $statuses[$i] === 'Settled' ? $amount : ($statuses[$i] === 'Partially Paid' ? $amount / 2 : 0);

            PaymentSchedule::create([
                'organization_id' => $this->organization->id,
                'branch_id' => $lc->branch_id,
                'trackable_type' => ImportLc::class,
                'trackable_id' => $lc->id,
                'schedule_number' => 'PAY-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'payment_type' => ['Sight', 'Usance', 'Deferred'][$i % 3],
                'due_date' => Carbon::now()->addDays(rand(5, 60)),
                'maturity_date' => Carbon::now()->addDays(rand(30, 90)),
                'amount' => $amount,
                'currency' => $lc->currency,
                'paid_amount' => $paid,
                'outstanding_amount' => max(0, $amount - $paid),
                'status' => $statuses[$i],
            ]);
        }
    }

    protected function seedBankCorrespondence(): void
    {
        $types = ['Email', 'SWIFT Record', 'Bank Advice', 'Document Submission Letter', 'Discrepancy Notice'];

        for ($i = 0; $i < 15; $i++) {
            $lc = $this->importLcs[$i % count($this->importLcs)];

            BankCorrespondence::create([
                'organization_id' => $this->organization->id,
                'branch_id' => $lc->branch_id,
                'trackable_type' => ImportLc::class,
                'trackable_id' => $lc->id,
                'bank_id' => $lc->issuing_bank_id,
                'correspondence_type' => $types[$i % count($types)],
                'reference_number' => 'BC-' . rand(10000, 99999),
                'subject' => 'Bank communication regarding ' . $lc->lc_number,
                'message_date' => Carbon::now()->subDays(rand(1, 45)),
                'direction' => $i % 2 === 0 ? 'Incoming' : 'Outgoing',
                'sender' => $this->banks[$i % count($this->banks)]->name,
                'recipient' => $this->company->name,
                'summary' => 'Demo SWIFT / letter trace for presentation cycle.',
                'body' => 'This is seeded demo correspondence for LC ' . $lc->lc_number . '.',
                'swift_message_type' => ['MT700', 'MT707', 'MT734', 'MT752', 'MT799'][$i % 5],
                'status' => 'Logged',
            ]);
        }
    }

    protected function seedTasks(): void
    {
        $manager = $this->users['manager@tradefinance.demo'];
        $importUser = $this->users['import@tradefinance.demo'];

        $titles = [
            'Collect signed invoice set',
            'Follow up amendment approval',
            'Prepare discrepancy waiver letter',
            'Review shipment ETD slip',
            'Confirm usance maturity with bank',
        ];

        foreach ($titles as $index => $title) {
            $lc = $this->importLcs[$index];

            Task::create([
                'organization_id' => $this->organization->id,
                'branch_id' => $lc->branch_id,
                'assigned_to_user_id' => $index % 2 === 0 ? $importUser->id : $manager->id,
                'created_by_user_id' => $manager->id,
                'trackable_type' => ImportLc::class,
                'trackable_id' => $lc->id,
                'title' => $title,
                'description' => 'Demo operational task linked to ' . $lc->lc_number,
                'task_type' => 'Document Collection',
                'priority' => ['medium', 'high', 'urgent'][$index % 3],
                'status' => ['todo', 'in_progress', 'completed', 'overdue', 'todo'][$index],
                'due_date' => Carbon::now()->addDays(rand(2, 14)),
            ]);
        }
    }

    protected function seedApprovals(): void
    {
        $owner = $this->users['owner@tradefinance.demo'];
        $manager = $this->users['manager@tradefinance.demo'];

        $workflow = ApprovalWorkflow::create([
            'organization_id' => $this->organization->id,
            'name' => 'Import LC Issuance Approval',
            'module' => 'import_lc',
            'description' => 'Two-step approval for LC submission above thresholds.',
            'min_amount' => 100000,
            'steps' => ['trade_finance_manager', 'organization_owner'],
            'is_active' => true,
        ]);

        foreach (array_slice($this->importLcs, 0, 3) as $index => $lc) {
            $request = ApprovalRequest::create([
                'organization_id' => $this->organization->id,
                'workflow_id' => $workflow->id,
                'approvable_type' => ImportLc::class,
                'approvable_id' => $lc->id,
                'requested_by_user_id' => $manager->id,
                'current_step' => $index < 2 ? 2 : 1,
                'total_steps' => 2,
                'status' => $index === 2 ? 'Pending' : 'Approved',
                'completed_at' => $index === 2 ? null : now(),
            ]);

            ApprovalHistory::create([
                'approval_request_id' => $request->id,
                'user_id' => $manager->id,
                'step_number' => 1,
                'action' => 'Approved',
                'comments' => 'Manager clearance for demo LC.',
                'action_at' => now()->subDays(2),
            ]);

            if ($index < 2) {
                ApprovalHistory::create([
                    'approval_request_id' => $request->id,
                    'user_id' => $owner->id,
                    'step_number' => 2,
                    'action' => 'Approved',
                    'comments' => 'Owner final approval.',
                    'action_at' => now()->subDay(),
                ]);
            }
        }
    }

    protected function seedSubscription(): void
    {
        $plan = SubscriptionPlan::where('slug', 'professional')->first();

        if ($plan) {
            Subscription::create([
                'organization_id' => $this->organization->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now()->subMonth(),
                'trial_ends_at' => null,
                'auto_renew' => true,
            ]);
        }
    }

    protected function seedPublicContent(): void
    {
        CmsPage::updateOrCreate(
            ['slug' => 'about-trade-finance-demo'],
            [
                'title' => 'About Global Trade Solutions Demo',
                'content' => '<p>Welcome to the Global Trade Solutions Ltd demo workspace showcasing import/export LC tracking.</p>',
                'meta_title' => 'Trade Finance Demo',
                'meta_description' => 'Explore a realistic Bangladesh-based trade finance operations demo.',
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        KnowledgeArticle::updateOrCreate(
            ['slug' => 'understanding-import-lc-lifecycle'],
            [
                'category' => 'Import LC',
                'title' => 'Understanding the Import LC Lifecycle',
                'summary' => 'A quick primer on statuses from draft through settlement.',
                'content' => 'This knowledge article explains how import letters of credit progress from internal draft to bank settlement in the demo environment.',
                'is_published' => true,
            ]
        );

        KnowledgeArticle::updateOrCreate(
            ['slug' => 'document-presentation-checklist'],
            [
                'category' => 'Documentation',
                'title' => 'Document Presentation Checklist',
                'summary' => 'Standard documents required under UCP 600 presentations.',
                'content' => 'Use this checklist when preparing invoices, packing lists, bills of lading, and certificates before bank submission.',
                'is_published' => true,
            ]
        );
    }
}
