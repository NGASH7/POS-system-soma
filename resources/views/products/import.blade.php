@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">Import Products</h1>
        <p class="text-slate-500 mt-2">Bulk import products from Excel or CSV file</p>
    </div>

    <div class="soma-card p-6">
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-blue-800 mb-2">Instructions:</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>✓ Download the sample template below</li>
                <li>✓ Fill in your product data</li>
                <li>✓ Upload the file (Excel or CSV format)</li>
                <li>✓ Maximum file size: 5MB</li>
            </ul>
        </div>
        
        <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Choose File</label>
                <input type="file" name="file" accept=".xlsx,.csv" required
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="flex justify-between items-center">
                <a href="#" class="text-blue-700 hover:text-blue-700 text-sm">
                    <i class="fas fa-download mr-1"></i>Download Sample Template
                </a>
                <div class="space-x-3">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-700 text-white rounded-xl hover:bg-blue-800 transition">
                        <i class="fas fa-upload mr-2"></i>Import Products
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
@endsection