@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Return Details</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Return No:</strong> {{ $return->return_no }}</p>
            <p><strong>Original Sale:</strong> {{ $return->originalSale->invoice_no ?? 'N/A' }}</p>
            <p><strong>Customer:</strong> {{ $return->customer->name ?? 'N/A' }}</p>
            <p><strong>Amount:</strong> ${{ number_format($return->refund_amount, 2) }}</p>
            <p><strong>Type:</strong> {{ ucfirst($return->return_type) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($return->status) }}</p>
            <p><strong>Reason:</strong> {{ $return->reason }}</p>
            <a href="{{ route('returns.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
