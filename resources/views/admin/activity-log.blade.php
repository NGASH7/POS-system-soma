@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">Activity Log</h1>
        <p class="text-slate-500 mt-2">Track user activities and system events</p>
    </div>

    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($activities as $activity)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-950">{{ $activity->user }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $activity->action }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $activity->details }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $activity->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">No activities found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection