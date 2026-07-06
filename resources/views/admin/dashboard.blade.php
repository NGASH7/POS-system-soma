@extends('layouts.app')

@section('header_title', 'Admin Dashboard')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-950">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">System administration and oversight</p>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="soma-card p-6">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-slate-500">Total Users</span>
                    <i class="fas fa-users text-xl text-blue-600"></i>
                </div>
                <div class="text-2xl font-bold text-slate-950">{{ $totalUsers }}</div>
                <div class="mt-2 text-xs text-slate-500">{{ $totalAdmins }} Admins | {{ $totalEmployees }} Employees</div>
            </div>

            <div class="soma-card p-6">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-slate-500">Total Sales</span>
                    <i class="fas fa-chart-line text-xl text-emerald-500"></i>
                </div>
                <div class="text-2xl font-bold text-slate-950">{{ number_format($totalSales) }}</div>
                <div class="mt-2 text-sm font-semibold text-emerald-600">KES {{ number_format($totalRevenue, 2) }}</div>
            </div>

            <div class="soma-card p-6">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-slate-500">Total Products</span>
                    <i class="fas fa-boxes text-xl text-sky-500"></i>
                </div>
                <div class="text-2xl font-bold text-slate-950">{{ number_format($totalProducts) }}</div>
                <div class="mt-2 text-sm font-semibold text-rose-600">{{ $lowStockProducts }} Low Stock</div>
            </div>

            <div class="soma-card p-6">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm text-slate-500">System Status</span>
                    <i class="fas fa-server text-xl text-violet-500"></i>
                </div>
                <div class="text-2xl font-bold text-emerald-600">Online</div>
            </div>
        </div>
    </div>
</div>
@endsection
