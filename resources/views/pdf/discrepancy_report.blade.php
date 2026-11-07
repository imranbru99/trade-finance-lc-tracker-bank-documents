<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discrepancy Notice - {{ $discrepancy->discrepancy_reference }}</title>
    <style>
        body { font-family: 'SolaimanLipi', 'Noto Sans Bengali', 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; padding: 20px; }
        .header { border-bottom: 2px solid #b91c1c; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #b91c1c; margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 8px 10px; vertical-align: top; }
        .table th { background: #fef2f2; width: 30%; color: #991b1b; }
        .badge { padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 11px; background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">OFFICIAL DISCREPANCY NOTICE</h1>
        <div>Discrepancy Ref: <strong>{{ $discrepancy->discrepancy_reference }}</strong> | Organization: <strong>{{ $organization->name ?? 'Trade Finance' }}</strong> | Date: {{ $generated_at }}</div>
    </div>

    <table class="table">
        <tr>
            <th>Status</th>
            <td><span class="badge">{{ $discrepancy->status }}</span></td>
        </tr>
        <tr>
            <th>Discrepancy Category</th>
            <td><strong>{{ $discrepancy->category }}</strong></td>
        </tr>
        <tr>
            <th>Date Identified</th>
            <td>{{ $discrepancy->identified_date?->toFormattedDateString() }}</td>
        </tr>
        <tr>
            <th>Resolution Deadline</th>
            <td><strong>{{ $discrepancy->resolution_deadline?->toFormattedDateString() ?: 'Urgent' }}</strong></td>
        </tr>
        <tr>
            <th>Bank Communication Ref</th>
            <td>{{ $discrepancy->bank_communication_ref ?: 'SWIFT Advice / Bank Discrepancy Memo' }}</td>
        </tr>
        <tr>
            <th>Discrepancy Description</th>
            <td>{{ $discrepancy->description }}</td>
        </tr>
        <tr>
            <th>Required Corrective Action</th>
            <td>{{ $discrepancy->corrective_action ?: 'Submit corrected replacement document or request applicant waiver.' }}</td>
        </tr>
        <tr>
            <th>Resolution Notes</th>
            <td>{{ $discrepancy->resolution_notes ?: 'Pending applicant review and bank waiver acceptance.' }}</td>
        </tr>
    </table>

    <div class="footer">
        Discrepancy Notice &bull; Article 16 UCP 600 Notification &bull; Trade Finance & LC Tracker
    </div>
</body>
</html>
