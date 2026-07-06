@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">Add New Product</h1>
        <p class="text-slate-500 mt-2">Create a new product in your inventory</p>
    </div>

    <div class="soma-card p-6">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Name -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- SKU -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" required
                           class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Barcode -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}"
                           class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Price -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Selling Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-500">Ksh</span>
                        <input type="number" name="price" step="0.01" value="{{ old('price') }}" required
                               class="w-full pl-8 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Cost -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cost Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-slate-500">Ksh</span>
                        <input type="number" name="cost" step="0.01" value="{{ old('cost') }}" required
                               class="w-full pl-8 pr-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    @error('cost') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Stock Quantity -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Initial Stock *</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required
                           class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('stock_quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Low Stock Threshold -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" required
                           class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('low_stock_threshold') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Category</label>
                    <select name="category_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="mr-2">
                        <span class="text-sm text-slate-500">Active</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-slate-200">
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-700 text-white rounded-xl hover:bg-blue-800 transition">
                    <i class="fas fa-save mr-2"></i>Create Product
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection