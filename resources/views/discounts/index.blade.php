@extends('layouts.app')

@section('header_title', 'Discounts & Promotions')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <i class="fas fa-tags text-blue-600"></i>
                    <span>Discounts & Promotions</span>
                </h1>
                <p class="text-sm text-slate-500 mt-1">Manage promotional discounts, coupon codes, and special price reductions.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('discounts.create') }}" class="soma-btn-primary">
                    <i class="fas fa-plus text-xs"></i> Add Discount
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm text-emerald-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50/80 p-4 text-sm text-rose-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-rose-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Summary KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="soma-card p-4 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Discounts</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $summary['total'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Configured promotions</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fas fa-percent"></i>
                </div>
            </div>
            <div class="soma-card p-4 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Active Campaigns</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $summary['active'] }}</p>
                    <p class="text-xs text-emerald-600/80 mt-0.5">Available for use</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="soma-card p-4 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Expired / Inactive</p>
                    <p class="text-2xl font-bold text-slate-600">{{ $summary['expired'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Lapsed or exhausted</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="soma-card p-4 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Total Uses</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($summary['total_used']) }}</p>
                    <p class="text-xs text-indigo-600/80 mt-0.5">Times redeemed</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="soma-card p-4 mb-6">
            <form method="GET" action="{{ route('discounts.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-5">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Search Discount</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, or description..." class="soma-input pl-9 py-2">
                    </div>
                </div>
                <div class="lg:col-span-3">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Discount Type</label>
                    <select name="type" class="soma-input py-2">
                        <option value="">All Types</option>
                        <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount (KES)</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                    <select name="status" class="soma-input py-2">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="lg:col-span-2 flex gap-2">
                    <button type="submit" class="soma-btn-primary flex-1 justify-center py-2">
                        <i class="fas fa-filter text-xs"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('discounts.index') }}" class="soma-btn-secondary px-3 py-2 text-slate-500 hover:text-slate-700" title="Clear Filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Discounts Table -->
        <div class="soma-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="soma-table w-full">
                    <thead>
                        <tr>
                            <th>Discount / Promo</th>
                            <th>Code</th>
                            <th>Value</th>
                            <th>Conditions</th>
                            <th>Redemptions</th>
                            <th>Validity Period</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($discounts as $discount)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td>
                                <div class="font-semibold text-slate-900">{{ $discount->name }}</div>
                                @if($discount->description)
                                    <div class="text-xs text-slate-500 mt-0.5 max-w-xs truncate">{{ $discount->description }}</div>
                                @endif
                            </td>
                            <td>
                                @if($discount->code)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 font-mono text-xs font-bold text-slate-800 tracking-wider">
                                        <span>{{ $discount->code }}</span>
                                        <button type="button" onclick="copyCode('{{ $discount->code }}')" class="text-slate-400 hover:text-slate-600 transition-colors" title="Copy code">
                                            <i class="fas fa-copy text-[10px]"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">No code (Automatic)</span>
                                @endif
                            </td>
                            <td>
                                @if($discount->type === 'percentage')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                        <i class="fas fa-percent text-[10px]"></i>
                                        {{ rtrim(rtrim(number_format($discount->value, 2), '0'), '.') }}% OFF
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        <i class="fas fa-money-bill-wave text-[10px]"></i>
                                        KES {{ number_format($discount->value, 2) }} OFF
                                    </span>
                                @endif
                            </td>
                            <td class="text-xs text-slate-600">
                                <div class="space-y-0.5">
                                    @if($discount->min_spend)
                                        <div><span class="text-slate-400">Min:</span> KES {{ number_format($discount->min_spend, 2) }}</div>
                                    @endif
                                    @if($discount->type === 'percentage' && $discount->max_discount)
                                        <div><span class="text-slate-400">Cap:</span> KES {{ number_format($discount->max_discount, 2) }}</div>
                                    @endif
                                    @if(!$discount->min_spend && (!$discount->max_discount || $discount->type === 'fixed'))
                                        <span class="text-slate-400">No restrictions</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-xs">
                                    <div class="font-medium text-slate-800">
                                        {{ $discount->used_count }} {{ $discount->usage_limit ? '/ ' . $discount->usage_limit : 'uses' }}
                                    </div>
                                    @if($discount->usage_limit)
                                        @php
                                            $percent = min(100, round(($discount->used_count / $discount->usage_limit) * 100));
                                        @endphp
                                        <div class="w-20 bg-slate-200 h-1.5 rounded-full mt-1 overflow-hidden">
                                            <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-xs text-slate-600">
                                @if($discount->starts_at || $discount->ends_at)
                                    <div class="space-y-0.5">
                                        @if($discount->starts_at)
                                            <div><span class="text-slate-400">From:</span> {{ $discount->starts_at->format('d M Y') }}</div>
                                        @endif
                                        @if($discount->ends_at)
                                            <div class="{{ $discount->ends_at->isPast() ? 'text-rose-600 font-semibold' : '' }}">
                                                <span class="text-slate-400">To:</span> {{ $discount->ends_at->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">Always active</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($discount->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @elseif($discount->status === 'expired')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Expired
                                    </span>
                                @elseif($discount->status === 'scheduled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Scheduled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Toggle Status Form -->
                                    <form action="{{ route('discounts.toggle-status', $discount) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 rounded text-xs transition-colors {{ $discount->is_active ? 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-100' }}" title="{{ $discount->is_active ? 'Deactivate discount' : 'Activate discount' }}">
                                            <i class="fas {{ $discount->is_active ? 'fa-toggle-on text-base' : 'fa-toggle-off text-base' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Edit Link -->
                                    <a href="{{ route('discounts.edit', $discount) }}" class="p-1.5 rounded text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors" title="Edit discount">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <button type="button" onclick="confirmDelete({{ $discount->id }}, '{{ addslashes($discount->name) }}')" class="p-1.5 rounded text-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors" title="Delete discount">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <h3 class="font-medium text-slate-900">No discounts found</h3>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Create promotional coupons or percentage reductions to incentivize sales at POS checkout.</p>
                                <div class="mt-4">
                                    <a href="{{ route('discounts.create') }}" class="soma-btn-primary inline-flex">
                                        <i class="fas fa-plus text-xs"></i> Add Your First Discount
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($discounts->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $discounts->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Hidden Delete Form -->
<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- Toast Notification -->
<div id="copy-toast" class="fixed bottom-5 right-5 z-50 transform transition-all duration-300 translate-y-20 opacity-0 pointer-events-none">
    <div class="bg-slate-900 text-white text-xs font-medium px-4 py-2.5 rounded-lg shadow-xl flex items-center gap-2">
        <i class="fas fa-check text-emerald-400"></i>
        <span>Code copied to clipboard!</span>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id, name) {
    if (confirm(`Are you sure you want to delete the discount "${name}"? This action cannot be undone.`)) {
        const form = document.getElementById('delete-form');
        form.action = `/discounts/${id}`;
        form.submit();
    }
}

function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const toast = document.getElementById('copy-toast');
        toast.classList.remove('translate-y-20', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 2200);
    });
}
</script>
@endpush
@endsection
