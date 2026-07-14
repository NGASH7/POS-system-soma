@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-edit text-purple-600 mr-2"></i>Edit Credit
        </h1>
        <p class="text-gray-600 mt-2">Update customer credit information</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('credits.update', $credit) }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Customer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                    <select name="customer_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $credit->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->phone ?? 'No phone' }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500"></span>
                        <input type="number" name="total_amount" step="0.01" value="{{ old('total_amount', $credit->total_amount) }}" required
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    @error('total_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Paid Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Paid Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500"></span>
                        <input type="number" name="paid_amount" step="0.01" value="{{ old('paid_amount', $credit->paid_amount) }}" readonly
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Paid amount is managed through payment records.</p>
                </div>
                
                <!-- Balance (Calculated) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Balance (Calculated)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500"></span>
                        <input type="text" value="{{ number_format($credit->balance, 2) }}" readonly
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg bg-gray-50 font-semibold">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Balance is automatically calculated: Total - Paid</p>
                </div>
                
                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $credit->due_date ? $credit->due_date->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="direct_credit" {{ old('type', $credit->type) == 'direct_credit' ? 'selected' : '' }}>Direct Credit</option>
                        <option value="layaway" {{ old('type', $credit->type) == 'layaway' ? 'selected' : '' }}>Layaway</option>
                        <option value="invoice" {{ old('type', $credit->type) == 'invoice' ? 'selected' : '' }}>Invoice</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="active" {{ old('status', $credit->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="overdue" {{ old('status', $credit->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="completed" {{ old('status', $credit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $credit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">{{ old('notes', $credit->notes) }}</textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Credit Info -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Credit Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Reference:</span>
                            <span class="font-medium">{{ $credit->reference }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="font-medium">{{ $credit->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created By:</span>
                            <span class="font-medium">{{ $credit->user->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Last Updated:</span>
                            <span class="font-medium">{{ $credit->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('credits.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-save mr-2"></i>Update Credit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection