@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">Employee Performance Report</h1>
        <p class="text-slate-500 mt-2">Track employee sales performance</p>
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
        </form>
    </div>

    <!-- Performance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($performanceData as $data)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg">{{ $data['employee']->name }}</h3>
                        <p class="text-blue-200 text-sm">{{ $data['employee']->email }}</p>
                    </div>
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                        <span class="text-blue-700 font-bold text-xl">{{ substr($data['employee']->name, 0, 1) }}</span>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center">
                        <p class="text-slate-500 text-xs">Total Sales</p>
                        <p class="text-xl font-bold text-green-600">Ksh {{ number_format($data['total_sales'], 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-slate-500 text-xs">Transactions</p>
                        <p class="text-xl font-bold text-blue-700">{{ number_format($data['transaction_count']) }}</p>
                    </div>
                </div>
                <div class="text-center pt-3 border-t border-slate-200">
                    <p class="text-slate-500 text-xs">Average Sale Value</p>
                    <p class="text-lg font-semibold text-slate-900">Ksh {{ number_format($data['average_sale'], 2) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Detailed Table -->
    <div class="soma-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-900">Detailed Performance</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Employee</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Total Sales</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Transactions</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Average Sale</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($performanceData as $data)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium">{{ $data['employee']->name }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-green-600">Ksh {{ number_format($data['total_sales'], 2) }}</td>
                        <td class="px-6 py-4 text-center">{{ number_format($data['transaction_count']) }}</td>
                        <td class="px-6 py-4 text-right">Ksh {{ number_format($data['average_sale'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection