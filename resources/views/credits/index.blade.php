@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-credit-card text-purple-600 mr-2"></i>Customer Credits
                </h1>
                <p class="text-gray-600 mt-2">Manage customer credit accounts and track outstanding balances</p>
            </div>
            <a href="{{ route('credits.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>New Credit
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Total Outstanding</span>
                <i class="fas fa-credit-card text-purple-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-purple-600">KES {{ number_format($summary['total_outstanding'] ?? 0, 2) }}</div>
            <div class="text-xs text-gray-500 mt-2">{{ $summary['active_count'] ?? 0 }} active credits</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Overdue</span>
                <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-red-600">KES {{ number_format($summary['overdue_total'] ?? 0, 2) }}</div>
            <div class="text-xs text-gray-500 mt-2">{{ $summary['overdue_count'] ?? 0 }} overdue accounts</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Completed</span>
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($summary['completed_count'] ?? 0) }}</div>
            <div class="text-xs text-gray-500 mt-2">fully paid</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Customers with Credit</span>
                <i class="fas fa-users text-blue-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($summary['customers_with_credit'] ?? 0) }}</div>
            <div class="text-xs text-gray-500 mt-2">active customers</div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <select name="customer_id" class="border border-gray-300 rounded-lg px-3 py-2 min-w-[200px]">
                    <option value="">All Customers</option>
                    @foreach($customers ?? [] as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('credits.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 ml-2">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Credits Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($credits as $credit)
                    <tr class="hover:bg-gray-50" id="credit-row-{{ $credit->id }}">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $credit->reference }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $credit->customer->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $credit->customer->phone ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold">KES {{ number_format($credit->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-green-600">KES {{ number_format($credit->paid_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold {{ $credit->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            KES {{ number_format($credit->balance, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($credit->status === 'completed')
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Completed</span>
                            @elseif($credit->status === 'overdue')
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Overdue</span>
                            @elseif($credit->status === 'cancelled')
                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">Cancelled</span>
                            @else
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $credit->due_date ? $credit->due_date->format('d/m/Y') : 'N/A' }}
                            @if($credit->isOverdue())
                                <span class="text-red-600 text-xs block">Overdue!</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('credits.show', $credit) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('credits.edit', $credit) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($credit->status !== 'completed' && $credit->balance > 0)
                                    <button onclick="openPaymentModal({{ $credit->id }}, {{ $credit->balance }})" class="text-green-600 hover:text-green-800" title="Record Payment">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                @endif
                                <button onclick="deleteCredit({{ $credit->id }})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-credit-card text-4xl mb-3 block"></i>
                            No credits found. Click "New Credit" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $credits->links() }}
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
        <form id="payment-form" method="POST" action="">
            @csrf
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Remaining Balance:</span>
                        <span id="payment-balance" class="font-bold text-red-600">KES 0.00</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">KES</span>
                        <input type="number" name="amount" id="payment-amount" step="0.01" required
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
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-money-bill-wave mr-2"></i>Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    // Store current credit ID globally
    let currentCreditId = null;
    
    function openPaymentModal(creditId, balance) {
        console.log('Opening payment modal for credit ID:', creditId, 'Balance:', balance);
        
        // Store the credit ID
        currentCreditId = creditId;
        
        // Set the balance directly from the parameter
        document.getElementById('payment-balance').textContent = 'KES ' + Number(balance).toFixed(2);
        document.getElementById('payment-form').action = `/credits/${creditId}/payment`;
        
        // Show the modal
        document.getElementById('payment-modal').classList.remove('hidden');
        document.getElementById('payment-modal').classList.add('flex');
        
        // Clear previous amount
        document.getElementById('payment-amount').value = '';
        
        // Focus on the amount input
        setTimeout(() => {
            document.getElementById('payment-amount').focus();
        }, 100);
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').classList.remove('flex');
        document.getElementById('payment-modal').classList.add('hidden');
        document.getElementById('payment-amount').value = '';
        currentCreditId = null;
    }

    function deleteCredit(id) {
        if (confirm('Are you sure you want to delete this credit?')) {
            const form = document.getElementById('delete-form');
            form.action = `/credits/${id}`;
            form.submit();
        }
    }

    // Close modal on outside click
    document.getElementById('payment-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });

    // Handle form submission with validation
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        const amountInput = document.getElementById('payment-amount');
        const amount = parseFloat(amountInput.value);
        
        if (!amount || amount <= 0) {
            e.preventDefault();
            alert('Please enter a valid amount greater than 0');
            amountInput.focus();
            return false;
        }
        
        const balanceText = document.getElementById('payment-balance').textContent;
        const balance = parseFloat(balanceText.replace(/[^0-9.]/g, ''));
        
        if (amount > balance) {
            e.preventDefault();
            alert(`Payment amount (${amount.toFixed(2)}) cannot exceed the remaining balance (${balance.toFixed(2)})`);
            amountInput.focus();
            return false;
        }
        
        // Show loading state on submit button
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    });

    // Handle keyboard shortcut (ESC to close)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePaymentModal();
        }
    });
</script>
@endsection