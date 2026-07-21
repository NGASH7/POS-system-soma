@extends('layouts.app')

@section('content')
<div class="soma-page-inner space-y-6">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/10 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-truck-ramp-box text-xl"></i>
                </div>
                Purchases List
            </h1>
            <p class="text-slate-500 text-sm mt-1">View, filter, and track all supplier purchase orders and stock receipts.</p>
        </div>
        <div>
            <a href="{{ route('purchases.create') }}" class="soma-btn-primary shadow-lg shadow-blue-500/20">
                <i class="fas fa-plus"></i>
                <span>Add Purchase</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="soma-card p-5 border-l-4 border-l-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Purchases</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($summary['total_count']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fas fa-boxes-packing"></i>
                </div>
            </div>
        </div>

        <div class="soma-card p-5 border-l-4 border-l-emerald-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Amount Spent</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">KES {{ number_format($summary['total_spent'], 2) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <div class="soma-card p-5 border-l-4 border-l-indigo-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Received Orders</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($summary['received_count']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="soma-card p-5 border-l-4 border-l-amber-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Due / Pending</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">KES {{ number_format($summary['total_due'], 2) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="soma-card p-5">
        <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="PO# or Supplier..." class="soma-input pl-9 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Supplier</label>
                <select name="supplier_id" class="soma-input text-sm">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Purchase Status</label>
                <select name="status" class="soma-input text-sm">
                    <option value="">All Statuses</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Payment Status</label>
                <select name="payment_status" class="soma-input text-sm">
                    <option value="">All Payment</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="due" {{ request('payment_status') == 'due' ? 'selected' : '' }}>Due</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Outlet</label>
                <select name="outlet_id" class="soma-input text-sm">
                    <option value="">All Outlets</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}" {{ request('outlet_id') == $outlet->id ? 'selected' : '' }}>
                            {{ $outlet->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="soma-btn-primary w-full justify-center text-sm py-2.5">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('purchases.index') }}" class="soma-btn-secondary w-full justify-center text-sm py-2.5">
                    <i class="fas fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Purchases Table -->
    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse soma-table">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="py-3.5 px-4 font-bold">Reference #</th>
                        <th class="py-3.5 px-4 font-bold">Date</th>
                        <th class="py-3.5 px-4 font-bold">Supplier</th>
                        <th class="py-3.5 px-4 font-bold">Outlet</th>
                        <th class="py-3.5 px-4 font-bold text-center">Status</th>
                        <th class="py-3.5 px-4 font-bold text-center">Payment</th>
                        <th class="py-3.5 px-4 font-bold text-right">Total Amount</th>
                        <th class="py-3.5 px-4 font-bold text-right">Paid Amount</th>
                        <th class="py-3.5 px-4 font-bold text-right">Due Amount</th>
                        <th class="py-3.5 px-4 font-bold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-blue-600">
                                {{ $purchase->reference_no }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                {{ $purchase->supplier->name ?? 'N/A' }}
                                @if(!empty($purchase->supplier->company_name))
                                    <span class="block text-xs text-slate-400">{{ $purchase->supplier->company_name }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $purchase->outlet->name ?? 'Main Outlet' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($purchase->status === 'received')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Received
                                    </span>
                                @elseif($purchase->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                    </span>
                                @elseif($purchase->status === 'ordered')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Ordered
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($purchase->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Paid
                                    </span>
                                @elseif($purchase->payment_status === 'partial')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Partial
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Due
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-900">
                                KES {{ number_format($purchase->total_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-emerald-600">
                                KES {{ number_format($purchase->paid_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium {{ $purchase->due_amount > 0 ? 'text-amber-600' : 'text-slate-500' }}">
                                KES {{ number_format($purchase->due_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="viewPurchaseDetails({{ $purchase->id }})" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                        <i class="fas fa-eye text-base"></i>
                                    </button>
                                    <form method="POST" action="{{ route('purchases.destroy', $purchase->id) }}" onsubmit="return confirm('Are you sure you want to delete this purchase? If it was received, stock levels will be decremented.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Purchase">
                                            <i class="fas fa-trash-can text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 text-2xl mb-3">
                                        <i class="fas fa-truck-ramp-box"></i>
                                    </div>
                                    <h4 class="text-base font-bold text-slate-800">No purchases found</h4>
                                    <p class="text-xs text-slate-500 mt-1 mb-4">Start by creating your first purchase order to restock inventory.</p>
                                    <a href="{{ route('purchases.create') }}" class="soma-btn-primary text-sm py-2">
                                        <i class="fas fa-plus"></i>
                                        <span>Create Purchase</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Purchase Details Modal -->
<div id="purchaseDetailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-600/10 text-blue-600 flex items-center justify-center font-bold">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h3 id="modalPoNumber" class="text-lg font-bold text-slate-900">Purchase Details</h3>
                    <p id="modalPoDate" class="text-xs text-slate-500">Date: -</p>
                </div>
            </div>
            <button type="button" onclick="closePurchaseModal()" class="w-8 h-8 text-slate-400 hover:text-slate-600 rounded-lg flex items-center justify-center hover:bg-slate-200/60">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="modalBodyContent" class="p-6 overflow-y-auto space-y-6">
            <div class="flex justify-center py-8">
                <div class="animate-spin text-blue-600 text-3xl">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button type="button" onclick="closePurchaseModal()" class="soma-btn-secondary">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewPurchaseDetails(purchaseId) {
    const modal = document.getElementById('purchaseDetailsModal');
    const modalBody = document.getElementById('modalBodyContent');
    const modalPoNumber = document.getElementById('modalPoNumber');
    const modalPoDate = document.getElementById('modalPoDate');

    modal.classList.remove('hidden');
    modalBody.innerHTML = `
        <div class="flex justify-center py-12">
            <div class="animate-spin text-blue-600 text-3xl">
                <i class="fas fa-spinner"></i>
            </div>
        </div>
    `;

    fetch(`/purchases/${purchaseId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Error loading purchase details');
        const p = data.purchase;

        modalPoNumber.textContent = `Purchase Ref: ${p.reference_no}`;
        modalPoDate.textContent = `Purchase Date: ${new Date(p.purchase_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}`;

        let itemsRows = '';
        if (p.items && p.items.length > 0) {
            itemsRows = p.items.map((item, idx) => `
                <tr class="border-b border-slate-100">
                    <td class="py-2.5 px-3 text-slate-500 font-medium">${idx + 1}</td>
                    <td class="py-2.5 px-3 text-slate-800 font-semibold">${item.product ? item.product.name : 'Unknown Product'}</td>
                    <td class="py-2.5 px-3 text-center text-slate-600 font-medium">${item.quantity}</td>
                    <td class="py-2.5 px-3 text-right text-slate-700">KES ${parseFloat(item.unit_cost).toFixed(2)}</td>
                    <td class="py-2.5 px-3 text-right text-slate-900 font-bold">KES ${parseFloat(item.subtotal).toFixed(2)}</td>
                </tr>
            `).join('');
        }

        modalBody.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-400 block font-semibold uppercase">Supplier</span>
                    <strong class="text-slate-800 text-sm">${p.supplier ? p.supplier.name : 'N/A'}</strong>
                    <p class="text-slate-500">${p.supplier && p.supplier.phone ? p.supplier.phone : ''}</p>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold uppercase">Outlet</span>
                    <strong class="text-slate-800 text-sm">${p.outlet ? p.outlet.name : 'Main Outlet'}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold uppercase">Status & Payment</span>
                    <span class="inline-block mt-1 font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800 uppercase">${p.status}</span>
                    <span class="inline-block mt-1 font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 uppercase">${p.payment_status}</span>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold text-slate-800 mb-2">Itemized Products</h4>
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-100 text-slate-600 uppercase font-semibold">
                            <tr>
                                <th class="py-2.5 px-3 text-left">#</th>
                                <th class="py-2.5 px-3 text-left">Product</th>
                                <th class="py-2.5 px-3 text-center">Qty</th>
                                <th class="py-2.5 px-3 text-right">Unit Cost</th>
                                <th class="py-2.5 px-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>${itemsRows}</tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between gap-4 pt-2">
                <div class="text-xs text-slate-500">
                    ${p.notes ? `<p><strong>Notes:</strong> ${p.notes}</p>` : ''}
                </div>
                <div class="w-full md:w-64 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs space-y-1.5">
                    <div class="flex justify-between text-slate-600"><span>Subtotal:</span><span>KES ${parseFloat(p.subtotal).toFixed(2)}</span></div>
                    <div class="flex justify-between text-slate-600"><span>Tax:</span><span>KES ${parseFloat(p.tax || 0).toFixed(2)}</span></div>
                    <div class="flex justify-between text-slate-600"><span>Discount:</span><span>- KES ${parseFloat(p.discount || 0).toFixed(2)}</span></div>
                    <div class="flex justify-between text-slate-600"><span>Shipping:</span><span>KES ${parseFloat(p.shipping_cost || 0).toFixed(2)}</span></div>
                    <div class="border-t border-slate-200 pt-1.5 flex justify-between font-bold text-sm text-slate-900">
                        <span>Total Amount:</span>
                        <span>KES ${parseFloat(p.total_amount).toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between text-emerald-600 font-semibold"><span>Paid Amount:</span><span>KES ${parseFloat(p.paid_amount || 0).toFixed(2)}</span></div>
                    <div class="flex justify-between text-amber-600 font-semibold"><span>Balance Due:</span><span>KES ${parseFloat(p.due_amount || 0).toFixed(2)}</span></div>
                </div>
            </div>
        `;
    })
    .catch(err => {
        modalBody.innerHTML = `<div class="text-rose-600 p-4 text-center text-sm">${err.message}</div>`;
    });
}

function closePurchaseModal() {
    document.getElementById('purchaseDetailsModal').classList.add('hidden');
}
</script>
@endpush
@endsection
