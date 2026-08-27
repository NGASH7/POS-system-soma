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
                    <h1 class="text-3xl font-bold text-gray-900">Customer Credits</h1>
                    <p class="text-gray-600 mt-1">Manage customer credit accounts and track outstanding balances</p>
                </div>
            </div>
            <a href="{{ route('credits.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition shadow-sm font-medium">
                <i class="fas fa-plus mr-2"></i>New Credit
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Total Outstanding</span>
                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="fas fa-credit-card text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-purple-600">KES {{ number_format($summary['total_outstanding'] ?? 0, 2) }}</div>
            <div class="text-xs text-gray-500 mt-2">{{ $summary['active_count'] ?? 0 }} active credits</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Overdue</span>
                <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation text-red-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-red-600">KES {{ number_format($summary['overdue_total'] ?? 0, 2) }}</div>
            <div class="text-xs text-gray-500 mt-2">{{ $summary['overdue_count'] ?? 0 }} overdue accounts</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Completed</span>
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($summary['completed_count'] ?? 0) }}</div>
            <div class="text-xs text-gray-500 mt-2">fully paid</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-gray-500 text-sm font-medium">Customers with Credit</span>
                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($summary['customers_with_credit'] ?? 0) }}</div>
            <div class="text-xs text-gray-500 mt-2">active customers</div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Customer</label>
                <select name="customer_id" class="border border-gray-300 rounded-lg px-3 py-2 min-w-[200px] text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">All Customers</option>
                    @foreach($customers ?? [] as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none min-w-[150px]">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm font-medium text-sm">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('credits.index') }}" class="bg-white text-gray-700 border border-gray-300 px-5 py-2 rounded-lg hover:bg-gray-50 transition font-medium text-sm">
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($credits as $credit)
                    <tr class="hover:bg-gray-50 transition" id="credit-row-{{ $credit->id }}">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $credit->reference }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $credit->customer->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $credit->customer->phone ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">KES {{ number_format($credit->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right text-green-600">KES {{ number_format($credit->paid_amount, 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold {{ $credit->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            KES {{ number_format($credit->balance, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($credit->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Completed</span>
                            @elseif($credit->status === 'overdue')
                                <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Overdue</span>
                            @elseif($credit->status === 'cancelled')
                                <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Cancelled</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $credit->due_date ? $credit->due_date->format('d/m/Y') : 'N/A' }}
                            @if($credit->isOverdue())
                                <span class="text-red-600 text-xs font-medium block mt-0.5"><i class="fas fa-circle-exclamation mr-1"></i>Overdue!</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center space-x-1">
                                <a href="{{ route('credits.show', $credit) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 transition" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('credits.edit', $credit) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-indigo-600 hover:bg-indigo-50 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($credit->status !== 'completed' && $credit->balance > 0)
                                    <button onclick="openPaymentModal({{ $credit->id }}, {{ $credit->balance }})" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-green-600 hover:bg-green-50 transition" title="Record Payment">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                @endif
                                <button onclick="deleteCredit({{ $credit->id }})" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-600 hover:bg-red-50 transition" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-credit-card text-4xl mb-3 block text-gray-300"></i>
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
    <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center mb-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Record Payment</h3>
            </div>
            <button type="button" onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form id="payment-form" method="POST" action="">
            @csrf
            <div class="space-y-4">
                <div class="bg-gray-50 border border-gray-100 p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">Remaining Balance</span>
                        <span id="payment-balance" class="font-bold text-red-600 text-lg">KES 0.00</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Amount *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">KES</span>
                        <input type="number" name="amount" id="payment-amount" step="0.01" required
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Method *</label>
                    <select name="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium shadow-sm">
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