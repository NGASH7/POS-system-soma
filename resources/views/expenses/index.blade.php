@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Expenses</h1>
                <p class="text-sm text-slate-500 mt-1">Track and manage business spending</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('expenses.categories') }}" class="soma-btn-secondary">
                    Categories
                </a>
                <a href="{{ route('expenses.create') }}" class="soma-btn-primary">
                    <i class="fas fa-plus text-xs"></i> Add Expense
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="soma-card p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Total</p>
                <p class="text-lg font-bold text-slate-900">KES {{ number_format($summary['total'], 2) }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $summary['count'] }} records</p>
            </div>
            <div class="soma-card p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Pending</p>
                <p class="text-lg font-bold text-amber-600">KES {{ number_format($summary['pending'], 2) }}</p>
            </div>
            <div class="soma-card p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Approved</p>
                <p class="text-lg font-bold text-emerald-600">KES {{ number_format($summary['approved'], 2) }}</p>
            </div>
            <div class="soma-card p-4">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Rejected</p>
                <p class="text-lg font-bold text-rose-600">KES {{ number_format($summary['rejected'], 2) }}</p>
            </div>
        </div>

        <div class="soma-card p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                <div class="lg:col-span-1">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Category</label>
                    <select name="category" class="soma-input py-2">
                        <option value="">All</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                    <select name="status" class="soma-input py-2">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="soma-input py-2">
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="soma-input py-2">
                </div>
                <div class="lg:col-span-2 flex gap-2">
                    <button type="submit" class="soma-btn-primary flex-1 justify-center">Filter</button>
                    <a href="{{ route('expenses.index') }}" class="soma-btn-secondary flex-1 justify-center">Clear</a>
                </div>
            </form>
        </div>

        <div class="soma-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="soma-table w-full">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Expense</th>
                            <th>Category</th>
                            <th class="text-right">Amount</th>
                            <th>Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($query as $expense)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $expense->reference_no }}</td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $expense->title }}</div>
                                @if($expense->vendor)
                                    <div class="text-xs text-slate-400">{{ $expense->vendor }}</div>
                                @endif
                            </td>
                            <td>
                                @if($expense->category)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600">
                                        <span class="w-2 h-2 rounded-full" style="background: {{ $expense->category->color }}"></span>
                                        {{ $expense->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="text-right font-semibold text-slate-900">KES {{ number_format($expense->amount, 2) }}</td>
                            <td class="text-slate-500">{{ $expense->expense_date->format('d M Y') }}</td>
                            <td class="text-center">
                                @php
                                    $statusStyles = [
                                        'pending' => 'bg-amber-50 text-amber-700',
                                        'approved' => 'bg-emerald-50 text-emerald-700',
                                        'rejected' => 'bg-rose-50 text-rose-700',
                                    ];
                                @endphp
                                <span class="text-xs font-medium px-2 py-1 rounded-md {{ $statusStyles[$expense->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($expense->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('expenses.show', $expense) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition" title="View">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('expenses.edit', $expense) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Edit">
                                        <i class="fas fa-pen text-sm"></i>
                                    </a>
                                    @if($expense->status === 'pending')
                                        <form method="POST" action="{{ route('expenses.approve', $expense) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Approve">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('expenses.reject', $expense) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Reject">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <p class="text-sm">No expenses yet.</p>
                                <a href="{{ route('expenses.create') }}" class="text-sm text-blue-600 hover:underline mt-1 inline-block">Add your first expense</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($query->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $query->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
