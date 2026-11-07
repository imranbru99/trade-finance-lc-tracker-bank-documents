<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Import LCs
        Schema::create('import_lcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Responsible Officer
            $table->foreignId('counterparty_id')->nullable()->constrained()->nullOnDelete(); // Supplier / Beneficiary
            
            // Banking Relationships
            $table->foreignId('issuing_bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('advising_bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('confirming_bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('negotiating_bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('nominated_bank_id')->nullable()->constrained('banks')->nullOnDelete();

            // Identifiers
            $table->string('lc_number')->index();
            $table->string('internal_reference')->nullable()->index();
            $table->string('lc_type')->default('Irrevocable'); // Irrevocable, Transferable, Revolving, Standby, Back-to-Back, Red Clause

            // Key Dates
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->index();
            $table->string('expiry_place')->nullable();
            $table->date('latest_shipment_date')->nullable()->index();
            $table->integer('presentation_period_days')->default(21);

            // Parties
            $table->string('applicant_name');
            $table->text('applicant_address')->nullable();
            $table->string('beneficiary_name');
            $table->text('beneficiary_address')->nullable();

            // Financials
            $table->string('currency', 10)->default('USD');
            $table->decimal('lc_amount', 15, 2);
            $table->decimal('tolerance_percentage', 5, 2)->default(0.00);
            $table->decimal('utilized_amount', 15, 2)->default(0.00);
            $table->decimal('outstanding_amount', 15, 2)->default(0.00);

            // Logistics & Shipment
            $table->string('country_of_origin')->nullable();
            $table->string('country_of_destination')->nullable();
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->string('incoterms', 20)->default('FOB');
            $table->text('goods_description')->nullable();
            $table->string('hs_code')->nullable();
            $table->boolean('partial_shipment_allowed')->default(true);
            $table->boolean('transshipment_allowed')->default(false);

            // Terms
            $table->string('payment_terms')->default('Sight'); // Sight, Deferred, Usance, Mixed
            $table->text('payment_details')->nullable();
            $table->json('required_documents')->nullable();
            $table->text('special_conditions')->nullable();
            $table->text('internal_notes')->nullable();

            // Status Lifecycle
            $table->string('status')->default('Draft')->index();
            // Draft, Pending Internal Review, Pending Approval, Submitted to Bank, Issued, Advised,
            // Amendment Requested, Amendment Pending, Active, Partially Shipped, Fully Shipped,
            // Documents Submitted, Under Bank Examination, Discrepancy Found, Accepted, Payment Pending,
            // Matured, Settled, Expired, Cancelled, Closed

            $table->timestamps();
            $table->softDeletes();
        });

        // Export LCs
        Schema::create('export_lcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Responsible Officer
            $table->foreignId('counterparty_id')->nullable()->constrained()->nullOnDelete(); // Buyer / Applicant

            $table->foreignId('advising_bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->string('issuing_bank_name')->nullable();
            $table->string('issuing_bank_swift', 20)->nullable();
            $table->string('confirming_bank_name')->nullable();

            // Identifiers & Contract
            $table->string('lc_number')->index();
            $table->string('contract_reference')->nullable()->index();
            $table->string('buyer_name');
            $table->text('buyer_address')->nullable();

            // Financials
            $table->string('currency', 10)->default('USD');
            $table->decimal('export_value', 15, 2);
            $table->decimal('realized_amount', 15, 2)->default(0.00);
            $table->decimal('pending_amount', 15, 2)->default(0.00);

            // Dates
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->index();
            $table->date('latest_shipment_date')->nullable()->index();
            $table->date('presentation_deadline')->nullable()->index();

            // Details
            $table->text('goods_description')->nullable();
            $table->string('hs_code')->nullable();
            $table->string('incoterms', 20)->default('FOB');
            $table->text('shipment_schedule')->nullable();
            $table->json('required_documents')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('internal_notes')->nullable();

            // Status
            $table->string('status')->default('Draft')->index();
            // Draft, Registered, Advised, Active, Partially Shipped, Fully Shipped, Documents Dispatched, Negotiated, Matured, Realized, Expired, Closed

            $table->timestamps();
            $table->softDeletes();
        });

        // LC Amendments (Polymorphic: ImportLC or ExportLC)
        Schema::create('lc_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->morphs('amendable'); // amendable_type, amendable_id
            $table->integer('amendment_number')->default(1);
            $table->date('request_date');
            $table->date('bank_submission_date')->nullable();
            $table->date('bank_approval_date')->nullable();
            $table->string('status')->default('Requested'); // Requested, Pending Bank, Approved, Rejected, Cancelled
            $table->text('reason');
            $table->json('requested_changes')->nullable();
            $table->json('previous_values')->nullable();
            $table->json('new_values')->nullable();
            $table->decimal('amount_change', 15, 2)->default(0.00);
            $table->date('new_expiry_date')->nullable();
            $table->date('new_latest_shipment_date')->nullable();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // LC Status Histories
        Schema::create('lc_status_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('trackable'); // ImportLC, ExportLC, etc.
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // LC Document Requirements Checklist
        Schema::create('lc_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->morphs('trackable');
            $table->string('document_type'); // commercial_invoice, packing_list, bill_of_lading, certificate_of_origin, etc.
            $table->string('document_name');
            $table->integer('copies_original')->default(1);
            $table->integer('copies_duplicate')->default(2);
            $table->boolean('is_mandatory')->default(true);
            $table->string('status')->default('Pending'); // Pending, Uploaded, Under Review, Approved, Discrepant, Waived
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lc_document_requirements');
        Schema::dropIfExists('lc_status_histories');
        Schema::dropIfExists('lc_amendments');
        Schema::dropIfExists('export_lcs');
        Schema::dropIfExists('import_lcs');
    }
};
