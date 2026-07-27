@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-arrow-up-wide-short text-blue-600 mr-2"></i>Stock Adjustments
                </h1>
                <p class="text-gray-600 mt-2">Manage inventory adjustments for spoilage, damage, returns, and corrections</p>
            </div>
            <a href="{{ route('stock-adjustments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition shadow-sm font-medium">
                <i class="fas fa-plus mr-2"></i>New Adjustment
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Total Adjustments</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-arrows-rotate text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_adjustments']) }}</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Stock Added</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-plus-circle text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($summary['total_increase']) }}</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Stock Removed</span>
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fas fa-minus-circle text-red-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-red-600">{{ number_format($summary['total_decrease']) }}</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Products Affected</span>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-boxes text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($summary['total_products_affected']) }}</div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                <select name="product_id" class="border border-gray-300 rounded-lg px-3 py-2 min-w-[200px] focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All</option>
                    <option value="increase" {{ request('type') == 'increase' ? 'selected' : '' }}>Increase</option>
                    <option value="decrease" {{ request('type') == 'decrease' ? 'selected' : '' }}>Decrease</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <select name="reason" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All</option>
                    <option value="spoilage" {{ request('reason') == 'spoilage' ? 'selected' : '' }}>Spoilage</option>
                    <option value="damage" {{ request('reason') == 'damage' ? 'selected' : '' }}>Damage</option>
                    <option value="theft" {{ request('reason') == 'theft' ? 'selected' : '' }}>Theft</option>
                    <option value="return" {{ request('reason') == 'return' ? 'selected' : '' }}>Return</option>
                    <option value="correction" {{ request('reason') == 'correction' ? 'selected' : '' }}>Correction</option>
                    <option value="expired" {{ request('reason') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="lost" {{ request('reason') == 'lost' ? 'selected' : '' }}>Lost</option>
                    <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm font-medium">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('stock-adjustments.index') }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition ml-2 font-medium">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Adjustments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Old Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">New Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($adjustments as $adjustment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $adjustment->adjustment_no }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $adjustment->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2 py-1 rounded-full {{ $adjustment->type === 'increase' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $adjustment->type === 'increase' ? '+' : '-' }}{{ $adjustment->quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-800">
                                {{ $adjustment->reason_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-700">{{ number_format($adjustment->old_stock) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-blue-600">{{ number_format($adjustment->new_stock) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $adjustment->adjustment_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('stock-adjustments.show', $adjustment) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-arrow-up-wide-short text-4xl mb-3 block text-gray-300"></i>
                            No stock adjustments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $adjustments->links() }}
        </div>
    </div>
</div>
@endsection