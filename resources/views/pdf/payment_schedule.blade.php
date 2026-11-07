<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Maturity Schedule - {{ $schedule->schedule_number }}</title>
    <style>
        body { font-family: 'SolaimanLipi', 'Noto Sans Bengali', 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; padding: 20px; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        .table th { background: #f8fafc; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; background: #e2e8f0; }
        .footer { margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $organization->name ?? 'Trade Finance' }} - Payment Maturity Advice</h1>
        <div>Schedule: <strong>{{ $schedule->schedule_number }}</strong> | Generated: {{ $generated_at }}</div>
    </div>

    <table class="table">
        <tr>
            <th style="width: 30%;">Payment Type</th>
            <td><strong>{{ $schedule->payment_type }}</strong></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span class="badge">{{ $schedule->status }}</span></td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td><strong>{{ $schedule->currency }} {{ number_format((float) $schedule->amount, 2) }}</strong></td>
        </tr>
        <tr>
            <th>Paid & Outstanding</th>
            <td>Paid: {{ $schedule->currency }} {{ number_format((float) $schedule->paid_amount, 2) }} | Outstanding: <strong>{{ $schedule->currency }} {{ number_format((float) $schedule->outstanding_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <th>Due & Maturity Date</th>
            <td>Due Date: <strong>{{ $schedule->due_date?->toFormattedDateString() }}</strong> (Maturity: {{ $schedule->maturity_date?->toFormattedDateString() ?: 'Sight' }})</td>
        </tr>
        <tr>
            <th>Bank Account / Settlement</th>
            <td>{{ $schedule->bank_account_details ?: 'Standard treasury settlement account' }}</td>
        </tr>
    </table>

    <h3>Recorded Settlements</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Transaction Reference</th>
                <th>Bank Charges</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $p)
            <tr>
                <td>{{ $p->payment_date?->toFormattedDateString() }}</td>
                <td><strong>{{ $p->currency }} {{ number_format((float) $p->amount, 2) }}</strong></td>
                <td>{{ $p->payment_method }}</td>
                <td>{{ $p->transaction_reference ?: 'N/A' }}</td>
                <td>{{ $p->currency }} {{ number_format((float) $p->bank_charge, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No partial payments recorded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Payment Maturity Advice &bull; Treasury & Trade Finance Platform &bull; Trade Finance & LC Tracker
    </div>
</body>
</html>
