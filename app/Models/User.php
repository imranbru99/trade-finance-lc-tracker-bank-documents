<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return (bool) $this->is_platform_admin;
        }

        if ($panel->getId() === 'app') {
            return $this->is_platform_admin || $this->organizations()->exists();
        }

        return true;
    }

    public function getTenants(Panel $panel): Collection
    {
        if ($this->is_platform_admin) {
            return Organization::all();
        }

        return $this->organizations;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($this->is_platform_admin) {
            return true;
        }

        return $this->organizations()->where('organizations.id', $tenant->id)->exists();
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'tenant_users')
            ->withPivot(['branch_id', 'role_slug', 'is_owner', 'status'])
            ->withTimestamps();
    }

    public function currentOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    public function tenantUsers(): HasMany
    {
        return $this->hasMany(TenantUser::class);
    }

    public function hasRole(string $roleSlug, ?int $organizationId = null): bool
    {
        if ($this->is_platform_admin) {
            return true;
        }

        $query = $this->tenantUsers();
        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        return $query->where('role_slug', $roleSlug)->exists();
    }

    public function hasPermission(string $permissionSlug, ?int $organizationId = null): bool
    {
        if ($this->is_platform_admin) {
            return true;
        }

        $tenantUser = $this->tenantUsers()
            ->when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->first();

        if (! $tenantUser) {
            return false;
        }

        if ($tenantUser->is_owner) {
            return true;
        }

        $role = Role::where('slug', $tenantUser->role_slug)->first();
        if (! $role) {
            return false;
        }

        return $role->permissions()->where('slug', $permissionSlug)->exists();
    }
}
