@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-3xl">

        <div class="mb-6">
            <a href="{{ route('expenses.index') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                <i class="fas fa-arrow-left text-xs"></i> Back to expenses
            </a>
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $expense->title }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $expense->reference_no }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @php
                        $statusStyles = [
                            'pending'  => 'bg-amber-50 text-amber-700',
                            'approved' => 'bg-emerald-50 text-emerald-700',
                            'rejected' => 'bg-rose-50 text-rose-700',
                        ];
                    @endphp
                    <span class="text-xs font-medium px-2.5 py-1 rounded-md {{ $statusStyles[$expense->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst($expense->status) }}
                    </span>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('expenses.edit', $expense) }}" class="soma-btn-secondary text-sm py-2">Edit</a>
                    @elseif($expense->user_id === auth()->id() && $expense->status === 'pending')
                        <a href="{{ route('expenses.edit', $expense) }}" class="soma-btn-secondary text-sm py-2">Edit</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="soma-card p-6 mb-4">
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Amount</p>
            <p class="text-3xl font-bold text-slate-900">KES {{ number_format($expense->amount, 2) }}</p>
        </div>

        <div class="soma-card divide-y divide-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5">
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Category</p>
                    <p class="text-sm font-medium text-slate-900">{{ $expense->category->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Date</p>
                    <p class="text-sm font-medium text-slate-900">{{ $expense->expense_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Payment method</p>
                    <p class="text-sm font-medium text-slate-900">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Vendor</p>
                    <p class="text-sm font-medium text-slate-900">{{ $expense->vendor ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-0.5">Recorded by</p>
                    <p class="text-sm font-medium text-slate-900">{{ $expense->user->name ?? '—' }}</p>
                </div>
            </div>

            @if($expense->description)
            <div class="p-5">
                <p class="text-xs text-slate-400 mb-1">Description</p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $expense->description }}</p>
            </div>
            @endif

            @if($expense->receipt_image)
            <div class="p-5">
                <p class="text-xs text-slate-400 mb-2">Receipt</p>
                <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank" class="soma-btn-secondary text-sm">
                    <i class="fas fa-file-alt text-xs"></i> View receipt
                </a>
            </div>
            @endif
        </div>

        @if($expense->status === 'pending')
        <div class="flex gap-2 mt-4">
            @if(auth()->user()->role === 'admin')
                <form method="POST" action="{{ route('expenses.approve', $expense) }}">
                    @csrf
                    <button type="submit" class="soma-btn-primary bg-emerald-600 hover:bg-emerald-700">
                        <i class="fas fa-check text-xs"></i> Approve
                    </button>
                </form>
                <form method="POST" action="{{ route('expenses.reject', $expense) }}">
                    @csrf
                    <button type="submit" class="soma-btn-secondary text-rose-600 border-rose-200 hover:bg-rose-50">
                        <i class="fas fa-times text-xs"></i> Reject
                    </button>
                </form>
            @else
                <p class="text-sm text-amber-600 inline-flex items-center gap-1.5">
                    <i class="fas fa-clock text-xs"></i>
                    Awaiting admin approval
                </p>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection
