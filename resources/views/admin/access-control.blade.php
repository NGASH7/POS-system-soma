@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-950">Access Control</h1>
        <p class="text-slate-500 mt-2">Manage user roles and permissions</p>
    </div>

    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Sales Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-950">{{ $user->name }}@if($user->id === auth()->id()) <span class="text-xs text-blue-500 ml-1">(You)</span>@endif</td>
                        <td class="px-6 py-4 text-slate-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($user->role === 'admin')
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                    <i class="fas fa-crown mr-1"></i>Admin
                                </span>
                            @else
                                <span class="bg-slate-100 text-slate-900 text-xs px-2 py-1 rounded-full">
                                    <i class="fas fa-user mr-1"></i>Employee
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                {{ $user->sales_count }} sales
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
</div>
@endsection