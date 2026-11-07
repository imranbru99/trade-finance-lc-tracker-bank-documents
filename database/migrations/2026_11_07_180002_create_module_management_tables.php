<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // System Modules Catalog
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('category', ['core', 'trade_finance', 'advanced', 'optional'])->default('trade_finance');
            $table->string('icon')->default('heroicon-o-cube');
            $table->string('version')->default('1.0.0');
            $table->boolean('is_core')->default(false);
            $table->json('dependencies')->nullable(); // Slugs of required modules
            $table->boolean('default_enabled')->default(true);
            $table->json('settings_schema')->nullable();
            $table->timestamps();
        });

        // Tenant Module Activations & Configs
        Schema::create('tenant_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('module_slug');
            $table->boolean('is_enabled')->default(true);
            $table->json('settings')->nullable();
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'module_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_modules');
        Schema::dropIfExists('modules');
    }
};
