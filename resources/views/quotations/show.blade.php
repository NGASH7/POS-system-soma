@extends('layouts.app')

@section('content')
<div class="container-fluid max-w-6xl mx-auto py-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-gray-900">{{ $quotation->quotation_no }}</h1>
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $quotation->progress }}">
                    {{ ucfirst($quotation->status) }}
                </span>
            </div>
            <p class="text-gray-500 text-sm mt-1">Created on {{ $quotation->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quotations.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <a href="{{ route('quotations.print', $quotation) }}" target="_blank" class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-print mr-2"></i>Print
            </a>
            @if(!in_array($quotation->status, ['converted', 'cancelled']))
            <a href="{{ route('quotations.edit', $quotation) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @endif
            @if(in_array($quotation->status, ['draft', 'sent', 'approved']))
            <form method="POST" action="{{ route('quotations.convert', $quotation) }}" class="inline">
                @csrf
                <button type="submit" onclick="return confirm('Convert this quotation to a sale?')"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-exchange-alt mr-2"></i>Convert to Sale
                </button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Quotation Details -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Quotation Details
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 block">Quotation Date</span>
                    <span class="font-semibold text-gray-900">{{ $quotation->quotation_date->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Expiry Date</span>
                    <span class="font-semibold {{ $quotation->isExpired() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $quotation->expiry_date ? $quotation->expiry_date->format('d/m/Y') : 'N/A' }}
                        @if($quotation->isExpired()) <span class="text-xs">(Expired)</span> @endif
                    </span>
                </div>
                <div>
                    <span class="text-gray-500 block">Created By</span>
                    <span class="font-semibold text-gray-900">{{ $quotation->user->name ?? 'N/A' }}</span>
                </div>
            </div>

            @if($quotation->notes)
            <div class="mt-4 border-t border-gray-100 pt-4">
                <span class="text-gray-500 text-sm font-medium block mb-1">Notes</span>
                <div class="bg-yellow-50 rounded-lg p-3 text-sm text-gray-700 whitespace-pre-line border border-yellow-100">{{ $quotation->notes }}</div>
            </div>
            @endif

            @if($quotation->terms)
            <div class="mt-4 border-t border-gray-100 pt-4">
                <span class="text-gray-500 text-sm font-medium block mb-1">Terms & Conditions</span>
                <div class="bg-gray-50 rounded-lg p-3 text-sm text-gray-700 whitespace-pre-line">{{ $quotation->terms }}</div>
            </div>
            @endif
        </div>

        <!-- Customer & Totals -->
        <div class="space-y-4">
            <!-- Customer -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-2 mb-3">
                    <i class="fas fa-user text-blue-600 mr-2"></i>Customer
                </h3>
                @if($quotation->customer)
                <div class="text-sm space-y-1">
                    <div class="font-semibold text-gray-900">{{ $quotation->customer->name }}</div>
                    <div class="text-gray-500"><i class="fas fa-phone text-gray-400 mr-1"></i> {{ $quotation->customer->phone ?? 'N/A' }}</div>
                    <div class="text-gray-500"><i class="fas fa-envelope text-gray-400 mr-1"></i> {{ $quotation->customer->email ?? 'N/A' }}</div>
                </div>
                @else
                <p class="text-sm text-gray-500">Walk-in Customer</p>
                @endif
            </div>

            <!-- Totals -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-2 mb-3">
                    <i class="fas fa-calculator text-blue-600 mr-2"></i>Totals
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium">KES {{ number_format($quotation->subtotal, 2) }}</span>
                    </div>
                    @if($quotation->discount > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Discount</span>
                        <span>- KES {{ number_format($quotation->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tax (16%)</span>
                        <span class="font-medium">KES {{ number_format($quotation->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                        <span>Total</span>
                        <span class="text-blue-600">KES {{ number_format($quotation->total, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($quotation->status === 'converted' && $quotation->convertedSale)
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 text-sm">
                <div class="font-semibold text-purple-800 mb-1"><i class="fas fa-check-circle mr-1"></i> Converted to Sale</div>
                <div class="text-purple-600">Invoice: {{ $quotation->convertedSale->invoice_no }}</div>
                <div class="text-purple-500 text-xs">{{ $quotation->converted_at?->format('d/m/Y H:i') }}</div>
                <a href="{{ route('pos.receipt', $quotation->convertedSale->id) }}" class="inline-block mt-2 text-purple-700 hover:text-purple-900 font-medium text-xs">
                    <i class="fas fa-receipt mr-1"></i>View sale receipt
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 mb-4">
            <i class="fas fa-list text-blue-600 mr-2"></i>Line Items
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Unit Price</th>
                        <th class="px-4 py-3 text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($quotation->items ?? [] as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item['product_name'] ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-center">{{ $item['quantity'] }}</td>
                        <td class="px-4 py-3 text-right">KES {{ number_format($item['price'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">KES {{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">No line items on this quotation.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-gray-200">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-900">Grand Total:</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600 text-base">KES {{ number_format($quotation->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Status Update Form (for non-converted) -->
    @if(!in_array($quotation->status, ['converted']))
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-md font-bold text-gray-900 mb-3"><i class="fas fa-tag mr-2 text-blue-600"></i>Update Status</h2>
        <form method="POST" action="{{ route('quotations.status', $quotation) }}" class="flex items-center gap-4">
            @csrf
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2">
                @foreach(['draft','sent','approved','expired','cancelled'] as $s)
                <option value="{{ $s }}" {{ $quotation->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                Update Status
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
