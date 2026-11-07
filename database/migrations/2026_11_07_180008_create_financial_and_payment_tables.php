<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Schedules & Maturities
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('trackable'); // import_lcs or export_lcs
            
            $table->string('schedule_number')->index();
            $table->string('payment_type')->default('Sight');
            // Sight, Deferred, Usance, Acceptance, Negotiation

            $table->date('due_date')->index();
            $table->date('maturity_date')->nullable()->index();
            $table->date('settlement_date')->nullable();
            
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->decimal('outstanding_amount', 15, 2)->default(0.00);
            
            $table->string('status')->default('Scheduled');
            // Scheduled, Due, Matured, Partially Paid, Settled, Overdue, Cancelled

            $table->string('payment_ref')->nullable();
            $table->text('bank_account_details')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Payment Records (Actual recorded settlements)
        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('payment_method')->default('Bank Wire / SWIFT');
            $table->string('transaction_reference')->nullable()->index();
            $table->decimal('bank_charge', 10, 2)->default(0.00);
            $table->foreignId('paid_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Bank Charges & Fees
        Schema::create('bank_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('trackable'); // import_lcs, export_lcs, amendments
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            
            $table->string('charge_type');
            // Issuance, Amendment, Negotiation, Advising, SWIFT, Courier, Discrepancy, Interest, Commission, Other

            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');
            $table->date('date_debited')->nullable();
            $table->string('debit_account')->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Currency Rates
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 10)->default('USD');
            $table->string('target_currency', 10);
            $table->decimal('exchange_rate', 14, 6);
            $table->date('effective_date')->index();
            $table->string('source')->default('Central Bank / Market');
            $table->timestamps();
            $table->unique(['base_currency', 'target_currency', 'effective_date']);
        });

        // Financial Exposure Records
        Schema::create('financial_exposures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->string('currency', 10)->default('USD');
            $table->decimal('total_lc_exposure', 18, 2)->default(0.00);
            $table->decimal('utilized_exposure', 18, 2)->default(0.00);
            $table->decimal('available_limit', 18, 2)->default(0.00);
            $table->date('snapshot_date')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_exposures');
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('bank_charges');
        Schema::dropIfExists('payment_records');
        Schema::dropIfExists('payment_schedules');
    }
};
