@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Customers</h1>
        <p class="text-sm text-slate-500">Manage your customers and view their credit</p>
    </div>
    
    <div class="flex gap-2">
        <a href="{{ route('customers.create') }}" class="flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-700">
            <i class="fas fa-plus"></i>
            <span>Add Customer</span>
        </a>
    </div>
</div>

<div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <form action="{{ route('customers.index') }}" method="GET" class="flex gap-2">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customers by name, phone or email..." class="h-11 w-full rounded-xl border-slate-200 pl-11 focus:border-blue-500 focus:ring-blue-500">
        </div>
        <button type="submit" class="rounded-xl bg-slate-800 px-6 font-bold text-white transition hover:bg-slate-700">
            Search
        </button>
    </form>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="whitespace-nowrap px-6 py-4 font-bold">Name</th>
                    <th class="whitespace-nowrap px-6 py-4 font-bold">Contact</th>
                    <th class="whitespace-nowrap px-6 py-4 font-bold">Credit Limit</th>
                    <th class="whitespace-nowrap px-6 py-4 font-bold">Available Credit</th>
                    <th class="whitespace-nowrap px-6 py-4 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($customers as $customer)
                <tr class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-800">
                        {{ $customer->name }}
                        @if($customer->loyalty_card)
                            <div class="text-xs text-slate-500 mt-1"><i class="fas fa-id-card mr-1"></i> {{ $customer->loyalty_card }}</div>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <div class="text-slate-800">{{ $customer->phone ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-500">{{ $customer->email ?? 'N/A' }}</div>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        KES {{ number_format($customer->credit_limit, 2) }}
                    </td>
                    <td class="whitespace-nowrap px-6 py-4">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $customer->available_credit > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                            KES {{ number_format($customer->available_credit, 2) }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('credits.customer', $customer->id) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-emerald-500 transition hover:bg-emerald-50" title="View Credit">
                                <i class="fas fa-money-check-dollar"></i>
                            </a>
                            <a href="{{ route('customers.edit', $customer) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-blue-500 transition hover:bg-blue-50">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-rose-500 transition hover:bg-rose-50">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        <div class="mb-2"><i class="fas fa-users text-4xl text-slate-300"></i></div>
                        <p>No customers found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($customers->hasPages())
    <div class="border-t border-slate-200 p-4">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
