@extends('layouts.app')

@section('header_title', 'Branches')

@section('content')
<x-soma-page title="Branch Management" subtitle="Manage your POS outlet locations">
    <x-slot:actions>
        <a href="{{ route('admin.outlets.create') }}" class="soma-btn-primary">
            <i class="fas fa-plus"></i> Add Branch
        </a>
    </x-slot:actions>

    <div class="soma-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="soma-table w-full">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Contact Info</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Users</th>
                        <th class="text-center">Products</th>
                        <th class="text-center">Sales</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outlets as $outlet)
                    <tr>
                        <td class="font-semibold text-slate-950">
                            {{ $outlet->name }}
                            @if($outlet->id === 1)
                                <span class="ml-2 rounded bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-800">HQ</span>
                            @endif
                            @if($outlet->id == session('active_outlet_id'))
                                <span class="ml-2 rounded bg-emerald-100 px-2 py-0.5 text-xs font-bold text-emerald-800">Active</span>
                            @endif
                        </td>
                        <td class="text-sm text-slate-500">{{ $outlet->location ?? '—' }}</td>
                        <td class="text-sm text-slate-500">{{ $outlet->contact_info ?? '—' }}</td>
                        <td class="text-center">
                            @if($outlet->is_active)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Active</span>
                            @else
                                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-800">
                                {{ $outlet->users_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold text-sky-800">
                                {{ $outlet->products_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-bold text-purple-800">
                                {{ $outlet->sales_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center gap-3">
                                <a href="{{ route('admin.outlets.edit', $outlet) }}" class="text-blue-700 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($outlet->id !== 1 && $outlet->id != session('active_outlet_id'))
                                <button type="button" onclick="deleteOutlet({{ $outlet->id }}, '{{ addslashes($outlet->name) }}')" class="text-rose-600 hover:text-rose-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            <i class="fas fa-store mb-3 block text-4xl"></i>
                            No branch outlets found. Click "Add Branch" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $outlets->links() }}
        </div>
    </div>
</x-soma-page>

<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function deleteOutlet(id, name) {
    if (confirm(`Are you sure you want to delete the branch "${name}"? This action cannot be undone.`)) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/outlets/${id}`;
        form.submit();
    }
}
</script>
@endpush
@endsection
