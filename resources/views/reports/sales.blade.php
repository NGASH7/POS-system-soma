@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-file-invoice text-blue-600 mr-2"></i>Sales Report
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">View all sales transactions</p>
                </div>
                <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
            </div>
            
            <!-- Date Filter -->
            <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">Total Sales</div>
                    <div class="text-2xl font-bold text-indigo-600">KES {{ number_format($summary['total_sales'], 2) }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">Transactions</div>
                    <div class="text-2xl font-bold text-green-600">{{ number_format($summary['total_transactions']) }}</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">Average Sale</div>
                    <div class="text-2xl font-bold text-blue-600">KES {{ number_format($summary['average_sale'], 2) }}</div>
                </div>
            </div>
            
            <!-- Sales Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cashier</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($sales as $sale)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $sale->invoice_no }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td class="px-4 py-3 text-right font-semibold">KES {{ number_format($sale->total, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="viewReceipt({{ $sale->id }})" class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-receipt mr-1"></i> View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function viewReceipt(saleId) {
    window.open('{{ url("/pos/print") }}/' + saleId, '_blank', 'width=400,height=600,scrollbars=yes');
}
</script>
@endsection