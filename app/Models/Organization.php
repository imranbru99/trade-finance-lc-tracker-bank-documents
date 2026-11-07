<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model implements HasCurrentTenantLabel, HasAvatar, HasName
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'settings' => 'array',
    ];

    public function getFilamentName(): string
    {
        return (string) $this->name;
    }

    public function getCurrentTenantLabel(): string
    {
        return 'Active Workspace: ' . $this->name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->logo_url;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_users')
            ->withPivot(['branch_id', 'role_slug', 'is_owner', 'status'])
            ->withTimestamps();
    }

    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function importLcs(): HasMany
    {
        return $this->hasMany(ImportLc::class);
    }

    public function exportLcs(): HasMany
    {
        return $this->hasMany(ExportLc::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(Deadline::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function discrepancies(): HasMany
    {
        return $this->hasMany(Discrepancy::class);
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function bankCorrespondence(): HasMany
    {
        return $this->hasMany(BankCorrespondence::class);
    }

    public function counterparties(): HasMany
    {
        return $this->hasMany(Counterparty::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function isModuleEnabled(string $moduleSlug): bool
    {
        $mod = $this->modules()->where('module_slug', $moduleSlug)->first();
        if ($mod) {
            return (bool) $mod->is_enabled;
        }

        // Check global default in modules catalog
        $globalMod = Module::where('slug', $moduleSlug)->first();
        return $globalMod ? (bool) $globalMod->default_enabled : true;
    }
}
