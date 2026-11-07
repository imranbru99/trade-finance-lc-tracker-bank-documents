<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AI Document Processing Jobs
        Schema::create('ai_document_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->string('provider')->default('openai'); // openai, gemini, anthropic, mock
            $table->string('model')->default('gpt-4o');
            $table->string('job_type')->default('extraction'); // classification, extraction, comparison, discrepancy_check
            $table->string('status')->default('queued'); // queued, processing, completed, failed
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->decimal('cost_estimate', 8, 4)->default(0.0000);
            $table->text('error')->nullable();
            $table->timestamps();
        });

        // AI Extractions & Human Review Records
        Schema::create('ai_extractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_document_job_id')->nullable()->constrained('ai_document_jobs')->nullOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('documents')->nullOnDelete();
            
            $table->json('extracted_data'); // key-value pairs (lc_number, applicant, beneficiary, amount, currency, dates, etc.)
            $table->json('confidence_scores')->nullable(); // scores 0.0 - 1.0 per field
            $table->json('source_references')->nullable(); // page number, raw matched text
            
            // Mandatory Human Review
            $table->string('human_review_status')->default('Pending'); // Pending, Accepted, Adjusted, Rejected
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
        });

        // AI Conversational Assistant Sessions
        Schema::create('ai_assistant_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title')->default('Trade Finance AI Assistant');
            $table->json('messages')->nullable();
            $table->timestamps();
        });

        // SaaS Subscription Plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0.00);
            $table->decimal('price_yearly', 10, 2)->default(0.00);
            $table->integer('max_users')->default(5);
            $table->integer('max_companies')->default(1);
            $table->integer('max_branches')->default(2);
            $table->integer('max_lcs')->default(50);
            $table->integer('storage_mb')->default(5120); // 5GB
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // Tenant Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('active'); // active, trialing, past_due, canceled, expired
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();
        });

        // Billing Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('status')->default('unpaid'); // paid, unpaid, void
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_gateway')->default('sandbox');
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });

        // Audit Logs (Tamper-resistant audit trail)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event'); // created, updated, deleted, approved, rejected, logged_in, etc.
            $table->nullableMorphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // CMS Public Pages
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('locale', 10)->default('en');
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Knowledge Base Articles
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('General');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('locale', 10)->default('en');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // External Integration Connections
        Schema::create('integration_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('service_name'); // smtp, s3, r2, google_calendar, outlook, erp, whatsapp, sms, bank_api
            $table->string('status')->default('disconnected'); // connected, disconnected, error, mock
            $table->json('config')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'service_name']);
        });

        // Webhooks
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('target_url');
            $table->string('secret')->nullable();
            $table->json('events')->nullable(); // lc.created, lc.updated, lc.expiring, document.uploaded, shipment.updated, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Webhook Deliveries
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->json('payload');
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->string('status')->default('pending'); // pending, success, failed
            $table->integer('attempt')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('integration_connections');
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('ai_assistant_sessions');
        Schema::dropIfExists('ai_extractions');
        Schema::dropIfExists('ai_document_jobs');
    }
};
