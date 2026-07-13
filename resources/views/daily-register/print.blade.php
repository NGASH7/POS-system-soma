<!DOCTYPE html>
<html>
<head>
    <title>Daily Register - {{ $date }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 20px; margin: 0; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box { background: #f9f9f9; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .row { display: flex; justify-content: space-between; padding: 4px 0; }
        .bold { font-weight: bold; }
        .green { color: #22c55e; }
        .red { color: #ef4444; }
        .section-title { font-weight: bold; font-size: 14px; margin-top: 15px; margin-bottom: 5px; border-bottom: 2px solid #333; padding-bottom: 5px; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>SOMA POS</h1>
        <p>Daily Register Report</p>
        <p>{{ Carbon::parse($date)->format('F d, Y') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <div class="row"><span>Total Sales</span><span class="green">KES {{ number_format($totalSales, 2) }}</span></div>
        <div class="row"><span>Credit Sales</span><span class="bold">KES {{ number_format($totalCreditSales, 2) }}</span></div>
        <div class="row"><span>Returns</span><span class="red">KES {{ number_format($totalReturns, 2) }}</span></div>
        <div class="row"><span>Expenses</span><span>KES {{ number_format($totalExpenses, 2) }}</span></div>
        <div class="row bold"><span>Net Sales</span><span>KES {{ number_format($totalSales - $totalReturns - $totalExpenses, 2) }}</span></div>
    </div>

    <!-- Sales -->
    <div class="section-title">Sales Transactions</div>
    <table>
        <thead>
            <tr><th>Invoice</th><th>Customer</th><th>Payment</th><th class="text-right">Amount</th></tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->invoice_no }}</td>
                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                <td>{{ ucfirst($sale->payment_method) }}</td>
                <td class="text-right">KES {{ number_format($sale->total, 2) }}</td>
            </tr>
            @endforeach
            <tr><td colspan="3" class="text-right bold">Total</td><td class="text-right bold">KES {{ number_format($totalSales, 2) }}</td></tr>
        </tbody>
    </table>

    <!-- Credit Sales -->
    @if($creditSales->count() > 0)
    <div class="section-title">Credit Sales</div>
    <table>
        <thead>
            <tr><th>Invoice</th><th>Customer</th><th class="text-right">Amount</th></tr>
        </thead>
        <tbody>
            @foreach($creditSales as $sale)
            <tr>
                <td>{{ $sale->invoice_no }}</td>
                <td>{{ $sale->customer->name ?? 'Unknown' }}</td>
                <td class="text-right">KES {{ number_format($sale->total, 2) }}</td>
            </tr>
            @endforeach
            <tr><td colspan="2" class="text-right bold">Total</td><td class="text-right bold">KES {{ number_format($totalCreditSales, 2) }}</td></tr>
        </tbody>
    </table>
    @endif

    <!-- Returns -->
    @if($returns->count() > 0)
    <div class="section-title">Returns</div>
    <table>
        <thead>
            <tr><th>Return #</th><th>Original Invoice</th><th>Reason</th><th class="text-right">Amount</th></tr>
        </thead>
        <tbody>
            @foreach($returns as $return)
            <tr>
                <td>{{ $return->return_no }}</td>
                <td>{{ $return->originalSale->invoice_no ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $return->reason)) }}</td>
                <td class="text-right">KES {{ number_format($return->refund_amount, 2) }}</td>
            </tr>
            @endforeach
            <tr><td colspan="3" class="text-right bold">Total</td><td class="text-right bold red">KES {{ number_format($totalReturns, 2) }}</td></tr>
        </tbody>
    </table>
    @endif

    <!-- Expenses -->
    @if($expenses->count() > 0)
    <div class="section-title">Expenses</div>
    <table>
        <thead>
            <tr><th>Reference</th><th>Category</th><th>Title</th><th class="text-right">Amount</th></tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $expense->reference_no }}</td>
                <td>{{ $expense->category->name ?? 'Uncategorized' }}</td>
                <td>{{ $expense->title }}</td>
                <td class="text-right">KES {{ number_format($expense->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr><td colspan="3" class="text-right bold">Total</td><td class="text-right bold">KES {{ number_format($totalExpenses, 2) }}</td></tr>
        </tbody>
    </table>
    @endif

    <div style="text-align: center; margin-top: 30px; color: #999; font-size: 11px;">
        Printed on {{ now()->format('F d, Y H:i:s') }} | SOMA POS System
    </div>
</body>
</html>