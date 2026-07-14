@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-file-invoice text-blue-600 mr-2"></i>Quotations
                </h1>
                <p class="text-gray-600 mt-2">Manage customer quotations and estimates</p>
            </div>
            <a href="{{ route('quotations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>New Quotation
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Total</div>
            <div class="text-xl font-bold text-gray-900">{{ number_format($summary['total']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Draft</div>
            <div class="text-xl font-bold text-gray-600">{{ number_format($summary['draft']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Sent</div>
            <div class="text-xl font-bold text-blue-600">{{ number_format($summary['sent']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Approved</div>
            <div class="text-xl font-bold text-green-600">{{ number_format($summary['approved']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Converted</div>
            <div class="text-xl font-bold text-purple-600">{{ number_format($summary['converted']) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Expired</div>
            <div class="text-xl font-bold text-red-600">{{ number_format($summary['expired']) }}</div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Quotation #..." class="border border-gray-300 rounded-lg px-3 py-2 min-w-[200px]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('quotations.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 ml-2">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quotation #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($quotations as $quotation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $quotation->quotation_no }}</td>
                        <td class="px-6 py-4">{{ $quotation->customer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-right font-semibold">KES {{ number_format($quotation->total, 2) }}</td>
                        <td class="px-6 py-4 text-sm">{{ $quotation->quotation_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ $quotation->expiry_date ? $quotation->expiry_date->format('d/m/Y') : 'N/A' }}
                            @if($quotation->isExpired())
                                <span class="text-red-600 text-xs block">Expired!</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2 py-1 rounded-full {{ $quotation->progress }}">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('quotations.show', $quotation) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(!in_array($quotation->status, ['converted', 'expired', 'cancelled']))
                                    <a href="{{ route('quotations.edit', $quotation) }}" class="text-indigo-600 hover:text-indigo-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('quotations.convert', $quotation) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800" onclick="return confirm('Convert this quotation to a sale?')">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('quotations.print', $quotation) }}" target="_blank" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-file-invoice text-4xl mb-3 block"></i>
                            No quotations found. Click "New Quotation" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $quotations->links() }}
        </div>
    </div>
</div>

<form id="status-form" method="POST" style="display: none;">
    @csrf
</form>
@endsection