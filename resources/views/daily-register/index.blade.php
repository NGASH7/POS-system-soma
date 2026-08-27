@extends('layouts.app')

@php
    use Carbon\Carbon;
@endphp

@section('content')

<div class="min-h-screen bg-slate-50">
    <div class="container-fluid px-4 py-6 lg:px-6">

        {{-- =========================================================
             PAGE HEADER
        ========================================================== --}}
        <div class="mb-6">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                            <i class="fas fa-clipboard-list text-lg"></i>
                        </div>

                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                                Daily Register
                            </h1>

                            <p class="mt-1 text-sm text-slate-500">
                                Complete store activity summary for
                                <span class="font-semibold text-slate-700">
                                    {{ Carbon::parse($date)->format('F d, Y') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Date + Actions --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                    <form method="GET" class="flex items-center gap-2">
                        <div class="relative">
                            <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>

                            <input
                                type="date"
                                name="date"
                                value="{{ $date }}"
                                class="h-11 rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm font-medium text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            >
                        </div>

                        <button
                            type="submit"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 hover:shadow-md"
                        >
                            <i class="fas fa-search"></i>
                            <span>View</span>
                        </button>
                    </form>

                    <a
                        href="{{ route('daily-register.print', $date) }}"
                        target="_blank"
                        class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:shadow-md"
                    >
                        <i class="fas fa-print text-slate-500"></i>
                        Print Register
                    </a>

                </div>
            </div>
        </div>


        {{-- =========================================================
             REGISTER STATUS
        ========================================================== --}}
        <div class="mb-6 overflow-hidden rounded-2xl border shadow-sm
            {{ $cashDrawerStatus
                ? 'border-emerald-200 bg-gradient-to-r from-emerald-50 to-white'
                : 'border-red-200 bg-gradient-to-r from-red-50 to-white'
            }}">

            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-4">

                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl
                        {{ $cashDrawerStatus
                            ? 'bg-emerald-100 text-emerald-600'
                            : 'bg-red-100 text-red-600'
                        }}">
                        <i class="fas fa-cash-register text-lg"></i>
                    </div>

                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-semibold text-slate-600">
                                Cash Drawer
                            </span>

                            @if($cashDrawerStatus)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    OPEN
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                    CLOSED
                                </span>
                            @endif
                        </div>

                        @if($cashDrawerStatus)
                            <p class="mt-1 text-sm text-slate-500">
                                Register opened at
                                <span class="font-semibold text-slate-700">
                                    {{ $cashDrawerStatus->opened_at->format('H:i') }}
                                </span>
                            </p>
                        @else
                            <p class="mt-1 text-sm text-slate-500">
                                The register is currently closed.
                            </p>
                        @endif
                    </div>
                </div>

                <div>
                    @if(!$cashDrawerStatus)
                        <button
                            onclick="openRegister()"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 hover:shadow-md"
                        >
                            <i class="fas fa-plus"></i>
                            Open Register
                        </button>
                    @else
                        <button
                            onclick="closeRegister()"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 hover:shadow-md"
                        >
                            <i class="fas fa-power-off"></i>
                            Close Register
                        </button>
                    @endif
                </div>

            </div>
        </div>


        {{-- =========================================================
             SUMMARY CARDS
        ========================================================== --}}

        <div class="mb-6 space-y-4">

            {{-- FIRST ROW — 4 CARDS --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

{{-- Total Sales --}}
<div
    class="group relative overflow-hidden rounded-2xl p-5 text-white shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl"
    style="background: linear-gradient(135deg, #059669 0%, #047857 100%);"
>

    {{-- Decorative circle --}}
    <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full bg-white/10"></div>

    <div class="relative">

        {{-- Header --}}
        <div class="mb-4 flex items-center justify-between">

            <span class="text-sm font-semibold text-white">
                Total Sales
            </span>

            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20 text-white">
                <i class="fas fa-chart-line"></i>
            </div>

        </div>

        {{-- Amount --}}
        <div class="text-2xl font-bold tracking-tight text-white">
            KES {{ number_format($summary['total_sales'], 2) }}
        </div>

        {{-- Transactions --}}
        <div class="mt-1 text-xs font-medium text-white/90">
            {{ $summary['total_transactions'] }} transactions
        </div>

    </div>

</div>
                {{-- Credit Sales --}}
                <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">
                            Credit Sales
                        </span>

                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>

                    <div class="text-2xl font-bold tracking-tight text-violet-600">
                        KES {{ number_format($summary['total_credit_sales'], 2) }}
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        {{ $summary['credit_transactions'] }} transactions
                    </div>
                </div>

                {{-- Returns --}}
                <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">
                            Returns
                        </span>

                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600">
                            <i class="fas fa-undo"></i>
                        </div>
                    </div>

                    <div class="text-2xl font-bold tracking-tight text-red-600">
                        KES {{ number_format($summary['total_returns'], 2) }}
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        {{ $summary['return_count'] }} returns
                    </div>
                </div>

                {{-- Expenses --}}
                <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">
                            Expenses
                        </span>

                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>

                    <div class="text-2xl font-bold tracking-tight text-orange-600">
                        KES {{ number_format($summary['total_expenses'], 2) }}
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        {{ $summary['expense_count'] }} expenses
                    </div>
                </div>

            </div>

            {{-- SECOND ROW — 2 CARDS --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2">

                {{-- Items Sold --}}
                <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">
                            Items Sold
                        </span>

                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>

                    <div class="text-2xl font-bold tracking-tight text-cyan-600">
                        {{ number_format($summary['items_sold_count']) }}
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        total units
                    </div>

                </div>

                {{-- Net Sales --}}
                <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">
                            Net Sales
                        </span>

                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>

                    <div class="text-2xl font-bold tracking-tight text-indigo-600">
                        KES {{ number_format($summary['net_sales'], 2) }}
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        after returns & expenses
                    </div>

                </div>

            </div>

        </div>

        {{-- =========================================================
             PAYMENT / EXPENSE / QUICK STATS
        ========================================================== --}}
        <div class="mb-6 grid grid-cols-1 gap-5 lg:grid-cols-3">

            {{-- Payment Methods --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h3 class="font-bold text-slate-900">
                            Payment Methods
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Today's payment breakdown
                        </p>
                    </div>

                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fas fa-credit-card"></i>
                    </div>
                </div>

                <div class="space-y-4 p-5">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>

                            <span class="text-sm font-medium text-slate-600">
                                Cash
                            </span>
                        </div>

                        <span class="font-bold text-slate-900">
                            KES {{ number_format($summary['cash_sales'] ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <i class="fas fa-credit-card"></i>
                            </div>

                            <span class="text-sm font-medium text-slate-600">
                                Card
                            </span>
                        </div>

                        <span class="font-bold text-slate-900">
                            KES {{ number_format($summary['card_sales'] ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                                <i class="fas fa-mobile-alt"></i>
                            </div>

                            <span class="text-sm font-medium text-slate-600">
                                Mobile Money
                            </span>
                        </div>

                        <span class="font-bold text-slate-900">
                            KES {{ number_format($summary['mobile_sales'] ?? 0, 2) }}
                        </span>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-900">
                                Total
                            </span>

                            <span class="text-lg font-bold text-blue-600">
                                KES {{ number_format($summary['total_sales'], 2) }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Expenses --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h3 class="font-bold text-slate-900">
                            Expenses by Category
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Where today's expenses went
                        </p>
                    </div>

                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>

                <div class="max-h-56 space-y-3 overflow-y-auto p-5">

                    @forelse($expensesByCategory as $category)

                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="h-2 w-2 shrink-0 rounded-full bg-orange-500"></div>

                                <span class="truncate text-sm font-medium text-slate-600">
                                    {{ $category['category_name'] }}
                                </span>
                            </div>

                            <span class="ml-3 shrink-0 text-sm font-bold text-slate-900">
                                KES {{ number_format($category['total'], 2) }}
                            </span>
                        </div>

                    @empty

                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <i class="fas fa-receipt"></i>
                            </div>

                            <p class="text-sm font-medium text-slate-500">
                                No expenses recorded
                            </p>
                        </div>

                    @endforelse

                    @if($expensesByCategory->count() > 0)

                        <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                            <span class="text-sm font-bold text-slate-900">
                                Total Expenses
                            </span>

                            <span class="text-lg font-bold text-orange-600">
                                KES {{ number_format($summary['total_expenses'], 2) }}
                            </span>
                        </div>

                    @endif

                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h3 class="font-bold text-slate-900">
                            Quick Stats
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Key performance indicators
                        </p>
                    </div>

                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">

                    <div class="flex items-center justify-between px-5 py-4">
                        <span class="text-sm text-slate-500">
                            Average Transaction
                        </span>

                        <span class="font-bold text-slate-900">
                            KES {{ number_format($summary['total_transactions'] > 0 ? $summary['total_sales'] / $summary['total_transactions'] : 0, 2) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between px-5 py-4">
                        <span class="text-sm text-slate-500">
                            Credit Percentage
                        </span>

                        <span class="rounded-full bg-violet-50 px-3 py-1 text-sm font-bold text-violet-600">
                            {{ $summary['total_sales'] > 0 ? round(($summary['total_credit_sales'] / $summary['total_sales']) * 100, 1) : 0 }}%
                        </span>
                    </div>

                    <div class="flex items-center justify-between px-5 py-4">
                        <span class="text-sm text-slate-500">
                            Return Rate
                        </span>

                        <span class="rounded-full bg-red-50 px-3 py-1 text-sm font-bold text-red-600">
                            {{ $summary['total_sales'] > 0 ? round(($summary['total_returns'] / $summary['total_sales']) * 100, 1) : 0 }}%
                        </span>
                    </div>

                    <div class="flex items-center justify-between px-5 py-4">
                        <span class="text-sm text-slate-500">
                            Items / Transaction
                        </span>

                        <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-bold text-blue-600">
                            {{ $summary['total_transactions'] > 0 ? round($summary['items_sold_count'] / $summary['total_transactions'], 1) : 0 }}
                        </span>
                    </div>

                </div>
            </div>

        </div>

        {{-- =========================================================
             TOP SELLING ITEMS
        ========================================================== --}}
        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="font-bold text-slate-900">
                        Top Selling Items
                    </h3>

                    <p class="mt-0.5 text-xs text-slate-400">
                        Best performing products for today
                    </p>
                </div>

                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <i class="fas fa-trophy"></i>
                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full min-w-[650px]">

                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70">
                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                #
                            </th>

                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Product
                            </th>

                            <th class="px-5 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Quantity Sold
                            </th>

                            <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Total Revenue
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse($itemsSoldSummary as $item)

                            <tr class="transition hover:bg-slate-50">

                                <td class="px-5 py-4">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500">
                                        {{ $loop->iteration }}
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $item['product_name'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex min-w-[45px] items-center justify-center rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                        {{ $item['quantity'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <span class="font-bold text-emerald-600">
                                        KES {{ number_format($item['total'], 2) }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">

                                    <div class="flex flex-col items-center">
                                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                            <i class="fas fa-box-open text-xl"></i>
                                        </div>

                                        <p class="font-medium text-slate-600">
                                            No items sold today
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Product sales will appear here.
                                        </p>
                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

        {{-- =========================================================
             RECENT ACTIVITY
        ========================================================== --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="font-bold text-slate-900">
                        Recent Activity
                    </h3>

                    <p class="mt-0.5 text-xs text-slate-400">
                        Latest register transactions and activity
                    </p>
                </div>

                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                    <i class="fas fa-history"></i>
                </div>

            </div>

            <div class="max-h-[480px] overflow-x-auto overflow-y-auto">

                <table class="w-full min-w-[900px]">

                    <thead class="sticky top-0 z-10">

                        <tr class="border-b border-slate-100 bg-slate-50">

                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Type
                            </th>

                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Reference
                            </th>

                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Customer
                            </th>

                            <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Amount
                            </th>

                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Payment
                            </th>

                            <th class="px-5 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Items
                            </th>

                            <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Time
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        @forelse($recentActivity as $activity)

                            <tr class="transition hover:bg-slate-50">

                                {{-- Type --}}
                                <td class="px-5 py-4">

                                    @if($activity->type == 'Sale')

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Sale
                                        </span>

                                    @elseif($activity->type == 'Credit Sale')

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>
                                            Credit
                                        </span>

                                    @elseif($activity->type == 'Return')

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Return
                                        </span>

                                    @elseif($activity->type == 'Expense')

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>
                                            Expense
                                        </span>

                                    @endif

                                </td>

                                {{-- Reference --}}
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-800">
                                        {{ $activity->invoice }}
                                    </span>
                                </td>

                                {{-- Customer --}}
                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $activity->customer }}
                                </td>

                                {{-- Amount --}}
                                <td class="px-5 py-4 text-right">

                                    <span class="font-bold {{ $activity->amount < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $activity->amount < 0 ? '-' : '' }}KES
                                        {{ number_format(abs($activity->amount), 2) }}
                                    </span>

                                </td>

                                {{-- Payment --}}
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        {{ ucfirst($activity->payment) }}
                                    </span>
                                </td>

                                {{-- Items --}}
                                <td class="px-5 py-4 text-center">

                                    <span class="font-semibold text-slate-600">
                                        {{ $activity->items }}
                                    </span>

                                </td>

                                {{-- Time --}}
                                <td class="px-5 py-4 text-sm font-medium text-slate-500">
                                    <i class="far fa-clock mr-1 text-slate-400"></i>
                                    {{ $activity->time->format('H:i:s') }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-5 py-14 text-center">

                                    <div class="flex flex-col items-center">

                                        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                            <i class="fas fa-history text-xl"></i>
                                        </div>

                                        <p class="font-medium text-slate-600">
                                            No activity recorded today
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Register activity will appear here as transactions occur.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</div>

{{-- ===============================================================
     OPEN REGISTER MODAL
================================================================ --}}
<div
    id="open-register-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
>

    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i class="fas fa-cash-register"></i>
                </div>

                <div>
                    <h3 class="font-bold text-slate-900">
                        Open Register
                    </h3>

                    <p class="text-xs text-slate-400">
                        Start today's cash session
                    </p>
                </div>

            </div>

            <button
                onclick="closeModal('open-register-modal')"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
            >
                <i class="fas fa-times"></i>
            </button>

        </div>

        <form method="POST" action="{{ route('daily-register.open') }}">

            @csrf

            <div class="space-y-5 px-6 py-6">

                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Opening Balance
                    </label>

                    <div class="relative">

                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">
                            KES
                        </span>

                        <input
                            type="number"
                            name="opening_balance"
                            step="0.01"
                            min="0"
                            required
                            placeholder="0.00"
                            class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-14 pr-4 text-lg font-semibold text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                        >

                    </div>

                    <p class="mt-2 text-xs text-slate-400">
                        Enter the amount of cash available when opening the register.
                    </p>

                </div>

            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">

                <button
                    type="button"
                    onclick="closeModal('open-register-modal')"
                    class="h-10 rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    <i class="fas fa-check"></i>
                    Open Register
                </button>

            </div>

        </form>

    </div>
</div>

{{-- ===============================================================
     CLOSE REGISTER MODAL
================================================================ --}}
<div
    id="close-register-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
>

    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <i class="fas fa-power-off"></i>
                </div>

                <div>
                    <h3 class="font-bold text-slate-900">
                        Close Register
                    </h3>

                    <p class="text-xs text-slate-400">
                        End today's cash session
                    </p>
                </div>

            </div>

            <button
                onclick="closeModal('close-register-modal')"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
            >
                <i class="fas fa-times"></i>
            </button>

        </div>

        <form method="POST" action="{{ route('daily-register.close') }}">

            @csrf

            <div class="space-y-5 px-6 py-6">

                {{-- Closing Balance --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Closing Balance
                    </label>

                    <div class="relative">

                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">
                            KES
                        </span>

                        <input
                            type="number"
                            name="closing_balance"
                            step="0.01"
                            min="0"
                            required
                            placeholder="0.00"
                            class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-14 pr-4 text-lg font-semibold text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10"
                        >

                    </div>

                </div>

                {{-- Notes --}}
                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Notes
                        <span class="font-normal text-slate-400">
                            (optional)
                        </span>
                    </label>

                    <textarea
                        name="notes"
                        rows="3"
                        placeholder="Add any notes about today's register..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-300 focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-500/10"
                    ></textarea>

                </div>

            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">

                <button
                    type="button"
                    onclick="closeModal('close-register-modal')"
                    class="h-10 rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-red-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-red-700"
                >
                    <i class="fas fa-check"></i>
                    Close Register
                </button>

            </div>

        </form>

    </div>
</div>

{{-- ===============================================================
     JAVASCRIPT
================================================================ --}}
<script>

    function openRegister() {
        const modal = document.getElementById('open-register-modal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            const input = modal.querySelector('input[name="opening_balance"]');

            if (input) {
                input.focus();
            }
        }, 100);
    }

    function closeRegister() {
        const modal = document.getElementById('close-register-modal');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            const input = modal.querySelector('input[name="closing_balance"]');

            if (input) {
                input.focus();
            }
        }, 100);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    // Close modal when clicking outside the modal content
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {

        modal.addEventListener('click', function (event) {

            if (event.target === this) {
                this.classList.remove('flex');
                this.classList.add('hidden');
            }

        });

    });

    // Close modal with Escape key
    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            document.querySelectorAll('.fixed.inset-0').forEach(modal => {

                if (!modal.classList.contains('hidden')) {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }

            });

        }

    });

</script>

@endsection