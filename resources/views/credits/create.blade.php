@extends('layouts.app')

@section('content')
<div class="px-6 py-8">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Customer Credits</h1>
            <p class="text-gray-500 mt-1">Track balances and payment plans</p>
        </div>
        <a href="{{ route('credits.create') }}"
           class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> New Credit
        </a>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Total credit</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">KES {{ number_format($totalCredit ?? 0, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $creditCount ?? 0 }} records</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Outstanding</p>
            <p class="text-2xl font-bold text-amber-600 mt-2">KES {{ number_format($totalOutstanding ?? 0, 2) }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Paid</p>
            <p class="text-2xl font-bold text-emerald-600 mt-2">KES {{ number_format($totalPaid ?? 0, 2) }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-semibold text-gray-400 tracking-wide uppercase">Overdue</p>
            <p class="text-2xl font-bold text-red-600 mt-2">KES {{ number_format($totalOverdue ?? 0, 2) }}</p>
        </div>

    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('credits.index') }}" class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                <select name="type" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    <option value="">All</option>
                    <option value="direct_credit" {{ request('type') == 'direct_credit' ? 'selected' : '' }}>Direct credit</option>
                    <option value="layaway" {{ request('type') == 'layaway' ? 'selected' : '' }}>Layaway</option>
                    <option value="invoice" {{ request('type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Due from</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Due to</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600">
            </div>

        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                Filter
            </button>
            <a href="{{ route('credits.index') }}"
               class="flex-1 py-3 text-center border border-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-50 transition">
                Clear
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    <th class="px-6 py-3.5">Reference</th>
                    <th class="px-6 py-3.5">Customer</th>
                    <th class="px-6 py-3.5">Type</th>
                    <th class="px-6 py-3.5">Total</th>
                    <th class="px-6 py-3.5">Balance</th>
                    <th class="px-6 py-3.5">Due date</th>
                    <th class="px-6 py-3.5">Status</th>
                    <th class="px-6 py-3.5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($credits as $credit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $credit->reference }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $credit->customer->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ ucfirst(str_replace('_', ' ', $credit->type)) }}</td>
                        <td class="px-6 py-4 text-gray-900">KES {{ number_format($credit->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-gray-900">KES {{ number_format($credit->balance, 2) }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ optional($credit->due_date)->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'active'    => 'bg-blue-50 text-blue-700',
                                    'paid'      => 'bg-emerald-50 text-emerald-700',
                                    'overdue'   => 'bg-red-50 text-red-700',
                                    'cancelled' => 'bg-gray-100 text-gray-500',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$credit->status] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($credit->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('credits.show', $credit->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            No customer credits yet. <a href="{{ route('credits.create') }}" class="text-blue-600 font-medium">Create one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($credits ?? null, 'links'))
        <div class="mt-6">
            {{ $credits->links() }}
        </div>
    @endif

</div>
@endsection