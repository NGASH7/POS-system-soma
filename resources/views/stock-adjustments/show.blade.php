@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-arrow-up-wide-short text-blue-600 mr-2"></i>Adjustment Details
                </h1>
                <p class="text-gray-600 mt-2">Reference: {{ $stockAdjustment->adjustment_no }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('stock-adjustments.index') }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                @if($stockAdjustment->status !== 'completed' && $stockAdjustment->created_at->diffInDays(now()) < 1)
                    <a href="{{ route('stock-adjustments.edit', $stockAdjustment) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm font-medium">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif
                @if($stockAdjustment->status !== 'completed')
                    <form method="POST" action="{{ route('stock-adjustments.complete', $stockAdjustment) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm font-medium" onclick="return confirm('Complete this stock adjustment? This will update the product stock.')">
                            <i class="fas fa-check mr-2"></i>Complete Adjustment
                        </button>
                    </form>
                @endif
                @if($stockAdjustment->status === 'pending' && $stockAdjustment->created_at->diffInDays(now()) < 1)
                    <form method="POST" action="{{ route('stock-adjustments.cancel', $stockAdjustment) }}" class="inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition shadow-sm font-medium" onclick="return confirm('Cancel this stock adjustment?')">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @if($stockAdjustment->status === 'pending')
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-clock text-yellow-600 text-xl mr-3"></i>
            <div>
                <h4 class="font-semibold text-yellow-800">Pending Adjustment</h4>
                <p class="text-sm text-yellow-700">This adjustment is pending. Click "Complete Adjustment" to apply the stock change.</p>
            </div>
        </div>
    </div>
    @elseif($stockAdjustment->status === 'completed')
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
            <div>
                <h4 class="font-semibold text-green-800">Adjustment Completed</h4>
                <p class="text-sm text-green-700">This adjustment has been applied to the product stock.</p>
            </div>
        </div>
    </div>
    @elseif($stockAdjustment->status === 'cancelled')
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-times-circle text-red-600 text-xl mr-3"></i>
            <div>
                <h4 class="font-semibold text-red-800">Adjustment Cancelled</h4>
                <p class="text-sm text-red-700">This adjustment has been cancelled and was not applied.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">Product</div>
            <div class="text-lg font-bold text-gray-900">{{ $stockAdjustment->product->name ?? 'N/A' }}</div>
            <div class="text-sm text-gray-500">SKU: {{ $stockAdjustment->product->sku ?? 'N/A' }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">Quantity</div>
            <div class="text-lg font-bold {{ $stockAdjustment->type === 'increase' ? 'text-green-600' : 'text-red-600' }}">
                {{ $stockAdjustment->type === 'increase' ? '+' : '-' }}{{ $stockAdjustment->quantity }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">Status</div>
            <div class="text-lg font-bold">
                <span class="text-xs px-2 py-1 rounded-full 
                    {{ $stockAdjustment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                       ($stockAdjustment->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                       'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($stockAdjustment->status) }}
                </span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">Adjustment Type</div>
            <div class="text-lg font-bold">
                <span class="text-xs px-2 py-1 rounded-full {{ $stockAdjustment->type_badge }}">
                    {{ ucfirst($stockAdjustment->type) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-circle-info text-blue-600 mr-2"></i>Adjustment Details
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Reference:</span>
                    <span class="font-medium">{{ $stockAdjustment->adjustment_no }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Type:</span>
                    <span class="font-medium">{{ ucfirst($stockAdjustment->type) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Reason:</span>
                    <span class="font-medium">{{ $stockAdjustment->reason_label }}</span>
                </div>
                @if($stockAdjustment->reason_description)
                <div class="flex justify-between">
                    <span class="text-gray-500">Reason Details:</span>
                    <span class="font-medium">{{ $stockAdjustment->reason_description }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Date:</span>
                    <span class="font-medium">{{ $stockAdjustment->adjustment_date->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Created:</span>
                    <span class="font-medium">{{ $stockAdjustment->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Created By:</span>
                    <span class="font-medium">{{ $stockAdjustment->user->name ?? 'N/A' }}</span>
                </div>
                @if($stockAdjustment->completed_at)
                <div class="flex justify-between">
                    <span class="text-gray-500">Completed:</span>
                    <span class="font-medium">{{ $stockAdjustment->completed_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-warehouse text-blue-600 mr-2"></i>Stock Impact
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Old Stock:</span>
                    <span class="font-medium">{{ number_format($stockAdjustment->old_stock) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Adjustment:</span>
                    <span class="font-medium {{ $stockAdjustment->type === 'increase' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stockAdjustment->type === 'increase' ? '+' : '-' }}{{ $stockAdjustment->quantity }}
                    </span>
                </div>
                <div class="flex justify-between border-t border-gray-100 pt-2 font-bold">
                    <span>New Stock:</span>
                    <span class="text-blue-600">{{ number_format($stockAdjustment->new_stock) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500 mt-2 pt-2 border-t border-gray-100">
                    <span>Status:</span>
                    <span class="{{ $stockAdjustment->status === 'completed' ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $stockAdjustment->status === 'completed' ? '✅ Applied' : '⏳ Pending' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($stockAdjustment->notes)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">
            <i class="fas fa-note-sticky text-blue-600 mr-2"></i>Notes
        </h3>
        <p class="text-gray-600">{{ $stockAdjustment->notes }}</p>
    </div>
    @endif
</div>
@endsection