<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportLcResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'lc_number' => $this->lc_number,
            'internal_reference' => $this->internal_reference,
            'lc_type' => $this->lc_type,
            'status' => $this->status,
            'issue_date' => $this->issue_date?->toDateString(),
            'expiry_date' => $this->expiry_date?->toDateString(),
            'latest_shipment_date' => $this->latest_shipment_date?->toDateString(),
            'currency' => $this->currency,
            'lc_amount' => $this->lc_amount,
            'utilized_amount' => $this->utilized_amount,
            'outstanding_amount' => $this->outstanding_amount,
            'applicant_name' => $this->applicant_name,
            'beneficiary_name' => $this->beneficiary_name,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
