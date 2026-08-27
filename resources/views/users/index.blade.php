@extends('layouts.app')

@section('header_title', 'Users')

@section('content')
<x-soma-page title="User Management" subtitle="Manage system users and their roles">
    <x-slot:actions>
        <a href="{{ route('users.create') }}" class="soma-btn-primary">
            <i class="fas fa-plus"></i> Add User
        </a>
    </x-slot:actions>

    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="soma-table w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Branch/Outlet</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="font-semibold text-slate-950">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="ml-2 rounded bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-800">You</span>
                            @endif
                        </td>
                        <td class="text-slate-500">{{ $user->email }}</td>
                        <td class="text-sm text-slate-500 font-medium">{{ $user->outlet->name ?? 'No Branch Assigned' }}</td>
                        <td class="text-center">
                            @if($user->role === 'admin')
                                <span class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-800">
                                    <i class="fas fa-crown mr-1"></i>Admin
                                </span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                    <i class="fas fa-user mr-1"></i>Employee
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Active</span>
                        </td>
                        <td class="text-center">
                            @if($user->id !== auth()->id())
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('users.edit', $user) }}" class="text-blue-700 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('users.reset-password', $user) }}" class="text-amber-600 hover:text-amber-700">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    <button type="button" onclick="deleteUser({{ $user->id }})" class="text-rose-600 hover:text-rose-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500">
                            <i class="fas fa-users mb-3 block text-4xl"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $users->links() }}
        </div>
    </div>
</x-soma-page>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        const form = document.getElementById('delete-form');
        form.action = `/users/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
