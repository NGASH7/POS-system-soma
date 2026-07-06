@extends('layouts.app')

@section('header_title', 'Products')

@section('content')
<x-soma-page title="Products" subtitle="Manage your product inventory">
    <x-slot:actions>
        <a href="{{ route('products.import.form') }}" class="soma-btn-secondary">
            <i class="fas fa-upload"></i> Import
        </a>
        <a href="{{ route('products.export') }}" class="soma-btn-secondary">
            <i class="fas fa-download"></i> Export
        </a>
        <a href="{{ route('products.create') }}" class="soma-btn-primary">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </x-slot:actions>

    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="soma-table w-full">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Cost</th>
                        <th class="text-right">Stock</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="font-semibold text-slate-950">{{ $product->name }}</div>
                            <div class="text-sm text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</div>
                        </td>
                        <td class="text-sm">{{ $product->sku }}</td>
                        <td class="text-right font-semibold text-slate-950">KES {{ number_format($product->price, 2) }}</td>
                        <td class="text-right text-slate-500">KES {{ number_format($product->cost, 2) }}</td>
                        <td class="text-right">
                            <span class="{{ $product->stock_quantity <= $product->low_stock_threshold ? 'font-bold text-rose-600' : 'text-slate-950' }}">
                                {{ number_format($product->stock_quantity) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($product->is_active)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Active</span>
                            @else
                                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('products.edit', $product) }}" class="text-blue-700 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" onclick="deleteProduct({{ $product->id }})" class="text-rose-600 hover:text-rose-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <i class="fas fa-box-open mb-3 block text-4xl"></i>
                            No products found. Click "Add Product" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $products->links() }}
        </div>
    </div>
</x-soma-page>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        const form = document.getElementById('delete-form');
        form.action = `/products/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
