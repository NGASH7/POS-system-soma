{{-- resources/views/reports/purchase-sale.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Purchase & Sale Report</h1>
                    <p class="text-gray-600 mt-1">Compare purchases against sales performance</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <button onclick="window.print()" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
                    <i class="fas fa-print mr-2 text-gray-500"></i>Print
                </button>
                <a href="{{ route('reports.export.purchase-sale', request()->all()) }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition font-medium">
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
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Report Type</label>
                <select name="report_type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[140px]">
                    <option value="daily" {{ request('report_type', 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('report_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm font-medium text-sm">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('reports.purchase-sale') }}" class="bg-white text-gray-700 border border-gray-300 px-5 py-2 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Sales</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-green-600">KES {{ number_format($summary['total_sales'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ number_format($summary['total_invoices'] ?? 0) }} transactions</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Purchases</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-box text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-blue-600">KES {{ number_format($summary['total_purchases'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ number_format($summary['total_units_sold'] ?? 0) }} units sold</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Gross Profit</span>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-600">KES {{ number_format($summary['gross_profit'] ?? 0, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">COGS: KES {{ number_format($summary['total_cost_of_goods'] ?? 0, 2) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full 
                @if(($summary['profit_margin'] ?? 0) > 20) bg-green-500
                @elseif(($summary['profit_margin'] ?? 0) > 10) bg-yellow-500
                @else bg-red-500
                @endif"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Profit Margin</span>
                <div class="w-9 h-9 rounded-lg flex items-center justify-center
                    @if(($summary['profit_margin'] ?? 0) > 20) bg-green-50
                    @elseif(($summary['profit_margin'] ?? 0) > 10) bg-yellow-50
                    @else bg-red-50
                    @endif">
                    <i class="fas fa-percentage
                        @if(($summary['profit_margin'] ?? 0) > 20) text-green-600
                        @elseif(($summary['profit_margin'] ?? 0) > 10) text-yellow-600
                        @else text-red-600
                        @endif"></i>
                </div>
            </div>
            <div class="text-2xl font-bold 
                @if(($summary['profit_margin'] ?? 0) > 20) text-green-600
                @elseif(($summary['profit_margin'] ?? 0) > 10) text-yellow-600
                @else text-red-600
                @endif">
                {{ number_format($summary['profit_margin'] ?? 0, 1) }}%
            </div>
            <div class="text-sm text-gray-500 mt-1">Sales to Purchases ratio</div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ ucfirst($reportType) }} Breakdown
            </h3>
            <span class="text-xs text-gray-500">{{ count($data) }} {{ Str::plural('period', count($data)) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Sales</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Purchases</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Net</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Profit/Loss</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $row['period'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-green-600 font-semibold">KES {{ number_format($row['sales_amount'], 2) }}</span>
                            <div class="text-xs text-gray-500">({{ $row['sales_count'] }} invoices)</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-blue-600 font-semibold">KES {{ number_format($row['purchases_amount'], 2) }}</span>
                            <div class="text-xs text-gray-500">({{ $row['purchases_count'] }} orders)</div>
                        </td>
                        <td class="px-6 py-4 text-center font-semibold
                            @if($row['sales_amount'] > $row['purchases_amount']) text-green-600
                            @elseif($row['sales_amount'] < $row['purchases_amount']) text-red-600
                            @else text-gray-600
                            @endif">
                            KES {{ number_format($row['sales_amount'] - $row['purchases_amount'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-center font-semibold
                            @if($row['profit'] > 0) text-green-600
                            @elseif($row['profit'] < 0) text-red-600
                            @else text-gray-600
                            @endif">
                            KES {{ number_format($row['profit'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($row['sales_amount'] > 0)
                                @php $margin = ($row['profit'] / $row['sales_amount']) * 100; @endphp
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full 
                                    @if($margin > 20) bg-green-100 text-green-800
                                    @elseif($margin > 10) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ number_format($margin, 1) }}%
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">0%</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-chart-bar text-4xl mb-3 block text-gray-300"></i>
                            No data available for the selected period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($data) > 0)
                <tfoot class="bg-gray-50 font-semibold border-t-2 border-gray-200">
                    <tr>
                        <td class="px-6 py-4 text-gray-900">Total</td>
                        <td class="px-6 py-4 text-center text-green-700">KES {{ number_format(collect($data)->sum('sales_amount'), 2) }}</td>
                        <td class="px-6 py-4 text-center text-blue-700">KES {{ number_format(collect($data)->sum('purchases_amount'), 2) }}</td>
                        <td class="px-6 py-4 text-center text-gray-700">KES {{ number_format(collect($data)->sum('sales_amount') - collect($data)->sum('purchases_amount'), 2) }}</td>
                        <td class="px-6 py-4 text-center text-gray-700">KES {{ number_format(collect($data)->sum('profit'), 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            @php $totalSales = collect($data)->sum('sales_amount'); @endphp
                            @if($totalSales > 0)
                                @php $totalMargin = (collect($data)->sum('profit') / $totalSales) * 100; @endphp
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full 
                                    @if($totalMargin > 20) bg-green-100 text-green-800
                                    @elseif($totalMargin > 10) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ number_format($totalMargin, 1) }}%
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">0%</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection