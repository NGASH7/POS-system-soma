@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-950">Database Backup</h1>
                <p class="text-slate-500 mt-2">Manage database backups</p>
            </div>
            <form method="POST" action="{{ route('admin.database-backup.create') }}">
                @csrf
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-xl transition">
                    <i class="fas fa-database mr-2"></i>Create New Backup
                </button>
            </form>
        </div>
    </div>

    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">File Name</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Size (KB)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($backupFiles as $backup)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-950">{{ $backup->name }}</td>
                        <td class="px-6 py-4 text-right text-slate-500">{{ number_format($backup->size, 2) }} KB</td>
                        <td class="px-6 py-4 text-slate-500">{{ $backup->date }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('admin.database-backup.download', $backup->name) }}" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.database-backup.delete', $backup->name) }}" class="inline" onsubmit="return confirm('Delete this backup?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-database text-4xl mb-3 block"></i>
                            No backups found. Click "Create New Backup" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection