@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-slate-950">Profit & Loss Report</h1>
                <p class="text-slate-500 mt-1">Track your business profitability</p>
            </div>
        </div>
        <div class="text-sm text-slate-500 bg-slate-50 border border-slate-200 rounded-lg px-4 py-2">
            <i class="far fa-calendar mr-2 text-slate-400"></i>
            {{ $startDate->format('M d, Y') }} &nbsp;–&nbsp; {{ $endDate->format('M d, Y') }}
        </div>
    </div>

    <!-- Date Filter -->
    <div class="soma-card p-5 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                       class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                       class="border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition shadow-sm font-medium text-sm">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('reports.export.profit-loss', request()->query()) }}" class="bg-white border border-slate-200 text-slate-700 px-5 py-2 rounded-lg hover:bg-slate-50 transition font-medium text-sm">
                    <i class="fas fa-download mr-2 text-green-600"></i>Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="soma-card p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-slate-500 text-sm font-medium">Total Revenue</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-950">Ksh {{ number_format($totalRevenue, 2) }}</div>
        </div>
        
        <div class="soma-card p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-slate-500 text-sm font-medium">Total Cost</span>
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fas fa-boxes text-red-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-950">Ksh {{ number_format($totalCost, 2) }}</div>
        </div>
        
        <div class="soma-card p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-slate-500 text-sm font-medium">Gross Profit</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold {{ $grossProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">Ksh {{ number_format($grossProfit, 2) }}</div>
        </div>
        
        <div class="soma-card p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-slate-500 text-sm font-medium">Profit Margin</span>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-percent text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-700">{{ number_format($profitMargin, 1) }}%</div>
        </div>
    </div>

    <!-- Daily Breakdown Chart -->
    <div class="soma-card p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-slate-900">Daily Profit Breakdown</h3>
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>Revenue</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>Profit</span>
            </div>
        </div>
        <canvas id="profitChart" height="300"></canvas>
    </div>

    <!-- Daily Data Table -->
    <div class="soma-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">Daily Details</h3>
            <span class="text-xs text-slate-500">{{ count($dailyData) }} {{ Str::plural('day', count($dailyData)) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Profit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dailyData as $data)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ \Carbon\Carbon::parse($data['date'])->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right text-green-600">Ksh {{ number_format($data['revenue'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-red-600">Ksh {{ number_format($data['cost'], 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold {{ $data['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Ksh {{ number_format($data['profit'], 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($data['revenue'] > 0)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ ($data['profit'] / $data['revenue']) * 100 >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ number_format(($data['profit'] / $data['revenue']) * 100, 1) }}%
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">0%</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <i class="fas fa-chart-pie text-4xl mb-3 block text-slate-300"></i>
                            No data available for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($dailyData) > 0)
                <tfoot>
                    <tr class="bg-slate-50 border-t-2 border-slate-200">
                        <td class="px-6 py-4 font-bold text-slate-900">Total</td>
                        <td class="px-6 py-4 text-right font-bold text-green-700">Ksh {{ number_format(collect($dailyData)->sum('revenue'), 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-red-700">Ksh {{ number_format(collect($dailyData)->sum('cost'), 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold {{ collect($dailyData)->sum('profit') >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            Ksh {{ number_format(collect($dailyData)->sum('profit'), 2) }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-slate-700">
                            {{ number_format($profitMargin, 1) }}%
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('profitChart').getContext('2d');
    const dailyData = @json($dailyData);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [
                {
                    label: 'Revenue',
                    data: dailyData.map(d => d.revenue),
                    backgroundColor: 'rgba(34, 197, 94, 0.5)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Profit',
                    data: dailyData.map(d => d.profit),
                    backgroundColor: 'rgba(79, 70, 229, 0.5)',
                    borderColor: 'rgb(79, 70, 229)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.15)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Ksh ' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush