@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-undo-alt text-blue-600 mr-2"></i>New Return / Exchange
        </h1>
        <p class="text-gray-600 mt-2">Search for the original sale to process a return or exchange</p>
    </div>

    <!-- Search Sale -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Find Original Sale</h3>
        <div class="flex gap-4">
            <div class="flex-1">
                <input type="text" id="searchSale" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter Invoice Number, Customer Name or Phone">
            </div>
            <button id="searchBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </div>
        <div id="searchResult" class="mt-4 hidden"></div>
    </div>

    <!-- Return Form -->
    <div id="returnForm" class="hidden">
        <form id="returnSubmitForm">
            @csrf
            <input type="hidden" id="original_sale_id" name="original_sale_id">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sale Details</h3>
                <div id="saleDetails" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Items to Return</h3>
                <div id="itemsList" class="space-y-2"></div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Return Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Return Type *</label>
                        <select name="return_type" id="returnType" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="return">Return Only</option>
                            <option value="exchange">Exchange</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Refund Method *</label>
                        <select name="refund_method" id="refundMethod" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="credit">Store Credit</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason *</label>
                        <select name="reason" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="defective">Defective Product</option>
                            <option value="wrong_item">Wrong Item Shipped</option>
                            <option value="customer_changed_mind">Customer Changed Mind</option>
                            <option value="damaged">Damaged During Delivery</option>
                            <option value="not_as_described">Product Not as Described</option>
                            <option value="size_issue">Size/Color Mismatch</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="returnNotes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Additional notes about the return..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Exchange Items -->
            <div id="exchangeSection" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Exchange Items</h3>
                <div class="flex gap-4 mb-4">
                    <div class="flex-1">
                        <select id="exchangeProductSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select product...</option>
                            @foreach($products ?? [] as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}">
                                    {{ $product->name }} - KES {{ number_format($product->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-24">
                        <input type="number" id="exchangeQuantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Qty" min="1" value="1">
                    </div>
                    <button type="button" id="addExchangeItem" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <div id="exchangeItemsList" class="space-y-2"></div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-500 mr-2">Refund Amount (KES):</span>
                        <input type="number" id="refundAmountInput" name="refund_amount" step="0.01" class="text-xl font-bold text-red-600 px-3 py-1 border border-gray-300 rounded-lg w-32 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500" value="0.00">
                    </div>
                    <div>
                        <button type="button" id="cancelReturn" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition mr-2">
                            Cancel
                        </button>
                        <button type="submit" id="processReturn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-check mr-2"></i>Process Return
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedItems = [];
    let exchangeItems = [];

    // Search
    document.getElementById('searchBtn').addEventListener('click', doSearch);
    document.getElementById('searchSale').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
    });

    function doSearch() {
        const q = document.getElementById('searchSale').value.trim();
        if (q.length < 2) {
            alert('Please enter at least 2 characters');
            return;
        }

        document.getElementById('searchBtn').disabled = true;
        document.getElementById('searchBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...';

        fetch(`{{ route('returns.search') }}?search=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(response => {
                document.getElementById('searchBtn').disabled = false;
                document.getElementById('searchBtn').innerHTML = '<i class="fas fa-search mr-2"></i>Search';

                if (response.success) {
                    displaySaleDetails(response);
                    document.getElementById('returnForm').classList.remove('hidden');
                    document.getElementById('searchResult').classList.add('hidden');
                } else {
                    document.getElementById('searchResult').innerHTML = `
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mt-4">
                            <i class="fas fa-exclamation-circle mr-2"></i>${response.message}
                        </div>
                    `;
                    document.getElementById('searchResult').classList.remove('hidden');
                    document.getElementById('returnForm').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                document.getElementById('searchBtn').disabled = false;
                document.getElementById('searchBtn').innerHTML = '<i class="fas fa-search mr-2"></i>Search';
                alert('Error searching for sale');
            });
    }

    function displaySaleDetails(response) {
        const sale = response.sale;
        document.getElementById('original_sale_id').value = sale.id;

        document.getElementById('saleDetails').innerHTML = `
            <div>
                <span class="text-sm text-gray-500">Invoice</span>
                <div class="font-bold text-gray-900">${sale.invoice_no}</div>
            </div>
            <div>
                <span class="text-sm text-gray-500">Customer</span>
                <div class="font-bold text-gray-900">${sale.customer ? sale.customer.name : 'Walk-in Customer'}</div>
            </div>
            <div>
                <span class="text-sm text-gray-500">Date</span>
                <div class="font-bold text-gray-900">${new Date(sale.sale_date).toLocaleDateString()}</div>
            </div>
            <div>
                <span class="text-sm text-gray-500">Total</span>
                <div class="font-bold text-gray-900">KES ${Number(sale.total).toFixed(2)}</div>
            </div>
        `;

        let itemsHtml = '';
        response.items.forEach(item => {
            itemsHtml += `
                <div class="flex items-center gap-4 p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                    <input type="checkbox" class="return-item-checkbox w-5 h-5 text-blue-600 rounded" 
                           value="${item.id}" ${item.already_returned ? 'disabled' : ''}
                           data-price="${item.total}">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${item.product_name}</div>
                        <div class="text-sm text-gray-500">Qty: ${item.quantity} × KES ${Number(item.price).toFixed(2)}</div>
                        ${item.already_returned ? '<span class="text-red-500 text-xs ml-2">(Already returned)</span>' : ''}
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-gray-900">KES ${Number(item.total).toFixed(2)}</div>
                    </div>
                </div>
            `;
        });

        document.getElementById('itemsList').innerHTML = itemsHtml;

        document.querySelectorAll('.return-item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateRefundAmount);
        });

        updateRefundAmount();
    }

    function updateRefundAmount() {
        let total = 0;
        // Add value of returned items
        document.querySelectorAll('.return-item-checkbox:checked').forEach(checkbox => {
            total += Number(checkbox.dataset.price);
        });

        // Subtract value of exchanged items
        if (document.getElementById('returnType').value === 'exchange') {
            exchangeItems.forEach(item => {
                total -= (item.price * item.quantity);
            });
        }

        document.getElementById('refundAmountInput').value = total.toFixed(2);
    }

    // Exchange toggle
    document.getElementById('returnType').addEventListener('change', function() {
        if (this.value === 'exchange') {
            document.getElementById('exchangeSection').classList.remove('hidden');
        } else {
            document.getElementById('exchangeSection').classList.add('hidden');
        }
        updateRefundAmount(); // Recalculate when type changes
    });

    // Add exchange item
    document.getElementById('addExchangeItem').addEventListener('click', function() {
        const select = document.getElementById('exchangeProductSelect');
        const productId = select.value;
        const quantity = parseInt(document.getElementById('exchangeQuantity').value) || 1;
        
        if (!productId) {
            alert('Please select a product');
            return;
        }

        const option = select.options[select.selectedIndex];
        const productName = option.text.split(' - ')[0];
        const price = parseFloat(option.dataset.price);

        // Add to exchange items
        exchangeItems.push({product_id: productId, product_name: productName, quantity: quantity, price: price});
        renderExchangeItems();
        
        select.value = '';
        document.getElementById('exchangeQuantity').value = 1;
    });

    function renderExchangeItems() {
        let html = '';
        exchangeItems.forEach((item, index) => {
            html += `
                <div class="flex items-center justify-between p-3 border border-purple-200 bg-purple-50 rounded-lg">
                    <div>
                        <span class="font-medium">${item.product_name}</span>
                        <span class="text-sm text-gray-500 ml-2">× ${item.quantity}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-semibold">KES ${(item.price * item.quantity).toFixed(2)}</span>
                        <button type="button" class="remove-exchange-item text-red-600 hover:text-red-800" data-index="${index}">
                            <i class="fas fa-times-circle"></i>
                        </button>
                        <input type="hidden" name="exchange_items[${index}][product_id]" value="${item.product_id}">
                        <input type="hidden" name="exchange_items[${index}][quantity]" value="${item.quantity}">
                    </div>
                </div>
            `;
        });
        document.getElementById('exchangeItemsList').innerHTML = html;
        updateRefundAmount(); // Recalculate when exchange items change
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-exchange-item')) {
            const index = e.target.closest('.remove-exchange-item').dataset.index;
            exchangeItems.splice(index, 1);
            renderExchangeItems();
        }
    });

    // Cancel
    document.getElementById('cancelReturn').addEventListener('click', function() {
        if (confirm('Cancel this return?')) {
            location.reload();
        }
    });

    // Submit
    document.getElementById('processReturn').addEventListener('click', function(e) {
        e.preventDefault();
        
        const selectedItems = [];
        document.querySelectorAll('.return-item-checkbox:checked').forEach(cb => {
            selectedItems.push(cb.value);
        });

        if (selectedItems.length === 0) {
            alert('Please select at least one item to return');
            return;
        }

        const formData = new FormData(document.getElementById('returnSubmitForm'));
        formData.append('items', JSON.stringify(selectedItems));
        formData.append('_token', '{{ csrf_token() }}');

        document.getElementById('processReturn').disabled = true;
        document.getElementById('processReturn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

        fetch('{{ route("returns.store") }}', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(response => {
            document.getElementById('processReturn').disabled = false;
            document.getElementById('processReturn').innerHTML = '<i class="fas fa-check mr-2"></i>Process Return';

            if (response.success) {
                alert(response.message);
                window.location.href = '{{ route("returns.index") }}';
            } else {
                alert(response.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('processReturn').disabled = false;
            document.getElementById('processReturn').innerHTML = '<i class="fas fa-check mr-2"></i>Process Return';
            alert('Failed to process return');
        });
    });
});
</script>
@endsection