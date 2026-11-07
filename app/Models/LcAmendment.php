<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LcAmendment extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $guarded = ['id'];

    protected $casts = [
        'request_date' => 'date',
        'bank_submission_date' => 'date',
        'bank_approval_date' => 'date',
        'new_expiry_date' => 'date',
        'new_latest_shipment_date' => 'date',
        'amount_change' => 'decimal:2',
        'requested_changes' => 'array',
        'previous_values' => 'array',
        'new_values' => 'array',
    ];

    public function amendable(): MorphTo
    {
        return $this->morphTo();
    }

    public function responsibleOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
}
