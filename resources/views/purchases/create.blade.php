@extends('layouts.app')

@section('content')
<div class="soma-page-inner space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <a href="{{ route('purchases.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center text-lg transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                Add New Purchase
            </h1>
            <p class="text-slate-500 text-sm mt-1">Create a purchase order from a supplier and receive inventory stock.</p>
        </div>
    </div>

    <!-- Main Purchase Form -->
    <form id="purchaseForm" method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Section 1: General Purchase Details -->
        <div class="soma-card p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i class="fas fa-file-signature text-blue-600"></i>
                Purchase Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Supplier Selection -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">Supplier <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="openSupplierModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                            <i class="fas fa-plus-circle"></i> Quick Add
                        </button>
                    </div>
                    <select id="supplier_id" name="supplier_id" required class="soma-input text-sm">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }} {{ $supplier->company_name ? '('.$supplier->company_name.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Purchase Date -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Purchase Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="soma-input text-sm">
                </div>

                <!-- Target Outlet -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Outlet Store <span class="text-rose-500">*</span></label>
                    <select name="outlet_id" required class="soma-input text-sm">
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}" {{ old('outlet_id', $defaultOutletId) == $outlet->id ? 'selected' : '' }}>
                                {{ $outlet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Reference PO Number -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Reference No.</label>
                    <input type="text" name="reference_no" value="{{ old('reference_no', $referenceNo) }}" placeholder="e.g. PO-20260721-0001" class="soma-input text-sm font-semibold">
                </div>

                <!-- Purchase Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Purchase Status <span class="text-rose-500">*</span></label>
                    <select name="status" required class="soma-input text-sm font-semibold">
                        <option value="received" {{ old('status', 'received') == 'received' ? 'selected' : '' }}>Received (Stock Incremented)</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="ordered" {{ old('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Payment Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Payment Status <span class="text-rose-500">*</span></label>
                    <select id="payment_status" name="payment_status" required class="soma-input text-sm font-semibold">
                        <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="due" {{ old('payment_status', 'due') == 'due' ? 'selected' : '' }}>Due</option>
                        <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Payment Method</label>
                    <select name="payment_method" class="soma-input text-sm">
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="credit">Supplier Credit</option>
                    </select>
                </div>

                <!-- Document Upload -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Attach Invoice / Document</label>
                    <input type="file" name="document" class="soma-input text-xs py-2">
                </div>
            </div>
        </div>

        <!-- Section 2: Products Search & Items Table -->
        <div class="soma-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-boxes-stacked text-blue-600"></i>
                    Select & Add Products
                </h3>
                <button type="button" onclick="openProductModal()" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg flex items-center gap-1.5 transition-colors">
                    <i class="fas fa-plus"></i> Quick Add New Product
                </button>
            </div>

            <!-- Live Product Search Bar -->
            <div class="relative mb-6">
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Search Products by Name or SKU</label>
                <div class="relative">
                    <i class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
                    <input type="text" id="productSearchInput" placeholder="Type product name or SKU to search and add to list..." autocomplete="off" class="soma-input pl-10 py-3 text-base shadow-sm">
                </div>
                <!-- Autocomplete Dropdown List -->
                <div id="productSearchResults" class="absolute left-0 right-0 top-full mt-1 bg-white rounded-xl shadow-xl border border-slate-200 z-50 hidden max-h-64 overflow-y-auto divide-y divide-slate-100">
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left border-collapse soma-table">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider">
                            <th class="py-3 px-4 font-bold">Product Name</th>
                            <th class="py-3 px-4 font-bold text-center w-28">Curr. Stock</th>
                            <th class="py-3 px-4 font-bold text-center w-32">Qty Purchased</th>
                            <th class="py-3 px-4 font-bold text-right w-36">Unit Cost (KES)</th>
                            <th class="py-3 px-4 font-bold text-right w-40">Subtotal (KES)</th>
                            <th class="py-3 px-4 font-bold text-center w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseItemsTableBody">
                        <tr id="emptyRow">
                            <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                <i class="fas fa-cart-flatbed text-2xl block mb-2"></i>
                                No products added yet. Use the search bar above to add products to this purchase order.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 3: Summary Totals & Notes -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Notes -->
            <div class="lg:col-span-2 soma-card p-6">
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Purchase Notes / Remarks</label>
                <textarea name="notes" rows="6" placeholder="Add optional notes, invoice instructions, or tracking details..." class="soma-input text-sm"></textarea>
            </div>

            <!-- Totals Card -->
            <div class="soma-card p-6 space-y-4 bg-slate-50/50">
                <h4 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-200 pb-2">Order Summary</h4>

                <div class="space-y-3 text-sm">
                    <!-- Subtotal -->
                    <div class="flex justify-between items-center text-slate-600 font-medium">
                        <span>Items Subtotal:</span>
                        <span id="displaySubtotal" class="font-bold text-slate-900">KES 0.00</span>
                        <input type="hidden" id="inputSubtotal" name="subtotal" value="0">
                    </div>

                    <!-- Tax -->
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-medium">Tax Amount (KES):</span>
                        <input type="number" step="0.01" min="0" id="inputTax" name="tax" value="0" oninput="calculateGrandTotals()" class="soma-input text-right w-32 py-1 text-sm">
                    </div>

                    <!-- Discount -->
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-medium">Discount (KES):</span>
                        <input type="number" step="0.01" min="0" id="inputDiscount" name="discount" value="0" oninput="calculateGrandTotals()" class="soma-input text-right w-32 py-1 text-sm">
                    </div>

                    <!-- Shipping -->
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-medium">Shipping Cost (KES):</span>
                        <input type="number" step="0.01" min="0" id="inputShipping" name="shipping_cost" value="0" oninput="calculateGrandTotals()" class="soma-input text-right w-32 py-1 text-sm">
                    </div>

                    <!-- Grand Total -->
                    <div class="border-t border-slate-200 pt-3 flex justify-between items-center text-base font-extrabold text-slate-900">
                        <span>Total Amount:</span>
                        <span id="displayTotal" class="text-lg text-blue-700">KES 0.00</span>
                        <input type="hidden" id="inputTotal" name="total_amount" value="0">
                    </div>

                    <!-- Amount Paid -->
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-slate-700 font-semibold">Amount Paid (KES):</span>
                        <input type="number" step="0.01" min="0" id="inputPaid" name="paid_amount" value="0" oninput="calculateGrandTotals()" class="soma-input text-right w-32 py-1 text-sm font-bold text-emerald-700">
                    </div>

                    <!-- Balance Due -->
                    <div class="flex justify-between items-center font-bold text-sm text-amber-700 pt-1">
                        <span>Balance Due:</span>
                        <span id="displayDue">KES 0.00</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200">
                    <button type="submit" class="soma-btn-primary w-full justify-center py-3 text-base shadow-lg shadow-blue-600/20">
                        <i class="fas fa-check-circle"></i>
                        <span>Save Purchase Order</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quick Add Supplier Modal -->
<div id="supplierModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-user-plus text-blue-600"></i>
                Quick Add Supplier
            </h3>
            <button type="button" onclick="closeSupplierModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="quickSupplierForm" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Supplier Name <span class="text-rose-500">*</span></label>
                <input type="text" id="sup_name" required placeholder="e.g. ABC Wholesalers" class="soma-input text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Company Name</label>
                <input type="text" id="sup_company" placeholder="e.g. ABC Trading Ltd" class="soma-input text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Phone Number</label>
                <input type="text" id="sup_phone" placeholder="e.g. +254 700 000 000" class="soma-input text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" id="sup_email" placeholder="e.g. supplier@example.com" class="soma-input text-sm">
            </div>

            <div class="pt-4 flex justify-end gap-2">
                <button type="button" onclick="closeSupplierModal()" class="soma-btn-secondary text-sm">Cancel</button>
                <button type="button" onclick="saveQuickSupplier()" class="soma-btn-primary text-sm">Save Supplier</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const productsCatalog = @json($products);
let purchaseItems = {}; // product_id => { id, name, sku, stock, quantity, unit_cost, subtotal }

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('productSearchInput');
    const searchResults = document.getElementById('productSearchResults');
    const paymentStatusSelect = document.getElementById('payment_status');

    // Live search input filtering
    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        if (!query) {
            searchResults.classList.add('hidden');
            return;
        }

        const filtered = productsCatalog.filter(p => 
            p.name.toLowerCase().includes(query) || 
            (p.sku && p.sku.toLowerCase().includes(query))
        );

        if (filtered.length === 0) {
            searchResults.innerHTML = `<div class="p-3 text-xs text-slate-400 text-center">No matching products found</div>`;
        } else {
            searchResults.innerHTML = filtered.map(p => `
                <div onclick="addProductToPurchase(${p.id})" class="p-3 hover:bg-blue-50 cursor-pointer flex items-center justify-between transition-colors">
                    <div>
                        <span class="font-bold text-slate-800 text-sm block">${p.name}</span>
                        <span class="text-xs text-slate-500">SKU: ${p.sku || 'N/A'} | Stock: ${p.stock_quantity}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold text-blue-600 block">Cost: KES ${parseFloat(p.cost || 0).toFixed(2)}</span>
                    </div>
                </div>
            `).join('');
        }
        searchResults.classList.remove('hidden');
    });

    // Hide search results on click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // Handle payment status auto-filling paid amount
    paymentStatusSelect.addEventListener('change', function() {
        const total = parseFloat(document.getElementById('inputTotal').value) || 0;
        const paidInput = document.getElementById('inputPaid');
        if (this.value === 'paid') {
            paidInput.value = total;
        } else if (this.value === 'due') {
            paidInput.value = 0;
        }
        calculateGrandTotals();
    });

    // Prevent submitting empty items table
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        if (Object.keys(purchaseItems).length === 0) {
            e.preventDefault();
            alert('Please add at least one product item to the purchase order.');
        }
    });
});

function addProductToPurchase(productId) {
    const product = productsCatalog.find(p => p.id === productId);
    if (!product) return;

    if (purchaseItems[productId]) {
        purchaseItems[productId].quantity += 1;
        purchaseItems[productId].subtotal = purchaseItems[productId].quantity * purchaseItems[productId].unit_cost;
    } else {
        const cost = parseFloat(product.cost) || parseFloat(product.price) || 0;
        purchaseItems[productId] = {
            id: product.id,
            name: product.name,
            sku: product.sku || 'N/A',
            stock: product.stock_quantity,
            quantity: 1,
            unit_cost: cost,
            subtotal: cost
        };
    }

    document.getElementById('productSearchInput').value = '';
    document.getElementById('productSearchResults').classList.add('hidden');
    renderItemsTable();
}

function removeProductItem(productId) {
    delete purchaseItems[productId];
    renderItemsTable();
}

function updateItemQuantity(productId, qty) {
    const newQty = parseInt(qty) || 1;
    if (purchaseItems[productId]) {
        purchaseItems[productId].quantity = newQty;
        purchaseItems[productId].subtotal = newQty * purchaseItems[productId].unit_cost;
        renderItemsTable(false);
    }
}

function updateItemCost(productId, cost) {
    const newCost = parseFloat(cost) || 0;
    if (purchaseItems[productId]) {
        purchaseItems[productId].unit_cost = newCost;
        purchaseItems[productId].subtotal = purchaseItems[productId].quantity * newCost;
        renderItemsTable(false);
    }
}

function renderItemsTable(rebuildRows = true) {
    const tbody = document.getElementById('purchaseItemsTableBody');
    const keys = Object.keys(purchaseItems);

    if (keys.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                    <i class="fas fa-cart-flatbed text-2xl block mb-2"></i>
                    No products added yet. Use the search bar above to add products to this purchase order.
                </td>
            </tr>
        `;
    } else if (rebuildRows) {
        let index = 0;
        tbody.innerHTML = keys.map(id => {
            const item = purchaseItems[id];
            const html = `
                <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                    <td class="py-3 px-4">
                        <span class="font-bold text-slate-800 text-sm block">${item.name}</span>
                        <span class="text-xs text-slate-400">SKU: ${item.sku}</span>
                        <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                    </td>
                    <td class="py-3 px-4 text-center font-semibold text-slate-600 text-sm">
                        ${item.stock}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <input type="number" min="1" value="${item.quantity}" oninput="updateItemQuantity(${item.id}, this.value)" name="items[${index}][quantity]" class="soma-input text-center w-20 py-1 text-sm font-bold">
                    </td>
                    <td class="py-3 px-4 text-right">
                        <input type="number" step="0.01" min="0" value="${item.unit_cost}" oninput="updateItemCost(${item.id}, this.value)" name="items[${index}][unit_cost]" class="soma-input text-right w-28 py-1 text-sm font-bold">
                    </td>
                    <td class="py-3 px-4 text-right font-bold text-slate-900 text-sm">
                        KES <span id="subtotal_span_${item.id}">${parseFloat(item.subtotal).toFixed(2)}</span>
                        <input type="hidden" id="subtotal_input_${item.id}" name="items[${index}][subtotal]" value="${item.subtotal}">
                    </td>
                    <td class="py-3 px-4 text-center">
                        <button type="button" onclick="removeProductItem(${item.id})" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;
            index++;
            return html;
        }).join('');
    } else {
        // Fast update without re-rendering focus inputs
        keys.forEach(id => {
            const item = purchaseItems[id];
            const span = document.getElementById(`subtotal_span_${id}`);
            const input = document.getElementById(`subtotal_input_${id}`);
            if (span) span.textContent = parseFloat(item.subtotal).toFixed(2);
            if (input) input.value = item.subtotal;
        });
    }

    calculateGrandTotals();
}

function calculateGrandTotals() {
    let itemsSubtotal = 0;
    Object.values(purchaseItems).forEach(item => {
        itemsSubtotal += parseFloat(item.subtotal) || 0;
    });

    const tax = parseFloat(document.getElementById('inputTax').value) || 0;
    const discount = parseFloat(document.getElementById('inputDiscount').value) || 0;
    const shipping = parseFloat(document.getElementById('inputShipping').value) || 0;

    const grandTotal = Math.max(0, itemsSubtotal + tax - discount + shipping);

    document.getElementById('displaySubtotal').textContent = `KES ${itemsSubtotal.toFixed(2)}`;
    document.getElementById('inputSubtotal').value = itemsSubtotal.toFixed(2);

    document.getElementById('displayTotal').textContent = `KES ${grandTotal.toFixed(2)}`;
    document.getElementById('inputTotal').value = grandTotal.toFixed(2);

    const paymentStatus = document.getElementById('payment_status').value;
    const paidInput = document.getElementById('inputPaid');

    if (paymentStatus === 'paid') {
        paidInput.value = grandTotal.toFixed(2);
    }

    const paid = parseFloat(paidInput.value) || 0;
    const due = Math.max(0, grandTotal - paid);

    document.getElementById('displayDue').textContent = `KES ${due.toFixed(2)}`;
}

// Modal Supplier Functions
function openSupplierModal() {
    document.getElementById('supplierModal').classList.remove('hidden');
}

function closeSupplierModal() {
    document.getElementById('supplierModal').classList.add('hidden');
}

function saveQuickSupplier() {
    const name = document.getElementById('sup_name').value.trim();
    if (!name) {
        alert('Supplier Name is required.');
        return;
    }

    const data = {
        name: name,
        company_name: document.getElementById('sup_company').value.trim(),
        phone: document.getElementById('sup_phone').value.trim(),
        email: document.getElementById('sup_email').value.trim(),
        _token: '{{ csrf_token() }}'
    };

    fetch('{{ route("purchases.suppliers.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            const select = document.getElementById('supplier_id');
            const opt = document.createElement('option');
            opt.value = res.supplier.id;
            opt.textContent = res.supplier.name + (res.supplier.company_name ? ` (${res.supplier.company_name})` : '');
            opt.selected = true;
            select.appendChild(opt);

            closeSupplierModal();
            document.getElementById('quickSupplierForm').reset();
        } else {
            alert(res.message || 'Error saving supplier');
        }
    })
    .catch(err => {
        alert('Failed to save supplier: ' + err.message);
    });
}

// Quick Add Product Functions
function openProductModal() {
    document.getElementById('productModal').classList.remove('hidden');
}

