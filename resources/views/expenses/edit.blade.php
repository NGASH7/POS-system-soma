@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-3xl">

        <div class="mb-6">
            <a href="{{ route('expenses.index') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                <i class="fas fa-arrow-left text-xs"></i> Back to expenses
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Expense</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $expense->reference_no }}</p>
        </div>

        <div class="soma-card p-6">
            <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $expense->title) }}" required class="soma-input">
                    @error('title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Category <span class="text-rose-500">*</span></label>
                        <select name="category_id" required class="soma-input">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Amount <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">KES</span>
                            <input type="number" name="amount" step="0.01" value="{{ old('amount', $expense->amount) }}" required class="soma-input pl-12">
                        </div>
                        @error('amount') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required class="soma-input">
                        @error('expense_date') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Payment method <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required class="soma-input">
                            @foreach(['cash', 'card', 'mobile_money', 'bank_transfer'] as $method)
                                <option value="{{ $method }}" {{ old('payment_method', $expense->payment_method) == $method ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $method)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Status <span class="text-rose-500">*</span></label>
                        <select name="status" required class="soma-input">
                            @foreach(['pending', 'approved', 'rejected'] as $status)
                                <option value="{{ $status }}" {{ old('status', $expense->status) == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Vendor</label>
                        <input type="text" name="vendor" value="{{ old('vendor', $expense->vendor) }}" placeholder="Optional" class="soma-input">
                        @error('vendor') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="soma-input resize-none">{{ old('description', $expense->description) }}</textarea>
                    @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Receipt</label>
                    @if($expense->receipt_image)
                        <p class="text-xs text-slate-500 mb-2">
                            <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank" class="text-blue-600 hover:underline">View current receipt</a>
                        </p>
                    @endif
                    <input type="file" name="receipt_image" accept="image/*,.pdf" class="soma-input">
                    @error('receipt_image') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('expenses.index') }}" class="soma-btn-secondary">Cancel</a>
                    <button type="submit" class="soma-btn-primary">Update expense</button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
