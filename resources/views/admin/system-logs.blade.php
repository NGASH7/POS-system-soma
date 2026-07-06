@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">System Logs</h1>
        <p class="text-slate-500 mt-2">View application error and activity logs</p>
    </div>

    <div class="soma-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-900">Laravel Log File</h3>
        </div>
        <div class="p-6">
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-xs" style="max-height: 500px; overflow-y: auto;">
                @forelse($logs as $log)
{{ $log }}
                @empty
No logs found.
                @endforelse
            </pre>
        </div>
    </div>
</div>
</div>
@endsection