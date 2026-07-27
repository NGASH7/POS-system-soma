@extends('layouts.app')

@section('content')
<div class="soma-page-inner space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-600/10 text-orange-600 flex items-center justify-center">
                    <i class="fas fa-rotate-left text-xl"></i>
                </div>
                Add Purchase Return
            </h1>
            <p class="text-slate-500 text-sm mt-1">Select a purchase order and specify the items being returned to the supplier.</p>
        </div>
        <a href="{{ route('purchases.returns') }}" class="soma-btn-secondary">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Returns</span>
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-exclamation-triangle text-rose-500"></i>
                <p class="font-semibold text-rose-700">Please fix the following errors:</p>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-rose-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('purchases.returns.store') }}" id="returnForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column: Return Info --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Return Reference --}}
                <div class="soma-card p-5 space-y-4">
                    <h2 class="font-bold text-slate-800 flex items-center gap-2 text-sm uppercase tracking-wide border-b border-slate-100 pb-3">
                        <i class="fas fa-file-lines text-orange-500"></i>
                        Return Information
                    </h2>
                    <div>
                        <label class="soma-label">Return Number</label>
                        <input type="text" name="return_no" value="{{ $returnNo }}" class="soma-input w-full font-mono" readonly>
                        <p class="text-xs text-slate-400 mt-1">Auto-generated</p>
                    </div>
                    <div>
                        <label class="soma-label">Return Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="return_date" value="{{ old('return_date', date('Y-m-d')) }}"
                            class="soma-input w-full @error('return_date') border-rose-400 @enderror" required>
                        @error('return_date')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="soma-label">Return Status <span class="text-rose-500">*</span></label>
                        <select name="status" class="soma-input w-full @error('status') border-rose-400 @enderror" required>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <p class="text-xs text-slate-400 mt-1">
                            <i class="fas fa-info-circle"></i> "Completed" will deduct stock automatically.
                        </p>
                        @error('status')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="soma-label">Refund Status <span class="text-rose-500">*</span></label>
                        <select name="refund_status" class="soma-input w-full @error('refund_status') border-rose-400 @enderror" required>
                            <option value="pending" {{ old('refund_status') == 'pending' ? 'selected' : '' }}>Pending Refund</option>
                            <option value="refunded" {{ old('refund_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                        @error('refund_status')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="soma-label">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Optional reason or notes for this return..."
                            class="soma-input w-full resize-none @error('notes') border-rose-400 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

            </div>

            {{-- Right Column: Purchase Selection + Items --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Purchase Order Selection --}}
                <div class="soma-card p-5 space-y-4">
                    <h2 class="font-bold text-slate-800 flex items-center gap-2 text-sm uppercase tracking-wide border-b border-slate-100 pb-3">
                        <i class="fas fa-receipt text-blue-500"></i>
                        Select Purchase Order
                    </h2>
                    <div>
                        <label class="soma-label">Purchase Order <span class="text-rose-500">*</span></label>
                        <select name="purchase_id" id="purchaseSelect"
                            class="soma-input w-full @error('purchase_id') border-rose-400 @enderror" required
                            onchange="loadPurchaseItems(this.value)">
                            <option value="">— Select a Purchase Order —</option>
                            @foreach($purchases as $purchase)
                                <option value="{{ $purchase->id }}"
                                    data-items="{{ json_encode($purchase->items->map(fn($i) => [
                                        'product_id'   => $i->product_id,
                                        'product_name' => $i->product->name ?? 'Unknown',
                                        'quantity'     => $i->quantity,
                                        'unit_cost'    => $i->unit_cost,
                                    ])) }}"
                                    {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                    {{ $purchase->reference_no ?? 'PO-'.$purchase->id }}
                                    — {{ $purchase->supplier->name ?? 'No Supplier' }}
                                    ({{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Purchase Summary (shown after selection) --}}
                    <div id="purchaseSummary" class="hidden p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm space-y-1">
                        <div class="flex justify-between"><span class="text-slate-500">Supplier:</span><span id="ps_supplier" class="font-semibold text-slate-800"></span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Purchase Date:</span><span id="ps_date" class="font-semibold text-slate-800"></span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Total Items:</span><span id="ps_items" class="font-semibold text-slate-800"></span></div>
                    </div>
                </div>

                {{-- Return Items --}}
                <div class="soma-card p-5 space-y-4">
                    <h2 class="font-bold text-slate-800 flex items-center gap-2 text-sm uppercase tracking-wide border-b border-slate-100 pb-3">
                        <i class="fas fa-boxes-packing text-orange-500"></i>
                        Return Items
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="returnItemsTable">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    <th class="text-left px-3 py-2">Product</th>
                                    <th class="text-center px-3 py-2 w-24">Return Qty</th>
                                    <th class="text-right px-3 py-2 w-32">Unit Cost</th>
                                    <th class="text-right px-3 py-2 w-32">Subtotal</th>
                                    <th class="text-left px-3 py-2 w-36">Reason</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="returnItemsBody">
                                <tr id="emptyItemsRow">
                                    <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="fas fa-receipt text-3xl text-slate-200"></i>
                                            <p>Select a purchase order above to load items</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Total Section --}}
                    <div class="flex justify-end pt-3 border-t border-slate-100">
                        <div class="w-72 space-y-2">
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Return Total:</span>
                                <span id="grandTotalDisplay" class="font-bold text-slate-900 text-base">KES 0.00</span>
                            </div>
                            <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('purchases.returns') }}" class="soma-btn-secondary">Cancel</a>
                        <button type="submit" class="soma-btn-primary" style="background: linear-gradient(135deg, #ea580c, #dc2626);" id="submitBtn" disabled>
                            <i class="fas fa-save"></i>
                            <span>Save Return</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const purchases = @json($purchases->keyBy('id'));

