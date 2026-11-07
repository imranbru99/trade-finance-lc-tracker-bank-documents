<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $guarded = ['id'];

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
}
