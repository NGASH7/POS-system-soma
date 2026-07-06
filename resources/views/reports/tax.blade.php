@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">Tax Report</h1>
        <p class="text-slate-500 mt-2">Tax collection report for filing</p>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                       class="border border-slate-200 rounded-xl px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                       class="border border-slate-200 rounded-xl px-3 py-2">
            </div>
            <div>
                <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-xl hover:bg-blue-800">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
            <div>
                <a href="{{ route('reports.export.tax', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export Excel
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="soma-card p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-slate-500 text-sm">Total Taxable Sales</span>
                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-slate-950">Ksh {{ number_format($totalTaxableSales, 2) }}</div>
        </div>
        
        <div class="soma-card p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-slate-500 text-sm">Tax Collected (16%)</span>
                <i class="fas fa-file-invoice-dollar text-yellow-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-blue-700">Ksh {{ number_format($totalTax, 2) }}</div>
        </div>
        
        <div class="soma-card p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-slate-500 text-sm">Total Sales (Inc. Tax)</span>
                <i class="fas fa-dollar-sign text-green-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-slate-950">Ksh {{ number_format($totalSales, 2) }}</div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    <div class="soma-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-900">Monthly Tax Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Month</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Taxable Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Tax (16%)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($monthlyData as $data)
                    <tr>
                        <td class="px-6 py-4 font-medium">{{ \Carbon\Carbon::parse($data->month . '-01')->format('F Y') }}</td>
                        <td class="px-6 py-4 text-right">Ksh {{ number_format($data->taxable_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-blue-700">Ksh {{ number_format($data->tax_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right">Ksh {{ number_format($data->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-100 font-bold">
                    <tr>
                        <td class="px-6 py-4">Total</td>
                        <td class="px-6 py-4 text-right">Ksh {{ number_format($monthlyData->sum('taxable_amount'), 2) }}</td>
                        <td class="px-6 py-4 text-right text-blue-700">Ksh {{ number_format($monthlyData->sum('tax_amount'), 2) }}</td>
                        <td class="px-6 py-4 text-right">Ksh {{ number_format($monthlyData->sum('total_amount'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</div>
@endsection