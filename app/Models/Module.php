<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_core' => 'boolean',
        'default_enabled' => 'boolean',
        'dependencies' => 'array',
        'settings_schema' => 'array',
    ];

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class, 'module_slug', 'slug');
    }
}
