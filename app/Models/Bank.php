<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(BankBranch::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(BankContact::class);
    }

    public function importLcsAsIssuing(): HasMany
    {
        return $this->hasMany(ImportLc::class, 'issuing_bank_id');
    }

    public function importLcsAsAdvising(): HasMany
    {
        return $this->hasMany(ImportLc::class, 'advising_bank_id');
    }
}
