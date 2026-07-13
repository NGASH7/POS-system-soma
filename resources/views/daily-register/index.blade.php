@extends('layouts.app')

@php
use Carbon\Carbon;
@endphp

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>Daily Register
                </h1>
                <p class="text-gray-600 mt-2">Complete store activity summary for {{ Carbon::parse($date)->format('F d, Y') }}</p>
            </div>
            <div class="flex space-x-3">
                <form method="GET" class="flex items-center space-x-2">
                    <input type="date" name="date" value="{{ $date }}" class="border border-gray-300 rounded-lg px-3 py-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>View
                    </button>
                </form>
                <a href="{{ route('daily-register.print', $date) }}" target="_blank" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    <i class="fas fa-print mr-2"></i>Print
                </a>
            </div>
        </div>
    </div>

    <!-- Cash Drawer Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <i class="fas fa-cash-register text-2xl text-blue-600"></i>
                <div>
                    <span class="font-semibold text-gray-700">Cash Drawer:</span>
                    @if($cashDrawerStatus)
                        <span class="text-green-600 font-bold">Open</span>
                        <span class="text-sm text-gray-500 ml-2">Opened at {{ $cashDrawerStatus->opened_at->format('H:i') }}</span>
                    @else
                        <span class="text-red-600 font-bold">Closed</span>
                    @endif
                </div>
            </div>
            <div>
                @if(!$cashDrawerStatus)
                    <button onclick="openRegister()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-plus mr-2"></i>Open Register
                    </button>
                @else
                    <button onclick="closeRegister()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        <i class="fas fa-times mr-2"></i>Close Register
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Total Sales</div>
            <div class="text-xl font-bold text-green-600">KES {{ number_format($summary['total_sales'], 2) }}</div>
            <div class="text-xs text-gray-400">{{ $summary['total_transactions'] }} transactions</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Credit Sales</div>
            <div class="text-xl font-bold text-purple-600">KES {{ number_format($summary['total_credit_sales'], 2) }}</div>
            <div class="text-xs text-gray-400">{{ $summary['credit_transactions'] }} transactions</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Returns</div>
            <div class="text-xl font-bold text-red-600">KES {{ number_format($summary['total_returns'], 2) }}</div>
            <div class="text-xs text-gray-400">{{ $summary['return_count'] }} returns</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Expenses</div>
            <div class="text-xl font-bold text-orange-600">KES {{ number_format($summary['total_expenses'], 2) }}</div>
            <div class="text-xs text-gray-400">{{ $summary['expense_count'] }} expenses</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Items Sold</div>
            <div class="text-xl font-bold text-blue-600">{{ number_format($summary['items_sold_count']) }}</div>
            <div class="text-xs text-gray-400">total units</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-500">Net Sales</div>
            <div class="text-xl font-bold text-indigo-600">KES {{ number_format($summary['net_sales'], 2) }}</div>
            <div class="text-xs text-gray-400">after returns & expenses</div>
        </div>
    </div>

    <!-- Payment Methods Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Methods</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span><i class="fas fa-money-bill-wave text-green-600 mr-2"></i>Cash</span>
                    <span class="font-semibold">KES {{ number_format($summary['cash_sales'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span><i class="fas fa-credit-card text-blue-600 mr-2"></i>Card</span>
                    <span class="font-semibold">KES {{ number_format($summary['card_sales'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span><i class="fas fa-mobile-alt text-purple-600 mr-2"></i>Mobile Money</span>
                    <span class="font-semibold">KES {{ number_format($summary['mobile_sales'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center border-t pt-2">
                    <span class="font-bold">Total</span>
                    <span class="font-bold">KES {{ number_format($summary['total_sales'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Expenses by Category -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Expenses by Category</h3>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @forelse($expensesByCategory as $category)
                <div class="flex justify-between items-center text-sm">
                    <span>{{ $category['category_name'] }}</span>
                    <span class="font-semibold">KES {{ number_format($category['total'], 2) }}</span>
                </div>
                @empty
                <div class="text-gray-500 text-sm">No expenses recorded</div>
                @endforelse
                @if($expensesByCategory->count() > 0)
                <div class="flex justify-between items-center border-t pt-2 font-bold">
                    <span>Total</span>
                    <span>KES {{ number_format($summary['total_expenses'], 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Stats</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Average Transaction</span>
                    <span class="font-semibold">KES {{ number_format($summary['total_transactions'] > 0 ? $summary['total_sales'] / $summary['total_transactions'] : 0, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Credit Percentage</span>
                    <span class="font-semibold">{{ $summary['total_sales'] > 0 ? round(($summary['total_credit_sales'] / $summary['total_sales']) * 100, 1) : 0 }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Return Rate</span>
                    <span class="font-semibold">{{ $summary['total_sales'] > 0 ? round(($summary['total_returns'] / $summary['total_sales']) * 100, 1) : 0 }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Items per Transaction</span>
                    <span class="font-semibold">{{ $summary['total_transactions'] > 0 ? round($summary['items_sold_count'] / $summary['total_transactions'], 1) : 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Items -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Top Selling Items Today</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity Sold</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($itemsSoldSummary as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item['product_name'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $item['quantity'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-green-600">KES {{ number_format($item['total'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No items sold today</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
        </div>
        <div class="overflow-x-auto max-h-96 overflow-y-auto">
            <table class="w-full">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recentActivity as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            @if($activity->type == 'Sale')
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Sale</span>
                            @elseif($activity->type == 'Credit Sale')
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Credit</span>
                            @elseif($activity->type == 'Return')
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Return</span>
                            @elseif($activity->type == 'Expense')
                                <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Expense</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $activity->invoice }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $activity->customer }}</td>
                        <td class="px-6 py-4 text-right font-semibold {{ $activity->amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                            KES {{ number_format($activity->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ ucfirst($activity->payment) }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $activity->items }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $activity->time->format('H:i:s') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No activity recorded today</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Open Register Modal -->
<div id="open-register-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="bg-white rounded-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Open Register</h3>
            <button onclick="closeModal('open-register-modal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('daily-register.open') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opening Balance</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">KES</span>
                        <input type="number" name="opening_balance" step="0.01" required
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal('open-register-modal')" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>Open Register
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Close Register Modal -->
<div id="close-register-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="bg-white rounded-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Close Register</h3>
            <button onclick="closeModal('close-register-modal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('daily-register.close') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Closing Balance</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">KES</span>
                        <input type="number" name="closing_balance" step="0.01" required
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal('close-register-modal')" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-check mr-2"></i>Close Register
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRegister() {
        document.getElementById('open-register-modal').classList.remove('hidden');
        document.getElementById('open-register-modal').classList.add('flex');
    }

    function closeRegister() {
        document.getElementById('close-register-modal').classList.remove('hidden');
        document.getElementById('close-register-modal').classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('flex');
        document.getElementById(id).classList.add('hidden');
    }

    // Close modal on outside click
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('flex');
                this.classList.add('hidden');
            }
        });
    });
</script>
@endsection