@extends('layouts.app')

@section('content')
<div class="soma-page-inner space-y-6">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-600/10 text-orange-600 flex items-center justify-center">
                    <i class="fas fa-rotate-left text-xl"></i>
                </div>
                Purchase Returns
            </h1>
            <p class="text-slate-500 text-sm mt-1">Manage and track all supplier purchase returns and refunds.</p>
        </div>
        <div>
            <a href="{{ route('purchases.returns.create') }}" class="soma-btn-primary shadow-lg shadow-orange-500/20" style="background: linear-gradient(135deg, #ea580c, #dc2626);">
                <i class="fas fa-plus"></i>
                <span>Add Return</span>
            </a>
        </div>
    </div>

    {{-- Summary KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="soma-card p-5 border-l-4 border-l-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Returns</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($summary['total_count']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl">
                    <i class="fas fa-boxes-packing"></i>
                </div>
            </div>
        </div>
        <div class="soma-card p-5 border-l-4 border-l-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Returned Value</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">KES {{ number_format($summary['total_amount'], 2) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <div class="soma-card p-5 border-l-4 border-l-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completed Returns</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($summary['completed_count']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="soma-card p-5">
        <form method="GET" action="{{ route('purchases.returns') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wider">Search</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><i class="fas fa-search text-sm"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Return #, purchase ref, or supplier..."
                        class="soma-input pl-9 w-full">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <label class="block text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wider">Status</label>
                <select name="status" class="soma-input w-full">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="block text-xs font-semibold text-slate-600 mb-1 uppercase tracking-wider">Supplier</label>
                <select name="supplier_id" class="soma-input w-full">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="soma-btn-primary">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'status', 'supplier_id']))
                    <a href="{{ route('purchases.returns') }}" class="soma-btn-secondary">
                        <i class="fas fa-times"></i>
                        <span>Clear</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Returns Table --}}
    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">#</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Return No.</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Purchase Ref.</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Supplier</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Return Date</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Items</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Total Amount</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Status</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Refund</th>
                        <th class="text-center px-4 py-3 font-semibold text-slate-600 uppercase tracking-wider text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $index => $return)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-4 py-3 text-slate-500 font-medium">{{ $returns->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-orange-700 font-mono text-xs bg-orange-50 px-2 py-1 rounded-md">
                                    {{ $return->return_no }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($return->purchase)
                                    <a href="{{ route('purchases.show', $return->purchase_id) }}"
                                       class="text-blue-600 hover:text-blue-800 font-mono text-xs underline underline-offset-2">
                                        {{ $return->purchase->reference_no ?? 'N/A' }}
                                    </a>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800">{{ $return->supplier->name ?? '—' }}</div>
                                @if($return->supplier?->phone)
                                    <div class="text-xs text-slate-400">{{ $return->supplier->phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-xs">
                                    {{ $return->items->count() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-900">
                                KES {{ number_format($return->total_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusClasses = [
                                        'completed' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                        'pending'   => 'bg-amber-100 text-amber-700 border border-amber-200',
                                        'cancelled' => 'bg-rose-100 text-rose-700 border border-rose-200',
                                    ];
                                    $statusClass = $statusClasses[$return->status] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $statusClass }}">
                                    {{ ucfirst($return->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $refundClass = $return->refund_status === 'refunded'
                                        ? 'bg-blue-100 text-blue-700 border border-blue-200'
                                        : 'bg-amber-100 text-amber-700 border border-amber-200';
                                @endphp
                                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $refundClass }}">
                                    {{ ucfirst($return->refund_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button"
                                        onclick="viewReturnDetails({{ $return->id }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                        title="View Details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    <form method="POST" action="{{ route('purchases.returns.destroy', $return->id) }}"
                                          onsubmit="return confirm('Delete this return record? Stock adjustments will be reversed if it was completed.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors"
                                            title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-300">
                                        <i class="fas fa-rotate-left text-3xl"></i>
                                    </div>
                                    <p class="font-semibold text-slate-500">No purchase returns found</p>
                                    <p class="text-sm text-slate-400">Returns you create will appear here.</p>
                                    <a href="{{ route('purchases.returns.create') }}" class="soma-btn-primary mt-2" style="background: linear-gradient(135deg, #ea580c, #dc2626);">
                                        <i class="fas fa-plus"></i>
                                        <span>Add First Return</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($returns->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
                <p class="text-sm text-slate-500">
                    Showing {{ $returns->firstItem() }}–{{ $returns->lastItem() }} of {{ $returns->total() }} returns
                </p>
                {{ $returns->links() }}
            </div>
        @endif
    </div>
</div>

{{-- View Return Details Modal --}}
<div id="returnDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background: rgba(15,23,42,0.6); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i class="fas fa-rotate-left"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Return Details</h3>
                    <p id="modalReturnNo" class="text-xs text-slate-500 font-mono"></p>
                </div>
            </div>
            <button onclick="closeReturnModal()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 p-6" id="modalContent">
            <div class="flex items-center justify-center py-12">
                <div class="w-8 h-8 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
    </div>
</div>

<script>
function viewReturnDetails(id) {
    const modal = document.getElementById('returnDetailModal');
    const content = document.getElementById('modalContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<div class="flex items-center justify-center py-12"><div class="w-8 h-8 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div></div>';

    fetch(`/purchases/returns/${id}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error('Failed');
            const ret = data.return;
            document.getElementById('modalReturnNo').textContent = ret.return_no;

            const statusColors = { completed: '#059669', pending: '#d97706', cancelled: '#e11d48' };
            const refundColors = { refunded: '#2563eb', pending: '#d97706' };

            let itemRows = (ret.items || []).map(item => `
                <tr class="border-b border-slate-100">
                    <td class="py-2 text-slate-700">${item.product?.name || '—'}</td>
                    <td class="py-2 text-center text-slate-600">${item.quantity}</td>
                    <td class="py-2 text-right text-slate-600">KES ${parseFloat(item.unit_cost).toLocaleString('en', {minimumFractionDigits: 2})}</td>
                    <td class="py-2 text-right font-semibold text-slate-800">KES ${parseFloat(item.subtotal).toLocaleString('en', {minimumFractionDigits: 2})}</td>
                    <td class="py-2 text-center text-xs text-slate-500">${item.reason || '—'}</td>
                </tr>
            `).join('');

            content.innerHTML = `
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div><p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Supplier</p><p class="font-semibold text-slate-800">${ret.supplier?.name || '—'}</p></div>
                    <div><p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Return Date</p><p class="font-semibold text-slate-800">${ret.return_date || '—'}</p></div>
                    <div><p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Purchase Ref.</p><p class="font-semibold text-slate-800">${ret.purchase?.reference_no || '—'}</p></div>
                    <div><p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Outlet</p><p class="font-semibold text-slate-800">${ret.outlet?.name || '—'}</p></div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Status</p>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full" style="color:${statusColors[ret.status]||'#64748b'};background:${statusColors[ret.status]||'#64748b'}22;">${ret.status?.charAt(0).toUpperCase()+ret.status?.slice(1)}</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase tracking-wide font-semibold mb-1">Refund Status</p>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full" style="color:${refundColors[ret.refund_status]||'#64748b'};background:${refundColors[ret.refund_status]||'#64748b'}22;">${ret.refund_status?.charAt(0).toUpperCase()+ret.refund_status?.slice(1)}</span>
                    </div>
                </div>
                ${ret.notes ? `<div class="mb-4 p-3 bg-slate-50 rounded-lg text-sm text-slate-600"><span class="font-semibold">Notes:</span> ${ret.notes}</div>` : ''}
                <h4 class="font-bold text-slate-700 mb-3 text-sm uppercase tracking-wide">Return Items</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b-2 border-slate-200 text-xs text-slate-500 uppercase tracking-wide">
                                <th class="text-left pb-2">Product</th>
                                <th class="text-center pb-2">Qty</th>
                                <th class="text-right pb-2">Unit Cost</th>
                                <th class="text-right pb-2">Subtotal</th>
                                <th class="text-center pb-2">Reason</th>
                            </tr>
                        </thead>
                        <tbody>${itemRows || '<tr><td colspan="5" class="py-4 text-center text-slate-400">No items</td></tr>'}</tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-200">
                                <td colspan="3" class="pt-2 text-right font-bold text-slate-700">Total Returned:</td>
                                <td class="pt-2 text-right font-bold text-slate-900">KES ${parseFloat(ret.total_amount).toLocaleString('en', {minimumFractionDigits: 2})}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;
        })
        .catch(() => {
            content.innerHTML = '<p class="text-center text-rose-500 py-8">Failed to load return details. Please try again.</p>';
        });
}

function closeReturnModal() {
    const modal = document.getElementById('returnDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('returnDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeReturnModal();
});
</script>
@endsection
