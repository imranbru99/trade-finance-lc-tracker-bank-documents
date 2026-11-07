<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>Import LC Register - {{ $organization->name }}</title>
    <style>
        body { font-family: 'SolaimanLipi', 'Noto Sans Bengali', 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; padding: 16px; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 11px; color: #64748b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f1f5f9; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $organization->name }}</h1>
        <div class="subtitle">Import LC Register | Generated: {{ $generated_at }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>LC Number</th>
                <th>Status</th>
                <th>Type</th>
                <th>Currency</th>
                <th>Amount</th>
                <th>Outstanding</th>
                <th>Issuing Bank</th>
                <th>Beneficiary</th>
                <th>Expiry</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($importLcs as $lc)
                <tr>
                    <td><strong>{{ $lc->lc_number }}</strong></td>
                    <td>{{ $lc->status }}</td>
                    <td>{{ $lc->lc_type }}</td>
                    <td>{{ $lc->currency }}</td>
                    <td>{{ number_format((float) $lc->lc_amount, 2) }}</td>
                    <td>{{ number_format((float) ($lc->outstanding_amount ?: $lc->lc_amount), 2) }}</td>
                    <td>{{ $lc->issuingBank?->name ?? '—' }}</td>
                    <td>{{ $lc->counterparty?->name ?? '—' }}</td>
                    <td>{{ $lc->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No active import LCs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Confidential trade finance register — verify against bank records before reliance.</div>
</body>
</html>
