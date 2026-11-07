<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\RespondsWithPagination;
use App\Http\Controllers\Controller;
use App\Models\PaymentSchedule;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    use RespondsWithPagination;

    public function index(): JsonResponse
    {
        $paginator = PaymentSchedule::query()
            ->withoutGlobalScopes()
            ->where('organization_id', $this->organizationId())
            ->orderBy('maturity_date')
            ->paginate(min((int) request('per_page', 25), 100));

        $paginator->getCollection()->transform(fn (PaymentSchedule $p) => [
            'id' => $p->id,
            'payment_type' => $p->payment_type ?? null,
            'amount' => $p->amount,
            'currency' => $p->currency,
            'maturity_date' => $p->maturity_date?->toDateString(),
            'status' => $p->status,
        ]);

        return $this->paginatedResponse($paginator);
    }
}
