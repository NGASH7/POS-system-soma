@extends('layouts.app')

@section('header_title', 'Categories')

@section('content')
<x-soma-page title="Categories" subtitle="Manage your product categories">
    <x-slot:actions>
        <a href="{{ route('categories.create') }}" class="soma-btn-primary">
            <i class="fas fa-plus"></i> Add Category
        </a>
    </x-slot:actions>

    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="soma-table w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th class="text-center">Products</th>
                        <th>Description</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="font-semibold text-slate-950">{{ $category->name }}</td>
                        <td class="text-sm text-slate-500">{{ $category->slug }}</td>
                        <td class="text-center">
                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td class="text-sm text-slate-500">{{ Str::limit($category->description, 50) }}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('categories.edit', $category) }}" class="text-blue-700 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" onclick="deleteCategory({{ $category->id }})" class="text-rose-600 hover:text-rose-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500">
                            <i class="fas fa-tags mb-3 block text-4xl"></i>
                            No categories found. Click "Add Category" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $categories->links() }}
        </div>
    </div>
</x-soma-page>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        const form = document.getElementById('delete-form');
        form.action = `/categories/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
