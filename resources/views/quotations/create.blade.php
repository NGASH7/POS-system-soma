@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-file-invoice text-blue-600 mr-2"></i>New Quotation
        </h1>
        <p class="text-gray-600 mt-2">Create a new quotation for a customer</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form id="quotation-form">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quotation #</label>
                    <input type="text" name="quotation_no" value="{{ $quotationNo }}" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quotation Date *</label>
                    <input type="date" name="quotation_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                <select name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone ?? 'No phone' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Products</label>
                <div class="flex gap-4 mb-4">
                    <div class="flex-1">
                        <select id="product-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Select product...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}">
                                    {{ $product->name }} - KES {{ number_format($product->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-24">
                        <input type="number" id="product-qty" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Qty" min="1" value="1">
                    </div>
                    <button type="button" id="add-product" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <div id="items-list" class="space-y-2"></div>
                <input type="hidden" name="items" id="items-json" value="[]">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                    <textarea name="terms" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Payment terms, delivery terms, etc."></textarea>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span id="subtotal" class="font-semibold">KES 0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount:</span>
                        <span class="font-semibold">
                            <input type="number" name="discount" id="discount" step="0.01" value="0" class="w-32 text-right border border-gray-300 rounded px-2 py-1">
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax (16%):</span>
                        <span id="tax" class="font-semibold">KES 0.00</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total:</span>
                        <span id="total" class="text-blue-600">KES 0.00</span>
                    </div>
                </div>
                <input type="hidden" name="subtotal" id="subtotal-input" value="0">
                <input type="hidden" name="tax" id="tax-input" value="0">
                <input type="hidden" name="total" id="total-input" value="0">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('quotations.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" id="save-quotation" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Save Quotation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let items = [];
    let productData = @json($products);

    $(document).ready(function() {
        // Add product to list
        $('#add-product').click(function() {
            const productId = $('#product-select').val();
            const quantity = parseInt($('#product-qty').val()) || 1;

            if (!productId) {
                alert('Please select a product');
                return;
            }

            const option = $('#product-select option:selected');
            const productName = option.text().split(' - ')[0];
            const price = parseFloat(option.data('price'));
            const stock = parseInt(option.data('stock'));

            if (quantity > stock) {
                alert(`Only ${stock} items available in stock`);
                return;
            }

            // Check if product already added
            const existing = items.find(item => item.product_id == productId);
            if (existing) {
                alert('Product already added. Update quantity in the list.');
                return;
            }

            items.push({
                product_id: parseInt(productId),
                product_name: productName,
                quantity: quantity,
                price: price
            });

            renderItems();
            updateTotals();
            $('#product-select').val('');
            $('#product-qty').val(1);
        });

        // Remove item
        $(document).on('click', '.remove-item', function() {
            const index = $(this).data('index');
            items.splice(index, 1);
            renderItems();
            updateTotals();
        });

        // Update quantity
        $(document).on('change', '.item-qty', function() {
            const index = $(this).data('index');
            items[index].quantity = parseInt($(this).val()) || 1;
            renderItems();
            updateTotals();
        });

        function renderItems() {
            let html = '';
            items.forEach((item, index) => {
                const total = item.price * item.quantity;
                html += `
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <span class="font-medium">${item.product_name}</span>
                            <span class="text-sm text-gray-500 ml-2">KES ${item.price.toFixed(2)} each</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <input type="number" class="item-qty w-16 px-2 py-1 border border-gray-300 rounded" 
                                   value="${item.quantity}" min="1" data-index="${index}">
                            <span class="font-semibold">KES ${total.toFixed(2)}</span>
                            <button type="button" class="remove-item text-red-600 hover:text-red-800" data-index="${index}">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                        <input type="hidden" name="items[${index}][product_name]" value="${item.product_name}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        <input type="hidden" name="items[${index}][price]" value="${item.price}">
                    </div>
                `;
            });

            if (items.length === 0) {
                html = '<div class="text-center text-gray-500 py-4">No items added yet. Select products above.</div>';
            }

            $('#items-list').html(html);
            $('#items-json').val(JSON.stringify(items));
        }

        function updateTotals() {
            let subtotal = 0;
            items.forEach(item => {
                subtotal += item.price * item.quantity;
            });

            const discount = parseFloat($('#discount').val()) || 0;
            const tax = subtotal * 0.16;
            const total = subtotal - discount + tax;

            $('#subtotal').text('KES ' + subtotal.toFixed(2));
            $('#tax').text('KES ' + tax.toFixed(2));
            $('#total').text('KES ' + total.toFixed(2));

            $('#subtotal-input').val(subtotal);
            $('#tax-input').val(tax);
            $('#total-input').val(total);
        }

        $('#discount').on('input', updateTotals);

        // Submit form
        $('#save-quotation').click(function(e) {
            e.preventDefault();

            if (items.length === 0) {
                alert('Please add at least one product');
                return;
            }

            const formData = new FormData(document.getElementById('quotation-form'));
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('items', JSON.stringify(items));

            $('#save-quotation').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

            $.ajax({
                url: '{{ route("quotations.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        window.location.href = '{{ route("quotations.index") }}';
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error saving quotation');
                    $('#save-quotation').prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Save Quotation');
                }
            });
        });
    });
</script>
@endsection