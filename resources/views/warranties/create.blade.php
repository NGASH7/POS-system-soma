@extends('layouts.app')

@section('content')
<div class="container-fluid max-w-4xl mx-auto py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-shield-alt text-blue-600 mr-2"></i>Register New Warranty
            </h1>
            <p class="text-gray-600 text-sm mt-1">Create a product warranty for a customer</p>
        </div>
        <a href="{{ route('warranties.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Back to Warranties
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('warranties.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Warranty Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warranty Reference #</label>
                <input type="text" name="warranty_no" value="{{ old('warranty_no', $warrantyNo) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-gray-700 font-mono font-semibold" readonly>
            </div>

            <!-- Warranty Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warranty Type <span class="text-red-500">*</span></label>
                <select name="warranty_type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="store" {{ old('warranty_type') == 'store' ? 'selected' : '' }}>Store Warranty</option>
                    <option value="manufacturer" {{ old('warranty_type') == 'manufacturer' ? 'selected' : '' }}>Manufacturer Warranty</option>
                    <option value="extended" {{ old('warranty_type') == 'extended' ? 'selected' : '' }}>Extended Warranty</option>
                </select>
            </div>

            <!-- Product -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product <span class="text-red-500">*</span></label>
                <select name="product_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} (SKU: {{ $product->sku ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Customer -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
                <select name="customer_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Customer...</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} {{ $customer->phone ? "({$customer->phone})" : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sale Reference (Optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Associated Sale (Optional)</label>
                <select name="sale_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">None / Manual Registration</option>
                    @foreach($sales as $sale)
                        <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                            {{ $sale->invoice_number ?? 'Sale #'.$sale->id }} - KES {{ number_format($sale->grand_total, 2) }} ({{ $sale->created_at->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Serial Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="e.g. SN-88912344" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Batch Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Batch / Lot Number</label>
                <input type="text" name="batch_number" value="{{ old('batch_number') }}" placeholder="e.g. BATCH-2026-07" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Duration Months -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warranty Duration (Months) <span class="text-red-500">*</span></label>
                <input type="number" id="warranty_duration_months" name="warranty_duration_months" value="{{ old('warranty_duration_months', 12) }}" min="1" max="120" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Purchase Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date <span class="text-red-500">*</span></label>
                <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Expiry Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date <span class="text-red-500">*</span></label>
                <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime('+12 months'))) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Terms -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warranty Terms & Conditions</label>
                <textarea name="terms" rows="3" placeholder="Covers hardware defects..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('terms') }}</textarea>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Internal Notes</label>
                <textarea name="notes" rows="3" placeholder="Customer notes or condition upon sale..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <a href="{{ route('warranties.index') }}" class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition flex items-center">
                <i class="fas fa-save mr-2"></i> Save Warranty
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const purchaseDateInput = document.getElementById('purchase_date');
    const durationInput = document.getElementById('warranty_duration_months');
    const expiryDateInput = document.getElementById('expiry_date');

    function updateExpiryDate() {
        if (!purchaseDateInput.value || !durationInput.value) return;
        const purchaseDate = new Date(purchaseDateInput.value);
        const months = parseInt(durationInput.value) || 12;
        purchaseDate.setMonth(purchaseDate.getMonth() + months);
        const yyyy = purchaseDate.getFullYear();
        const mm = String(purchaseDate.getMonth() + 1).padStart(2, '0');
        const dd = String(purchaseDate.getDate()).padStart(2, '0');
        expiryDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    purchaseDateInput.addEventListener('change', updateExpiryDate);
    durationInput.addEventListener('input', updateExpiryDate);
});
</script>
@endsection
