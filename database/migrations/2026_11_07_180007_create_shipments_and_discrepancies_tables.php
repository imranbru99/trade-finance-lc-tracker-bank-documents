<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shipments
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('trackable'); // import_lcs or export_lcs
            
            $table->string('shipment_reference')->index();
            $table->integer('shipment_number')->default(1);
            $table->string('shipping_company')->nullable();
            $table->string('freight_forwarder')->nullable();
            $table->string('carrier')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('container_number')->nullable();
            $table->string('bill_of_lading_number')->nullable()->index();
            
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->date('etd')->nullable();
            $table->date('eta')->nullable();
            $table->date('actual_departure_date')->nullable();
            $table->date('actual_arrival_date')->nullable();
            
            $table->text('goods_description')->nullable();
            $table->string('shipment_quantity')->nullable();
            $table->decimal('shipment_value', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('USD');
            
            $table->string('status')->default('Planned');
            // Planned, Booked, Ready for Shipment, Shipped, In Transit, Arrived, Customs Processing, Delivered, Delayed, Cancelled

            $table->json('milestones')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Discrepancies
        Schema::create('discrepancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('trackable'); // import_lcs or export_lcs
            
            $table->foreignId('document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->string('discrepancy_reference')->index();
            $table->string('category')->default('Documentary');
            // Documentary, Technical, Shipment Delay, Missing Clause, Value Mismatch, Other

            $table->text('description');
            $table->date('identified_date');
            $table->date('resolution_deadline')->nullable();
            $table->text('corrective_action')->nullable();
            $table->string('bank_communication_ref')->nullable();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('status')->default('Identified');
            // Identified, Under Review, Awaiting Correction, Submitted for Waiver, Waiver Pending, Resolved, Rejected, Closed

            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Discrepancy Resolutions / Actions History
        Schema::create('discrepancy_resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discrepancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action_taken');
            $table->string('status_transition');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discrepancy_resolutions');
        Schema::dropIfExists('discrepancies');
        Schema::dropIfExists('shipments');
    }
};
