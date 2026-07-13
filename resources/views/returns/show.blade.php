@extends('layouts.app')

@section('content')
<div class="return-page">
    <a href="{{ route('returns.index') }}" class="back-link">← Back to returns</a>

    <div class="return-card">
        <div class="return-header">
            <div>
                <span class="eyebrow">Return Details</span>
                <h1>{{ $return->return_no }}</h1>
            </div>
            <span class="status-badge status-{{ strtolower($return->status) }}">
                {{ ucfirst($return->status) }}
            </span>
        </div>

        <div class="divider"></div>

        <dl class="detail-list">
            <div class="detail-row">
                <dt>Original sale</dt>
                <dd>{{ $return->originalSale->invoice_no ?? 'N/A' }}</dd>
            </div>
            <div class="detail-row">
                <dt>Customer</dt>
                <dd>{{ $return->customer->name ?? 'N/A' }}</dd>
            </div>
            <div class="detail-row">
                <dt>Type</dt>
                <dd>{{ ucfirst($return->return_type) }}</dd>
            </div>
            <div class="detail-row">
                <dt>Reason</dt>
                <dd>{{ ucfirst($return->reason) }}</dd>
            </div>
        </dl>

        <div class="divider"></div>

        <div class="refund-row">
            <span>Amount</span>
            <span class="refund-amount">${{ number_format($return->refund_amount, 2) }}</span>
        </div>
    </div>
</div>

<style>
    .return-page {
        max-width: 480px;
        margin: 3rem auto;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .back-link {
        display: inline-block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #5a5f68;
        text-decoration: none;
        margin-bottom: 1rem;
    }
    .back-link:hover { color: #1a1d23; }

    .return-card {
        background: #fff;
        border: 1px solid #e6e8eb;
        border-radius: 12px;
        padding: 2rem;
    }

    .return-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .eyebrow {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #8a8f98;
    }

    .return-header h1 {
        font-size: 1.375rem;
        font-weight: 700;
        color: #1a1d23;
        margin: 0.15rem 0 0;
    }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.3rem 0.7rem;
        border-radius: 999px;
        white-space: nowrap;
        text-transform: capitalize;
    }

    .status-completed { background: #e6f7ee; color: #1e8a4c; }
    .status-pending   { background: #fff4e0; color: #b8720a; }
    .status-cancelled { background: #fbe8e8; color: #c23b3b; }

    .divider {
        height: 1px;
        background: #eef0f2;
        margin: 1.5rem 0;
    }

    .detail-list { margin: 0; }

    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.4rem 0;
        font-size: 0.9rem;
    }

    .detail-row dt { color: #8a8f98; }
    .detail-row dd { margin: 0; color: #1a1d23; font-weight: 500; text-align: right; }

    .refund-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        font-size: 0.95rem;
        color: #1a1d23;
    }

    .refund-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2451e0;
    }

    @media (max-width: 520px) {
        .return-page { margin: 1.5rem 1rem; }
    }
</style>
@endsection