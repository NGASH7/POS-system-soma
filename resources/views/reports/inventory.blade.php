@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-warehouse text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-950">Inventory Report</h2>
                    <p class="text-slate-500 text-sm mt-0.5">Stock value and low-stock visibility across your catalog</p>
                </div>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-slate-500 text-sm font-medium">Total Inventory Value (Cost)</span>
                        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                            <i class="fas fa-tag text-blue-600"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-950">Ksh {{ number_format($totalValue, 2) }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-slate-500 text-sm font-medium">Total Inventory Value (Retail)</span>
                        <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                            <i class="fas fa-store text-purple-600"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-950">Ksh {{ number_format($totalRetailValue, 2) }}</div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Cost</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Value</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($products as $product)
                            @php $isLow = $product->stock_quantity <= $product->low_stock_threshold; @endphp
                            <tr class="hover:bg-slate-50 transition {{ $isLow ? 'bg-red-50/40' : '' }}">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500 font-mono">{{ $product->sku }}</td>
                                <td class="px-6 py-4 text-right text-slate-700">Ksh {{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 text-right text-slate-700">Ksh {{ number_format($product->cost, 2) }}</td>
                                <td class="px-6 py-4 text-right {{ $isLow ? 'text-red-600 font-bold' : 'text-slate-700' }}">
                                    @if($isLow)
                                        <i class="fas fa-triangle-exclamation text-red-500 text-xs mr-1"></i>
                                    @endif
                                    {{ number_format($product->stock_quantity) }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-slate-900">Ksh {{ number_format($product->stock_quantity * $product->cost, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($isLow)
                                        <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Low Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>In Stock
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fas fa-boxes-stacked text-4xl mb-3 block text-slate-300"></i>
                                    No products found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $products->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection