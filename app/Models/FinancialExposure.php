<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialExposure extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $guarded = ['id'];

    protected $casts = [
        'total_lc_exposure' => 'decimal:2',
        'utilized_exposure' => 'decimal:2',
        'available_limit' => 'decimal:2',
        'snapshot_date' => 'date',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
