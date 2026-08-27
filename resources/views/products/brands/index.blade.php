@extends('layouts.app')

@section('header_title', 'Brands')

@section('content')
<x-soma-page title="Brands" subtitle="Manage product brands">
    <x-slot:actions>
        <a href="{{ route('brands.create') }}" class="soma-btn-primary">
            <i class="fas fa-plus"></i> Add Brand
        </a>
    </x-slot:actions>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium flex items-center gap-2">
            <i class="fas fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="soma-card overflow-hidden">
        @if($brands->isEmpty())
            <div class="py-20 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 mb-4">
                    <i class="fas fa-tag text-2xl text-slate-400"></i>
                </div>
                <p class="text-slate-500 font-medium">No brands yet</p>
                <p class="text-slate-400 text-sm mt-1">Add your first brand to get started</p>
                <a href="{{ route('brands.create') }}" class="soma-btn-primary mt-5 inline-flex">
                    <i class="fas fa-plus"></i> Add Brand
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="soma-table w-full">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th class="text-center">Products</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($brands as $brand)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($brand->logo)
                                        <img src="{{ Storage::url($brand->logo) }}"
                                             alt="{{ $brand->name }}"
                                             class="w-10 h-10 rounded-xl object-contain bg-slate-50 border border-slate-100 p-1">
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center border border-slate-100">
                                            <i class="fas fa-tag text-indigo-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $brand->name }}</div>
                                        @if($brand->website)
                                            <a href="{{ $brand->website }}" target="_blank"
                                               class="text-xs text-blue-500 hover:underline">
                                                {{ $brand->website }}
                                            </a>
                                        @elseif($brand->description)
                                            <div class="text-xs text-slate-400 truncate max-w-xs">{{ $brand->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $brand->products_count > 0 ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $brand->products_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($brand->is_active)
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Active</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('brands.edit', $brand) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition">
                                        <i class="fas fa-pen"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('brands.destroy', $brand) }}"
                                          onsubmit="return confirm('Delete {{ addslashes($brand->name) }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($brands->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $brands->links() }}
                </div>
            @endif
        @endif
    </div>
</x-soma-page>
@endsection
