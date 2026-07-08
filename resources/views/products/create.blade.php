@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-950">Add New Product</h1>
            <p class="text-slate-500 mt-2">Create a new product or add stock to an existing product</p>
        </div>

        <!-- Search Existing Product -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">
                <i class="fas fa-search text-blue-600 mr-2"></i>Search Existing Product
            </h3>
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" id="search-product" 
                           class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Search by name, SKU, or barcode...">
                </div>
                <button id="search-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl transition">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
            <div id="search-results" class="mt-4 hidden">
                <div class="bg-slate-50 rounded-xl p-4">
                    <h4 class="font-semibold text-slate-700 mb-2">Search Results:</h4>
                    <div id="results-list" class="space-y-2 max-h-60 overflow-y-auto"></div>
                </div>
            </div>
            <div id="search-error" class="mt-4 hidden">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span id="error-message">Error searching for products</span>
                </div>
            </div>
        </div>

        <!-- Product Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" id="product-form">
                @csrf
                <input type="hidden" name="existing_product_id" id="existing_product_id" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Product Name *</label>
                        <input type="text" name="name" id="product_name" value="{{ old('name') }}" required
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">SKU *</label>
                        <input type="text" name="sku" id="product_sku" value="{{ old('sku') }}" required
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Barcode</label>
                        <input type="text" name="barcode" id="product_barcode" value="{{ old('barcode') }}"
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Selling Price *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-500">KES</span>
                            <input type="number" name="price" id="product_price" step="0.01" value="{{ old('price') }}" required
                                   class="w-full pl-12 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Cost Price *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-500">KES</span>
                            <input type="number" name="cost" id="product_cost" step="0.01" value="{{ old('cost') }}" required
                                   class="w-full pl-12 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        @error('cost') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" id="product_stock" value="{{ old('stock_quantity', 0) }}" required
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="existing-stock-info" class="mt-2 hidden text-sm">
                            <span class="text-slate-500">Current stock:</span>
                            <span id="current-stock-value" class="font-semibold text-blue-600">0</span>
                            <span class="text-slate-500 ml-2">New stock will be:</span>
                            <span id="new-stock-value" class="font-semibold text-green-600">0</span>
                        </div>
                        @error('stock_quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" id="product_low_stock" value="{{ old('low_stock_threshold', 5) }}" required
                               class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('low_stock_threshold') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Category</label>
                        <select name="category_id" id="product_category" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="product_status" value="1" checked class="mr-2">
                            <span class="text-sm text-slate-500">Active</span>
                        </label>
                    </div>

                    <div id="existing-product-alert" class="col-span-2 hidden">
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span id="existing-product-message"></span>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-slate-200">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition">
                        Cancel
                    </a>
                    <button type="submit" id="submit-btn" class="px-6 py-2 bg-blue-700 text-white rounded-xl hover:bg-blue-800 transition">
                        <i class="fas fa-save mr-2"></i>Create / Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let selectedProduct = null;

    // Search button click
    $('#search-btn').click(function() {
        const search = $('#search-product').val().trim();
        
        if (search.length < 2) {
            alert('Please enter at least 2 characters to search');
            return;
        }

        // Show loading
        $('#search-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Searching...');
        $('#search-error').addClass('hidden');

        // Use the full URL directly
        $.ajax({
            url: '/products/search',
            method: 'GET',
            data: { q: search },
            success: function(products) {
                $('#search-btn').prop('disabled', false).html('<i class="fas fa-search mr-2"></i>Search');

                if (!products || products.length === 0) {
                    $('#results-list').html(`
                        <div class="text-center text-slate-500 py-4">
                            <i class="fas fa-search text-2xl block mb-2"></i>
                            No products found. You can create a new product below.
                        </div>
                    `);
                    $('#search-results').removeClass('hidden');
                    return;
                }

                let html = '';
                products.forEach(product => {
                    html += `
                        <button type="button" class="product-result w-full text-left p-3 bg-white border border-slate-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition flex justify-between items-center" 
                                data-id="${product.id}" 
                                data-name="${product.name}" 
                                data-sku="${product.sku}" 
                                data-price="${product.price}" 
                                data-stock="${product.stock_quantity}">
                            <div>
                                <div class="font-medium text-slate-900">${product.name}</div>
                                <div class="text-sm text-slate-500">SKU: ${product.sku} | Stock: ${product.stock_quantity}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-blue-600">KES ${Number(product.price).toFixed(2)}</div>
                            </div>
                        </button>
                    `;
                });

                $('#results-list').html(html);
                $('#search-results').removeClass('hidden');
            },
            error: function(xhr) {
                $('#search-btn').prop('disabled', false).html('<i class="fas fa-search mr-2"></i>Search');
                $('#error-message').text('Error: ' + xhr.status + ' - ' + xhr.statusText);
                $('#search-error').removeClass('hidden');
                alert('Error searching. Check console for details.');
                console.log('Search error:', xhr);
            }
        });
    });

    // Enter key in search input
    $('#search-product').keypress(function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#search-btn').click();
        }
    });

    // Select product from results
    $(document).on('click', '.product-result', function() {
        const product = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            sku: $(this).data('sku'),
            price: $(this).data('price'),
            stock: $(this).data('stock')
        };

        selectedProduct = product;
        
        $('#existing_product_id').val(product.id);
        $('#product_name').val(product.name).prop('readonly', true).addClass('bg-slate-50');
        $('#product_sku').val(product.sku).prop('readonly', true).addClass('bg-slate-50');
        $('#product_price').val(product.price).prop('readonly', true).addClass('bg-slate-50');

        // Show stock info
        $('#current-stock-value').text(product.stock);
        $('#existing-stock-info').removeClass('hidden');
        $('#product_stock').val(0).addClass('border-blue-500 bg-blue-50');

        // Show alert
        $('#existing-product-message').text(`You are adding stock to "${product.name}". Current stock: ${product.stock}`);
        $('#existing-product-alert').removeClass('hidden');

        // Update submit button
        $('#submit-btn').html('<i class="fas fa-plus-circle mr-2"></i>Add Stock & Update');
        
        $('#search-results').addClass('hidden');
        showNotification('Product loaded! Add stock quantity below.', 'success');
    });

    // Update new stock calculation
    $('#product_stock').on('input', function() {
        const currentStock = parseInt($('#current-stock-value').text()) || 0;
        const addStock = parseInt($(this).val()) || 0;
        const newStock = currentStock + addStock;
        $('#new-stock-value').text(newStock);
    });

    // Handle form submission
    $('#product-form').submit(function(e) {
        if (selectedProduct) {
            const addStock = parseInt($('#product_stock').val()) || 0;
            if (addStock <= 0) {
                e.preventDefault();
                alert('Please enter the quantity to add to stock');
                return false;
            }
        }
    });
});
</script>

<style>
    .product-result {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .product-result:hover {
        transform: translateX(4px);
        border-color: #3b82f6 !important;
    }
</style>
@endsection