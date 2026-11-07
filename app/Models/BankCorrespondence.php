<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankCorrespondence extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $table = 'bank_correspondence';

    protected $guarded = ['id'];

    protected $casts = [
        'message_date' => 'date',
        'attachment_paths' => 'array',
    ];

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
