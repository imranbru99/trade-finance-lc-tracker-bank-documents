<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExportLcResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'lc_number' => $this->lc_number,
            'internal_reference' => $this->internal_reference,
            'status' => $this->status,
            'issue_date' => $this->issue_date?->toDateString(),
            'expiry_date' => $this->expiry_date?->toDateString(),
            'latest_shipment_date' => $this->latest_shipment_date?->toDateString(),
            'presentation_deadline' => $this->presentation_deadline?->toDateString(),
            'currency' => $this->currency,
            'export_value' => $this->export_value,
            'realized_amount' => $this->realized_amount,
            'pending_amount' => $this->pending_amount,
            'buyer_name' => $this->buyer_name,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
