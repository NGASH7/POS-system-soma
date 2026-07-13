@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-4xl">

        <div class="mb-6">
            <a href="{{ route('stock-transfers.index') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                <i class="fas fa-arrow-left text-xs"></i> Back to transfers
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">New stock transfer</h1>
            <p class="text-sm text-slate-500 mt-1">Move products from one store to another</p>
        </div>

        <div class="soma-card p-6">
            <form method="POST" action="{{ route('stock-transfers.store') }}" id="transfer-form">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">From store <span class="text-rose-500">*</span></label>
                        <select name="from_outlet_id" id="from-outlet" class="soma-input" required {{ auth()->user()->isAdmin() ? '' : 'disabled' }}>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('from_outlet_id', $defaultFromOutlet) == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(!auth()->user()->isAdmin())
                            <input type="hidden" name="from_outlet_id" value="{{ auth()->user()->outlet_id }}">
                        @endif
                        @error('from_outlet_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">To store <span class="text-rose-500">*</span></label>
                        <select name="to_outlet_id" id="to-outlet" class="soma-input" required>
                            <option value="">Select destination</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}" {{ old('to_outlet_id') == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_outlet_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" class="soma-input resize-none" placeholder="Optional">{{ old('notes') }}</textarea>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-sm font-semibold text-slate-900">Products</h2>
                        <button type="button" id="add-row" class="soma-btn-secondary text-sm py-2">
                            <i class="fas fa-plus text-xs"></i> Add product
                        </button>
                    </div>

                    <div id="product-rows" class="space-y-3">
                        <div class="product-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-7">
                                <label class="block text-xs font-medium text-slate-500 mb-1.5">Product</label>
                                <select name="items[0][product_id]" class="soma-input product-select" required>
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock_quantity }}">
                                            {{ $product->name }} ({{ $product->sku }}) — {{ $product->stock_quantity }} in stock
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-medium text-slate-500 mb-1.5">Quantity</label>
                                <input type="number" name="items[0][quantity]" min="1" value="1" class="soma-input quantity-input" required>
                            </div>
                            <div class="sm:col-span-2">
                                <button type="button" class="remove-row soma-btn-secondary w-full justify-center text-sm py-2 text-rose-600 border-rose-200 hover:bg-rose-50 hidden">Remove</button>
                            </div>
                        </div>
                    </div>
                    @error('items') <p class="text-rose-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('stock-transfers.index') }}" class="soma-btn-secondary">Cancel</a>
                    <button type="submit" class="soma-btn-primary">Complete transfer</button>
                </div>
            </form>
        </div>

    </div>
</div>

<template id="product-row-template">
    <div class="product-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
        <div class="sm:col-span-7">
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Product</label>
            <select name="items[__INDEX__][product_id]" class="soma-input product-select" required>
                <option value="">Select product</option>
            </select>
        </div>
        <div class="sm:col-span-3">
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Quantity</label>
            <input type="number" name="items[__INDEX__][quantity]" min="1" value="1" class="soma-input quantity-input" required>
        </div>
        <div class="sm:col-span-2">
            <button type="button" class="remove-row soma-btn-secondary w-full justify-center text-sm py-2 text-rose-600 border-rose-200 hover:bg-rose-50">Remove</button>
        </div>
    </div>
</template>

<script>
    let rowIndex = 1;
    const productRows = document.getElementById('product-rows');
    const fromOutlet = document.getElementById('from-outlet');
    const toOutlet = document.getElementById('to-outlet');
    const rowTemplate = document.getElementById('product-row-template').innerHTML;

    function updateRemoveButtons() {
        const rows = productRows.querySelectorAll('.product-row');
        rows.forEach((row, i) => {
            const btn = row.querySelector('.remove-row');
            btn.classList.toggle('hidden', rows.length === 1);
        });
    }

    function populateSelect(select, products) {
        const current = select.value;
        select.innerHTML = '<option value="">Select product</option>';
        products.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = `${p.name} (${p.sku}) — ${p.stock_quantity} in stock`;
            opt.dataset.stock = p.stock_quantity;
            select.appendChild(opt);
        });
        if (current) select.value = current;
    }

    async function loadProducts() {
        const outletId = fromOutlet?.value || document.querySelector('input[name="from_outlet_id"]')?.value;
        if (!outletId) return;

        const res = await fetch(`{{ route('stock-transfers.products') }}?outlet_id=${outletId}`);
        const products = await res.json();

        productRows.querySelectorAll('.product-select').forEach(select => populateSelect(select, products));

        // Update to-outlet options — exclude from outlet
        if (toOutlet) {
            Array.from(toOutlet.options).forEach(opt => {
                if (!opt.value) return;
                opt.hidden = opt.value === outletId;
                if (opt.value === outletId && toOutlet.value === outletId) {
                    toOutlet.value = '';
                }
            });
        }
    }

    function addRow() {
        const html = rowTemplate.replace(/__INDEX__/g, rowIndex++);
        productRows.insertAdjacentHTML('beforeend', html);
        const newSelect = productRows.lastElementChild.querySelector('.product-select');
        fetch(`{{ route('stock-transfers.products') }}?outlet_id=${fromOutlet?.value || document.querySelector('input[name="from_outlet_id"]').value}`)
            .then(r => r.json())
            .then(products => populateSelect(newSelect, products));
        updateRemoveButtons();
    }

    document.getElementById('add-row').addEventListener('click', addRow);

    productRows.addEventListener('click', e => {
        if (e.target.closest('.remove-row')) {
            e.target.closest('.product-row').remove();
            updateRemoveButtons();
        }
    });

    productRows.addEventListener('change', e => {
        if (e.target.classList.contains('product-select')) {
            const stock = e.target.selectedOptions[0]?.dataset.stock;
            const qtyInput = e.target.closest('.product-row').querySelector('.quantity-input');
            if (stock) qtyInput.max = stock;
        }
    });

    if (fromOutlet) {
        fromOutlet.addEventListener('change', loadProducts);
    }

    loadProducts();
    updateRemoveButtons();
</script>
@endsection
