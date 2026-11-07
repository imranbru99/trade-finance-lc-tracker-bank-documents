<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>Letter of Credit Summary - {{ $lc->lc_number }}</title>
    <style>
        body { font-family: 'SolaimanLipi', 'Noto Sans Bengali', 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; line-height: 1.5; padding: 20px; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; color: #0f172a; margin: 0; }
        .subtitle { font-size: 13px; color: #64748b; margin-top: 4px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; background: #e0f2fe; color: #0369a1; font-weight: bold; font-size: 11px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; vertical-align: top; }
        .table th { background: #f8fafc; font-weight: bold; color: #334155; width: 28%; }
        .section-heading { font-size: 14px; font-weight: bold; color: #0f172a; border-left: 4px solid #0284c7; padding-left: 8px; margin-top: 20px; margin-bottom: 10px; }
        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 10px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ $organization->name ?? 'Trade Finance & LC Tracker' }}</h1>
        <div class="subtitle">Documentary Credit Summary / লেটার অফ ক্রেডিট বিবরণী | Generated: {{ $generated_at }}</div>
    </div>

    <div style="margin-bottom: 15px;">
        <strong>LC Status:</strong> <span class="badge">{{ $lc->status }}</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;<strong>LC Type:</strong> {{ $lc->lc_type }}
        &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Internal Ref:</strong> {{ $lc->internal_reference ?: 'N/A' }}
    </div>

    <div class="section-heading">1. General & Financial Terms / সাধারণ ও আর্থিক শর্তাবলী</div>
    <table class="table">
        <tr>
            <th>LC Reference Number</th>
            <td><strong>{{ $lc->lc_number }}</strong></td>
        </tr>
        <tr>
            <th>LC Amount & Currency</th>
            <td><strong>{{ $lc->currency }} {{ number_format((float) $lc->lc_amount, 2) }}</strong> (Tolerance: ±{{ $lc->tolerance_percentage }}%)</td>
        </tr>
        <tr>
            <th>Utilized & Outstanding</th>
            <td>Utilized: {{ $lc->currency }} {{ number_format((float) $lc->utilized_amount, 2) }} | Outstanding: {{ $lc->currency }} {{ number_format((float) $lc->outstanding_amount, 2) }}</td>
        </tr>
        <tr>
            <th>Payment Terms</th>
            <td>{{ $lc->payment_terms }} {{ $lc->payment_details ? "({$lc->payment_details})" : '' }}</td>
        </tr>
        <tr>
            <th>Issue & Expiry Dates</th>
            <td>Issued: {{ $lc->issue_date?->toFormattedDateString() ?: 'N/A' }} | Expiry: <strong>{{ $lc->expiry_date?->toFormattedDateString() }}</strong> (Place: {{ $lc->expiry_place ?: 'Beneficiary Country' }})</td>
        </tr>
        <tr>
            <th>Latest Shipment Date</th>
            <td><strong>{{ $lc->latest_shipment_date?->toFormattedDateString() ?: 'N/A' }}</strong> (Presentation Period: {{ $lc->presentation_period_days }} days)</td>
        </tr>
    </table>

    <div class="section-heading">2. Contracting Parties & Banks / পক্ষ ও ব্যাংক বিবরণী</div>
    <table class="table">
        <tr>
            <th>Applicant (আমদানিকারক)</th>
            <td><strong>{{ $lc->applicant_name }}</strong><br>{{ $lc->applicant_address }}</td>
        </tr>
        <tr>
            <th>Beneficiary (রপ্তানিকারক)</th>
            <td><strong>{{ $lc->beneficiary_name }}</strong><br>{{ $lc->beneficiary_address }}</td>
        </tr>
        <tr>
            <th>Issuing Bank (ইস্যুকারী ব্যাংক)</th>
            <td>{{ $lc->issuingBank?->name ?: 'N/A' }} (SWIFT: {{ $lc->issuingBank?->swift_bic ?: 'N/A' }})</td>
        </tr>
        <tr>
            <th>Advising / Confirming Bank</th>
            <td>Advising: {{ $lc->advisingBank?->name ?: 'N/A' }} | Confirming: {{ $lc->confirmingBank?->name ?: 'None' }}</td>
        </tr>
    </table>

    <div class="section-heading">3. Shipment & Goods Details / পণ্য ও জাহাজীকরণ বিবরণী</div>
    <table class="table">
        <tr>
            <th>Goods Description</th>
            <td>{{ $lc->goods_description ?: 'As per proforma invoice' }} (HS Code: {{ $lc->hs_code ?: 'N/A' }})</td>
        </tr>
        <tr>
            <th>Ports & Incoterms</th>
            <td>Loading: {{ $lc->port_of_loading ?: 'Any Port' }} &rarr; Discharge: {{ $lc->port_of_discharge ?: 'Any Port' }} (Incoterms: {{ $lc->incoterms }})</td>
        </tr>
        <tr>
            <th>Partial / Transshipment</th>
            <td>Partial Shipment: {{ $lc->partial_shipment_allowed ? 'Allowed' : 'Prohibited' }} | Transshipment: {{ $lc->transshipment_allowed ? 'Allowed' : 'Prohibited' }}</td>
        </tr>
    </table>

    <div class="footer">
        Confidential Trade Finance Record &bull; Subject to UCP 600 International Rules &bull; Generated by Trade Finance & LC Tracker
    </div>
</body>
</html>
