@extends('layouts.app')

@section('header_title', 'M-Pesa Transactions')

@section('content')
<x-soma-page title="M-Pesa Transactions" subtitle="Track and verify all mobile money payments">
    <x-slot:actions>
        <button type="button" onclick="window.print()" class="soma-btn-primary bg-emerald-600 hover:bg-emerald-700 border-emerald-600">
            <i class="fas fa-print"></i> Print Report
        </button>
    </x-slot:actions>

    <div class="soma-card p-6 border-t-4 border-emerald-500">
        <!-- Filters -->
        <form method="GET" class="mb-6 flex flex-wrap gap-4">
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="soma-input">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="soma-input">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-slate-700">Status</label>
                <select name="status" class="soma-input">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="soma-btn-primary">Filter</button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-emerald-100 p-5 shadow-sm transition-all hover:shadow-md">
                <div class="mb-1 flex items-center justify-between">
                    <div class="text-sm font-medium text-emerald-800">Total Volume</div>
                    <div class="rounded-full bg-emerald-200 p-1.5 text-emerald-700"><i class="fas fa-money-bill-wave"></i></div>
                </div>
                <div class="text-3xl font-extrabold leading-tight text-emerald-900">KES {{ number_format($summary['total_volume'], 2) }}</div>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-blue-100 p-5 shadow-sm transition-all hover:shadow-md">
                <div class="mb-1 flex items-center justify-between">
                    <div class="text-sm font-medium text-blue-800">Total Transactions</div>
                    <div class="rounded-full bg-blue-200 p-1.5 text-blue-700"><i class="fas fa-receipt"></i></div>
                </div>
                <div class="text-3xl font-extrabold leading-tight text-blue-900">{{ number_format($summary['total_transactions']) }}</div>
            </div>
            <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50 to-indigo-100 p-5 shadow-sm transition-all hover:shadow-md">
                <div class="mb-1 flex items-center justify-between">
                    <div class="text-sm font-medium text-indigo-800">Success Rate</div>
                    <div class="rounded-full bg-indigo-200 p-1.5 text-indigo-700"><i class="fas fa-chart-line"></i></div>
                </div>
                <div class="text-3xl font-extrabold leading-tight text-indigo-900">{{ $summary['success_rate'] }}%</div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
            <table class="soma-table w-full whitespace-nowrap">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">System Ref</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-800 bg-emerald-100 rounded-tl-lg rounded-bl-lg">M-Pesa Receipt</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Phone Number</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($transactions as $txn)
                    <tr class="transition-colors hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $txn->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $txn->reference }}</td>
                        <td class="px-4 py-3 text-sm font-extrabold text-emerald-700 bg-emerald-50 border-l border-r border-emerald-100">{{ $txn->provider_reference ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $txn->phone }}</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-slate-900">KES {{ number_format($txn->amount, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($txn->status === 'completed')
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                                    <i class="fas fa-check-circle mr-1.5"></i> Completed
                                </span>
                            @elseif($txn->status === 'pending' || $txn->status === 'processing')
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                                    <i class="fas fa-clock mr-1.5"></i> Pending
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-800" title="{{ $txn->error_message }}">
                                    <i class="fas fa-times-circle mr-1.5"></i> Failed
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 mb-3">
                                <i class="fas fa-search text-slate-400"></i>
                            </div>
                            <p>No M-Pesa transactions found for this period.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</x-soma-page>
@endsection
