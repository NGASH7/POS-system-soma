@extends('layouts.app')

@section('content')
<div class="soma-page">
    <div class="soma-page-inner max-w-4xl">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('expenses.index') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1 mb-3">
                    <i class="fas fa-arrow-left text-xs"></i> Back to expenses
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Categories</h1>
                <p class="text-sm text-slate-500 mt-1">Organize expenses by type</p>
            </div>
            <button onclick="openCreateModal()" class="soma-btn-primary">
                <i class="fas fa-plus text-xs"></i> Add category
            </button>
        </div>

        <div class="soma-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="soma-table w-full">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Expenses</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $category->color }}"></span>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $category->name }}</p>
                                        @if($category->description)
                                            <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($category->description, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center text-slate-500">{{ $category->expenses_count ?? 0 }}</td>
                            <td class="text-center">
                                @if($category->is_active)
                                    <span class="text-xs font-medium px-2 py-1 rounded-md bg-emerald-50 text-emerald-700">Active</span>
                                @else
                                    <span class="text-xs font-medium px-2 py-1 rounded-md bg-slate-100 text-slate-500">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="openEditModal({{ $category->id }})" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Edit">
                                        <i class="fas fa-pen text-sm"></i>
                                    </button>
                                    <button onclick="deleteCategory({{ $category->id }})" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-16 text-slate-400">
                                <p class="text-sm">No categories yet.</p>
                                <button onclick="openCreateModal()" class="text-sm text-blue-600 hover:underline mt-1">Add your first category</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

<div id="category-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
    <div class="soma-card w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center mb-5">
            <h3 id="modal-title" class="text-lg font-bold text-slate-900">Add category</h3>
            <button onclick="closeModal()" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="category-form" method="POST" action="" class="space-y-4">
            @csrf
            <input type="hidden" id="category-method" name="_method" value="">

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Name <span class="text-rose-500">*</span></label>
                <input type="text" id="category-name" name="name" required placeholder="e.g. Utilities" class="soma-input">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="category-color" name="color" value="#6b7280"
                           class="w-10 h-10 p-0.5 border border-slate-200 rounded-lg cursor-pointer bg-white">
                    <span id="color-hex" class="text-xs text-slate-400 font-mono">#6b7280</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1.5">Description</label>
                <textarea id="category-description" name="description" rows="2" placeholder="Optional" class="soma-input resize-none"></textarea>
            </div>

            <div id="active-field">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="category-active" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-slate-700">Active</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="soma-btn-secondary">Cancel</button>
                <button type="submit" class="soma-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modal-title').textContent = 'Add category';
        document.getElementById('category-form').action = '{{ route("expenses.categories.store") }}';
        document.getElementById('category-method').value = '';
        document.getElementById('category-name').value = '';
        document.getElementById('category-description').value = '';
        document.getElementById('category-color').value = '#6b7280';
        document.getElementById('color-hex').textContent = '#6b7280';
        document.getElementById('category-active').checked = true;
        document.getElementById('active-field').classList.add('hidden');
        document.getElementById('category-modal').classList.remove('hidden');
        document.getElementById('category-modal').classList.add('flex');
    }

    function openEditModal(id) {
        fetch(`/expenses/categories/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modal-title').textContent = 'Edit category';
                document.getElementById('category-form').action = `/expenses/categories/${id}`;
                document.getElementById('category-method').value = 'PUT';
                document.getElementById('category-name').value = data.name;
                document.getElementById('category-description').value = data.description || '';
                document.getElementById('category-color').value = data.color || '#6b7280';
                document.getElementById('color-hex').textContent = data.color || '#6b7280';
                document.getElementById('category-active').checked = data.is_active == 1;
                document.getElementById('active-field').classList.remove('hidden');
                document.getElementById('category-modal').classList.remove('hidden');
                document.getElementById('category-modal').classList.add('flex');
            })
            .catch(() => alert('Could not load category data.'));
    }

    function closeModal() {
        document.getElementById('category-modal').classList.remove('flex');
        document.getElementById('category-modal').classList.add('hidden');
    }

    document.getElementById('category-color').addEventListener('input', function() {
        document.getElementById('color-hex').textContent = this.value;
    });

    function deleteCategory(id) {
        if (confirm('Delete this category? Categories with expenses cannot be removed.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/expenses/categories/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.getElementById('category-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection
