<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Deadlines Engine
        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->morphs('trackable'); // import_lcs, export_lcs, shipments, discrepancies, payment_schedules, etc.
            
            $table->string('title');
            $table->string('deadline_type');
            // lc_expiry, latest_shipment, presentation_deadline, payment_maturity, discrepancy_resolution, insurance_expiry, custom

            $table->date('due_date')->index();
            $table->time('due_time')->nullable();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('high');
            $table->enum('status', ['pending', 'completed', 'overdue', 'waived'])->default('pending')->index();
            
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Reminders (90, 60, 30, 15, 7, 3, 1 day before, due date, overdue)
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deadline_id')->constrained()->cascadeOnDelete();
            $table->integer('days_before'); // e.g. 90, 60, 30, 15, 7, 3, 1, 0, -1 (overdue)
            $table->date('scheduled_for')->index();
            $table->enum('channel', ['in_app', 'email', 'sms', 'whatsapp'])->default('in_app');
            $table->enum('status', ['pending', 'sent', 'failed', 'cancelled'])->default('pending')->index();
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->unique(['deadline_id', 'days_before', 'channel']);
        });

        // Notification Logs
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel')->default('in_app');
            $table->string('subject');
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->string('status')->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // Escalation Rules
        Schema::create('escalation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('deadline_type');
            $table->integer('days_overdue')->default(1);
            $table->string('escalate_to_role')->default('trade_finance_manager');
            $table->string('notify_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_rules');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('reminders');
        Schema::dropIfExists('deadlines');
    }
};
