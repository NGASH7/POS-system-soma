@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Returns Management</h1>
    <a href="{{ route('returns.create') }}" class="btn btn-primary mb-3">New Return</a>
    
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Total Returns</h5>
                    <h3>{{ $summary['total_returns'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Total Refunded</h5>
                    <h3>${{ number_format($summary['total_refunded'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Pending</h5>
                    <h3>{{ $summary['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Exchanges</h5>
                    <h3>{{ $summary['exchanges'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Return No</th>
                <th>Original Sale</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($returns as $return)
            <tr>
                <td>{{ $return->return_no }}</td>
                <td>{{ $return->originalSale->invoice_no ?? 'N/A' }}</td>
                <td>{{ $return->customer->name ?? 'N/A' }}</td>
                <td>${{ number_format($return->refund_amount, 2) }}</td>
                <td>{{ ucfirst($return->return_type) }}</td>
                <td>{{ ucfirst($return->status) }}</td>
                <td>
                    <a href="{{ route('returns.show', $return->id) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('returns.edit', $return->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <a href="{{ route('returns.print', $return->id) }}" class="btn btn-sm btn-secondary">Print</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No returns found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    {{ $returns->links() }}
</div>
@endsection
