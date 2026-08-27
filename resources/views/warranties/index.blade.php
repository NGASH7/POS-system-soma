@extends('layouts.app')

@section('content')
<div class="container-fluid py-6">

    <!-- Header -->
    <div class="relative mb-8">
        <a href="{{ route('warranties.create') }}"
           class="absolute top-0 right-0 bg-blue-600 hover:bg-blue-700 active:scale-[0.97] text-white px-4 py-2.5 rounded-lg transition-all duration-150 font-medium text-sm inline-flex items-center shadow-sm hover:shadow-md">
            <i class="fas fa-plus mr-2"></i>New Warranty
        </a>
        <div class="pr-32 md:pr-40">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <span class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mr-3 shrink-0">
                    <i class="fas fa-shield-alt text-blue-600"></i>
                </span>
                Warranties & Claim Tickets
            </h1>
            <p class="text-gray-500 mt-1 text-sm ml-[52px]">Manage product warranties and service/claim tickets</p>
        </div>
    </div>

    <!-- Summary Cards: strict 4-per-row on desktop -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Warranties</div>
                <div class="text-2xl font-bold text-gray-900 mt-1 stat-value" data-target="{{ $summary['total'] }}">0</div>
            </div>
            <div class="w-11 h-11 rounded-full bg-gray-50 group-hover:bg-gray-100 flex items-center justify-center transition-colors">
                <i class="fas fa-shield-alt text-gray-400"></i>
            </div>
        </div>

        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Active</div>
                <div class="text-2xl font-bold text-green-600 mt-1 stat-value" data-target="{{ $summary['active'] }}">0</div>
            </div>
            <div class="w-11 h-11 rounded-full bg-green-50 group-hover:bg-green-100 flex items-center justify-center transition-colors">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
        </div>

        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Expired</div>
                <div class="text-2xl font-bold text-red-600 mt-1 stat-value" data-target="{{ $summary['expired'] }}">0</div>
            </div>
            <div class="w-11 h-11 rounded-full bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition-colors">
                <i class="fas fa-times-circle text-red-500"></i>
            </div>
        </div>

        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Claimed</div>
                <div class="text-2xl font-bold text-yellow-600 mt-1 stat-value" data-target="{{ $summary['claimed'] }}">0</div>
            </div>
            <div class="w-11 h-11 rounded-full bg-yellow-50 group-hover:bg-yellow-100 flex items-center justify-center transition-colors">
                <i class="fas fa-ticket-alt text-yellow-500"></i>
            </div>
        </div>

        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            <div>
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Expiring Soon</div>
                <div class="text-2xl font-bold text-orange-600 mt-1 stat-value" data-target="{{ $summary['expiring_soon'] }}">0</div>
            </div>
            <div class="w-11 h-11 rounded-full bg-orange-50 group-hover:bg-orange-100 flex items-center justify-center transition-colors">
                <i class="fas fa-clock text-orange-500"></i>
            </div>
        </div>

        <div class="stat-card group bg-blue-50/60 rounded-xl shadow-sm border border-blue-200 p-5 flex items-center justify-between hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 relative overflow-hidden">
            @if($summary['open_claims'] > 0)
                <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-blue-500 animate-ping"></span>
                <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-blue-500"></span>
            @endif
            <div>
                <div class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Open Tickets</div>
                <div class="text-2xl font-bold text-blue-700 mt-1 stat-value" data-target="{{ $summary['open_claims'] }}">0</div>
            </div>
            <div class="w-11 h-11 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fas fa-bell text-blue-600"></i>
            </div>
        </div>

    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 mb-6 bg-white rounded-t-xl px-4 pt-2 shadow-sm">
        <a href="{{ route('warranties.index', array_merge(request()->query(), ['tab' => 'warranties'])) }}"
           class="py-3 px-6 font-semibold text-sm border-b-2 flex items-center transition-colors duration-150 {{ $activeTab === 'warranties' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fas fa-list-alt mr-2"></i> Warranties List
            <span class="ml-2 bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full font-bold {{ $activeTab === 'warranties' ? 'bg-blue-100 text-blue-700' : '' }}">{{ $summary['total'] }}</span>
        </a>
        <a href="{{ route('warranties.index', array_merge(request()->query(), ['tab' => 'claims'])) }}"
           class="py-3 px-6 font-semibold text-sm border-b-2 flex items-center transition-colors duration-150 {{ $activeTab === 'claims' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fas fa-ticket-alt mr-2"></i> Warranty Claim Tickets
            @if($summary['open_claims'] > 0)
                <span class="ml-2 bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">{{ $summary['open_claims'] }} Open</span>
            @endif
        </a>
    </div>

    @if($activeTab === 'warranties')
        <!-- Filter Form for Warranties -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('warranties.index') }}" class="flex flex-wrap gap-4 items-end" id="warrantyFilterForm">
                <input type="hidden" name="tab" value="warranties">
                <div class="relative">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                    <i class="fas fa-search absolute left-3 top-[34px] text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Warranty #, Serial..."
                           class="border border-gray-300 rounded-lg pl-8 pr-3 py-2 text-sm min-w-[220px] focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none transition"
                           id="warrantySearchInput">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none transition" onchange="this.form.submit()">
                        <option value="">All </option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="claimed" {{ request('status') == 'claimed' ? 'selected' : '' }}>Claimed</option>
                        <option value="replaced" {{ request('status') == 'replaced' ? 'selected' : '' }}>Replaced</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 active:scale-[0.97] transition-all">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('warranties.index', ['tab' => 'warranties']) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition ml-2">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Warranties Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-700 text-xs font-semibold uppercase sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3">Warranty #</th>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Serial #</th>
                            <th class="px-6 py-3">Expiry Date</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($warranties as $warranty)
                        <tr class="hover:bg-blue-50/40 transition-colors duration-100">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">{{ $warranty->warranty_no }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $warranty->product->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $warranty->customer->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-600">{{ $warranty->serial_number ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs font-medium {{ $warranty->expiry_date->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                                {{ $warranty->expiry_date->format('d/m/Y') }}
                                @if($warranty->expiry_date->diffInDays(now()) <= 30 && $warranty->isActive())
                                    <span class="text-orange-600 text-xs font-normal block">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Expiring soon
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $warranty->status_badge }}">
                                    {{ ucfirst($warranty->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-3 text-base">
                                    <a href="{{ route('warranties.show', $warranty) }}" title="View Warranty" class="text-blue-600 hover:text-blue-800 hover:scale-110 transition-transform">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('warranties.edit', $warranty) }}" title="Edit Warranty" class="text-indigo-600 hover:text-indigo-800 hover:scale-110 transition-transform">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('warranties.claims.create', $warranty) }}" title="File Ticket" class="text-yellow-600 hover:text-yellow-800 hover:scale-110 transition-transform">
                                        <i class="fas fa-ticket-alt"></i>
                                    </a>
                                    <a href="{{ route('warranties.print', $warranty) }}" target="_blank" title="Print Certificate" class="text-gray-600 hover:text-gray-800 hover:scale-110 transition-transform">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                <i class="fas fa-shield-alt text-4xl mb-3 block text-gray-300"></i>
                                <p class="font-medium">No warranties found</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting your search or filters</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $warranties->appends(['tab' => 'warranties'])->links() }}
            </div>
        </div>
    @else
        <!-- Filter Form for Claim Tickets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('warranties.index') }}" class="flex flex-wrap gap-4 items-end">
                <input type="hidden" name="tab" value="claims">
                <div class="relative">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search Tickets</label>
                    <i class="fas fa-search absolute left-3 top-[34px] text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Claim #, Issue, Serial..."
                           class="border border-gray-300 rounded-lg pl-8 pr-3 py-2 text-sm min-w-[220px] focus:ring-2 focus:ring-yellow-200 focus:border-yellow-400 outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Ticket Status</label>
                    <select name="claim_status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-200 focus:border-yellow-400 outline-none transition" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('claim_status') == 'pending' ? 'selected' : '' }}>Pending (Open)</option>
                        <option value="approved" {{ request('claim_status') == 'approved' ? 'selected' : '' }}>Approved (Open)</option>
                        <option value="rejected" {{ request('claim_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ request('claim_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700 active:scale-[0.97] transition-all">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('search') || request('claim_status'))
                        <a href="{{ route('warranties.index', ['tab' => 'claims']) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition ml-2">
                            <i class="fas fa-times mr-1"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Claim Tickets Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-700 text-xs font-semibold uppercase sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3">Claim Ticket #</th>
                            <th class="px-6 py-3">Warranty #</th>
                            <th class="px-6 py-3">Customer & Product</th>
                            <th class="px-6 py-3">Issue Description</th>
                            <th class="px-6 py-3">Claim Date</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($claims as $claim)
                        <tr class="hover:bg-yellow-50/40 transition-colors duration-100">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">{{ $claim->claim_no }}</td>
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-blue-600">
                                @if($claim->warranty)
                                    <a href="{{ route('warranties.show', $claim->warranty) }}" class="hover:underline">
                                        {{ $claim->warranty->warranty_no }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $claim->warranty->customer->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $claim->warranty->product->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate" title="{{ $claim->issue_description }}">
                                {{ $claim->issue_description }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $claim->claim_date ? $claim->claim_date->format('d/m/Y') : '' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $claim->status_badge }}">
                                    {{ ucfirst($claim->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($claim->warranty)
                                    <a href="{{ route('warranties.show', $claim->warranty) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1 rounded text-xs font-medium transition inline-flex items-center">
                                        <i class="fas fa-eye mr-1"></i> View / Resolve
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-500">
                                <i class="fas fa-ticket-alt text-4xl mb-3 block text-gray-300"></i>
                                <p class="font-medium">No warranty claim tickets found</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting your search or filters</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $claims->appends(['tab' => 'claims'])->links() }}
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Animate stat counters on load
    document.querySelectorAll('.stat-value').forEach(function (el) {
        var target = parseInt(el.dataset.target, 10) || 0;
        var duration = 700;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            el.textContent = Math.floor(eased * target).toLocaleString();
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target.toLocaleString();
            }
        }
        requestAnimationFrame(step);
    });

    // Debounced live search (auto-submits filter form after typing pause)
    var searchInput = document.getElementById('warrantySearchInput');
    var searchForm = document.getElementById('warrantyFilterForm');
    if (searchInput && searchForm) {
        var debounceTimer;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                searchForm.submit();
            }, 600);
        });
    }
});
</script>
@endsection