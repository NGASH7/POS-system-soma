@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">

        {{-- =========================================================
             PAGE HEADER
        ========================================================== --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                    <i class="fas fa-shield-halved text-lg"></i>
                </div>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">
                        Access Control
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Manage user roles and permissions
                    </p>
                </div>
            </div>

            <div class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm sm:self-auto">
                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                {{ $users->total() }} Users
            </div>

        </div>


        {{-- =========================================================
             USER MANAGEMENT CARD
        ========================================================== --}}
        <div class="soma-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Card header --}}
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fas fa-users"></i>
                    </div>

                    <div>
                        <h2 class="font-bold text-slate-900">
                            System Users
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-400">
                            User accounts, roles and sales activity
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
                    <i class="fas fa-lock text-slate-300"></i>
                    Access controlled
                </div>

            </div>


            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px]">

                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80">

                            <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Name
                            </th>

                            <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Email
                            </th>

                            <th class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Role
                            </th>

                            <th class="px-5 py-3.5 text-center text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Sales Count
                            </th>

                            <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Joined
                            </th>

                        </tr>
                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($users as $user)

                        <tr class="group transition hover:bg-blue-50/30">

                            {{-- Name --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-600">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}

                                        @if($user->id === auth()->id())
                                            <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-emerald-500"></span>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800">
                                            {{ $user->name }}

                                            @if($user->id === auth()->id())
                                                <span class="ml-1.5 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-blue-600">
                                                    You
                                                </span>
                                            @endif
                                        </div>

                                        @if($user->id === auth()->id())
                                            <div class="mt-0.5 text-xs text-emerald-600">
                                                <i class="fas fa-circle text-[6px] mr-1"></i>
                                                Current account
                                            </div>
                                        @endif
                                    </div>

                                </div>

                            </td>


                            {{-- Email --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <i class="far fa-envelope text-slate-300"></i>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </td>


                            {{-- Role --}}
                            <td class="px-5 py-4 text-center">

                                @if($user->role === 'admin')

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-50 px-3 py-1.5 text-xs font-bold text-purple-700">
                                        <i class="fas fa-crown text-[10px]"></i>
                                        Admin
                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600">
                                        <i class="fas fa-user text-[10px]"></i>
                                        Employee
                                    </span>

                                @endif

                            </td>


                            {{-- Sales Count --}}
                            <td class="px-5 py-4 text-center">

                                <span class="inline-flex min-w-[82px] items-center justify-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
                                    <i class="fas fa-cart-shopping text-[10px]"></i>
                                    {{ number_format($user->sales_count) }}
                                    {{ $user->sales_count == 1 ? 'sale' : 'sales' }}
                                </span>

                            </td>


                            {{-- Joined --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-2">
                                    <i class="far fa-calendar text-slate-300"></i>

                                    <div>
                                        <div class="text-sm font-medium text-slate-600">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </div>

                                        <div class="mt-0.5 text-xs text-slate-400">
                                            {{ $user->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>

                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <i class="fas fa-users text-2xl"></i>
                                    </div>

                                    <h3 class="font-bold text-slate-700">
                                        No users found
                                    </h3>

                                    <p class="mt-1 max-w-sm text-sm text-slate-400">
                                        There are currently no user accounts available to display.
                                    </p>

                                </div>

                            </td>
                        </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>


            {{-- Pagination --}}
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $users->links() }}
            </div>

        </div>

    </div>
</div>
@endsection