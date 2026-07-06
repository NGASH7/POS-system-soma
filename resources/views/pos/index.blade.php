@extends('layouts.app')

@section('title', 'New Sale')

@section('content')
    @php
        $invoiceNumber = 'TRX-' . str_pad((\App\Models\Sale::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
        $quickCash = [1000, 2000, 5000, 10000];
    @endphp

    <div class="soma-page">
        <div class="soma-page-inner">

        {{-- POS Top Bar --}}
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900">New Sale</h1>
                <p class="text-xs text-slate-500">Terminal TERM-01</p>
            </div>
            <div class="relative ml-4 hidden flex-1 md:block" style="max-width:480px;">
                <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="search-product"
                    class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-11 pr-14 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                    placeholder="Scan barcode or search product (SKU, name)" autofocus>
                <i class="fas fa-barcode absolute right-4 top-1/2 -translate-y-1/2 text-lg text-slate-500"></i>
                <div id="search-results"
                    class="absolute left-0 right-0 top-12 z-40 hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                </div>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <button id="hold-ticket-btn"
                    class="hidden h-10 rounded-xl border border-blue-600 px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-50 md:inline-flex md:items-center">Hold Ticket</button>
                <span class="hidden items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-800 md:inline-flex">
                    #{{ $invoiceNumber }}
                </span>
            </div>
        </div>


        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[1.05fr_1.2fr] 2xl:grid-cols-[1.05fr_1.25fr_1.05fr]">
                <section class="min-h-[620px] rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-extrabold">Products</h2>
                            <p class="mt-1 text-xs text-slate-500">{{ $products->total() }} sellable items</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-blue-500 text-blue-600"><i
                                    class="fas fa-border-all"></i></button>
                            <button
                                class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500"><i
                                    class="fas fa-list"></i></button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('pos.index') }}" class="mb-4 flex flex-wrap items-center gap-2 text-sm">
                        <div class="flex items-center">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="h-10 w-48 rounded-l-xl border border-slate-200 px-3 text-sm outline-none focus:border-blue-500">
                            <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-r-xl border border-l-0 border-slate-200 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-blue-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <a href="{{ route('pos.index') }}" class="rounded-lg {{ !request('favorites') && !request('category_id') && !request('search') ? 'bg-slate-950 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }} px-4 py-2 font-bold transition">All</a>
                        
                        <label class="cursor-pointer rounded-xl px-4 py-2 font-semibold transition {{ request('favorites') ? 'bg-rose-100 text-rose-600' : 'text-slate-600 hover:bg-slate-100' }}">
                            <input type="checkbox" name="favorites" value="1" class="hidden" onchange="this.form.submit()" {{ request('favorites') ? 'checked' : '' }}>
                            <i class="fas fa-heart mr-1 {{ request('favorites') ? '' : 'text-slate-400' }}"></i> Favorites
                        </label>
                        
                        <select name="category_id" onchange="this.form.submit()" class="h-10 rounded-xl border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-600 outline-none hover:bg-slate-50 focus:border-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </form>

                    <div class="grid max-h-[calc(100vh-290px)] grid-cols-2 gap-3 overflow-y-auto pr-1 sm:grid-cols-3">
                        @forelse ($products as $product)
                            <button
                                class="product-btn group relative rounded-xl border border-slate-200 bg-white p-2 text-left transition hover:-translate-y-0.5 hover:border-blue-400 hover:shadow-md"
                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}">

                                <div
                                    class="mb-3 flex aspect-[1.35] items-center justify-center overflow-hidden rounded-lg bg-slate-100">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                            class="h-full w-full object-cover">
                                    @else
                                        <i class="fas fa-box-open text-3xl text-slate-400"></i>
                                    @endif
                                </div>
                                <div class="truncate text-sm font-extrabold text-slate-950">{{ $product->name }}</div>
                                <div class="mt-1 text-xs text-slate-500">SKU: {{ $product->sku }}</div>
                                <div class="mt-2 text-sm font-extrabold text-slate-950">KES
                                    {{ number_format($product->price, 2) }}</div>
                                <div
                                    class="mt-1 text-xs font-bold {{ $product->stock_quantity <= $product->low_stock_threshold ? 'text-amber-600' : 'text-emerald-600' }}">
                                    In stock ({{ $product->stock_quantity }})
                                </div>
                            </button>
                        @empty
                            <div class="col-span-3 py-12 text-center text-slate-500">No products available</div>
                        @endforelse
                    </div>

                    <div class="mt-5">
                        {{ $products->links() }}
                    </div>
                </section>

                <section class="flex min-h-[620px] flex-col rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="text-base font-extrabold">Current Sale <span id="cart-count-label"
                                    class="font-semibold text-slate-500">(0 items)</span></h2>
                            <p class="mt-1 text-xs text-slate-500">Customer and sale details</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="showAddCustomerModal()"
                                class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100"><i
                                    class="fas fa-user-plus"></i></button>
                            <button id="clear-cart-btn"
                                class="flex h-10 w-10 items-center justify-center rounded-xl text-rose-500 hover:bg-rose-50"><i
                                    class="fas fa-trash"></i></button>
                        </div>
                    </div>

                    <div class="border-b border-slate-200 px-5 py-4">
                        <label for="customer-id"
                            class="mb-2 block text-xs font-bold uppercase text-slate-400">Customer</label>
                        <select id="customer-id"
                            class="h-12 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                            <option value="">Walk-in Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} @if ($customer->phone)
                                        - {{ $customer->phone }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="cart-items" class="flex-1 space-y-4 overflow-y-auto px-5 py-4"></div>

                    <div class="border-t border-slate-200 p-5">
                        <button
                            class="mb-4 flex h-11 w-full items-center gap-3 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            <i class="far fa-pen-to-square"></i>
                            Add Note
                        </button>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span
                                    id="subtotal" class="font-semibold">KES 0.00</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Discount</span><span
                                    class="font-semibold text-rose-600">-KES 0.00</span></div>
                            <button class="text-sm font-bold text-blue-600">Add Coupon</button>
                            <div class="flex justify-between"><span class="text-slate-500">Tax (16% VAT)</span><span
                                    id="tax" class="font-semibold">KES 0.00</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Rounding</span><span
                                    class="font-semibold">KES 0.00</span></div>
                            <div
                                class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4 text-xl font-extrabold">
                                <span>Total</span>
                                <span id="total">KES 0.00</span>
                            </div>
                        </div>
                        <div
                            class="mt-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700">
                            You will earn <span id="points-earned">0</span> points from this sale
                        </div>
                    </div>
                </section>

                <section
                    class="flex min-h-[620px] flex-col rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2 2xl:col-span-1">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="text-base font-extrabold">Payment</h2>
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                            Split Payment
                            <span class="relative inline-flex h-6 w-10 rounded-full bg-slate-200">
                                <span class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white shadow"></span>
                            </span>
                        </label>
                    </div>

                    <div class="mb-5 rounded-xl border border-blue-100 bg-blue-50 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-bold text-slate-700">Total Payable</span>
                            <span id="payment-total" class="text-3xl font-extrabold text-blue-700">KES 0.00</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ([['cash', 'Cash', 'fa-money-bill-wave', 'text-emerald-600'], ['card', 'Card', 'fa-credit-card', 'text-blue-600'], ['mobile_money', 'M-Pesa', 'fa-mobile-screen', 'text-emerald-600'], ['bank_transfer', 'Bank Transfer', 'fa-building-columns', 'text-violet-600'], ['credit', 'Credit', 'fa-hand-holding-dollar', 'text-orange-500'], ['other', 'Other', 'fa-ellipsis', 'text-slate-600']] as $method)
                            <button type="button"
                                class="payment-method-btn rounded-xl border border-slate-200 p-4 text-center transition hover:border-blue-400 hover:bg-blue-50"
                                data-method="{{ $method[0] }}">
                                <i class="fas {{ $method[2] }} mb-3 text-2xl {{ $method[3] }}"></i>
                                <div class="text-sm font-bold">{{ $method[1] }}</div>
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" id="payment-method" value="">

                    <div id="mobile-provider-group" class="mt-5 hidden">
                        <label class="mb-2 block text-sm font-bold text-slate-700">Mobile Money Provider</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button"
                                class="mobile-provider-btn rounded-xl border border-slate-200 p-3 text-sm font-bold hover:bg-emerald-50"
                                data-provider="mpesa">M-Pesa</button>
                            <button type="button"
                                class="mobile-provider-btn rounded-xl border border-slate-200 p-3 text-sm font-bold hover:bg-amber-50"
                                data-provider="airtel_money">Airtel Money</button>
                        </div>
                        <input type="hidden" id="mobile-provider" value="">
                    </div>

                    <div id="phone-input-group" class="mt-5 hidden">
                        <label class="mb-2 block text-sm font-bold text-slate-700">Phone Number</label>
                        <input type="tel" id="phone-number"
                            class="h-12 w-full rounded-xl border border-slate-200 px-4 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            placeholder="0712345678">
                    </div>

                    <div id="cash-input-group" class="mt-5">
                        <label class="mb-2 block text-sm font-bold text-slate-700">Cash Received</label>
                        <input type="number" id="paid-amount"
                            class="h-14 w-full rounded-xl border border-slate-200 px-5 text-2xl font-semibold outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            placeholder="KES 0.00">
                        <div class="mt-5 flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-700">Change Due</span>
                            <span id="change-due" class="text-2xl font-extrabold text-emerald-600">KES 0.00</span>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="mb-3 text-sm font-bold text-slate-700">Quick Cash</div>
                        <div id="quick-cash-buttons" class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            @foreach ($quickCash as $amount)
                                <button type="button"
                                    class="quick-cash-btn rounded-lg border border-slate-200 px-3 py-3 text-xs font-bold text-slate-700 hover:border-blue-400 hover:bg-blue-50"
                                    data-amount="{{ $amount }}">KES {{ number_format($amount) }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div id="payment-processing"
                        class="mt-5 hidden rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processing payment. Complete the prompt if required.
                    </div>

                    <div class="mt-auto pt-6">
                        <div
                            class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                            Cash drawer will be opened after completing the sale.
                        </div>
                        <button id="process-payment"
                            class="flex h-14 w-full items-center justify-center gap-3 rounded-xl bg-blue-700 text-lg font-extrabold text-white shadow-lg shadow-blue-900/20 transition hover:bg-blue-800">
                            <i class="fas fa-circle-check"></i>
                            Complete Sale
                            <span class="ml-auto mr-4 rounded-lg bg-blue-800 px-2 py-1 text-xs">F9</span>
                        </button>
                    </div>
                </section>
            </div>

            <div class="mt-4 grid rounded-xl border border-slate-200 bg-white shadow-sm md:grid-cols-4">
                @foreach ([['Cash Drawer', 'Connected', 'fa-cash-register'], ['Printer', 'Connected', 'fa-print'], ['Barcode Scanner', 'Connected', 'fa-barcode'], ['Last Sync: 2 mins ago', 'Online', 'fa-arrows-rotate']] as $device)
                    <div
                        class="flex items-center justify-between gap-4 border-slate-200 px-6 py-4 md:border-r last:border-r-0">
                        <div class="flex items-center gap-3">
                            <i class="fas {{ $device[2] }} text-slate-400"></i>
                            <div>
                                <div class="text-xs font-semibold text-slate-500">{{ $device[0] }}</div>
                                <div class="text-xs font-bold text-emerald-600">{{ $device[1] }}</div>
                            </div>
                        </div>
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receipt-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4 overflow-y-auto">
        <div class="flex my-auto w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl max-h-[90vh]">
            <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-lg font-extrabold text-slate-900">Sale Receipt</h3>
                <button id="close-receipt" type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="receipt-content" class="min-h-0 flex-1 overflow-y-auto bg-slate-50 p-5"></div>
          <div class="mx-auto mt-6 w-full max-w-xl px-4">
    <div class="flex flex-row items-center justify-between gap-3">
        
        <a id="go-to-sales-btn" href="{{ route('reports.sales') }}" 
           class="flex h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 md:text-sm">
            <i class="fas fa-arrow-left text-slate-500"></i>
            <span id="go-to-sales-text">Go to Sales</span>
        </a>

        <button id="print-receipt" type="button" 
                class="flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-amber-600 px-3 text-xs font-bold text-white shadow-md shadow-amber-600/10 transition hover:bg-amber-700 md:text-sm">
            <i class="fas fa-print"></i>
            <span>Print Receipt</span>
        </button>

        <a href="{{ route('pos.index') }}" 
           class="flex h-11 flex-[1.2] items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-extrabold text-white shadow-md shadow-blue-600/10 transition hover:bg-blue-700 md:text-sm">
            <i class="fas fa-plus"></i>
            <span>New Sale</span>
        </a>

    </div>
</div>
        </div>
    </div>
    <!-- Add Customer Modal -->
    <div id="add-customer-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-lg font-extrabold text-slate-900">Add New Customer</h3>
                <button type="button" onclick="$('#add-customer-modal').addClass('hidden')"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="add-customer-form" class="p-5">
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-bold text-slate-700">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="new-customer-name" required
                            class="w-full rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-bold text-slate-700">Phone Number <span class="text-rose-500">*</span></label>
                        <input type="text" id="new-customer-phone" required placeholder="e.g. 0712345678"
                            class="w-full rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-bold text-slate-700">Email Address (Optional)</label>
                        <input type="email" id="new-customer-email"
                            class="w-full rounded-xl border-slate-200 px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="$('#add-customer-modal').addClass('hidden')"
                        class="rounded-xl px-5 py-2 text-sm font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                    <button type="submit" id="save-customer-btn"
                        class="flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-700">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const urls = {
            search: '{{ url('/pos/search-product') }}',
            clear: '{{ url('/pos/clear-cart') }}',
            checkout: '{{ url('/pos/checkout') }}',
            update: '{{ url('/pos/update-cart') }}',
            add: '{{ url('/pos/add-to-cart') }}',
            cart: '{{ url('/pos/get-cart') }}',
            print: '{{ url('/pos/print') }}'
        };

        let cart = [];
        let selectedPaymentMethod = '';
        let selectedMobileProvider = '';
        let currentSaleId = null;
        let statusCheckInterval = null;

        $(document).ready(function() {
            selectPaymentMethod('cash');
            loadCartFromServer();

            $(document).on('click', '.product-btn', function() {
                const productId = $(this).data('id');
                const productStock = $(this).data('stock');

                if (productStock <= 0) {
                    showNotification('Product is out of stock!', 'error');
                    return;
                }

                addToCartViaServer(productId);
            });

            let searchTimeout;
            $('#search-product').on('input', function() {
                clearTimeout(searchTimeout);
                const query = $(this).val();

                if (query.length < 2) {
                    $('#search-results').addClass('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    $.ajax({
                        url: urls.search,
                        method: 'GET',
                        data: {
                            q: query
                        },
                        success: function(products) {
                            if (products.length > 0) {
                                let html = '';
                                products.forEach(product => {
                                    html += `
                                        <button class="search-result-item flex w-full items-center justify-between gap-4 border-b border-slate-100 p-3 text-left hover:bg-slate-50" data-id="${product.id}">
                                            <span>
                                                <span class="block text-sm font-bold text-slate-900">${escapeHtml(product.name)}</span>
                                                <span class="block text-xs text-slate-500">SKU: ${escapeHtml(product.sku)} | Stock: ${product.stock_quantity}</span>
                                            </span>
                                            <span class="text-sm font-extrabold text-blue-700">KES ${Number(product.price).toFixed(2)}</span>
                                        </button>
                                    `;
                                });
                                $('#search-results').html(html).removeClass('hidden');
                            } else {
                                $('#search-results').html(
                                    '<div class="p-4 text-sm font-semibold text-slate-500">No products found</div>'
                                    ).removeClass('hidden');
                            }
                        }
                    });
                }, 250);
            });

            $(document).on('click', '.search-result-item', function() {
                addToCartViaServer($(this).data('id'));
                $('#search-product').val('');
                $('#search-results').addClass('hidden');
            });

            $('#clear-cart-btn').click(function() {
                if (cart.length === 0) {
                    showNotification('Cart is already empty', 'info');
                    return;
                }

                if (confirm('Clear entire cart?')) {
                    $.ajax({
                        url: urls.clear,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                cart = [];
                                updateCartDisplay();
                                showNotification('Cart cleared', 'info');
                            }
                        }
                    });
                }
            });

            $('.payment-method-btn').click(function() {
                selectPaymentMethod($(this).data('method'));
            });

            $('.mobile-provider-btn').click(function() {
                $('.mobile-provider-btn').removeClass('border-blue-500 bg-blue-50 text-blue-700');
                $(this).addClass('border-blue-500 bg-blue-50 text-blue-700');
                selectedMobileProvider = $(this).data('provider');
                $('#mobile-provider').val(selectedMobileProvider);
                $('#phone-input-group').removeClass('hidden');
            });

            $('#paid-amount').on('input', updateChangeDue);

            $(document).on('click', '.quick-cash-btn', function() {
                $('#paid-amount').val($(this).data('amount'));
                updateChangeDue();
            });

            $('#process-payment').click(processPayment);

            $(document).on('keydown', function(e) {
                if (e.key === 'F9') {
                    e.preventDefault();
                    processPayment();
                }
            });

            $('#save-draft-btn, #hold-ticket-btn').click(function() {
                showNotification('Ticket saved as draft', 'info');
            });

            $('#print-preview-btn').click(function() {
                showNotification('Complete the sale to print a receipt', 'info');
            });

            $('#print-receipt').click(function() {
                if (currentSaleId) {
                    openReceiptPrintWindow(currentSaleId);
                }
            });

            $('#new-sale, #close-receipt').click(function() {
                $('#receipt-modal').removeClass('flex').addClass('hidden');
                $('body').removeClass('overflow-hidden');
                location.reload();
            });
        });

        function openReceiptPrintWindow(saleId) {
            const printUrl = urls.print + '/' + saleId;
            const printWindow = window.open(
                printUrl,
                'ReceiptPrint',
                'width=480,height=720,scrollbars=yes,resizable=yes,menubar=no,toolbar=no'
            );

            if (!printWindow) {
                showNotification('Please allow popups to print the receipt', 'warning');
                return;
            }

            printWindow.focus();
        }

        function showReceiptModal(saleId) {
            currentSaleId = saleId;

            $('#receipt-content').html(`
        <div class="flex items-center justify-center py-12">
            <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
        </div>
    `);

            // Unhide modal container and lock behind-modal page scroll interactions safely
            $('#receipt-modal').removeClass('hidden').addClass('flex');
            $('body').addClass('overflow-hidden');

            $.ajax({
                url: urls.print + '/' + saleId + '?embed=1',
                method: 'GET',
                success: function(receiptHtml) {
                    $('#receipt-content').html(receiptHtml);
                },
                error: function() {
                    $('#receipt-content').html(`
                <div class="py-8 text-center text-sm text-rose-600">
                    Failed to load receipt. <button type="button" class="font-bold underline" onclick="showReceiptModal(${saleId})">Try again</button>
                </div>
            `);
                }
            });
        }

        function selectPaymentMethod(method) {
            selectedPaymentMethod = ['bank_transfer', 'other'].includes(method) ? 'card' : method;
            $('#payment-method').val(selectedPaymentMethod);

            $('.payment-method-btn').removeClass('border-blue-600 bg-blue-50 ring-2 ring-blue-100');
            $(`.payment-method-btn[data-method="${method}"]`).addClass('border-blue-600 bg-blue-50 ring-2 ring-blue-100');

            $('#cash-input-group').toggleClass('hidden', selectedPaymentMethod !== 'cash');
            $('#mobile-provider-group').toggleClass('hidden', selectedPaymentMethod !== 'mobile_money');
            $('#phone-input-group').toggleClass('hidden', selectedPaymentMethod !== 'mobile_money' || !
                selectedMobileProvider);
            $('#payment-processing').addClass('hidden');

            if (selectedPaymentMethod !== 'mobile_money') {
                selectedMobileProvider = '';
                $('#mobile-provider').val('');
                $('#phone-number').val('');
                $('.mobile-provider-btn').removeClass('border-blue-500 bg-blue-50 text-blue-700');
            }

            updateChangeDue();
        }

        function processPayment() {
            if (cart.length === 0) {
                showNotification('Cart is empty! Please add items before checkout.', 'error');
                return;
            }

            if (!selectedPaymentMethod) {
                showNotification('Select payment method', 'error');
                return;
            }

            let paymentData = {
                payment_method: selectedPaymentMethod,
                customer_id: $('#customer-id').val(),
                _token: '{{ csrf_token() }}'
            };

            if (selectedPaymentMethod === 'credit' && !paymentData.customer_id) {
                showNotification('Customer selection is required for credit sales. Please add or select a customer.', 'error');
                showAddCustomerModal();
                return;
            }

            if (selectedPaymentMethod === 'cash') {
                const paidAmount = parseFloat($('#paid-amount').val()) || 0;
                const total = calculateCartTotal();

                if (paidAmount < total) {
                    showNotification('Insufficient payment amount', 'error');
                    return;
                }

                paymentData.paid_amount = paidAmount;
            } else if (selectedPaymentMethod === 'card') {
                paymentData.paid_amount = calculateCartTotal();
            } else if (selectedPaymentMethod === 'mobile_money') {
                const mobileProvider = $('#mobile-provider').val();
                const phoneNumber = $('#phone-number').val();

                if (!mobileProvider) {
                    showNotification('Select mobile money provider', 'error');
                    return;
                }
                if (!phoneNumber) {
                    showNotification('Enter phone number', 'error');
                    return;
                }

                paymentData.mobile_provider = mobileProvider;
                paymentData.phone_number = phoneNumber;
                paymentData.paid_amount = calculateCartTotal();
                $('#payment-processing').removeClass('hidden');
            }

            $('#process-payment').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: urls.checkout,
                method: 'POST',
                data: paymentData,
                success: function(response) {
                    if (response.success) {
                        if (response.requires_payment) {
                            showNotification(response.message, 'info');
                            checkTransactionStatus(response.transaction_id);
                        } else {
                            if (paymentData.payment_method === 'credit') {
                                $('#go-to-sales-btn').attr('href', '{{ route("credits.index") }}');
                                $('#go-to-sales-text').text('Go to Credits');
                            } else {
                                $('#go-to-sales-btn').attr('href', '{{ route("reports.sales") }}');
                                $('#go-to-sales-text').text('Go to Sales');
                            }
                            
                            currentSaleId = response.sale_id;
                            showNotification(`Sale completed! Invoice: ${response.invoice_no}`, 'success');
                            loadReceipt(response.sale_id, response.change_due);
                        }
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Payment failed';
                    showNotification(message, 'error');
                    $('#payment-processing').addClass('hidden');
                    $('#process-payment').prop('disabled', false).html(
                        '<i class="fas fa-circle-check"></i> Complete Sale <span class="ml-auto mr-4 rounded-lg bg-blue-800 px-2 py-1 text-xs">F9</span>'
                        );
                }
            });
        }

        function loadReceipt(saleId, changeDue = 0) {
            showReceiptModal(saleId);

            if (selectedPaymentMethod === 'cash' && changeDue > 0) {
                showNotification(`Change due: KES ${Number(changeDue).toFixed(2)}`, 'info');
            }

            playSound('success');
            cart = [];
            updateCartDisplay();
            resetPaymentForm();
            $('#process-payment').prop('disabled', false).html(
                '<i class="fas fa-circle-check"></i> Complete Sale <span class="ml-auto mr-4 rounded-lg bg-blue-800 px-2 py-1 text-xs">F9</span>'
                );
        }

        function checkTransactionStatus(transactionId) {
            let attempts = 0;
            const maxAttempts = 30;

            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
            }

            statusCheckInterval = setInterval(function() {
                attempts++;

                $.ajax({
                    url: `/transaction/${transactionId}/status`,
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'completed') {
                            clearInterval(statusCheckInterval);
                            $('#payment-processing').addClass('hidden');
                            currentSaleId = response.sale_id;
                            showReceiptModal(response.sale_id);

                            $('#process-payment').prop('disabled', false).html(
                                '<i class="fas fa-circle-check"></i> Complete Sale <span class="ml-auto mr-4 rounded-lg bg-blue-800 px-2 py-1 text-xs">F9</span>'
                                );
                            showNotification(response.message || 'Payment completed successfully!',
                                'success');
                            playSound('success');

                            cart = [];
                            updateCartDisplay();
                            resetPaymentForm();

                        } else if (response.status === 'failed' || attempts >= maxAttempts) {
                            clearInterval(statusCheckInterval);
                            $('#payment-processing').addClass('hidden');
                            $('#process-payment').prop('disabled', false).html(
                                '<i class="fas fa-circle-check"></i> Complete Sale <span class="ml-auto mr-4 rounded-lg bg-blue-800 px-2 py-1 text-xs">F9</span>'
                                );
                            showNotification(response.status === 'failed' ?
                                'Payment failed. Please try again.' :
                                'Payment timeout. Please check transaction status.', 'warning');
                        }
                    }
                });
            }, 1000);
        }

        function resetPaymentForm() {
            $('#paid-amount').val('');
            $('#phone-number').val('');
            $('#mobile-provider').val('');
            selectedMobileProvider = '';
            $('.mobile-provider-btn').removeClass('border-blue-500 bg-blue-50 text-blue-700');
            $('#payment-processing').addClass('hidden');
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
            }
            selectPaymentMethod('cash');
        }

        function addToCartViaServer(productId) {
            $.ajax({
                url: urls.add,
                method: 'POST',
                data: {
                    product_id: productId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        cart = response.cart;
                        updateCartDisplay();

                        const product = cart.find(item => item.id == productId);
                        if (product) {
                            showNotification(`${product.name} added to cart!`);
                        }
                        playSound('add');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Error adding product to cart';
                    showNotification(message, 'error');
                }
            });
        }

        function loadCartFromServer() {
            $.ajax({
                url: urls.cart,
                method: 'GET',
                success: function(data) {
                    cart = data.success && data.cart ? data.cart : [];
                    updateCartDisplay();
                },
                error: function() {
                    cart = [];
                    updateCartDisplay();
                }
            });
        }

        function updateQuantityOnServer(productId, action) {
            $.ajax({
                url: urls.update,
                method: 'POST',
                data: {
                    product_id: productId,
                    action: action,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        cart = response.cart;
                        updateCartDisplay();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Error updating cart';
                    showNotification(message, 'error');
                }
            });
        }

        function updateCartDisplay() {
            const itemCount = cart.reduce((sum, item) => sum + Number(item.quantity), 0);
            $('#cart-count-label').text(`(${itemCount} ${itemCount === 1 ? 'item' : 'items'})`);

            if (cart.length === 0) {
                $('#cart-items').html(`
                    <div class="flex h-full min-h-[280px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50 text-center">
                        <i class="fas fa-cart-shopping mb-3 text-4xl text-slate-300"></i>
                        <p class="text-sm font-bold text-slate-600">Cart is empty</p>
                        <p class="mt-1 text-xs text-slate-400">Scan or select products to begin.</p>
                    </div>
                `);
                setTotals(0, 0, 0);
                return;
            }

            let subtotal = 0;
            let html = '';

            cart.forEach((item) => {
                const total = item.price * item.quantity;
                subtotal += total;

                html += `
                    <div class="cart-item-added flex gap-4 border-b border-slate-100 pb-4 last:border-b-0">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex justify-between gap-4">
                                <div>
                                    <div class="truncate text-sm font-extrabold text-slate-950">${escapeHtml(item.name)}</div>
                                    <div class="mt-1 text-xs text-slate-500">KES ${Number(item.price).toFixed(2)} each</div>
                                </div>
                                <button class="remove-item text-slate-400 hover:text-rose-600" data-id="${item.id}">
                                    <i class="far fa-trash-can"></i>
                                </button>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="inline-flex items-center rounded-lg border border-slate-200">
                                    <button class="qty-decrease flex h-8 w-8 items-center justify-center text-slate-600 hover:bg-slate-100" data-id="${item.id}">-</button>
                                    <span class="w-9 text-center text-sm font-bold">${item.quantity}</span>
                                    <button class="qty-increase flex h-8 w-8 items-center justify-center text-slate-600 hover:bg-slate-100" data-id="${item.id}">+</button>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-semibold text-slate-500">KES ${Number(item.price).toFixed(2)}</div>
                                    <div class="text-sm font-extrabold text-slate-950">KES ${total.toFixed(2)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            const tax = subtotal * 0.16;
            const total = subtotal + tax;
            $('#cart-items').html(html);
            setTotals(subtotal, tax, total);
        }

        function setTotals(subtotal, tax, total) {
            $('#subtotal').text(`KES ${subtotal.toFixed(2)}`);
            $('#tax').text(`KES ${tax.toFixed(2)}`);
            $('#total').text(`KES ${total.toFixed(2)}`);
            $('#payment-total').text(`KES ${total.toFixed(2)}`);
            $('#points-earned').text(Math.floor(total / 100));
            updateQuickCash(total);
            updateChangeDue();
        }

        function updateQuickCash(total) {
            const rounded = Math.ceil(total / 100) * 100;
            const values = [rounded, rounded + 500, rounded + 1000, rounded + 5000].filter((value, index, array) => value >
                0 && array.indexOf(value) === index);
            let html = '';
            values.slice(0, 4).forEach(value => {
                html +=
                    `<button type="button" class="quick-cash-btn rounded-lg border border-slate-200 px-3 py-3 text-xs font-bold text-slate-700 hover:border-blue-400 hover:bg-blue-50" data-amount="${value}">KES ${Number(value).toLocaleString()}</button>`;
            });
            $('#quick-cash-buttons').html(html);
        }

        function updateChangeDue() {
            const total = calculateCartTotal();
            const paid = parseFloat($('#paid-amount').val()) || 0;
            const change = paid - total;

            if (selectedPaymentMethod !== 'cash') {
                $('#change-due').text('KES 0.00').removeClass('text-rose-600').addClass('text-emerald-600');
                return;
            }

            if (change >= 0) {
                $('#change-due').text(`KES ${change.toFixed(2)}`).removeClass('text-rose-600').addClass('text-emerald-600');
            } else {
                $('#change-due').text(`KES ${Math.abs(change).toFixed(2)} short`).removeClass('text-emerald-600').addClass(
                    'text-rose-600');
            }
        }

        $(document).on('click', '.qty-increase', function() {
            updateQuantityOnServer($(this).data('id'), 'increase');
        });

        $(document).on('click', '.qty-decrease', function() {
            updateQuantityOnServer($(this).data('id'), 'decrease');
        });

        $(document).on('click', '.remove-item', function() {
            updateQuantityOnServer($(this).data('id'), 'remove');
            showNotification('Item removed', 'info');
        });

        function calculateCartTotal() {
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += item.price * item.quantity;
            });
            return subtotal + (subtotal * 0.16);
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function showAddCustomerModal() {
            $('#add-customer-modal').removeClass('hidden').addClass('flex');
            $('#new-customer-name').val('');
            $('#new-customer-phone').val('');
            $('#new-customer-email').val('');
            setTimeout(() => $('#new-customer-name').focus(), 100);
        }

        $('#add-customer-form').on('submit', function(e) {
            e.preventDefault();
            
            const btn = $('#save-customer-btn');
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
            
            $.ajax({
                url: '{{ route("customers.storeAjax") }}',
                method: 'POST',
                data: {
                    name: $('#new-customer-name').val(),
                    phone: $('#new-customer-phone').val(),
                    email: $('#new-customer-email').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const c = response.customer;
                        const optionText = `${c.name} ${c.phone ? '- ' + c.phone : ''}`;
                        $('#customer-id').append(new Option(optionText, c.id));
                        $('#customer-id').val(c.id);
                        
                        $('#add-customer-modal').addClass('hidden');
                        showNotification('Customer added and selected', 'success');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Failed to add customer';
                    showNotification(message, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
@endpush
