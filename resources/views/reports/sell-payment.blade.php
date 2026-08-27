{{-- resources/views/reports/sell-payment.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Sell Payment Report</h1>
                    <p class="text-gray-600 mt-1">Track sales by payment method</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                    <i class="fas fa-print mr-2 text-gray-500"></i>Print
                </button>
                <a href="{{ route('reports.export.sell-payment', request()->all()) }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                    <i class="fas fa-file-excel mr-2 text-green-600"></i>Export
                </a>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Payment Method</label>
                <select name="payment_method" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[170px]">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                    <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>Credit</option>
                </select>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm font-medium text-sm">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('reports.sell-payment') }}" class="bg-white text-gray-700 border border-gray-300 px-5 py-2 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Transactions</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($summary['total_transactions'] ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Amount</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-green-600">KES {{ number_format($summary['total_amount'] ?? 0, 2) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Average Transaction</span>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-chart-bar text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-600">KES {{ number_format($summary['average_amount'] ?? 0, 2) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Most Used Method</span>
                <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <i class="fas fa-credit-card text-indigo-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-indigo-600">
                @php
                    $maxMethod = $paymentBreakdown->sortByDesc('total_amount')->first();
                @endphp
                {{ $maxMethod ? ucwords(str_replace('_', ' ', $maxMethod['method'] ?? 'N/A')) : 'N/A' }}
            </div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method Breakdown</h3>
            <div class="space-y-4">
                @foreach(['cash', 'card', 'mobile_money', 'credit'] as $method)
                    @php
                        $data = $paymentBreakdown->get($method);
                        $percentage = $summary['total_amount'] > 0 ? ($data['total_amount'] / $summary['total_amount']) * 100 : 0;
                        $barColor = match($method) {
                            'cash' => 'bg-green-500',
                            'card' => 'bg-blue-500',
                            'mobile_money' => 'bg-purple-500',
                            default => 'bg-orange-500',
                        };
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="font-medium text-gray-700">{{ ucwords(str_replace('_', ' ', $method)) }}</span>
                            <span class="text-gray-600">KES {{ number_format($data['total_amount'] ?? 0, 2) }} <span class="text-gray-400">({{ number_format($percentage, 1) }}%)</span></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $barColor }} transition-all"
                                style="width: {{ $percentage }}%">
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1.5">
                            <span>{{ number_format($data['count'] ?? 0) }} transactions</span>
                            <span>Avg: KES {{ number_format($data['average_amount'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Trend</h3>
            <div class="h-48 flex items-end space-x-1">
                @php
                    $maxDailyTotal = max(array_column($dailyData, 'total')) ?: 1;
                @endphp
                @foreach($dailyData as $day)
                    <div class="flex-1 flex flex-col items-center group relative">
                        <div class="absolute -top-7 opacity-0 group-hover:opacity-100 transition bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap pointer-events-none">
                            KES {{ number_format($day['total'], 0) }}
                        </div>
                        <div class="w-full bg-blue-500 group-hover:bg-blue-600 rounded-t transition-colors" 
                             style="height: {{ ($day['total'] / $maxDailyTotal) * 100 }}%">
                        </div>
                        <span class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($day['date'])->format('d') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentSales as $sale)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">#{{ $sale->invoice_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                @if($sale->payment_method == 'cash') bg-green-100 text-green-800
                                @elseif($sale->payment_method == 'card') bg-blue-100 text-blue-800
                                @elseif($sale->payment_method == 'mobile_money') bg-purple-100 text-purple-800
                                @else bg-orange-100 text-orange-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $sale->payment_method)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">KES {{ number_format($sale->paid, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full bg-green-100 text-green-800 font-medium">Completed</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-receipt text-4xl mb-3 block text-gray-300"></i>
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection