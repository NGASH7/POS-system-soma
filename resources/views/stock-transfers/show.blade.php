@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-3xl">

        <div class="mb-6">
            <a href="{{ route('stock-transfers.index') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                <i class="fas fa-arrow-left text-xs"></i> Back to transfers
            </a>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $stockTransfer->reference_no }}</h1>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ $stockTransfer->fromOutlet->name ?? '—' }}
                        <i class="fas fa-arrow-right text-xs mx-1"></i>
                        {{ $stockTransfer->toOutlet->name ?? '—' }}
                    </p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700">Completed</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="soma-card p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Products</p>
                <p class="text-lg font-bold text-slate-900">{{ $stockTransfer->items->count() }}</p>
            </div>
            <div class="soma-card p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Total units</p>
                <p class="text-lg font-bold text-slate-900">{{ $stockTransfer->total_quantity }}</p>
            </div>
            <div class="soma-card p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Date</p>
                <p class="text-sm font-semibold text-slate-900">{{ $stockTransfer->transferred_at?->format('d M Y') }}</p>
            </div>
            <div class="soma-card p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">By</p>
                <p class="text-sm font-semibold text-slate-900">{{ $stockTransfer->user->name ?? '—' }}</p>
            </div>
        </div>

        @if($stockTransfer->notes)
        <div class="soma-card p-4 mb-6">
            <p class="text-xs text-slate-400 mb-1">Notes</p>
            <p class="text-sm text-slate-700">{{ $stockTransfer->notes }}</p>
        </div>
        @endif

        <div class="soma-card overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-900">Transferred items</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="soma-table w-full">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-right">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockTransfer->items as $item)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $item->product->name ?? '—' }}</td>
                            <td class="text-slate-500">{{ $item->product->sku ?? '—' }}</td>
                            <td class="text-right font-semibold">{{ $item->quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
