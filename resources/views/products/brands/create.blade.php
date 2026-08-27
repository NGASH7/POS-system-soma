@extends('layouts.app')

@section('header_title', 'Add Brand')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-xl">

        <div class="mb-6">
            <a href="{{ route('products.brands') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                <i class="fas fa-arrow-left text-xs"></i> Back to brands
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Add Brand</h1>
            <p class="text-sm text-slate-500 mt-1">Create a new product brand</p>
        </div>

        <div class="soma-card p-6">
            <form method="POST" action="{{ route('brands.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-5">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-medium text-slate-500 mb-1.5">
                            Brand name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                               class="soma-input" placeholder="e.g. Samsung, Apple, Unilever" autofocus>
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-xs font-medium text-slate-500 mb-1.5">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="soma-input resize-none" placeholder="Optional short description">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website --}}
                    <div>
                        <label for="website" class="block text-xs font-medium text-slate-500 mb-1.5">Website</label>
                        <input type="url" id="website" name="website" value="{{ old('website') }}"
                               class="soma-input" placeholder="https://example.com">
                        @error('website')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Logo --}}
                    <div>
                        <label for="logo" class="block text-xs font-medium text-slate-500 mb-1.5">Logo</label>
                        <input type="file" id="logo" name="logo" accept="image/*" class="soma-input">
                        <p class="text-xs text-slate-400 mt-1">PNG, JPG, GIF, SVG up to 2 MB</p>
                        @error('logo')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active --}}
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700 font-medium">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6 pt-5 border-t border-slate-100">
                    <a href="{{ route('products.brands') }}" class="soma-btn-secondary">Cancel</a>
                    <button type="submit" class="soma-btn-primary">
                        <i class="fas fa-plus"></i> Create Brand
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
