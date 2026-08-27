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
                    <i class="fas fa-clock-rotate-left text-lg"></i>
                </div>

                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">
                        Activity Log
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Track user activities and system events
                    </p>
                </div>
            </div>

            <div class="inline-flex items-center gap-2 self-start rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm sm:self-auto">
                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                System Activity
            </div>
        </div>


        {{-- =========================================================
             ACTIVITY CARD
        ========================================================== --}}
        <div class="soma-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            {{-- Card header --}}
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fas fa-list"></i>
                    </div>

                    <div>
                        <h2 class="font-bold text-slate-900">
                            Recent Activities
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-400">
                            User actions and system events
                        </p>
                    </div>
                </div>

                @if(isset($activities) && method_exists($activities, 'total'))
                    <div class="text-sm text-slate-400">
                        {{ number_format($activities->total()) }} activities
                    </div>
                @endif
            </div>


            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px]">

                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/80">
                            <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                User
                            </th>

                            <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Action
                            </th>

                            <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Details
                            </th>

                            <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                Time
                            </th>
                        </tr>
                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($activities as $activity)

                            <tr class="group transition hover:bg-blue-50/30">

                                {{-- User --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-600">
                                            {{ strtoupper(substr($activity->user, 0, 1)) }}
                                        </div>

                                        <span class="font-semibold text-slate-800">
                                            {{ $activity->user }}
                                        </span>
                                    </div>
                                </td>


                                {{-- Action --}}
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                        {{ $activity->action }}
                                    </span>
                                </td>


                                {{-- Details --}}
                                <td class="max-w-xl px-5 py-4 text-sm leading-6 text-slate-500">
                                    {{ $activity->details }}
                                </td>


                                {{-- Time --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-2 text-sm font-medium text-slate-600">
                                        <i class="far fa-clock text-slate-400"></i>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $activity->created_at->format('d M Y, H:i') }}
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">

                                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                            <i class="fas fa-clock-rotate-left text-2xl"></i>
                                        </div>

                                        <h3 class="font-bold text-slate-700">
                                            No activities found
                                        </h3>

                                        <p class="mt-1 max-w-sm text-sm text-slate-400">
                                            There are no user activities or system events to display yet.
                                        </p>

                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>


            {{-- Pagination, if supplied --}}
            @if(isset($activities) && method_exists($activities, 'links'))
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $activities->links() }}
                </div>
            @endif

        </div>

    </div>
</div>
@endsection