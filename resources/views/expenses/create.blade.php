@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-3xl">

        <div class="mb-6">
            <a href="{{ route('expenses.index') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                <i class="fas fa-arrow-left text-xs"></i> Back to expenses
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Add Expense</h1>
            <p class="text-sm text-slate-500 mt-1">Record a new business expense</p>
        </div>

        @if($categories->isEmpty())
        <div class="soma-card p-4 mb-6 border-amber-200 bg-amber-50">
            <p class="text-sm text-amber-800">
                No categories yet.
                <a href="{{ route('expenses.categories') }}" class="font-medium underline">Create a category</a>
                before adding expenses.
            </p>
        </div>
        @endif

        <div class="soma-card p-6 {{ $categories->isEmpty() ? 'opacity-60 pointer-events-none' : '' }}">
            <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Office rent" class="soma-input">
                    @error('title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Category <span class="text-rose-500">*</span></label>
                        <select name="category_id" required class="soma-input">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                            <input type="number" name="amount" step="0.01" value="{{ old('amount') }}" required placeholder="0.00"
                                   class="soma-input pl-12">
                        </div>
                        @error('amount') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required class="soma-input">
                        @error('expense_date') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Payment method <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required class="soma-input">
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                            <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                        @error('payment_method') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Vendor</label>
                    <input type="text" name="vendor" value="{{ old('vendor') }}" placeholder="Optional" class="soma-input">
                    @error('vendor') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Description</label>
                    <textarea name="description" rows="3" placeholder="Optional notes" class="soma-input resize-none">{{ old('description') }}</textarea>
                    @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Receipt</label>
                    <input type="file" name="receipt_image" accept="image/*,.pdf"
                           class="soma-input file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200">
                    <p class="text-xs text-slate-400 mt-1">Image or PDF, max 5MB</p>
                    @error('receipt_image') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_recurring" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" {{ old('is_recurring') ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">Recurring expense</span>
                    </label>

                    <div id="recurring-fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 {{ old('is_recurring') ? '' : 'hidden' }}">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Frequency</label>
                            <select name="recurring_frequency" class="soma-input">
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly" selected>Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">End date</label>
                            <input type="date" name="recurring_end_date" class="soma-input">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('expenses.index') }}" class="soma-btn-secondary">Cancel</a>
                    <button type="submit" class="soma-btn-primary">Save expense</button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.querySelector('input[name="is_recurring"]').addEventListener('change', function() {
        document.getElementById('recurring-fields').classList.toggle('hidden');
    });
</script>
@endsection
