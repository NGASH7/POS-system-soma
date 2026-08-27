<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate/Quote {{ $quotation->quotation_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /*
          Color choices are deliberate: every colored fill below is dark/saturated
          enough that it still converts to a strong, legible gray when printed on
          a black & white printer (no pastel/light washes that disappear on paper).
        */
        :root {
            --navy: #0f1b3d;      /* prints near-black */
            --blue: #1d4ed8;      /* prints medium-dark gray, reads clearly */
            --blue-dark: #15359e; /* prints dark gray */
            --text: #111318;
            --muted: #55596b;
            --border: #cfd3de;
        }

        body {
            background: #fff;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 12.5px;
            color: var(--text);
            display: flex;
            justify-content: center;
            padding: 30px 0;
        }

        .quote-paper {
            width: 700px;
        }

        /* HEADER */
        .header {
            background: var(--navy);
            color: #fff;
            padding: 20px 26px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px 10px 0 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-square {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .brand-text .company-name {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .brand-text .company-tagline {
            font-size: 10px;
            color: #cdd4ea;
            margin-top: 1px;
        }

        .header-meta {
            text-align: right;
        }

        .doc-type-pill {
            display: inline-block;
            background: var(--blue);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 6px;
        }

        .header-meta .company-info {
            font-size: 10px;
            color: #cdd4ea;
            line-height: 1.5;
        }

        /* BODY */
        .content-wrap {
            border: 2px solid var(--navy);
            border-top: none;
            border-radius: 0 0 10px 10px;
            padding: 20px 26px 22px;
        }

        /* Title + meta row */
        .title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .title-main {
            font-size: 17px;
            font-weight: 800;
            color: var(--navy);
        }

        .title-sub {
            font-size: 10.5px;
            color: var(--muted);
            margin-top: 2px;
        }

        .meta-list {
            font-size: 11px;
            text-align: right;
            line-height: 1.7;
        }

        .meta-list .meta-label {
            color: var(--muted);
            display: inline-block;
            min-width: 80px;
            text-align: left;
        }

        .meta-list .meta-value {
            font-weight: 700;
            color: var(--blue-dark);
        }

        /* Customer block */
        .customer-block {
            border: 1.5px solid var(--navy);
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            display: flex;
            gap: 24px;
        }

        .cust-col {
            flex: 1;
        }

        .cust-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--blue-dark);
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .cust-value {
            font-size: 12px;
            font-weight: 600;
        }

        .cust-sub {
            font-size: 10.5px;
            color: var(--muted);
            margin-top: 2px;
        }

        /* Items table */
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        table.items-table thead tr {
            background: var(--navy);
            color: #fff;
        }

        table.items-table thead th {
            padding: 8px 8px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }

        table.items-table thead th:last-child { text-align: right; }
        table.items-table thead th:nth-child(2),
        table.items-table thead th:nth-child(3) { text-align: center; }

        table.items-table tbody tr {
            border-bottom: 1px solid var(--border);
        }

        table.items-table tbody td {
            padding: 6px 8px;
            font-size: 11.5px;
            min-height: 20px;
        }
        table.items-table tbody td:nth-child(2),
        table.items-table tbody td:nth-child(3) { text-align: center; }
        table.items-table tbody td:last-child { text-align: right; font-weight: 700; }

        .empty-row td { height: 22px; }

        /* Footer section */
        .footer-block {
            display: flex;
            gap: 16px;
            align-items: stretch;
        }

        .terms-cell {
            flex: 1;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 10.5px;
            color: var(--muted);
        }

        .terms-cell .terms-label {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            color: var(--navy);
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .total-cell {
            width: 190px;
            border-radius: 8px;
            background: var(--blue-dark);
            color: #fff;
            padding: 10px 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .total-cell .total-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #cdd8fb;
        }

        .total-cell .total-value {
            font-size: 20px;
            font-weight: 800;
            margin-top: 2px;
        }

        .thankyou {
            text-align: center;
            margin-top: 18px;
            color: var(--blue-dark);
            font-size: 11px;
            font-weight: 600;
        }

        /* Print button (no print) */
        .no-print {
            text-align: center;
            margin-bottom: 18px;
        }
        .no-print button, .no-print a {
            padding: 9px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-block;
        }
        .no-print button { background: var(--blue); color: #fff; margin-right: 8px; }
        .no-print a { background: #eef0f6; color: var(--text); }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            .quote-paper { width: 100%; }
            /* Ensure background colors actually print (off by default in many browsers) */
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div>
    <div class="no-print">
        <button onclick="window.print()">🖨 Print / Save PDF</button>
        <a href="{{ route('quotations.show', $quotation) }}">← Back</a>
    </div>

    <div class="quote-paper">

        <!-- HEADER -->
        <div class="header">
            <div class="brand">
                <div class="logo-square">S</div>
                <div class="brand-text">
                    <div class="company-name">{{ config('app.name', 'Soma POS') }}</div>
                    <div class="company-tagline">Retail terminal</div>
                </div>
            </div>
            <div class="header-meta">
                <div class="doc-type-pill">Estimate / Quote</div>
                <div class="company-info">
                    @if($quotation->user)
                        Prepared by: {{ $quotation->user->name }}<br>
                    @endif
                    Tel: +254 700 000 000 &nbsp;|&nbsp; sales@somapos.com
                </div>
            </div>
        </div>

        <div class="content-wrap">

            <!-- Title row -->
            <div class="title-row">
                <div>
                    <div class="title-main">Quotation</div>
                    <div class="title-sub">Prices are in Kenyan Shillings (KES)</div>
                </div>
                <div class="meta-list">
                    <div><span class="meta-label">Number</span><span class="meta-value">{{ $quotation->quotation_no }}</span></div>
                    <div><span class="meta-label">Date</span><span class="meta-value">{{ $quotation->quotation_date->format('d/m/Y') }}</span></div>
                    @if($quotation->expiry_date)
                    <div><span class="meta-label">Valid Until</span><span class="meta-value">{{ $quotation->expiry_date->format('d/m/Y') }}</span></div>
                    @endif
                </div>
            </div>

            <!-- Customer block -->
            <div class="customer-block">
                <div class="cust-col">
                    <div class="cust-label">Customer</div>
                    <div class="cust-value">{{ $quotation->customer->name ?? 'Walk-in Customer' }}</div>
                    @if($quotation->customer && $quotation->customer->address)
                        <div class="cust-sub">{{ $quotation->customer->address }}</div>
                    @endif
                </div>
                <div class="cust-col">
                    <div class="cust-label">Contact</div>
                    <div class="cust-value">{{ $quotation->customer->phone ?? '—' }}</div>
                    <div class="cust-sub">{{ $quotation->customer->email ?? '—' }}</div>
                </div>
            </div>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:30px; text-align:center;">#</th>
                        <th style="text-align:left;">Description</th>
                        <th style="width:50px;">Qty</th>
                        <th style="width:90px; text-align:right;">Unit (KES)</th>
                        <th style="width:100px; text-align:right;">Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $itemList = $quotation->items ?? []; $filled = count($itemList); $minRows = max(10, $filled + 2); @endphp

                    @foreach($itemList as $i => $item)
                    <tr>
                        <td style="text-align:center; color:var(--muted);">{{ $i + 1 }}</td>
                        <td>{{ $item['product_name'] ?? '—' }}</td>
                        <td style="text-align:center;">{{ $item['quantity'] }}</td>
                        <td style="text-align:right;">{{ number_format($item['price'], 2) }}</td>
                        <td style="text-align:right;">{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                    </tr>
                    @endforeach

                    @for($r = $filled; $r < $minRows; $r++)
                    <tr class="empty-row">
                        <td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Footer: terms + total -->
            <div class="footer-block">
                <div class="terms-cell">
                    <div class="terms-label">Terms</div>
                    @if($quotation->terms)
                        {{ $quotation->terms }}
                    @else
                        Payment due on receipt. Prices valid as per quote date.
                    @endif
                    @if($quotation->notes)
                        <br><br><em>{{ $quotation->notes }}</em>
                    @endif
                </div>
                <div class="total-cell">
                    <div class="total-label">Total Due</div>
                    <div class="total-value">Ksh {{ number_format($quotation->total, 2) }}</div>
                </div>
            </div>

            <div class="thankyou">Thank you for your business — Soma POS</div>

        </div><!-- /content-wrap -->
    </div><!-- /quote-paper -->
</div>

</body>
</html>