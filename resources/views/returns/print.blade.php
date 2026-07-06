@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Return Receipt</h1>
    <div class="card">
        <div class="card-body">
            <h3>Return #{{ $return->return_no }}</h3>
            <p><strong>Date:</strong> {{ $return->created_at->format('Y-m-d H:i:s') }}</p>
            <hr>
            <p><strong>Customer:</strong> {{ $return->customer->name ?? 'N/A' }}</p>
            <p><strong>Original Sale:</strong> {{ $return->originalSale->invoice_no ?? 'N/A' }}</p>
            <p><strong>Amount Refunded:</strong> ${{ number_format($return->refund_amount, 2) }}</p>
            <p><strong>Reason:</strong> {{ $return->reason }}</p>
        </div>
    </div>
    <button onclick="window.print()" class="btn btn-primary mt-3">Print</button>
</div>
@endsection
