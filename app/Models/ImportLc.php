<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportLc extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $guarded = ['id'];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'latest_shipment_date' => 'date',
        'lc_amount' => 'decimal:2',
        'tolerance_percentage' => 'decimal:2',
        'utilized_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
        'partial_shipment_allowed' => 'boolean',
        'transshipment_allowed' => 'boolean',
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

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    public function issuingBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'issuing_bank_id');
    }

    public function advisingBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'advising_bank_id');
    }

    public function confirmingBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'confirming_bank_id');
    }

    public function negotiatingBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'negotiating_bank_id');
    }

    public function nominatedBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'nominated_bank_id');
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

    public function bankCharges(): MorphMany
    {
        return $this->morphMany(BankCharge::class, 'trackable');
    }

    public function approvalRequests(): MorphMany
    {
        return $this->morphMany(ApprovalRequest::class, 'approvable');
    }
}
