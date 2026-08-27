@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-edit text-blue-600 mr-2"></i>Edit Quotation
            </h1>
            <p class="text-gray-600 mt-2">Editing: <span class="font-semibold">{{ $quotation->quotation_no }}</span></p>
        </div>
        <a href="{{ route('quotations.show', $quotation) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
        <ul class="text-sm text-red-800 list-disc list-inside">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form id="quotation-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quotation #</label>
                    <input type="text" value="{{ $quotation->quotation_no }}" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quotation Date *</label>
                    <input type="date" name="quotation_date" value="{{ $quotation->quotation_date->format('Y-m-d') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ $quotation->expiry_date ? $quotation->expiry_date->format('Y-m-d') : '' }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                <select name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Walk-in Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $quotation->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} - {{ $customer->phone ?? 'No phone' }}
                        </option>
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
                <input type="hidden" name="items" id="items-json" value="">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ $quotation->notes }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                    <textarea name="terms" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ $quotation->terms }}</textarea>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span id="subtotal" class="font-semibold">KES {{ number_format($quotation->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Discount:</span>
                        <span class="font-semibold">
                            <input type="number" name="discount" id="discount" step="0.01" value="{{ $quotation->discount }}" class="w-32 text-right border border-gray-300 rounded px-2 py-1">
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax (16%):</span>
                        <span id="tax" class="font-semibold">KES {{ number_format($quotation->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total:</span>
                        <span id="total" class="text-blue-600">KES {{ number_format($quotation->total, 2) }}</span>
                    </div>
                </div>
                <input type="hidden" name="subtotal" id="subtotal-input" value="{{ $quotation->subtotal }}">
                <input type="hidden" name="tax" id="tax-input" value="{{ $quotation->tax }}">
                <input type="hidden" name="total" id="total-input" value="{{ $quotation->total }}">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('quotations.show', $quotation) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" id="save-quotation" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Update Quotation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pre-load existing items
    let items = @json($quotation->items ?? []);

    $(document).ready(function() {
        renderItems();
        updateTotals();

        $('#add-product').click(function() {
            const productId = $('#product-select').val();
            const quantity = parseInt($('#product-qty').val()) || 1;

            if (!productId) { alert('Please select a product'); return; }

            const option = $('#product-select option:selected');
            const productName = option.text().split(' - ')[0];
            const price = parseFloat(option.attr('data-price')) || 0;
            if (!price || price <= 0) { alert('This product has no selling price set'); return; }

            const existing = items.find(item => item.product_id == productId);
            if (existing) { alert('Product already added. Update quantity in the list.'); return; }

            items.push({ product_id: parseInt(productId), product_name: productName, quantity: quantity, price: price });
            renderItems();
            updateTotals();
            $('#product-select').val('');
            $('#product-qty').val(1);
        });

        $(document).on('click', '.remove-item', function() {
            items.splice($(this).data('index'), 1);
            renderItems();
            updateTotals();
        });

        $(document).on('change', '.item-qty', function() {
            items[$(this).data('index')].quantity = parseInt($(this).val()) || 1;
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
                            <span class="text-sm text-gray-500 ml-2">KES ${parseFloat(item.price).toFixed(2)} each</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <input type="number" class="item-qty w-16 px-2 py-1 border border-gray-300 rounded"
                                   value="${item.quantity}" min="1" data-index="${index}">
                            <span class="font-semibold">KES ${total.toFixed(2)}</span>
                            <button type="button" class="remove-item text-red-600 hover:text-red-800" data-index="${index}">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            if (items.length === 0) {
                html = '<div class="text-center text-gray-500 py-4">No items. Select products above.</div>';
            }

            $('#items-list').html(html);
            $('#items-json').val(JSON.stringify(items));
        }

        function updateTotals() {
            let grossTotal = 0;
            items.forEach(item => { grossTotal += parseFloat(item.price) * item.quantity; });
            const discount = parseFloat($('#discount').val()) || 0;
            const total = Math.max(0, grossTotal - discount);
            const subtotal = total > 0 ? (total / 1.16) : 0;
            const tax = total > 0 ? (total - subtotal) : 0;

            $('#subtotal').text('KES ' + subtotal.toFixed(2));
            $('#tax').text('KES ' + tax.toFixed(2));
            $('#total').text('KES ' + total.toFixed(2));
            $('#subtotal-input').val(subtotal.toFixed(2));
            $('#tax-input').val(tax.toFixed(2));
            $('#total-input').val(total.toFixed(2));
            $('#items-json').val(JSON.stringify(items));
        }

        $('#discount').on('input', updateTotals);

        $('#save-quotation').click(function(e) {
            e.preventDefault();

            if (items.length === 0) { alert('Please add at least one product'); return; }

            const formData = new FormData(document.getElementById('quotation-form'));
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            formData.append('items', JSON.stringify(items));

            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

            $.ajax({
                url: '{{ route("quotations.update", $quotation) }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        window.location.href = '{{ route("quotations.show", $quotation) }}';
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error saving quotation');
                    $('#save-quotation').prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Update Quotation');
                }
            });
        });
    });
</script>
@endpush
