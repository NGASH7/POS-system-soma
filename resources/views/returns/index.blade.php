@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Returns & Exchanges</h1>
                <p class="text-sm text-slate-500 mt-1">Process refunds and product exchanges</p>
            </div>
            <a href="{{ route('returns.create') }}" class="soma-btn-primary">
                <i class="fas fa-plus text-xs"></i> New Return
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="flex items-center gap-3 p-4 mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
            <i class="fas fa-check-circle text-emerald-500"></i>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 p-4 mb-6 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <i class="fas fa-exclamation-circle text-rose-500"></i>
            {{ session('error') }}
        </div>
        @endif

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="soma-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Returns</p>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-undo text-blue-500 text-xs"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900">{{ $summary['total_returns'] ?? 0 }}</p>
                <p class="text-xs text-slate-400 mt-1">All time</p>
            </div>
            <div class="soma-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Refunded</p>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-rose-500 text-xs"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-rose-600">KES {{ number_format($summary['total_refunded'] ?? 0, 2) }}</p>
                <p class="text-xs text-slate-400 mt-1">Total refunds</p>
            </div>
            <div class="soma-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Pending</p>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                        <i class="fas fa-clock text-amber-500 text-xs"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-amber-600">{{ $summary['pending'] ?? 0 }}</p>
                <p class="text-xs text-slate-400 mt-1">Awaiting review</p>
            </div>
            <div class="soma-card p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Exchanges</p>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-purple-500 text-xs"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-purple-600">{{ $summary['exchanges'] ?? 0 }}</p>
                <p class="text-xs text-slate-400 mt-1">Product swaps</p>
            </div>
        </div>

        {{-- Returns Table --}}
        <div class="soma-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="soma-table w-full">
                    <thead>
                        <tr>
                            <th>Return No</th>
                            <th>Original Invoice</th>
                            <th>Customer</th>
                            <th class="text-right">Refund Amount</th>
                            <th class="text-center">Type</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $return)
                        <tr>
                            <td class="font-medium text-slate-900 whitespace-nowrap">
                                {{ $return->return_no }}
                            </td>
                            <td class="text-slate-500 whitespace-nowrap">
                                {{ $return->originalSale->invoice_no ?? '—' }}
                            </td>
                            <td>
                                @if($return->customer)
                                    <div class="font-medium text-slate-900">{{ $return->customer->name }}</div>
                                    @if($return->customer->phone)
                                    <div class="text-xs text-slate-400">{{ $return->customer->phone }}</div>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">Walk-in</span>
                                @endif
                            </td>
                            <td class="text-right font-semibold text-rose-600 whitespace-nowrap">
                                KES {{ number_format($return->refund_amount, 2) }}
                            </td>
                            <td class="text-center">
                                @if($return->return_type === 'exchange')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-md bg-purple-50 text-purple-700">
                                        <i class="fas fa-exchange-alt text-[10px]"></i> Exchange
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-md bg-blue-50 text-blue-700">
                                        <i class="fas fa-undo text-[10px]"></i> Return
                                    </span>
                                @endif
                            </td>
                            <td class="text-slate-500 max-w-[160px]">
                                <span class="truncate block" title="{{ $return->reason }}">{{ $return->reason }}</span>
                            </td>
                            <td class="text-slate-500 whitespace-nowrap text-sm">
                                {{ $return->created_at->format('d M Y') }}
                                <div class="text-xs text-slate-400">{{ $return->created_at->format('H:i') }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusStyles = [
                                        'completed' => 'bg-emerald-50 text-emerald-700',
                                        'pending'   => 'bg-amber-50 text-amber-700',
                                        'approved'  => 'bg-blue-50 text-blue-700',
                                        'rejected'  => 'bg-rose-50 text-rose-700',
                                    ];
                                @endphp
                                <span class="text-xs font-medium px-2 py-1 rounded-md {{ $statusStyles[$return->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($return->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('returns.show', $return->id) }}"
                                       class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                       title="View">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('returns.print', $return->id) }}"
                                       class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                                       title="Print receipt" target="_blank">
                                        <i class="fas fa-print text-sm"></i>
                                    </a>
                                    @if(auth()->user()->role === 'admin')
                                    <form method="POST" action="{{ route('returns.destroy', $return->id) }}" class="inline"
                                          onsubmit="return confirm('Delete this return record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                                                title="Delete">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-16 text-slate-400">
                                <i class="fas fa-undo text-3xl mb-3 block opacity-30"></i>
                                <p class="text-sm">No returns yet.</p>
                                <a href="{{ route('returns.create') }}" class="text-sm text-blue-600 hover:underline mt-1 inline-block">
                                    Process your first return
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($returns->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $returns->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
