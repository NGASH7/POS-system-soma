@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-file-invoice text-blue-600 mr-2"></i>New Quotation
            </h1>
            <p class="text-gray-500 text-sm mt-1">Fill in details and add products, then save and print.</p>
        </div>
        <a href="{{ route('quotations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Alert area -->
    <div id="alert-box" class="hidden mb-4 p-4 rounded-lg text-sm font-medium"></div>

    <form id="quotation-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT: Main Form -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 border-b pb-2">Quotation Info</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Quotation #</label>
                            <input type="text" name="quotation_no" value="{{ $quotationNo }}" readonly
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Date *</label>
                            <input type="date" name="quotation_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Valid Until</label>
                            <input type="date" name="expiry_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Customer -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 border-b pb-2">Customer</h2>
                    <select name="customer_id" id="customer-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                    data-phone="{{ $customer->phone }}"
                                    data-email="{{ $customer->email }}"
                                    data-address="{{ $customer->address ?? '' }}">
                                {{ $customer->name }}{{ $customer->phone ? ' — '.$customer->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Customer preview -->
                    <div id="customer-preview" class="hidden mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-800 space-y-0.5">
                        <div id="cust-name" class="font-semibold"></div>
                        <div id="cust-phone" class="text-xs"></div>
                        <div id="cust-email" class="text-xs"></div>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 border-b pb-2">
                        Products <span id="items-count" class="ml-2 bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-semibold">0</span>
                    </h2>

                    <!-- Add product row -->
                    <div class="flex gap-3 mb-4 p-3 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="flex-1">
                            <select id="product-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">— Select product —</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            data-price="{{ $product->price }}"
                                            data-stock="{{ $product->stock_quantity }}"
                                            data-sku="{{ $product->sku }}">
                                        {{ $product->name }} ({{ $product->sku }}) — KES {{ number_format($product->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-20">
                            <input type="number" id="product-qty" placeholder="Qty"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-center" min="1" value="1">
                        </div>
                        <div class="w-36 flex items-center justify-end text-sm text-gray-600 pr-1">
                            <span id="product-price-preview" class="font-medium text-gray-900">—</span>
                        </div>
                        <button type="button" id="add-product"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap">
                            <i class="fas fa-plus mr-1"></i> Add
                        </button>
                    </div>

                    <!-- Items table -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-2 text-left">#</th>
                                    <th class="px-3 py-2 text-left">Description</th>
                                    <th class="px-3 py-2 text-center w-20">Qty</th>
                                    <th class="px-3 py-2 text-right w-28">Unit Price</th>
                                    <th class="px-3 py-2 text-right w-28">Amount</th>
                                    <th class="px-3 py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody" class="divide-y divide-gray-100">
                                <tr id="empty-row">
                                    <td colspan="6" class="px-3 py-8 text-center text-gray-400 text-sm">
                                        <i class="fas fa-box-open text-2xl block mb-2"></i>
                                        No products added yet. Use the selector above.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="items" id="items-json" value="[]">
                </div>

                <!-- Notes & Terms -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 border-b pb-2">Notes & Terms</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Notes to Customer</label>
                            <textarea name="notes" rows="3" placeholder="e.g. Delivery within 3 days..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Terms & Conditions</label>
                            <textarea name="terms" rows="3" placeholder="e.g. Payment due within 7 days..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Totals & Actions -->
            <div class="space-y-5">
                <!-- Totals -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-4">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 border-b pb-2">Summary</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Subtotal</span>
                            <span id="disp-subtotal" class="font-semibold text-gray-900">KES 0.00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Discount (KES)</span>
                            <input type="number" name="discount" id="discount" step="0.01" value="0" min="0"
                                   class="w-28 text-right border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tax (16% VAT)</span>
                            <span id="disp-tax" class="font-semibold text-gray-900">KES 0.00</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t-2 border-gray-800">
                            <span class="text-base font-bold text-gray-900">TOTAL</span>
                            <span id="disp-total" class="text-xl font-extrabold text-blue-700">KES 0.00</span>
                        </div>
                    </div>

                    <input type="hidden" name="subtotal" id="subtotal-input" value="0">
                    <input type="hidden" name="tax"      id="tax-input"      value="0">
                    <input type="hidden" name="total"    id="total-input"    value="0">

                    <div class="mt-6 space-y-3">
                        <button type="button" id="save-quotation"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-semibold text-sm transition">
                            <i class="fas fa-save mr-2"></i>Save Quotation
                        </button>
                        <a href="{{ route('quotations.index') }}"
                           class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 py-2.5 rounded-lg font-medium text-sm transition">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Items quick summary -->
                <div id="items-summary" class="hidden bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800">
                    <div class="font-semibold mb-2">Items added:</div>
                    <ul id="summary-list" class="space-y-1 list-disc list-inside text-xs"></ul>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let items = [];

$(document).ready(function () {

    // Show customer preview
    $('#customer-select').on('change', function () {
        const opt = $(this).find(':selected');
        const name = opt.text().split('—')[0].trim();
        if (!$(this).val()) {
            $('#customer-preview').addClass('hidden');
            return;
        }
        $('#cust-name').text(name);
        $('#cust-phone').text(opt.data('phone') ? 'Phone: ' + opt.data('phone') : '');
        $('#cust-email').text(opt.data('email') ? 'Email: ' + opt.data('email') : '');
        $('#customer-preview').removeClass('hidden');
    });

    function selectedProductPrice() {
        const opt = $('#product-select option:selected');
        if (!opt.val()) return 0;
        return parseFloat(opt.attr('data-price')) || 0;
    }

    function updatePricePreview() {
        const price = selectedProductPrice();
        $('#product-price-preview').text(price > 0 ? 'KES ' + price.toFixed(2) : '—');
    }

    $('#product-select').on('change', updatePricePreview);

    function addSelectedProduct() {
        const productId = $('#product-select').val();
        const qty       = parseInt($('#product-qty').val(), 10) || 1;

        if (!productId) { showAlert('Please select a product.', 'error'); return; }

        const opt   = $('#product-select option:selected');
        const name  = opt.text().split('(')[0].trim();
        const price = selectedProductPrice();

        if (!price || price <= 0) { showAlert('This product has no selling price set.', 'error'); return; }
        if (qty < 1) { showAlert('Quantity must be at least 1.', 'error'); return; }

        const existing = items.findIndex(i => i.product_id == productId);
        if (existing > -1) {
            items[existing].quantity += qty;
        } else {
            items.push({ product_id: parseInt(productId, 10), product_name: name, quantity: qty, price: price });
        }

        renderItems();
        updateTotals();
        $('#product-select').val('');
        $('#product-price-preview').text('—');
        $('#product-qty').val(1);
    }

    $('#add-product').on('click', addSelectedProduct);
    $('#product-qty').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); addSelectedProduct(); }
    });

    // Remove item
    $(document).on('click', '.remove-item', function () {
        items.splice(parseInt($(this).data('index')), 1);
        renderItems();
        updateTotals();
    });

    // Inline qty change
    $(document).on('input', '.item-qty', function () {
        const idx = parseInt($(this).data('index'));
        items[idx].quantity = parseInt($(this).val()) || 1;
        const lineTotal = items[idx].price * items[idx].quantity;
        $(this).closest('tr').find('.line-total').text('KES ' + lineTotal.toFixed(2));
        updateTotals();
        syncItemsJson();
    });

    $('#discount').on('input', updateTotals);

    function renderItems() {
        const tbody = $('#items-tbody');
        if (items.length === 0) {
            tbody.html(`<tr id="empty-row"><td colspan="6" class="px-3 py-8 text-center text-gray-400 text-sm">
                <i class="fas fa-box-open text-2xl block mb-2"></i>No products added yet.
            </td></tr>`);
            $('#items-count').text('0');
            $('#items-summary').addClass('hidden');
            return;
        }

        let rows = '';
        let summaryItems = '';
        items.forEach((item, idx) => {
            const lineTotal = item.price * item.quantity;
            rows += `
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 text-gray-400 text-xs">${idx + 1}</td>
                <td class="px-3 py-2 font-medium text-gray-900">${item.product_name}</td>
                <td class="px-3 py-2 text-center">
                    <input type="number" class="item-qty w-16 px-1 py-0.5 border border-gray-300 rounded text-center text-sm"
                           value="${item.quantity}" min="1" data-index="${idx}">
                </td>
                <td class="px-3 py-2 text-right text-gray-700">KES ${parseFloat(item.price).toFixed(2)}</td>
                <td class="px-3 py-2 text-right font-semibold line-total">KES ${lineTotal.toFixed(2)}</td>
                <td class="px-3 py-2 text-center">
                    <button type="button" class="remove-item text-red-400 hover:text-red-600" data-index="${idx}" title="Remove">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </td>
            </tr>`;
            summaryItems += `<li>${item.product_name} × ${item.quantity}</li>`;
        });

        tbody.html(rows);
        $('#items-count').text(items.length);
        $('#summary-list').html(summaryItems);
        $('#items-summary').removeClass('hidden');
        syncItemsJson();
    }

    function syncItemsJson() {
        $('#items-json').val(JSON.stringify(items));
    }

    function updateTotals() {
        let grossTotal = 0;
        items.forEach(i => { grossTotal += i.price * i.quantity; });
        const discount = parseFloat($('#discount').val()) || 0;
        const total = Math.max(0, grossTotal - discount);
        const subtotal = total > 0 ? (total / 1.16) : 0;
        const tax = total > 0 ? (total - subtotal) : 0;

        $('#disp-subtotal').text('KES ' + subtotal.toFixed(2));
        $('#disp-tax').text('KES ' + tax.toFixed(2));
        $('#disp-total').text('KES ' + total.toFixed(2));

        $('#subtotal-input').val(subtotal.toFixed(2));
        $('#tax-input').val(tax.toFixed(2));
        $('#total-input').val(total.toFixed(2));
    }

    function showAlert(msg, type) {
        const box = $('#alert-box');
        box.removeClass('hidden bg-green-100 text-green-800 bg-red-100 text-red-800');
        if (type === 'error') box.addClass('bg-red-100 text-red-800');
        else box.addClass('bg-green-100 text-green-800');
        box.text(msg).removeClass('hidden');
        setTimeout(() => box.addClass('hidden'), 4000);
    }

    // Save
    $('#save-quotation').on('click', function () {
        if (items.length === 0) { showAlert('Please add at least one product.', 'error'); return; }

        syncItemsJson();
        const formData = new FormData(document.getElementById('quotation-form'));
        formData.append('_token', '{{ csrf_token() }}');
        formData.set('items', JSON.stringify(items));

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

        $.ajax({
            url: '{{ route("quotations.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    showAlert('Quotation saved! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '/quotations/' + res.quotation_id;
                    }, 800);
                }
            },
            error: function (xhr) {
                showAlert(xhr.responseJSON?.message || 'Error saving quotation.', 'error');
                $('#save-quotation').prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Save Quotation');
            }
        });
    });
});
</script>
@endpush