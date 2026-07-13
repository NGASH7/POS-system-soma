@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Stock Transfers</h1>
                <p class="text-sm text-slate-500 mt-1">Move inventory between stores</p>
            </div>
            <a href="{{ route('stock-transfers.create') }}" class="soma-btn-primary">
                <i class="fas fa-plus text-xs"></i> New transfer
            </a>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="soma-card p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="min-w-[200px]">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Store</label>
                    <select name="outlet_id" class="soma-input py-2" onchange="this.form.submit()">
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}" {{ $outletId == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        @endif

        <div class="soma-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="soma-table w-full">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>From</th>
                            <th>To</th>
                            <th class="text-center">Items</th>
                            <th>Date</th>
                            <th>By</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $transfer->reference_no }}</td>
                            <td>{{ $transfer->fromOutlet->name ?? '—' }}</td>
                            <td>{{ $transfer->toOutlet->name ?? '—' }}</td>
                            <td class="text-center text-slate-500">{{ $transfer->items->count() }} ({{ $transfer->total_quantity }} units)</td>
                            <td class="text-slate-500">{{ $transfer->transferred_at?->format('d M Y, H:i') ?? '—' }}</td>
                            <td class="text-slate-500">{{ $transfer->user->name ?? '—' }}</td>
                            <td class="text-center">
                                <a href="{{ route('stock-transfers.show', $transfer) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition" title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <p class="text-sm">No stock transfers yet.</p>
                                <a href="{{ route('stock-transfers.create') }}" class="text-sm text-blue-600 hover:underline mt-1 inline-block">Create your first transfer</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $transfers->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
