@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>Expenses
                </h1>
                <p class="text-gray-600 mt-2">Track and manage all business expenses</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('expenses.categories') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-tags mr-2"></i>Categories
                </a>
                <a href="{{ route('expenses.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Add Expense
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('expenses.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 ml-2">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Total Expenses</span>
                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-gray-900">KES {{ number_format($summary['total'], 2) }}</div>
            <div class="text-xs text-gray-500 mt-2">{{ $summary['count'] }} transactions</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Pending</span>
                <i class="fas fa-clock text-yellow-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-yellow-600">KES {{ number_format($summary['pending'], 2) }}</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Approved</span>
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-green-600">KES {{ number_format($summary['approved'], 2) }}</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-500 text-sm">Rejected</span>
                <i class="fas fa-times-circle text-red-500 text-xl"></i>
            </div>
            <div class="text-2xl font-bold text-red-600">KES {{ number_format($summary['rejected'], 2) }}</div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($query as $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $expense->reference_no }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $expense->title }}</div>
                            <div class="text-xs text-gray-500">{{ $expense->vendor ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-1 rounded-full" style="background: {{ $expense->category->color }}20; color: {{ $expense->category->color }}">
                                {{ $expense->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold">KES {{ number_format($expense->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $expense->expense_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2 py-1 rounded-full {{ $expense->status_badge }}">
                                {{ ucfirst($expense->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($expense->status === 'pending')
                                    <form method="POST" action="{{ route('expenses.approve', $expense) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('expenses.reject', $expense) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-money-bill-wave text-4xl mb-3 block"></i>
                            No expenses found. Click "Add Expense" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $query->links() }}
        </div>
    </div>
</div>
@endsection