<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bank Document Checklist - {{ $lc->lc_number }}</title>
    <style>
        body { font-family: 'SolaimanLipi', 'Noto Sans Bengali', 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; padding: 20px; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; color: #0f172a; }
        .meta { font-size: 12px; color: #475569; margin-top: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        .table th { background: #f1f5f9; font-weight: bold; }
        .status-badge { padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        .status-Approved { background: #dcfce7; color: #15803d; }
        .status-Pending { background: #fef3c7; color: #b45309; }
        .status-Uploaded { background: #e0f2fe; color: #0369a1; }
        .status-Discrepant { background: #fee2e2; color: #b91c1c; }
        .footer { margin-top: 30px; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $organization->name ?? 'Trade Finance' }} - Document Presentation Checklist</h1>
        <div class="meta">
            LC Number: <strong>{{ $lc->lc_number }}</strong> | Currency & Value: <strong>{{ $lc->currency }} {{ number_format((float) $lc->lc_amount, 2) }}</strong> | Generated: {{ $generated_at }}
        </div>
    </div>

    <h3>Required Documents & Examination Status</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Document Type</th>
                <th>Originals / Copies</th>
                <th>Mandatory</th>
                <th>Current Status</th>
                <th>Verification Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requirements as $req)
            <tr>
                <td><strong>{{ $req->document_name }}</strong></td>
                <td>{{ $req->copies_original }} Original / {{ $req->copies_duplicate }} Copy</td>
                <td>{{ $req->is_mandatory ? 'Yes' : 'No' }}</td>
                <td>
                    <span class="status-badge status-{{ $req->status }}">{{ $req->status }}</span>
                </td>
                <td>{{ $req->notes ?: 'Pending submission / bank examination' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No specific document requirements recorded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Bank Document Checklist &bull; Subject to UCP 600 Examination &bull; Trade Finance & LC Tracker
    </div>
</body>
</html>