function closeProductModal() {
    document.getElementById('productModal').classList.add('hidden');
}

function saveQuickProduct() {
    const name = document.getElementById('prd_name').value.trim();
    const cost = parseFloat(document.getElementById('prd_cost').value) || 0;
    const price = parseFloat(document.getElementById('prd_price').value) || 0;
    const sku = document.getElementById('prd_sku').value.trim();

    if (!name) {
        alert('Product Name is required.');
        return;
    }

    const data = {
        name: name,
        cost: cost,
        price: price,
        sku: sku,
        _token: '{{ csrf_token() }}'
    };

    fetch('{{ route("purchases.products.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            const p = res.product;
            productsCatalog.push(p);
            addProductToPurchase(p.id);
            closeProductModal();
            document.getElementById('quickProductForm').reset();
        } else {
            alert(res.message || 'Error creating product');
        }
    })
    .catch(err => {
        alert('Failed to create product: ' + err.message);
    });
}
</script>

<!-- Quick Add Product Modal -->
<div id="productModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-box-open text-blue-600"></i>
                Quick Add New Product
            </h3>
            <button type="button" onclick="closeProductModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="quickProductForm" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" id="prd_name" required placeholder="e.g. Wireless Keyboard" class="soma-input text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">SKU / Code (Optional)</label>
                <input type="text" id="prd_sku" placeholder="e.g. KB-100" class="soma-input text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Purchase Cost (KES) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" id="prd_cost" required placeholder="0.00" class="soma-input text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Selling Price (KES) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" id="prd_price" required placeholder="0.00" class="soma-input text-sm">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-2">
                <button type="button" onclick="closeProductModal()" class="soma-btn-secondary text-sm">Cancel</button>
                <button type="button" onclick="saveQuickProduct()" class="soma-btn-primary text-sm">Create & Add Product</button>
            </div>
        </form>
    </div>
</div>
@endpush
@endsection
