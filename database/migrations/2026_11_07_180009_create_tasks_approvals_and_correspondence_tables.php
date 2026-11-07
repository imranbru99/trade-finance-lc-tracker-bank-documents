<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tasks Engine
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('trackable'); // link to LC, shipment, discrepancy, etc.
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('task_type')->default('General Follow-up');
            // LC Preparation, Document Collection, Bank Follow-up, Shipment Follow-up, Amendment Processing, Discrepancy Resolution, Payment Follow-up, Compliance Review
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['todo', 'in_progress', 'completed', 'overdue', 'cancelled'])->default('todo')->index();
            $table->date('due_date')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Task Comments
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
        });

        // Approval Workflows Definition
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('module')->default('import_lc'); // import_lc, export_lc, lc_amendment, payment, discrepancy
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('min_amount', 15, 2)->default(0.00);
            $table->json('steps')->nullable(); // ordered list of roles or user IDs
            $table->timestamps();
        });

        // Approval Requests
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->nullable()->constrained('approval_workflows')->nullOnDelete();
            $table->morphs('approvable'); // import_lcs, export_lcs, lc_amendments, payment_schedules, etc.
            
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('current_step')->default(1);
            $table->integer('total_steps')->default(1);
            
            $table->string('status')->default('Pending')->index(); // Pending, Approved, Rejected, Delegated, Cancelled
            $table->text('decision_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Approval Histories
        Schema::create('approval_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('step_number')->default(1);
            $table->string('action'); // Approved, Rejected, Delegated, Resubmitted
            $table->text('comments')->nullable();
            $table->timestamp('action_at');
            $table->timestamps();
        });

        // Bank Correspondence & SWIFT Messages
        Schema::create('bank_correspondence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('trackable'); // link to LC
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            
            $table->string('correspondence_type')->default('Email');
            // Email, Bank Letter, Bank Advice, Amendment Communication, Document Submission Letter, Discrepancy Notice, Acceptance Notice, Payment Advice, SWIFT Record

            $table->string('reference_number')->nullable()->index();
            $table->string('subject');
            $table->date('message_date');
            $table->enum('direction', ['Incoming', 'Outgoing'])->default('Incoming');
            $table->string('sender')->nullable();
            $table->string('recipient')->nullable();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->json('attachment_paths')->nullable();
            $table->string('swift_message_type', 20)->nullable(); // MT700, MT707, MT710, MT720, MT734, MT752, MT799
            
            $table->string('status')->default('Logged'); // Logged, Action Required, Resolved, Archived
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_correspondence');
        Schema::dropIfExists('approval_histories');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_workflows');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('tasks');
    }
};
