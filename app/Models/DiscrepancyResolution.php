<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscrepancyResolution extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function discrepancy(): BelongsTo
    {
        return $this->belongsTo(Discrepancy::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
