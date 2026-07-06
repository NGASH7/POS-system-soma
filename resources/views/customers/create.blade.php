@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Add Customer</h1>
        <p class="text-sm text-slate-500">Create a new customer profile</p>
    </div>
    <a href="{{ route('customers.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
        <i class="fas fa-arrow-left mr-2"></i>Back to Customers
    </a>
</div>

<div class="mx-auto max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <form action="{{ route('customers.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="mb-6 grid gap-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-bold text-slate-700">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-xl border-slate-200 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full rounded-xl border-slate-200 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 @error('phone') border-rose-500 @enderror">
                @error('phone')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full rounded-xl border-slate-200 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 @error('email') border-rose-500 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">Loyalty Card ID</label>
                <input type="text" name="loyalty_card" value="{{ old('loyalty_card') }}"
                       class="w-full rounded-xl border-slate-200 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 @error('loyalty_card') border-rose-500 @enderror">
                @error('loyalty_card')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">Credit Limit (KES)</label>
                <input type="number" step="0.01" name="credit_limit" value="{{ old('credit_limit', 0) }}"
                       class="w-full rounded-xl border-slate-200 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 @error('credit_limit') border-rose-500 @enderror">
                @error('credit_limit')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t border-slate-100 pt-6">
            <a href="{{ route('customers.index') }}" class="rounded-xl px-6 py-2.5 font-bold text-slate-600 transition hover:bg-slate-100">
                Cancel
            </a>
            <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 font-bold text-white transition hover:bg-blue-700">
                Save Customer
            </button>
        </div>
    </form>
</div>
@endsection
