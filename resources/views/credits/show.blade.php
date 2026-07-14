@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-credit-card text-purple-600 mr-2"></i>Credit Details
                </h1>
                <p class="text-gray-600 mt-2">Reference: {{ $credit->reference }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('credits.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                @if($credit->status !== 'completed' && $credit->balance > 0)
                    <button onclick="openPaymentModal({{ $credit->id }})" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-money-bill-wave mr-2"></i>Record Payment
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">Total Amount</div>
            <div class="text-2xl font-bold text-gray-900">KES {{ number_format($credit->total_amount, 2) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">Paid Amount</div>
            <div class="text-2xl font-bold text-green-600">KES {{ number_format($credit->paid_amount, 2) }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $credit->getProgressPercentage() }}% paid</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm text-gray-500 mb-1">Remaining Balance</div>
            <div class="text-2xl font-bold text-red-600">KES {{ number_format($credit->balance, 2) }}</div>
        </div>
    </div>

    <!-- Credit Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Name:</span>
                    <span class="font-medium">{{ $credit->customer->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Phone:</span>
                    <span class="font-medium">{{ $credit->customer->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Email:</span>
                    <span class="font-medium">{{ $credit->customer->email ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Spent:</span>
                    <span class="font-medium">KES {{ number_format($credit->customer->total_spent ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Credit Information</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Type:</span>
                    <span class="font-medium capitalize">{{ str_replace('_', ' ', $credit->type) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Status:</span>
                    <span>
                        @if($credit->status === 'completed')
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Completed</span>
                        @elseif($credit->status === 'overdue')
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Overdue</span>
                        @elseif($credit->status === 'cancelled')
                            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">Cancelled</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Active</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Due Date:</span>
                    <span class="font-medium">{{ $credit->due_date ? $credit->due_date->format('d/m/Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Created By:</span>
                    <span class="font-medium">{{ $credit->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Created:</span>
                    <span class="font-medium">{{ $credit->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800">Payment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($credit->payment_history ?? [] as $payment)
                    <tr>
                        <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($payment['date'])->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-green-600">KES {{ number_format($payment['amount'], 2) }}</td>
                        <td class="px-6 py-4 text-sm capitalize">{{ str_replace('_', ' ', $payment['method']) }}</td>
                        <td class="px-6 py-4 text-sm">{{ $payment['user'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $payment['notes'] ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No payments recorded yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="bg-white rounded-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Record Payment</h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="payment-form" method="POST" action="{{ route('credits.payment', $credit) }}">
            @csrf
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Remaining Balance:</span>
                        <span class="font-bold text-red-600">KES {{ number_format($credit->balance, 2) }}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">KES</span>
                        <input type="number" name="amount" id="payment-amount" step="0.01" max="{{ $credit->balance }}" required
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                    <select name="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-money-bill-wave mr-2"></i>Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal() {
        document.getElementById('payment-modal').classList.remove('hidden');
        document.getElementById('payment-modal').classList.add('flex');
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').classList.remove('flex');
        document.getElementById('payment-modal').classList.add('hidden');
        document.getElementById('payment-amount').value = '';
    }

    document.getElementById('payment-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });
</script>
@endsection