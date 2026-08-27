@extends('layouts.app')

@section('header_title', 'Edit Brand')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-xl">

        <div class="mb-6">
            <a href="{{ route('products.brands') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                <i class="fas fa-arrow-left text-xs"></i> Back to brands
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Brand</h1>
            <p class="text-sm text-slate-500 mt-1">Update brand information</p>
        </div>

        <div class="soma-card p-6">
            <form method="POST" action="{{ route('brands.update', $brand) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    {{-- Logo preview --}}
                    @if($brand->logo)
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <img src="{{ Storage::url($brand->logo) }}" alt="{{ $brand->name }}"
                             class="w-16 h-16 object-contain rounded-lg border border-slate-200 bg-white p-1">
                        <div>
                            <p class="text-xs font-medium text-slate-700">Current logo</p>
                            <p class="text-xs text-slate-400 mt-0.5">Upload a new image to replace it</p>
                        </div>
                    </div>
                    @endif

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-medium text-slate-500 mb-1.5">
                            Brand name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $brand->name) }}"
                               class="soma-input" autofocus>
                        @error('name')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-xs font-medium text-slate-500 mb-1.5">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="soma-input resize-none">{{ old('description', $brand->description) }}</textarea>
                        @error('description')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website --}}
                    <div>
                        <label for="website" class="block text-xs font-medium text-slate-500 mb-1.5">Website</label>
                        <input type="url" id="website" name="website" value="{{ old('website', $brand->website) }}"
                               class="soma-input" placeholder="https://example.com">
                        @error('website')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Logo --}}
                    <div>
                        <label for="logo" class="block text-xs font-medium text-slate-500 mb-1.5">
                            {{ $brand->logo ? 'Replace logo' : 'Logo' }}
                        </label>
                        <input type="file" id="logo" name="logo" accept="image/*" class="soma-input">
                        <p class="text-xs text-slate-400 mt-1">PNG, JPG, GIF, SVG up to 2 MB</p>
                        @error('logo')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active --}}
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $brand->is_active) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-700 font-medium">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between gap-2 mt-6 pt-5 border-t border-slate-100">
                    <form method="POST" action="{{ route('brands.destroy', $brand) }}"
                          onsubmit="return confirm('Delete this brand? Products using it will lose their brand assignment.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                    <div class="flex gap-2">
                        <a href="{{ route('products.brands') }}" class="soma-btn-secondary">Cancel</a>
                        <button type="submit" class="soma-btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
