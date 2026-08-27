{{-- resources/views/reports/product-sell.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Product Sell Report</h1>
                    <p class="text-gray-600 mt-1">Track product sales performance and popularity</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                    <i class="fas fa-print mr-2 text-gray-500"></i>Print
                </button>
                <a href="{{ route('reports.export.product-sell', request()->all()) }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                    <i class="fas fa-file-excel mr-2 text-green-600"></i>Export
                </a>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Category</label>
                <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[160px]">
                    <option value="">All Categories      .</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Sort By</label>
                <select name="sort_by" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[150px]">
                    <option value="quantity" {{ request('sort_by', 'quantity') == 'quantity' ? 'selected' : '' }}>Units Sold</option>
                    <option value="revenue" {{ request('sort_by') == 'revenue' ? 'selected' : '' }}>Revenue</option>
                    <option value="profit" {{ request('sort_by') == 'profit' ? 'selected' : '' }}>Profit</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Product Name</option>
                </select>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm font-medium text-sm">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('reports.product-sell') }}" class="bg-white text-gray-700 border border-gray-300 px-5 py-2 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Products Sold</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_products'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Units</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-cubes text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($summary['total_units'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Revenue</span>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-600">KES {{ number_format($summary['total_revenue'] ?? 0, 2) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Profit</span>
                <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <i class="fas fa-chart-line text-indigo-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-indigo-600">KES {{ number_format($summary['total_profit'] ?? 0, 2) }}</div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Product Performance</h3>
            <span class="text-xs text-gray-500">{{ $paginatedProducts->total() ?? $paginatedProducts->count() }} products</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Units Sold</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Profit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paginatedProducts as $index => $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $product->sku }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">{{ number_format($product->total_quantity) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-green-600">KES {{ number_format($product->total_revenue, 2) }}</td>
                        <td class="px-6 py-4 text-right text-gray-600">KES {{ number_format($product->total_cost, 2) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-indigo-600">KES {{ number_format($product->total_profit, 2) }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($product->total_revenue > 0)
                                @php $margin = ($product->total_profit / $product->total_revenue) * 100; @endphp
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $margin > 20 ? 'bg-green-100 text-green-800' : ($margin > 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($margin, 1) }}%
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">0%</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-3 block text-gray-300"></i>
                            No products sold during this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $paginatedProducts->links() }}
        </div>
    </div>
</div>
@endsection