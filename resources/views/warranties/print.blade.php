<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warranty Certificate - {{ $warranty->warranty_no }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #1f2937;
            background-color: #f9fafb;
        }
        .certificate-card {
            max-w: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #2563eb;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .ref-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-family: monospace;
            margin-top: 15px;
            font-size: 16px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        .box-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .box-value {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
        }
        .terms {
            border-t: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-bottom: 30px;
        }
        .terms h3 {
            font-size: 14px;
            text-transform: uppercase;
            color: #4b5563;
            margin-bottom: 8px;
        }
        .terms p {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #94a3b8;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            padding-bottom: 5px;
        }
        @media print {
            body { background: white; padding: 0; }
            .certificate-card { border: 2px solid #000; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: bold; cursor: pointer;">
        Print Certificate
    </button>
</div>

<div class="certificate-card">
    <div class="header">
        <h1>Warranty Certificate</h1>
        <p>Official Product Warranty Document</p>
        <div class="ref-badge">{{ $warranty->warranty_no }}</div>
    </div>

    <div class="grid">
        <div class="box">
            <div class="box-title">Product Name</div>
            <div class="box-value">{{ $warranty->product->name ?? 'N/A' }}</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">SKU: {{ $warranty->product->sku ?? 'N/A' }}</div>
        </div>
        <div class="box">
            <div class="box-title">Customer Name</div>
            <div class="box-value">{{ $warranty->customer->name ?? 'N/A' }}</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Phone: {{ $warranty->customer->phone ?? 'N/A' }}</div>
        </div>
        <div class="box">
            <div class="box-title">Serial / Batch Number</div>
            <div class="box-value font-mono">{{ $warranty->serial_number ?? ($warranty->batch_number ?? 'N/A') }}</div>
        </div>
        <div class="box">
            <div class="box-title">Warranty Coverage</div>
            <div class="box-value">{{ $warranty->type_label }} ({{ $warranty->warranty_duration_months }} Months)</div>
        </div>
        <div class="box">
            <div class="box-title">Purchase Date</div>
            <div class="box-value">{{ $warranty->purchase_date ? $warranty->purchase_date->format('d F Y') : 'N/A' }}</div>
        </div>
        <div class="box">
            <div class="box-title">Expiration Date</div>
            <div class="box-value" style="color: #2563eb;">{{ $warranty->expiry_date ? $warranty->expiry_date->format('d F Y') : 'N/A' }}</div>
        </div>
    </div>

    @if($warranty->terms)
        <div class="terms">
            <h3>Terms & Conditions</h3>
            <p>{{ $warranty->terms }}</p>
        </div>
    @endif

    <div class="footer">
        <div style="font-size: 11px; color: #94a3b8;">
            Issued on {{ date('d F Y') }}<br>
            Authorized Store Stamp / Signature
        </div>
        <div class="signature-line">
            Authorized Signature
        </div>
    </div>
</div>

</body>
</html>
