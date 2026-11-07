<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bank Directory
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('swift_bic', 20)->nullable();
            $table->string('routing_number', 50)->nullable();
            $table->string('country', 100)->default('Bangladesh');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('category', ['commercial', 'central', 'development', 'islamic', 'foreign', 'offshore'])->default('commercial');
            $table->boolean('is_global')->default(false);
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Bank Branches
        Schema::create('bank_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete();
            $table->string('branch_name');
            $table->string('branch_code', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Bangladesh');
            $table->string('swift_bic', 20)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // Bank Contacts / Relationship Managers
        Schema::create('bank_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Counterparties (Buyers, Suppliers, Carriers, Freight Forwarders)
        Schema::create('counterparties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['buyer', 'supplier', 'freight_forwarder', 'shipping_line', 'insurer', 'other'])->default('supplier');
            $table->string('country', 100)->default('Bangladesh');
            $table->string('tax_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0.00);
            $table->string('rating', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counterparties');
        Schema::dropIfExists('bank_contacts');
        Schema::dropIfExists('bank_branches');
        Schema::dropIfExists('banks');
    }
};
