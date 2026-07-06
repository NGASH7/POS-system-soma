<div class="soma-receipt">
    <div class="text-center">
        <div class="store-name">SOMA POS</div>
        <div class="store-details">
            P.O BOX 123,Njoro<br>
            Tel: 0712 345 678<br>
            Email: info@somapos.com
        </div>
    </div>

    <hr class="divider">

    <div class="text-center">
        <div class="invoice-no">INVOICE: {{ $sale->invoice_no }}</div>
        <div class="receipt-meta">
            Date: {{ $sale->created_at->format('d/m/Y H:i:s') }}<br>
            Cashier: {{ $sale->user->name }}<br>
            Terminal: {{ $sale->terminal_id }}
        </div>
    </div>

    <hr class="divider">

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="col-qty">Qty</th>
                <th class="col-price">Price</th>
                <th class="col-total">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td><span class="item-name" title="{{ $item->product->name }}">{{ $item->product->name }}</span></td>
                <td class="col-qty">{{ $item->quantity }}</td>
                <td class="col-price"><span class="amount">{{ number_format($item->price, 2) }}</span></td>
                <td class="col-total"><span class="amount">{{ number_format($item->total, 2) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="divider">

    <div class="row-line">
        <span class="label">Subtotal</span>
        <span class="value amount">KES {{ number_format($sale->subtotal, 2) }}</span>
    </div>
    <div class="row-line">
        <span class="label">Tax (16% VAT)</span>
        <span class="value amount">KES {{ number_format($sale->tax, 2) }}</span>
    </div>
    <div class="row-line total">
        <span class="label">TOTAL</span>
        <span class="value amount">KES {{ number_format($sale->total, 2) }}</span>
    </div>

    <hr class="divider">

    <div class="row-line">
        <span class="label">Paid</span>
        <span class="value amount">KES {{ number_format($sale->paid, 2) }}</span>
    </div>
    <div class="row-line">
        <span class="label">Change</span>
        <span class="value amount">KES {{ number_format($sale->change_due, 2) }}</span>
    </div>
    <div class="row-line">
        <span class="label">Payment Method</span>
        <span class="value">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
    </div>
    @if($sale->mpesa_receipt)
    <div class="row-line">
        <span class="label">M-Pesa Receipt</span>
        <span class="value">{{ $sale->mpesa_receipt }}</span>
    </div>
    @endif

    @if($sale->customer)
    <hr class="divider">
    <div class="text-center receipt-meta">
        <strong>Customer:</strong> {{ $sale->customer->name }}<br>
        Points earned: {{ floor($sale->total / 10) }}
    </div>
    @endif

    <hr class="divider">

    <div class="text-center footer-message">
        <p class="thankyou">Thank you for shopping with us!</p>
        <p>Returns accepted within 7 days</p>
        <p>Keep receipt for warranty</p>
    </div>

    <div class="text-center" style="margin-top: 12px;">
        <div class="end-marker">*** End of Receipt ***</div>
        <div class="timestamp">{{ now()->format('Y-m-d H:i:s') }}</div>
    </div>
</div>
