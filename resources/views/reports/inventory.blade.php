@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Inventory Report</h2>
            
            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-slate-50 p-4 rounded-lg">
                    <div class="text-sm text-slate-500">Total Inventory Value (Cost)</div>
                    <div class="text-2xl font-bold text-slate-900">Ksh {{ number_format($totalValue, 2) }}</div>
                </div>
                <div class="bg-slate-50 p-4 rounded-lg">
                    <div class="text-sm text-slate-500">Total Inventory Value (Retail)</div>
                    <div class="text-2xl font-bold text-slate-900">Ksh {{ number_format($totalRetailValue, 2) }}</div>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">SKU</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Price</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Cost</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Stock</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Value</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($products as $product)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $product->sku }}</td>
                            <td class="px-4 py-3 text-right">Ksh {{ number_format($product->price, 2) }}</td>
                            <td class="px-4 py-3 text-right">Ksh {{ number_format($product->cost, 2) }}</td>
                            <td class="px-4 py-3 text-right @if($product->stock_quantity <= $product->low_stock_threshold) text-red-600 font-bold @endif">
                                {{ number_format($product->stock_quantity) }}
                            </td>
                            <td class="px-4 py-3 text-right">Ksh {{ number_format($product->stock_quantity * $product->cost, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($product->stock_quantity <= $product->low_stock_threshold)
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Low Stock</span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">In Stock</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
    </div>
</div>
@endsection