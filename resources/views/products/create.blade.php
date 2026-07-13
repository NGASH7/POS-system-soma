@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-2xl">

        <div class="mb-6">
            <a href="{{ route('products.index') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                <i class="fas fa-arrow-left text-xs"></i> Back to products
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Add Product</h1>
            <p class="text-sm text-slate-500 mt-1">Create a new product or add stock to an existing one</p>
        </div>

        <div class="flex gap-2 mb-6 p-1 bg-slate-100 rounded-xl">
            <button type="button" id="mode-new" class="mode-tab flex-1 py-2.5 text-sm font-semibold rounded-lg transition bg-white text-slate-900 shadow-sm">
                New product
            </button>
            <button type="button" id="mode-existing" class="mode-tab flex-1 py-2.5 text-sm font-semibold rounded-lg transition text-slate-500 hover:text-slate-700">
                Add to existing
            </button>
        </div>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" id="product-form">
            @csrf
            <input type="hidden" name="existing_product_id" id="existing_product_id" value="">

            {{-- Existing product mode --}}
            <div id="existing-panel" class="hidden space-y-4">
                <div class="soma-card p-5">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Search product</label>
                    <div class="flex gap-2">
                        <input type="text" id="search-product" class="soma-input flex-1" placeholder="Name, SKU, or barcode...">
                        <button type="button" id="search-btn" class="soma-btn-secondary">Search</button>
                    </div>
                    <div id="search-results" class="mt-3 space-y-2 hidden"></div>
                </div>

                <div id="selected-product-card" class="soma-card p-5 hidden">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs text-slate-400 mb-1">Selected product</p>
                            <p id="selected-name" class="font-semibold text-slate-900"></p>
                            <p id="selected-meta" class="text-sm text-slate-500 mt-0.5"></p>
                        </div>
                        <button type="button" id="clear-selection" class="text-xs text-slate-400 hover:text-rose-600">Change</button>
                    </div>
                    <div class="mt-4 p-3 bg-slate-50 rounded-lg flex justify-between text-sm">
                        <span class="text-slate-500">Current stock</span>
                        <span id="current-stock" class="font-bold text-slate-900">0</span>
                    </div>
                </div>

                <div id="add-stock-fields" class="soma-card p-5 hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Quantity to add <span class="text-rose-500">*</span></label>
                            <input type="number" name="stock_quantity" id="add_quantity" min="1" value="{{ old('stock_quantity', 1) }}" class="soma-input">
                            <p class="text-xs text-slate-400 mt-1">This amount is added to current stock</p>
                            @error('stock_quantity') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">New stock total</label>
                            <div id="new-stock-preview" class="soma-input bg-emerald-50 text-emerald-700 font-bold border-emerald-200">—</div>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Low stock threshold</label>
                            <input type="number" name="low_stock_threshold" id="existing_low_stock" min="0" class="soma-input">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-slate-100">
                        <a href="{{ route('products.index') }}" class="soma-btn-secondary">Cancel</a>
                        <button type="submit" class="soma-btn-primary">Add stock</button>
                    </div>
                </div>
            </div>

            {{-- New product mode --}}
            <div id="new-panel" class="soma-card p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Product name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="field_name" value="{{ old('name') }}" class="soma-input new-required">
                        @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">SKU <span class="text-rose-500">*</span></label>
                        <input type="text" name="sku" id="field_sku" value="{{ old('sku') }}" class="soma-input new-required">
                        @error('sku') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Barcode</label>
                        <input type="text" name="barcode" id="field_barcode" value="{{ old('barcode') }}" class="soma-input">
                        @error('barcode') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Selling price <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></span>
                            <input type="number" name="price" id="field_price" step="0.01" value="{{ old('price') }}" class="soma-input pl-12 new-required">
                        </div>
                        @error('price') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Cost price <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></span>
                            <input type="number" name="cost" id="field_cost" step="0.01" value="{{ old('cost') }}" class="soma-input pl-12 new-required">
                        </div>
                        @error('cost') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Initial stock <span class="text-rose-500">*</span></label>
                        <input type="number" name="stock_quantity" id="field_stock" min="0" value="{{ old('stock_quantity', 0) }}" class="soma-input new-required">
                        @error('stock_quantity') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Low stock threshold <span class="text-rose-500">*</span></label>
                        <input type="number" name="low_stock_threshold" id="field_low_stock" min="0" value="{{ old('low_stock_threshold', 5) }}" class="soma-input new-required">
                        @error('low_stock_threshold') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Category</label>
                        <select name="category_id" id="field_category" class="soma-input">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer pb-2">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600">
                            <span class="text-sm text-slate-700">Active</span>
                        </label>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Image</label>
                        <input type="file" name="image" accept="image/*" class="soma-input">
                        @error('image') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('products.index') }}" class="soma-btn-secondary">Cancel</a>
                    <button type="submit" class="soma-btn-primary">Create product</button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    const modeNew = document.getElementById('mode-new');
    const modeExisting = document.getElementById('mode-existing');
    const newPanel = document.getElementById('new-panel');
    const existingPanel = document.getElementById('existing-panel');
    const existingIdInput = document.getElementById('existing_product_id');
    const addQtyInput = document.getElementById('add_quantity');
    const fieldStock = document.getElementById('field_stock');

    let currentStock = 0;
    let selectedProduct = null;

    function setMode(mode) {
        const isExisting = mode === 'existing';
        newPanel.classList.toggle('hidden', isExisting);
        existingPanel.classList.toggle('hidden', !isExisting);

        modeNew.classList.toggle('bg-white', !isExisting);
        modeNew.classList.toggle('shadow-sm', !isExisting);
        modeNew.classList.toggle('text-slate-900', !isExisting);
        modeNew.classList.toggle('text-slate-500', isExisting);

        modeExisting.classList.toggle('bg-white', isExisting);
        modeExisting.classList.toggle('shadow-sm', isExisting);
        modeExisting.classList.toggle('text-slate-900', isExisting);
        modeExisting.classList.toggle('text-slate-500', !isExisting);

        document.querySelectorAll('.new-required').forEach(el => {
            if (isExisting) {
                el.removeAttribute('required');
                el.disabled = true;
            } else {
                el.setAttribute('required', 'required');
                el.disabled = false;
            }
        });

        fieldStock.disabled = isExisting;
        if (addQtyInput) {
            addQtyInput.disabled = !isExisting;
            if (isExisting) {
                addQtyInput.setAttribute('required', 'required');
            } else {
                addQtyInput.removeAttribute('required');
                clearSelection();
            }
        }
    }

    function updateStockPreview() {
        const add = parseInt(addQtyInput?.value) || 0;
        const preview = document.getElementById('new-stock-preview');
        if (selectedProduct && preview) {
            preview.textContent = currentStock + add;
        }
    }

    function clearSelection() {
        selectedProduct = null;
        currentStock = 0;
        existingIdInput.value = '';
        document.getElementById('selected-product-card').classList.add('hidden');
        document.getElementById('add-stock-fields').classList.add('hidden');
        document.getElementById('search-results').classList.add('hidden');
        document.getElementById('search-product').value = '';
    }

    function selectProduct(product) {
        selectedProduct = product;
        currentStock = parseInt(product.stock_quantity) || 0;
        existingIdInput.value = product.id;

        document.getElementById('selected-name').textContent = product.name;
        document.getElementById('selected-meta').textContent = `SKU: ${product.sku} · KES ${Number(product.price).toFixed(2)}`;
        document.getElementById('current-stock').textContent = currentStock;
        document.getElementById('existing_low_stock').value = product.low_stock_threshold ?? 5;

        document.getElementById('selected-product-card').classList.remove('hidden');
        document.getElementById('add-stock-fields').classList.remove('hidden');
        document.getElementById('search-results').classList.add('hidden');
        addQtyInput.value = 1;
        updateStockPreview();
    }

    modeNew.addEventListener('click', () => setMode('new'));
    modeExisting.addEventListener('click', () => setMode('existing'));

    document.getElementById('clear-selection').addEventListener('click', clearSelection);
    addQtyInput?.addEventListener('input', updateStockPreview);

    document.getElementById('search-btn').addEventListener('click', async () => {
        const q = document.getElementById('search-product').value.trim();
        if (q.length < 2) {
            alert('Enter at least 2 characters to search');
            return;
        }

        const res = await fetch(`{{ route('products.search') }}?q=${encodeURIComponent(q)}`);
        const products = await res.json();
        const container = document.getElementById('search-results');

        if (!products.length) {
            container.innerHTML = '<p class="text-sm text-slate-400 text-center py-3">No products found</p>';
        } else {
            container.innerHTML = products.map(p => `
                <button type="button" class="w-full text-left p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition search-result" data-id="${p.id}">
                    <div class="font-medium text-slate-900">${p.name}</div>
                    <div class="text-xs text-slate-500">SKU: ${p.sku} · Stock: ${p.stock_quantity}</div>
                </button>
            `).join('');
        }
        container.classList.remove('hidden');
    });

    document.getElementById('search-product').addEventListener('keypress', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('search-btn').click();
        }
    });

    document.getElementById('search-results').addEventListener('click', async e => {
        const btn = e.target.closest('.search-result');
        if (!btn) return;
        const res = await fetch(`/products/get/${btn.dataset.id}`);
        const product = await res.json();
        selectProduct(product);
    });

    document.getElementById('product-form').addEventListener('submit', e => {
        if (!existingPanel.classList.contains('hidden')) {
            if (!selectedProduct) {
                e.preventDefault();
                alert('Please search and select a product first');
                return;
            }
            const qty = parseInt(addQtyInput.value) || 0;
            if (qty < 1) {
                e.preventDefault();
                alert('Enter at least 1 to add to stock');
            }
        }
    });

    @if(old('existing_product_id'))
    setMode('existing');
    fetch(`{{ route('products.get', old('existing_product_id')) }}`)
        .then(r => r.json())
        .then(selectProduct);
    @else
    setMode('new');
    @endif
</script>
@endsection