function loadPurchaseItems(purchaseId) {
    const tbody = document.getElementById('returnItemsBody');
    const emptyRow = document.getElementById('emptyItemsRow');
    const purchaseSummary = document.getElementById('purchaseSummary');
    const submitBtn = document.getElementById('submitBtn');

    if (!purchaseId) {
        tbody.innerHTML = `<tr id="emptyItemsRow"><td colspan="6" class="px-4 py-10 text-center text-slate-400"><div class="flex flex-col items-center gap-2"><i class="fas fa-receipt text-3xl text-slate-200"></i><p>Select a purchase order above to load items</p></div></td></tr>`;
        purchaseSummary.classList.add('hidden');
        submitBtn.disabled = true;
        recalcTotal();
        return;
    }

    const select = document.getElementById('purchaseSelect');
    const selected = select.options[select.selectedIndex];
    const items = JSON.parse(selected.dataset.items || '[]');

    // Show purchase summary
    const purchase = purchases[purchaseId];
    if (purchase) {
        document.getElementById('ps_supplier').textContent = purchase.supplier?.name || '—';
        document.getElementById('ps_date').textContent = purchase.purchase_date || '—';
        document.getElementById('ps_items').textContent = items.length + ' product(s)';
        purchaseSummary.classList.remove('hidden');
    }

    if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">This purchase has no items recorded.</td></tr>`;
        submitBtn.disabled = true;
        recalcTotal();
        return;
    }

    tbody.innerHTML = '';
    items.forEach((item, idx) => {
        const row = document.createElement('tr');
        row.className = 'border-b border-slate-100 hover:bg-slate-50';
        row.innerHTML = `
            <td class="px-3 py-2">
                <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                <span class="font-medium text-slate-800">${item.product_name}</span>
                <p class="text-xs text-slate-400">Max: ${item.quantity} units KES ${parseFloat(item.unit_cost).toLocaleString()}</p>
            </td>
            <td class="px-3 py-2">
                <input type="number" name="items[${idx}][quantity]"
                    class="soma-input w-full text-center return-qty" style="min-width:70px"
                    min="0" max="${item.quantity}" value="0"
                    data-unit-cost="${item.unit_cost}"
                    data-row="${idx}"
                    oninput="recalcRow(this)">
            </td>
            <td class="px-3 py-2">
                <input type="number" name="items[${idx}][unit_cost]"
                    class="soma-input w-full text-right return-cost" style="min-width:100px"
                    step="0.01" min="0" value="${parseFloat(item.unit_cost).toFixed(2)}"
                    data-row="${idx}"
                    oninput="recalcRow(this)">
            </td>
            <td class="px-3 py-2 text-right">
                <input type="hidden" name="items[${idx}][subtotal]" class="subtotal-input" data-row="${idx}" value="0">
                <span class="subtotal-display font-semibold text-slate-800" data-row="${idx}">KES 0.00</span>
            </td>
            <td class="px-3 py-2">
                <input type="text" name="items[${idx}][reason]" placeholder="e.g., Damaged"
                    class="soma-input w-full" style="min-width:120px">
            </td>
            <td class="px-3 py-2 text-center">
                <button type="button" onclick="removeItem(this)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-100 transition-colors mx-auto">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });

    submitBtn.disabled = false;
    recalcTotal();
}

function recalcRow(input) {
    const row = input.dataset.row;
    const qtyInput = document.querySelector(`.return-qty[data-row="${row}"]`);
    const costInput = document.querySelector(`.return-cost[data-row="${row}"]`);
    const subtotalInput = document.querySelector(`.subtotal-input[data-row="${row}"]`);
    const subtotalDisplay = document.querySelector(`.subtotal-display[data-row="${row}"]`);

    const qty = parseFloat(qtyInput?.value) || 0;
    const cost = parseFloat(costInput?.value) || 0;
    const subtotal = qty * cost;

    if (subtotalInput) subtotalInput.value = subtotal.toFixed(2);
    if (subtotalDisplay) subtotalDisplay.textContent = 'KES ' + subtotal.toLocaleString('en', { minimumFractionDigits: 2 });

    recalcTotal();
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal-input').forEach(inp => {
        total += parseFloat(inp.value) || 0;
    });
    document.getElementById('grandTotalDisplay').textContent = 'KES ' + total.toLocaleString('en', { minimumFractionDigits: 2 });
    document.getElementById('totalAmountInput').value = total.toFixed(2);
}

function removeItem(btn) {
    btn.closest('tr').remove();
    recalcTotal();

    if (!document.querySelectorAll('#returnItemsBody tr').length) {
        const tbody = document.getElementById('returnItemsBody');
        tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">All items removed. Please reload the purchase to restore items.</td></tr>`;
        document.getElementById('submitBtn').disabled = true;
    }
}

// Auto-load if there's an old selection
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('purchaseSelect');
    if (sel.value) loadPurchaseItems(sel.value);
});
</script>
@endsection
