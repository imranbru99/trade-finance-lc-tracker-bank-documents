<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::creating(function ($model) {
            if (empty($model->organization_id)) {
                $tenant = Filament::getTenant();
                if ($tenant) {
                    $model->organization_id = $tenant->getKey();
                } elseif (auth()->check() && auth()->user()->current_organization_id) {
                    $model->organization_id = auth()->user()->current_organization_id;
                }
            }
        });

        static::addGlobalScope('organization', function (Builder $builder) {
            // If in Filament tenant context
            if ($tenant = Filament::getTenant()) {
                $builder->where($builder->getModel()->getTable() . '.organization_id', $tenant->getKey());
            } elseif (auth()->check() && !auth()->user()->is_platform_admin && auth()->user()->current_organization_id) {
                // If logged-in tenant user outside filament or API
                $builder->where($builder->getModel()->getTable() . '.organization_id', auth()->user()->current_organization_id);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
