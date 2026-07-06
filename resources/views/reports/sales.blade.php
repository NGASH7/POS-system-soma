@extends('layouts.app')

@section('header_title', 'Sales Report')

@section('content')
<x-soma-page title="Sales Report" subtitle="View and filter completed transactions">
    <x-slot:actions>
        <button type="button" onclick="window.print()" class="soma-btn-primary">
            <i class="fas fa-print"></i> Print Report
        </button>
    </x-slot:actions>

    <div class="soma-card p-6">
            <form method="GET" class="mb-6 flex flex-wrap gap-4">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="soma-input">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-700">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="soma-input">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="soma-btn-primary">Filter</button>
                </div>
            </form>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-blue-50 p-4">
                    <div class="mb-1 text-sm text-slate-500">Total Sales</div>
                    <div class="text-2xl font-bold leading-tight text-blue-700">KES {{ number_format($summary['total_sales'], 2) }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-emerald-50 p-4">
                    <div class="mb-1 text-sm text-slate-500">Transactions</div>
                    <div class="text-2xl font-bold leading-tight text-emerald-600">{{ number_format($summary['total_transactions']) }}</div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-sky-50 p-4">
                    <div class="mb-1 text-sm text-slate-500">Average Sale</div>
                    <div class="text-2xl font-bold leading-tight text-sky-600">KES {{ number_format($summary['average_sale'], 2) }}</div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="soma-table w-full">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Cashier</th>
                            <th>Customer</th>
                            <th class="text-right">Total</th>
                            <th>Payment</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td class="font-semibold text-slate-950">{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->user->name }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td class="text-right font-semibold">KES {{ number_format($sale->total, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                            <td class="text-center">
                                <button type="button" onclick="viewReceipt({{ $sale->id }})" class="text-sm font-semibold text-blue-700 hover:text-blue-800">
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
</x-soma-page>

<!-- Receipt Modal -->
<div id="receiptModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="flex max-h-[95vh] w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-6 py-4">
            <h3 class="text-lg font-bold text-slate-950">Transaction Receipt</h3>
            <button onclick="closeReceiptModal()" class="text-slate-400 transition hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="receiptContent" class="min-h-0 flex-1 overflow-y-auto bg-slate-50 p-6">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-blue-700 text-3xl"></i>
                <p class="mt-2 text-slate-500">Loading receipt...</p>
            </div>
        </div>
        <div class="flex shrink-0 gap-3 border-t border-slate-200 px-6 py-4">
            <button onclick="printReceipt()" class="flex-1 rounded-xl bg-slate-700 py-2 font-semibold text-white transition hover:bg-slate-800">
                <i class="fas fa-print mr-2"></i>Print Receipt
            </button>
            <button onclick="closeReceiptModal()" class="flex-1 bg-blue-700 text-white py-2 rounded-xl hover:bg-blue-800 transition font-medium">
                <i class="fas fa-times mr-2"></i>Close
            </button>
        </div>
    </div>
</div>

<script>
    let currentReceiptSaleId = null;

    function viewReceipt(saleId) {
        currentReceiptSaleId = saleId;
        const modal = document.getElementById('receiptModal');
        const content = document.getElementById('receiptContent');
        
        // Show loading
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-blue-700 text-3xl"></i>
                <p class="mt-2 text-slate-500">Loading receipt...</p>
            </div>
        `;
        
        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        // Load receipt
        fetch(`/pos/print/${saleId}?embed=1`)
            .then(response => response.text())
            .then(html => {
                // Extract only the receipt content from the print view
                // The print view already has its own styling, we just need the content
                content.innerHTML = html;
            })
            .catch(error => {
                content.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-circle text-red-500 text-3xl"></i>
                        <p class="mt-2 text-red-600">Failed to load receipt</p>
                        <button onclick="viewReceipt(${saleId})" class="mt-2 text-blue-700 hover:underline">Try Again</button>
                    </div>
                `;
                console.error('Error loading receipt:', error);
            });
    }
    
    function closeReceiptModal() {
        const modal = document.getElementById('receiptModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    function printReceipt() {
        if (!currentReceiptSaleId) {
            return;
        }

        const printWindow = window.open(
            `/pos/print/${currentReceiptSaleId}`,
            'ReceiptPrint',
            'width=480,height=720,scrollbars=yes,resizable=yes,menubar=no,toolbar=no'
        );

        if (!printWindow) {
            alert('Please allow popups to print the receipt.');
            return;
        }

        printWindow.focus();
    }
    
    // Close modal on background click
    document.getElementById('receiptModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReceiptModal();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReceiptModal();
        }
    });
</script>
@endsection