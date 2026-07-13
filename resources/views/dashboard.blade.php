@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
        
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Good morning, {{ auth()->user()->name ?? 'System Admin' }}</h1>
                <p class="text-sm text-gray-500 mt-1">Live retail overview for {{ $period == 'today' ? 'today' : ($period == 'week' ? 'this week' : ($period == 'month' ? 'this month' : ($period == 'year' ? 'this year' : 'all time'))) }} store performance.</p>
            </div>
            
            <div class="flex items-center gap-4 relative z-50">
                <!-- Active Outlet -->
                @php
                    $activeOutletId = session('active_outlet_id', auth()->user()->outlet_id ?? 1);
                    $activeOutlet = \App\Models\Outlet::find($activeOutletId);
                @endphp
                <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 flex items-center gap-2">
                    <i class="fas fa-store text-blue-600"></i>
                    {{ $activeOutlet->name ?? 'Outlet' }}
                </div>
                
                <!-- Period Dropdown -->
                <div class="relative group">
                    <button class="bg-white border border-gray-200 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 flex items-center gap-2 hover:bg-gray-50">
                        <i class="far fa-calendar-alt text-blue-600"></i> 
                        {{ $period == 'today' ? 'Today' : ($period == 'week' ? 'This Week' : ($period == 'month' ? 'This Month' : ($period == 'year' ? 'This Year' : 'All Time'))) }} 
                        <i class="fas fa-chevron-down text-xs ml-1 text-gray-400"></i>
                    </button>
                    <div class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg hidden group-hover:block overflow-hidden">
                        <a href="?period=today" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Today</a>
                        <a href="?period=week" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">This Week</a>
                        <a href="?period=month" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">This Month</a>
                        <a href="?period=year" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">This Year</a>
                        <a href="?period=all" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">All Time</a>
                    </div>
                </div>

                <!-- Notification Bell -->
                <button class="relative bg-white border border-gray-200 w-10 h-10 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-50">
                    <i class="far fa-bell text-gray-600"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border border-white">5</span>
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-5 mb-8 relative z-10">
            <!-- TOTAL SALES -->
            <div class="soma-card p-5 relative overflow-hidden group hover:border-blue-200 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs font-semibold">+0%</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total Sales</p>
                    <h3 class="text-xl font-bold text-gray-900">Ksh {{ number_format($totalSales ?? 0, 2) }}</h3>
                </div>
            </div>

            <!-- TRANSACTIONS -->
            <div class="soma-card p-5 relative overflow-hidden group hover:border-emerald-200 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs font-semibold">+0%</span>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Transactions</p>
                    <h3 class="text-xl font-bold text-gray-900">{{ $totalTransactions ?? 0 }}</h3>
                </div>
            </div>

            <!-- AVG SALE -->
            <div class="soma-card p-5 relative overflow-hidden group hover:border-sky-200 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Avg Sale</p>
                    <h3 class="text-xl font-bold text-gray-900">Ksh {{ number_format($avgSale ?? 0, 2) }}</h3>
                </div>
            </div>

            <!-- ITEMS SOLD -->
            <div class="soma-card p-5 relative overflow-hidden group hover:border-purple-200 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Items Sold</p>
                    <h3 class="text-xl font-bold text-gray-900">{{ $itemsSold ?? 0 }}</h3>
                </div>
            </div>

            <!-- GROSS PROFIT -->
            <div class="soma-card p-5 relative overflow-hidden group hover:border-amber-200 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Gross Profit</p>
                    <h3 class="text-xl font-bold text-gray-900">Ksh {{ number_format($grossProfit ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>

        <!-- Main Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 relative z-0">
            
            <!-- Sales Overview -->
            <div class="soma-card p-6 flex flex-col h-[350px]">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Sales Overview</h3>
                        <p class="text-xs text-gray-500 mt-1">Revenue trends over time</p>
                    </div>
                    <span class="inline-flex px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-xs font-semibold">{{ ucfirst($period == 'all' ? 'All Time' : $period) }}</span>
                </div>
                <div class="flex-1 w-full relative">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="soma-card p-6 flex flex-col" style="height:420px;">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Payment Methods</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ ucfirst($period == 'all' ? 'All Time' : $period) }} tender mix</p>
                    </div>
                </div>
                <div class="flex-1 w-full relative min-h-0">
                    @if(empty($paymentChartData['data']) || count($paymentChartData['data']) == 0)
                        <div class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">No payment data available</div>
                    @else
                        <canvas id="paymentChart"></canvas>
                    @endif
                </div>
            </div>

            <!-- Top Products -->
            <div class="soma-card p-6 flex flex-col">
                <div class="flex justify-between items-start mb-4 flex-shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Top Products</h3>
                        <p class="text-xs text-gray-500 mt-1">Best performers for {{ $period == 'all' ? 'all time' : $period }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2 py-1 bg-blue-50 text-blue-600 rounded-md text-xs font-semibold">{{ ucfirst($period == 'all' ? 'All Time' : $period) }}</span>
                        <a href="{{ route('reports.sales') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline whitespace-nowrap">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @forelse(($topProducts ?? [])->take(5) as $item)
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 flex-shrink-0 group-hover:bg-blue-50 transition-colors">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <h4 class="text-sm font-semibold text-gray-900 truncate pr-2">{{ $item->product->name ?? 'Unknown Product' }}</h4>
                                <span class="text-sm font-bold text-gray-900 whitespace-nowrap">Ksh {{ number_format($item->product->price ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500">{{ $item->total_qty }} sold</span>
                                    <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">{{ $item->product->category->name ?? 'General' }}</span>
                                </div>
                                <span class="text-xs font-bold text-green-600">Up 12%</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500 text-sm">No products sold.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="soma-card p-6 flex flex-col">
                <div class="flex justify-between items-start mb-4 flex-shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Recent Sales</h3>
                    </div>
                    <a href="{{ route('reports.sales') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse(($recentSales ?? [])->take(5) as $sale)
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-600 flex-shrink-0">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $sale->invoice_no ?? 'N/A' }}</h4>
                            <span class="text-[11px] text-gray-500">{{ $sale->customer->name ?? 'Guest Customer' }} &bull; {{ $sale->created_at->diffForHumans() }}</span>
                        </div>
                        <span class="text-sm font-bold text-green-600">Ksh {{ number_format($sale->total ?? 0, 2) }}</span>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500 text-sm">No recent sales.</div>
                    @endforelse
                </div>
            </div>

            <!-- Stock Needing Restocking -->
            <div class="soma-card p-6 flex flex-col">
                <div class="flex justify-between items-start mb-4 flex-shrink-0">
                    <div>
                        <h3 class="text-base font-bold text-red-600">Needs Restocking</h3>
                    </div>
                    <a href="{{ route('reports.inventory') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse(($lowStockProducts ?? [])->take(5) as $product)
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-500 flex-shrink-0">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $product->name ?? 'Unknown Product' }}</h4>
                            <span class="text-[11px] text-gray-500">Threshold: {{ $product->low_stock_threshold ?? 0 }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-sm font-bold text-red-600">{{ $product->stock_quantity ?? 0 }}</span>
                            <span class="text-[10px] text-red-400">in stock</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500 text-sm">All stock levels are healthy!</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Sales Chart
    const salesCanvas = document.getElementById('salesChart');
    if (salesCanvas) {
        const salesCtx = salesCanvas.getContext('2d');
        const salesLabels = {!! json_encode($salesChartData['labels'] ?? []) !!};
        const salesData = {!! json_encode($salesChartData['data'] ?? []) !!};
        
        if (salesLabels.length > 0 && salesData.length > 0) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Total Sales (Ksh)',
                        data: salesData,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#4f46e5',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Ksh ' + new Intl.NumberFormat('en-KE').format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('en-KE').format(value);
                                },
                                font: { size: 10 }
                            },
                            grid: { borderDash: [2, 4], color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        }
    }

    // Payment Methods Chart
    const paymentCanvas = document.getElementById('paymentChart');
    if (paymentCanvas) {
        const paymentCtx = paymentCanvas.getContext('2d');
        
        const pLabels = {!! json_encode($paymentChartData['labels'] ?? []) !!};
        const pData = {!! json_encode($paymentChartData['data'] ?? []) !!};
        
        console.log('Payment Chart Data:', {labels: pLabels, data: pData}); // Debug
        
        if (pData && pData.length > 0) {
            // Calculate total for percentages
            const pTotal = pData.reduce((a, b) => a + b, 0);
            
            // Color palette for payment methods
            const colors = [
                '#8b5cf6', // purple
                '#10b981', // emerald
                '#3b82f6', // blue
                '#f59e0b', // amber
                '#ef4444', // red
                '#64748b'  // slate
            ];
            
            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: pLabels,
                    datasets: [{
                        data: pData,
                        backgroundColor: colors.slice(0, pData.length),
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 10,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 10 },
                                boxWidth: 8,
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map(function(label, i) {
                                            const ds = data.datasets[0];
                                            const value = ds.data[i];
                                            const total = ds.data.reduce((a, b) => a + b, 0);
                                            const pct = total > 0 ? Math.round((value / total) * 100) : 0;
                                            // Format label nicely
                                            let niceName = label.charAt(0).toUpperCase() + label.slice(1).replace('_', ' ');
                                            // If label is 'Mobile_money', format properly
                                            if (niceName.toLowerCase().includes('mobile')) {
                                                niceName = 'Mobile Money';
                                            }
                                            return {
                                                text: niceName + ' ' + pct + '%',
                                                fillStyle: ds.backgroundColor[i],
                                                strokeStyle: ds.backgroundColor[i],
                                                lineWidth: 0,
                                                pointStyle: 'circle',
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                        label += 'Ksh ' + new Intl.NumberFormat('en-KE').format(context.parsed) + ' (' + pct + '%)';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Show no data message
            const parent = paymentCanvas.parentElement;
            if (parent) {
                parent.innerHTML = `
                    <div class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm">
                        <i class="fas fa-chart-pie mr-2"></i> No payment data available
                    </div>
                `;
            }
        }
    }
});
</script>
@endpush
@endsection