<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExportLc extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $guarded = ['id'];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'latest_shipment_date' => 'date',
        'presentation_deadline' => 'date',
        'export_value' => 'decimal:2',
        'realized_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
        'required_documents' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function responsibleOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class, 'counterparty_id');
    }

    public function advisingBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'advising_bank_id');
    }

    public function amendments(): MorphMany
    {
        return $this->morphMany(LcAmendment::class, 'amendable');
    }

    public function statusHistories(): MorphMany
    {
        return $this->morphMany(LcStatusHistory::class, 'trackable');
    }

    public function documentRequirements(): MorphMany
    {
        return $this->morphMany(LcDocumentRequirement::class, 'trackable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function deadlines(): MorphMany
    {
        return $this->morphMany(Deadline::class, 'trackable');
    }

    public function shipments(): MorphMany
    {
        return $this->morphMany(Shipment::class, 'trackable');
    }

    public function discrepancies(): MorphMany
    {
        return $this->morphMany(Discrepancy::class, 'trackable');
    }

    public function paymentSchedules(): MorphMany
    {
        return $this->morphMany(PaymentSchedule::class, 'trackable');
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'trackable');
    }

    public function bankCorrespondence(): MorphMany
    {
        return $this->morphMany(BankCorrespondence::class, 'trackable');
    }
}
