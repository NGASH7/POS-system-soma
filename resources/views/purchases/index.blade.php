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

<!-- ===== Purchase Details Drawer ===== -->
<style>
#pd-overlay { transition: opacity 0.25s ease; }
#pd-panel   { transition: transform 0.28s cubic-bezier(.4,0,.2,1); }
#pd-panel.pd-open   { transform: translateX(0); }
#pd-panel.pd-closed { transform: translateX(100%); }
</style>

<div id="pd-overlay" class="fixed inset-0 opacity-0 pointer-events-none"
     style="z-index:99998;background:rgba(15,23,42,0.55);backdrop-filter:blur(3px);"
     onclick="closePurchaseModal()"></div>

<div id="pd-panel" class="fixed top-0 right-0 h-full pd-closed flex flex-col bg-white shadow-2xl overflow-hidden"
     style="z-index:99999;width:400px;max-width:94vw;">

    <div class="flex-shrink-0 flex items-center justify-between px-4 py-3"
         style="background:linear-gradient(135deg,#1e3a5f,#1d4ed8);">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center text-white flex-shrink-0">
                <i class="fas fa-file-invoice text-sm"></i>
            </div>
            <div class="min-w-0">
                <p id="pd-ref" class="font-bold text-white text-sm truncate">Purchase Details</p>
                <p id="pd-date" class="text-blue-200 text-xs">Loading…</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 ml-2 flex-shrink-0">
            <button onclick="printPurchaseModal()"
                class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white/15 hover:bg-white/25 text-white text-xs font-semibold transition-colors">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="closePurchaseModal()"
                class="w-8 h-8 rounded-lg bg-white/15 hover:bg-white/25 flex items-center justify-center text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div id="pd-body" class="flex-1 overflow-y-auto p-4 space-y-3" style="font-size:12px;">
        <div class="flex flex-col items-center justify-center py-12 gap-3">
            <div class="w-8 h-8 border-[3px] border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-slate-400 text-xs">Loading…</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewPurchaseDetails(id) {
    const overlay = document.getElementById('pd-overlay');
    const panel   = document.getElementById('pd-panel');
    const body    = document.getElementById('pd-body');

    overlay.classList.remove('pointer-events-none');
    overlay.style.opacity = '1';
    panel.classList.replace('pd-closed','pd-open');
    document.body.style.overflow = 'hidden';

    document.getElementById('pd-ref').textContent  = 'Loading…';
    document.getElementById('pd-date').textContent = '';
    body.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 0;gap:12px"><div style="width:32px;height:32px;border:3px solid #3b82f6;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite"></div><p style="color:#94a3b8;font-size:12px">Loading…</p></div>';

    fetch('/purchases/'+id, { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
    .then(r=>r.json())
    .then(data=>{
        if(!data.success) throw new Error(data.message||'Failed');
        const p = data.purchase;

        document.getElementById('pd-ref').textContent  = p.reference_no || 'PO-'+p.id;
        const dt = p.purchase_date ? new Date(p.purchase_date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}) : '—';
        document.getElementById('pd-date').textContent = dt;

        const stMap={received:{bg:'#d1fae5',c:'#065f46',d:'#10b981'},pending:{bg:'#fef3c7',c:'#92400e',d:'#f59e0b'},ordered:{bg:'#dbeafe',c:'#1e40af',d:'#3b82f6'},cancelled:{bg:'#fee2e2',c:'#991b1b',d:'#ef4444'}};
        const pyMap={paid:{bg:'#d1fae5',c:'#065f46'},partial:{bg:'#fef3c7',c:'#92400e'},due:{bg:'#fee2e2',c:'#991b1b'},unpaid:{bg:'#fee2e2',c:'#991b1b'}};
        const st=stMap[p.status]||{bg:'#f1f5f9',c:'#475569',d:'#94a3b8'};
        const py=pyMap[p.payment_status]||{bg:'#f1f5f9',c:'#475569'};

        const stBadge=`<span style="background:${st.bg};color:${st.c};display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:999px;font-weight:700;font-size:11px"><span style="width:6px;height:6px;border-radius:50%;background:${st.d};display:inline-block"></span>${(p.status||'').charAt(0).toUpperCase()+(p.status||'').slice(1)}</span>`;
        const pyBadge=`<span style="background:${py.bg};color:${py.c};display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-weight:700;font-size:11px">${(p.payment_status||'').charAt(0).toUpperCase()+(p.payment_status||'').slice(1)}</span>`;

        let rows=(p.items||[]).map((item,i)=>{
            const nm=item.product?item.product.name:'Unknown';
            const sku=item.product&&item.product.sku?item.product.sku:'';
            const cost=parseFloat(item.unit_cost||0);
            const sub=parseFloat(item.subtotal||0);
            return `<tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:7px 8px;color:#94a3b8;font-family:monospace">${i+1}</td>
                <td style="padding:7px 8px"><p style="font-weight:600;color:#1e293b;margin:0">${nm}</p>${sku?`<p style="font-size:10px;color:#94a3b8;margin:1px 0 0;font-family:monospace">${sku}</p>`:''}</td>
                <td style="padding:7px 8px;text-align:center"><span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:20px;background:#f1f5f9;border-radius:4px;font-weight:700;color:#334155">${item.quantity}</span></td>
                <td style="padding:7px 8px;text-align:right;color:#64748b">KES ${cost.toLocaleString('en',{minimumFractionDigits:2})}</td>
                <td style="padding:7px 8px;text-align:right;font-weight:700;color:#0f172a">KES ${sub.toLocaleString('en',{minimumFractionDigits:2})}</td>
            </tr>`;
        }).join('')||'<tr><td colspan="5" style="padding:24px;text-align:center;color:#94a3b8">No items</td></tr>';

        const sub2=parseFloat(p.subtotal||0),tax=parseFloat(p.tax||0),disc=parseFloat(p.discount||0),ship=parseFloat(p.shipping_cost||0),total=parseFloat(p.total_amount||0),paid=parseFloat(p.paid_amount||0),due=parseFloat(p.due_amount||0);

        const summaryRow=(label,val,color='#64748b')=>`<div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;color:${color}">${label}<span style="font-weight:600">KES ${val.toLocaleString('en',{minimumFractionDigits:2})}</span></div>`;

        body.innerHTML = `
<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
    <div style="border:1px solid #f1f5f9;background:#f8fafc;border-radius:8px;padding:10px">
        <p style="font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Supplier</p>
        <p style="font-weight:700;color:#1e293b;margin:0;line-height:1.3">${p.supplier?p.supplier.name:'—'}</p>
        ${p.supplier&&p.supplier.phone?`<p style="font-size:10px;color:#64748b;margin:2px 0 0">${p.supplier.phone}</p>`:''}
    </div>
    <div style="border:1px solid #f1f5f9;background:#f8fafc;border-radius:8px;padding:10px">
        <p style="font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Outlet</p>
        <p style="font-weight:700;color:#1e293b;margin:0">${p.outlet?p.outlet.name:'Main Outlet'}</p>
    </div>
    <div style="border:1px solid #f1f5f9;background:#f8fafc;border-radius:8px;padding:10px">
        <p style="font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 6px">Status</p>
        ${stBadge}
    </div>
    <div style="border:1px solid #f1f5f9;background:#f8fafc;border-radius:8px;padding:10px">
        <p style="font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 6px">Payment</p>
        ${pyBadge}
    </div>
</div>

<div style="margin-bottom:12px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
        <p style="font-size:10px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;margin:0;display:flex;align-items:center;gap:6px">
            <span style="width:18px;height:18px;background:#dbeafe;color:#2563eb;border-radius:4px;display:inline-flex;align-items:center;justify-content:center;font-size:9px"><i class="fas fa-box"></i></span>
            Items
        </p>
        <span style="font-size:10px;color:#94a3b8">${(p.items||[]).length} item(s)</span>
    </div>
    <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:11px">
            <thead><tr style="background:linear-gradient(90deg,#334155,#475569);color:white">
                <th style="padding:7px 8px;text-align:left;font-size:9px;text-transform:uppercase;letter-spacing:.05em">#</th>
                <th style="padding:7px 8px;text-align:left;font-size:9px;text-transform:uppercase;letter-spacing:.05em">Product</th>
                <th style="padding:7px 8px;text-align:center;font-size:9px;text-transform:uppercase;letter-spacing:.05em">Qty</th>
                <th style="padding:7px 8px;text-align:right;font-size:9px;text-transform:uppercase;letter-spacing:.05em">Cost</th>
                <th style="padding:7px 8px;text-align:right;font-size:9px;text-transform:uppercase;letter-spacing:.05em">Sub</th>
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>
    </div>
</div>

<div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:12px">
    <div style="background:#f8fafc;padding:8px 12px;border-bottom:1px solid #e2e8f0">
        <p style="font-size:10px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.06em;margin:0">Payment Summary</p>
    </div>
    <div style="padding:10px 12px">
        ${summaryRow('Subtotal',sub2)}
        ${tax>0?summaryRow('Tax',tax):''}
        ${disc>0?summaryRow('<span style="color:#e11d48">Discount</span>',disc,'#e11d48'):''}
        ${ship>0?summaryRow('Shipping',ship):''}
        <div style="border-top:1px solid #e2e8f0;margin-top:6px;padding-top:8px;display:flex;justify-content:space-between;font-weight:800;font-size:14px;color:#0f172a"><span>Grand Total</span><span>KES ${total.toLocaleString('en',{minimumFractionDigits:2})}</span></div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;color:#059669;font-weight:600"><span style="display:flex;align-items:center;gap:4px"><i class="fas fa-check-circle" style="font-size:10px"></i> Paid</span><span>KES ${paid.toLocaleString('en',{minimumFractionDigits:2})}</span></div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;font-weight:700;color:${due>0?'#e11d48':'#94a3b8'}"><span style="display:flex;align-items:center;gap:4px"><i class="fas ${due>0?'fa-hourglass-half':'fa-circle-check'}" style="font-size:10px"></i> Balance Due</span><span>KES ${due.toLocaleString('en',{minimumFractionDigits:2})}</span></div>
    </div>
    <div style="padding:8px 12px;text-align:center;background:${due<=0?'#f0fdf4':'#fff1f2'};border-top:1px solid ${due<=0?'#bbf7d0':'#fecdd3'}">
        <span style="font-size:11px;font-weight:700;color:${due<=0?'#15803d':'#be123c'}"><i class="fas ${due<=0?'fa-circle-check':'fa-triangle-exclamation'}"></i> ${due<=0?'Fully Paid':'Balance Outstanding'}</span>
    </div>
</div>

${p.notes?`<div style="border:1px solid #fef08a;background:#fefce8;border-radius:8px;padding:10px 12px"><p style="font-size:9px;font-weight:700;color:#854d0e;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px"><i class="fas fa-note-sticky"></i> Notes</p><p style="color:#713f12;margin:0;font-size:11px">${p.notes}</p></div>`:''}`;
    })
    .catch(err=>{
        body.innerHTML=`<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 0;gap:10px"><div style="width:40px;height:40px;background:#fee2e2;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#ef4444;font-size:18px"><i class="fas fa-triangle-exclamation"></i></div><p style="font-weight:600;color:#334155;font-size:12px;margin:0">Failed to load</p><p style="color:#94a3b8;font-size:11px;margin:0">${err.message}</p></div>`;
    });
}

function closePurchaseModal() {
    const overlay=document.getElementById('pd-overlay');
    const panel=document.getElementById('pd-panel');
    overlay.style.opacity='0';
    overlay.classList.add('pointer-events-none');
    panel.classList.replace('pd-open','pd-closed');
    document.body.style.overflow='';
}

function printPurchaseModal() {
    const content=document.getElementById('pd-body').innerHTML;
    const ref=document.getElementById('pd-ref').textContent;
    const dt=document.getElementById('pd-date').textContent;
    const w=window.open('','_blank','width=720,height=600');
    w.document.write('<!DOCTYPE html><html><head><title>'+ref+'</title><style>body{font-family:Arial,sans-serif;font-size:12px;color:#1e293b;padding:20px}h2{font-size:18px;font-weight:800;margin:0 0 4px}@keyframes spin{to{transform:rotate(360deg)}}@media print{body{padding:0}}</style></head><body><h2>'+ref+'</h2><p style="color:#64748b;margin:0 0 16px">'+dt+'</p><hr><div>'+content+'</div><script>window.onload=function(){window.print()}<\/script></body></html>');
    w.document.close();
}

document.addEventListener('keydown',e=>{if(e.key==='Escape')closePurchaseModal();});
</script>
@endpush
@endsection