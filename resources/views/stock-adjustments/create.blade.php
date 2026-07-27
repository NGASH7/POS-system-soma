@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-plus-circle text-blue-600 mr-2"></i>New Stock Adjustment
                </h1>
                <p class="text-gray-600 mt-2">Adjust inventory for spoilage, damage, returns, or corrections</p>
            </div>
            <a href="{{ route('stock-adjustments.index') }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('stock-adjustments.store') }}" id="adjustment-form">
            @csrf
            
            <div class="space-y-6">
                <!-- Product Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Product *</label>
                    <div class="relative">
                        <select name="product_id" id="product_id" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                            <option value="">-- Search and Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-stock="{{ $product->stock_quantity }}"
                                        data-sku="{{ $product->sku }}"
                                        data-price="{{ $product->price }}"
                                        data-name="{{ $product->name }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (SKU: {{ $product->sku }}) - Stock: {{ $product->stock_quantity }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    
                    <!-- Product Info Display -->
                    <div id="product-info" class="mt-3 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <span class="text-xs text-gray-500">Product Name</span>
                                    <p id="display-product-name" class="font-semibold text-gray-900">-</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">SKU</span>
                                    <p id="display-sku" class="font-semibold text-gray-900">-</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Current Stock</span>
                                    <p id="display-stock" class="font-semibold text-blue-600">-</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Price</span>
                                    <p id="display-price" class="font-semibold text-gray-900">-</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Adjustment Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Type *</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-400 transition has-[:checked]:border-green-600 has-[:checked]:bg-green-50">
                            <input type="radio" name="type" value="increase" {{ old('type') == 'increase' ? 'checked' : '' }} class="mr-2 text-green-600" required>
                            <div>
                                <div class="font-semibold text-green-600">Increase Stock</div>
                                <div class="text-xs text-gray-500">Add items to inventory</div>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-400 transition has-[:checked]:border-red-600 has-[:checked]:bg-red-50">
                            <input type="radio" name="type" value="decrease" {{ old('type') == 'decrease' ? 'checked' : '' }} class="mr-2 text-red-600" required>
                            <div>
                                <div class="font-semibold text-red-600">Decrease Stock</div>
                                <div class="text-xs text-gray-500">Remove items from inventory</div>
                            </div>
                        </label>
                    </div>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="document.getElementById('quantity').value = parseInt(document.getElementById('quantity').value) + 1; updateSummary();" 
                                    class="w-10 h-10 flex items-center justify-center bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition border border-gray-200">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button type="button" onclick="if(parseInt(document.getElementById('quantity').value) > 1) { document.getElementById('quantity').value = parseInt(document.getElementById('quantity').value) - 1; updateSummary(); }" 
                                    class="w-10 h-10 flex items-center justify-center bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition border border-gray-200">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason *</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach([
                            'spoilage' => ['label' => 'Spoilage', 'color' => 'red'],
                            'damage' => ['label' => 'Damage', 'color' => 'orange'],
                            'theft' => ['label' => 'Theft', 'color' => 'red'],
                            'return' => ['label' => 'Return to Supplier', 'color' => 'blue'],
                            'correction' => ['label' => 'Stock Correction', 'color' => 'purple'],
                            'expired' => ['label' => 'Expired', 'color' => 'yellow'],
                            'lost' => ['label' => 'Lost', 'color' => 'gray'],
                            'other' => ['label' => 'Other', 'color' => 'gray'],
                        ] as $value => $reason)
                        <label class="reason-option flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-{{ $reason['color'] }}-400 transition has-[:checked]:border-{{ $reason['color'] }}-600 has-[:checked]:bg-{{ $reason['color'] }}-50">
                            <input type="radio" name="reason" value="{{ $value }}" {{ old('reason') == $value ? 'checked' : '' }} 
                                   class="mr-2 text-{{ $reason['color'] }}-600" required>
                            <div>
                                <div class="font-semibold text-sm">{{ $reason['label'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Reason Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason Description</label>
                    <input type="text" name="reason_description" id="reason_description" value="{{ old('reason_description') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="E.g., Items damaged during delivery, 5 units expired">
                    @error('reason_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Adjustment Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adjustment Date *</label>
                    <input type="date" name="adjustment_date" id="adjustment_date" value="{{ old('adjustment_date', date('Y-m-d')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('adjustment_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" id="notes" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Any additional notes about this adjustment...">{{ old('notes') }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Adjustment Summary -->
                <div class="border-t border-gray-200 pt-5 mt-2">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-list-check text-blue-600 mr-2"></i>Adjustment Summary
                    </h4>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-5">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                                <span class="text-gray-500 text-xs block">Product</span>
                                <span id="summary-product" class="font-semibold text-gray-900">-</span>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                                <span class="text-gray-500 text-xs block">Type</span>
                                <span id="summary-type" class="font-semibold">-</span>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                                <span class="text-gray-500 text-xs block">Quantity</span>
                                <span id="summary-quantity" class="font-semibold text-blue-600">-</span>
                            </div>
                            <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-100">
                                <span class="text-gray-500 text-xs block">New Stock</span>
                                <span id="summary-new-stock" class="font-semibold text-blue-600">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('stock-adjustments.index') }}" 
                   class="px-8 py-3 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-semibold text-base">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" id="submit-btn" 
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-bold text-base shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transform hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-check-circle mr-2"></i>Confirm Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productSelect = document.getElementById('product_id');
        const productInfo = document.getElementById('product-info');
        const summaryProduct = document.getElementById('summary-product');
        const summaryType = document.getElementById('summary-type');
        const summaryQuantity = document.getElementById('summary-quantity');
        const summaryNewStock = document.getElementById('summary-new-stock');

        // Show product info when selected
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stock = selectedOption.dataset.stock;
            const sku = selectedOption.dataset.sku || '-';
            const price = selectedOption.dataset.price || '0';
            const name = selectedOption.dataset.name || selectedOption.text.split(' (SKU:')[0];
            
            if (this.value) {
                productInfo.classList.remove('hidden');
                document.getElementById('display-product-name').textContent = name;
                document.getElementById('display-sku').textContent = sku;
                document.getElementById('display-stock').textContent = stock;
                document.getElementById('display-price').textContent = 'KES ' + parseFloat(price).toFixed(2);
                summaryProduct.textContent = name;
                updateSummary();
            } else {
                productInfo.classList.add('hidden');
                summaryProduct.textContent = '-';
            }
        });

        // Update summary on change
        document.querySelectorAll('input[name="type"], input[name="quantity"]').forEach(el => {
            el.addEventListener('change', updateSummary);
            el.addEventListener('input', updateSummary);
        });

        // Update reason description placeholder based on reason
        document.querySelectorAll('input[name="reason"]').forEach(el => {
            el.addEventListener('change', function() {
                const reason = this.value;
                const descriptions = {
                    'spoilage': 'E.g., Items spoiled due to temperature issues',
                    'damage': 'E.g., Items damaged during handling or delivery',
                    'theft': 'E.g., Items missing from inventory',
                    'return': 'E.g., Returning items to supplier',
                    'correction': 'E.g., Correcting inventory count discrepancy',
                    'expired': 'E.g., Items past expiry date',
                    'lost': 'E.g., Items misplaced or lost',
                    'other': 'Describe the reason for this adjustment'
                };
                document.getElementById('reason_description').placeholder = descriptions[reason] || 'Describe the reason...';
            });
        });

        window.updateSummary = function() {
            const type = document.querySelector('input[name="type"]:checked');
            const quantity = parseInt(document.getElementById('quantity').value) || 0;
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const currentStock = parseInt(selectedOption.dataset.stock) || 0;
            
            if (type && quantity > 0 && productSelect.value) {
                const typeLabel = type.value === 'increase' ? 'Increase' : 'Decrease';
                const typeColor = type.value === 'increase' ? 'text-green-600' : 'text-red-600';
                summaryType.textContent = typeLabel;
                summaryType.className = `font-semibold ${typeColor}`;
                summaryQuantity.textContent = quantity;
                
                const newStock = type.value === 'increase' ? currentStock + quantity : currentStock - quantity;
                if (newStock < 0) {
                    summaryNewStock.textContent = '⚠️ Insufficient Stock';
                    summaryNewStock.className = 'font-semibold text-red-600';
                } else {
                    summaryNewStock.textContent = newStock;
                    summaryNewStock.className = 'font-semibold text-blue-600';
                }
            } else {
                summaryType.textContent = '-';
                summaryType.className = 'font-semibold';
                summaryQuantity.textContent = '-';
                summaryNewStock.textContent = '-';
                summaryNewStock.className = 'font-semibold text-blue-600';
            }
        };

        // Trigger initial update
        setTimeout(window.updateSummary, 100);
    });
</script>

<style>
    .reason-option:has(input:checked) {
        border-color: #2563eb;
        background-color: #eff6ff;
    }
    
    #submit-btn {
        transition: all 0.3s ease;
        min-width: 200px;
        letter-spacing: 0.3px;
    }
    
    #submit-btn:active {
        transform: scale(0.98);
    }
</style>
@endsection