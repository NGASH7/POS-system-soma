@extends('layouts.app')

@section('header_title', 'POS Sales List')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fas fa-receipt text-blue-600"></i>
                    <span>POS Sales List</span>
                </h1>
                <p class="text-sm text-slate-500 mt-1">All transactions processed through the point of sale.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="exportVisibleRows()"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all">
                    <i class="fas fa-download text-xs"></i> Export CSV
                </button>
                <a href="{{ route('pos.index') }}" class="soma-btn-primary group">
                    <i class="fas fa-cash-register text-xs group-hover:rotate-6 transition-transform"></i> New Sale
                </a>
            </div>
        </div>

        {{-- Flash --}}
        @if(session('success'))
            <div id="flash-msg" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm text-emerald-800 flex items-center gap-2 animate-fade-in">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span>{{ session('success') }}</span>
                <button type="button" onclick="document.getElementById('flash-msg').remove()" class="ml-auto text-emerald-400 hover:text-emerald-700">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="kpi-card rounded-2xl border border-slate-100 bg-white p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Total Revenue</div>
                        <div class="text-2xl font-extrabold text-slate-900 tabular-nums" data-count="{{ $totalRevenue }}">KES {{ number_format($totalRevenue, 2) }}</div>
                    </div>
                    <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-sack-dollar text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="kpi-card rounded-2xl border border-slate-100 bg-white p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Transactions</div>
                        <div class="text-2xl font-extrabold text-slate-900 tabular-nums">{{ number_format($totalCount) }}</div>
                    </div>
                    <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-receipt text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="kpi-card rounded-2xl border border-slate-100 bg-white p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Total Tax (VAT)</div>
                        <div class="text-2xl font-extrabold text-slate-900 tabular-nums">KES {{ number_format($totalTax, 2) }}</div>
                    </div>
                    <div class="h-9 w-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-percent text-sm"></i>
                    </div>
                </div>
            </div>
            <div class="kpi-card rounded-2xl border border-rose-100 bg-rose-50 p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-rose-400 mb-1">Total Discounts</div>
                        <div class="text-2xl font-extrabold text-rose-700 tabular-nums">-KES {{ number_format($totalDiscount, 2) }}</div>
                    </div>
                    <div class="h-9 w-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-tag text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('sales.pos-list') }}" id="filter-form" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Search Invoice / Customer</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                        <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                            placeholder="Invoice no. or customer name..." autocomplete="off"
                            class="w-full rounded-xl border border-slate-200 pl-8 pr-8 py-2 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all">
                        <button type="button" id="clear-search" onclick="document.getElementById('search-input').value='';filterRows();"
                            class="hidden absolute inset-y-0 right-0 flex items-center pr-3 text-slate-300 hover:text-slate-500">
                            <i class="fas fa-circle-xmark text-xs"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">From Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">To Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all">
                        <option value="">All Methods</option>
                        <option value="cash" @selected(request('payment_method') === 'cash')>Cash</option>
                        <option value="card" @selected(request('payment_method') === 'card')>Card</option>
                        <option value="mobile_money" @selected(request('payment_method') === 'mobile_money')>M-Pesa</option>
                        <option value="credit" @selected(request('payment_method') === 'credit')>Credit</option>
                        <option value="bank_transfer" @selected(request('payment_method') === 'bank_transfer')>Bank Transfer</option>
                        <option value="other" @selected(request('payment_method') === 'other')>Other</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" id="filter-submit" class="soma-btn-primary flex-1 justify-center py-2">
                        <span class="btn-label inline-flex items-center gap-2"><i class="fas fa-filter text-xs"></i> Filter</span>
                        <i class="fas fa-circle-notch fa-spin text-xs hidden btn-spinner"></i>
                    </button>
                    @if(request()->hasAny(['search','start_date','end_date','payment_method','status']))
                        <a href="{{ route('sales.pos-list') }}" class="flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all" title="Clear filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>

            @if(request()->hasAny(['search','start_date','end_date','payment_method']))
                <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                    <span class="text-[11px] font-bold text-slate-400 uppercase">Active:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold px-2.5 py-1">
                            <i class="fas fa-search text-[9px]"></i> "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('start_date'))
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold px-2.5 py-1">
                            From {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }}
                        </span>
                    @endif
                    @if(request('end_date'))
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold px-2.5 py-1">
                            To {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
                        </span>
                    @endif
                    @if(request('payment_method'))
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold px-2.5 py-1 capitalize">
                            {{ str_replace('_',' ', request('payment_method')) }}
                        </span>
                    @endif
                </div>
            @endif
        </form>

        {{-- Sales Table --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            @if($sales->isEmpty())
                <div class="py-16 text-center text-slate-400">
                    <i class="fas fa-receipt text-4xl mb-3 block"></i>
                    <p class="text-sm font-bold text-slate-600">No sales found</p>
                    <p class="text-xs mt-1">Try adjusting your filters or make a new sale.</p>
                    <a href="{{ route('pos.index') }}" class="soma-btn-primary mt-4 inline-flex">
                        <i class="fas fa-cash-register text-xs"></i> Go to POS
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="sales-table">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 sticky top-0 z-10">
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wide w-8"></th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Invoice</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wide cursor-pointer select-none hover:text-slate-700" onclick="sortTable(2,'date')">
                                    Date <i class="fas fa-sort text-[9px] ml-0.5 opacity-40"></i>
                                </th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Customer</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Items</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wide">Gross</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wide">Discount</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wide">Tax (VAT)</th>
                                <th class="px-5 py-3.5 text-right text-xs font-bold text-slate-500 uppercase tracking-wide cursor-pointer select-none hover:text-slate-700" onclick="sortTable(8,'number')">
                                    Total <i class="fas fa-sort text-[9px] ml-0.5 opacity-40"></i>
                                </th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Payment</th>
                                <th class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wide">Status</th>
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="sales-tbody">
                            @foreach($sales as $sale)
                                @php
                                    $gross = $sale->total + $sale->discount;
                                @endphp
                                <tr class="sale-row hover:bg-slate-50/50 transition-colors cursor-pointer"
                                    data-row-index="{{ $loop->index }}"
                                    data-search="{{ strtolower($sale->invoice_no.' '.($sale->customer->name ?? 'walk-in')) }}"
                                    data-date="{{ $sale->sale_date->timestamp }}"
                                    data-total="{{ $sale->total }}"
                                    onclick="toggleRow({{ $loop->index }})">
                                    <td class="px-5 py-3.5 text-slate-300">
                                        <i class="fas fa-chevron-right text-[10px] chevron-{{ $loop->index }} transition-transform"></i>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-mono font-bold text-xs text-blue-700 bg-blue-50 px-2 py-1 rounded-lg inline-flex items-center gap-1.5 group"
                                              onclick="event.stopPropagation(); copyInvoice(this, '{{ $sale->invoice_no }}')">
                                            {{ $sale->invoice_no }}
                                            <i class="fas fa-copy text-[9px] opacity-0 group-hover:opacity-60 transition-opacity"></i>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600 text-xs whitespace-nowrap">
                                        {{ $sale->sale_date->format('d M Y') }}<br>
                                        <span class="text-slate-400">{{ $sale->sale_date->format('H:i') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($sale->customer)
                                            <div class="font-semibold text-slate-900 text-xs">{{ $sale->customer->name }}</div>
                                            <div class="text-slate-400 text-[11px]">{{ $sale->customer->phone ?? '' }}</div>
                                        @else
                                            <span class="text-slate-400 text-xs italic">Walk-in</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700">
                                            <i class="fas fa-box text-slate-400 text-[10px]"></i>
                                            {{ $sale->items->sum('quantity') }} item(s)
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-semibold text-slate-700 text-xs">
                                        KES {{ number_format($gross, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-xs">
                                        @if($sale->discount > 0)
                                            <span class="font-bold text-rose-600">-KES {{ number_format($sale->discount, 2) }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-xs font-semibold text-slate-600">
                                        KES {{ number_format($sale->tax, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <span class="font-extrabold text-sm text-slate-900">KES {{ number_format($sale->total, 2) }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @php
                                            $methodColors = [
                                                'cash'          => 'bg-emerald-100 text-emerald-800',
                                                'card'          => 'bg-blue-100 text-blue-800',
                                                'mobile_money'  => 'bg-green-100 text-green-800',
                                                'credit'        => 'bg-orange-100 text-orange-800',
                                                'bank_transfer' => 'bg-violet-100 text-violet-800',
                                                'other'         => 'bg-slate-100 text-slate-600',
                                            ];
                                            $methodLabels = [
                                                'cash'          => 'Cash',
                                                'card'          => 'Card',
                                                'mobile_money'  => 'M-Pesa',
                                                'credit'        => 'Credit',
                                                'bank_transfer' => 'Bank Transfer',
                                                'other'         => 'Other',
                                            ];
                                            $cls = $methodColors[$sale->payment_method] ?? 'bg-slate-100 text-slate-600';
                                            $lbl = $methodLabels[$sale->payment_method] ?? ucfirst($sale->payment_method);
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $cls }}">
                                            {{ $lbl }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($sale->status === 'completed')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Completed</span>
                                        @elseif($sale->status === 'pending')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Pending</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-600">{{ ucfirst($sale->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-center" onclick="event.stopPropagation()">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('pos.receipt', $sale->id) }}"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition"
                                                title="View Receipt">
                                                <i class="fas fa-eye text-xs"></i>
                                            </a>
                                            <a href="{{ route('pos.print', $sale->id) }}" target="_blank"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600 transition"
                                                title="Print Receipt">
                                                <i class="fas fa-print text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                {{-- Expandable detail row --}}
                                <tr class="detail-row-{{ $loop->index }} hidden bg-slate-50/60">
                                    <td colspan="11" class="px-5 py-0">
                                        <div class="detail-inner py-4 pl-9">
                                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wide mb-2">Items in this sale</div>
                                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                                @foreach($sale->items as $item)
                                                    <div class="flex items-center justify-between rounded-lg bg-white border border-slate-100 px-3 py-2 text-xs">
                                                        <span class="font-semibold text-slate-700 truncate pr-2">
                                                            {{ $item->product->name ?? $item->name ?? 'Item' }}
                                                            <span class="text-slate-400 font-normal">×{{ $item->quantity }}</span>
                                                        </span>
                                                        <span class="font-bold text-slate-900 shrink-0">
                                                            KES {{ number_format($item->price * $item->quantity, 2) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 border-t-2 border-slate-200">
                                <td colspan="5" class="px-5 py-3 text-xs font-bold text-slate-600 uppercase">
                                    Page Totals ({{ $sales->count() }} of {{ $sales->total() }} records)
                                </td>
                                <td class="px-5 py-3 text-right text-xs font-bold text-slate-700">
                                    KES {{ number_format($sales->sum(fn($s) => $s->total + $s->discount), 2) }}
                                </td>
                                <td class="px-5 py-3 text-right text-xs font-bold text-rose-600">
                                    -KES {{ number_format($sales->sum('discount'), 2) }}
                                </td>
                                <td class="px-5 py-3 text-right text-xs font-bold text-slate-700">
                                    KES {{ number_format($sales->sum('tax'), 2) }}
                                </td>
                                <td class="px-5 py-3 text-right text-sm font-extrabold text-slate-900">
                                    KES {{ number_format($sales->sum('total'), 2) }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($sales->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4">
                        {{ $sales->links() }}
                    </div>
                @endif
            @endif
        </div>

        <p id="no-results-msg" class="hidden text-center text-sm text-slate-400 mt-6">
            <i class="fas fa-filter-circle-xmark mr-1"></i> No rows match your search on this page.
        </p>

    </div>
</div>

{{-- Styles --}}
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fadeIn .3s ease-out; }

    .kpi-card { transition: transform .18s ease, box-shadow .18s ease; }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -8px rgba(15,23,42,.15); }

    .sale-row.row-open { background-color: rgba(241,245,249,.6); }
    .chevron-open { transform: rotate(90deg); }

    tr[class*="detail-row-"] td { border-top: none; }
    .detail-inner { animation: fadeIn .18s ease-out; }

    #sales-table thead th { position: sticky; top: 0; }

    .btn-loading .btn-label { display: none; }
    .btn-loading .btn-spinner { display: inline-block !important; }
    #filter-submit { position: relative; }

    .copied-badge {
        position: absolute; transform: translateY(-100%); margin-top: -6px;
        background: #0f172a; color: #fff; font-size: 10px; font-weight: 700;
        padding: 2px 8px; border-radius: 6px; pointer-events: none;
        animation: fadeIn .15s ease-out;
    }
</style>

{{-- Interactivity --}}
<script>
    // Expand/collapse row details
    function toggleRow(idx) {
        const detailRows = document.getElementsByClassName('detail-row-' + idx);
        const chevrons = document.getElementsByClassName('chevron-' + idx);
        const row = document.querySelector(`.sale-row[data-row-index="${idx}"]`);
        if (!detailRows.length) return;
        const isHidden = detailRows[0].classList.contains('hidden');
        Array.from(detailRows).forEach(r => r.classList.toggle('hidden', !isHidden));
        Array.from(chevrons).forEach(c => c.classList.toggle('chevron-open', isHidden));
        if (row) row.classList.toggle('row-open', isHidden);
    }

    // Copy invoice number
    function copyInvoice(el, text) {
        navigator.clipboard?.writeText(text).then(() => {
            const badge = document.createElement('span');
            badge.className = 'copied-badge';
            badge.textContent = 'Copied!';
            el.style.position = 'relative';
            el.appendChild(badge);
            setTimeout(() => badge.remove(), 1000);
        });
    }

    // Client-side instant filter of rows already on this page (search only,
    // real filtering across all records still happens via the form submit)
    const searchInput = document.getElementById('search-input');
    const clearBtn = document.getElementById('clear-search');
    let searchTimer;

    function filterRows() {
        const q = (searchInput?.value || '').toLowerCase().trim();
        clearBtn.classList.toggle('hidden', q.length === 0);
        const rows = document.querySelectorAll('.sale-row');
        let visibleCount = 0;
        rows.forEach(row => {
            const idx = row.dataset.rowIndex;
            const match = row.dataset.search.includes(q);
            row.style.display = match ? '' : 'none';
            const detail = document.querySelector('.detail-row-' + idx);
            if (detail && !match) detail.classList.add('hidden');
            if (match) visibleCount++;
        });
        document.getElementById('no-results-msg')?.classList.toggle('hidden', visibleCount !== 0 || rows.length === 0);
    }

    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(filterRows, 150);
    });
    filterRows();

    // Sortable columns (client-side, current page only)
    let sortState = {};
    function sortTable(colIndex, type) {
        const tbody = document.getElementById('sales-tbody');
        const rows = Array.from(document.querySelectorAll('.sale-row'));
        const asc = !(sortState[colIndex] === 'asc');
        sortState = { [colIndex]: asc ? 'asc' : 'desc' };

        rows.sort((a, b) => {
            let av, bv;
            if (type === 'date') { av = +a.dataset.date; bv = +b.dataset.date; }
            else { av = parseFloat(a.dataset.total); bv = parseFloat(b.dataset.total); }
            return asc ? av - bv : bv - av;
        });

        rows.forEach(row => {
            const idx = row.dataset.rowIndex;
            const detail = document.querySelector('.detail-row-' + idx);
            tbody.appendChild(row);
            if (detail) tbody.appendChild(detail);
        });
    }

    // Loading state on filter submit
    document.getElementById('filter-form')?.addEventListener('submit', function () {
        document.getElementById('filter-submit')?.classList.add('btn-loading');
    });

    // Auto-dismiss flash message
    setTimeout(() => {
        const flash = document.getElementById('flash-msg');
        if (flash) {
            flash.style.transition = 'opacity .4s ease';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }
    }, 5000);

    // Export currently visible rows to CSV
    function exportVisibleRows() {
        const rows = document.querySelectorAll('.sale-row');
        const header = ['Invoice', 'Date', 'Customer', 'Items', 'Total', 'Payment', 'Status'];
        const lines = [header.join(',')];
        rows.forEach(row => {
            if (row.style.display === 'none') return;
            const cells = row.querySelectorAll('td');
            const invoice = cells[1]?.innerText.trim().replace(/\s+/g, ' ');
            const date = cells[2]?.innerText.trim().replace(/\s+/g, ' ');
            const customer = cells[3]?.innerText.trim().replace(/\s+/g, ' ');
            const items = cells[4]?.innerText.trim().replace(/\s+/g, ' ');
            const total = cells[8]?.innerText.trim().replace(/\s+/g, ' ');
            const payment = cells[9]?.innerText.trim();
            const status = cells[10]?.innerText.trim();
            const line = [invoice, date, customer, items, total, payment, status]
                .map(v => `"${(v || '').replace(/"/g, '""')}"`)
                .join(',');
            lines.push(line);
        });
        const blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'pos-sales-' + new Date().toISOString().slice(0, 10) + '.csv';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
@endsection