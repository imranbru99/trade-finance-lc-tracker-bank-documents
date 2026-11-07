<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BankCharge extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $guarded = ['id'];

    protected $casts = [
        'date_debited' => 'date',
        'amount' => 'decimal:2',
    ];

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
