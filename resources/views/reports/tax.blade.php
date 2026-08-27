@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                <i class="fas fa-file-invoice-dollar text-blue-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-slate-950">Tax Report</h1>
                <p class="text-slate-500 mt-1">Tax collection report for filing</p>
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
                <a href="{{ route('reports.export.tax', request()->query()) }}" class="bg-white border border-slate-200 text-slate-700 px-5 py-2 rounded-lg hover:bg-slate-50 transition font-medium text-sm">
                    <i class="fas fa-download mr-2 text-green-600"></i>Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="soma-card p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-slate-500 text-sm font-medium">Total Taxable Sales</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-950">Ksh {{ number_format($totalTaxableSales, 2) }}</div>
        </div>
        
        <div class="soma-card p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-amber-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-slate-500 text-sm font-medium">Tax Collected (16%)</span>
                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-amber-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-blue-700">Ksh {{ number_format($totalTax, 2) }}</div>
        </div>
        
        <div class="soma-card p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-slate-500 text-sm font-medium">Total Sales (Inc. Tax)</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-950">Ksh {{ number_format($totalSales, 2) }}</div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="soma-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">Monthly Tax Breakdown</h3>
            <span class="text-xs text-slate-500">{{ count($monthlyData) }} {{ Str::plural('month', count($monthlyData)) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Taxable Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Tax (16%)</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($monthlyData as $data)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ \Carbon\Carbon::parse($data->month . '-01')->format('F Y') }}</td>
                        <td class="px-6 py-4 text-right text-slate-700">Ksh {{ number_format($data->taxable_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-amber-700">Ksh {{ number_format($data->tax_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-900">Ksh {{ number_format($data->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <i class="fas fa-file-invoice-dollar text-4xl mb-3 block text-slate-300"></i>
                            No tax data available for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($monthlyData) > 0)
                <tfoot>
                    <tr class="bg-slate-50 border-t-2 border-slate-200 font-bold">
                        <td class="px-6 py-4 text-slate-900">Total</td>
                        <td class="px-6 py-4 text-right text-slate-900">Ksh {{ number_format($monthlyData->sum('taxable_amount'), 2) }}</td>
                        <td class="px-6 py-4 text-right text-amber-700">Ksh {{ number_format($monthlyData->sum('tax_amount'), 2) }}</td>
                        <td class="px-6 py-4 text-right text-slate-900">Ksh {{ number_format($monthlyData->sum('total_amount'), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
</div>
@endsection