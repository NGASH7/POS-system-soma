@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-tags text-purple-600 mr-2"></i>Expense Categories
                </h1>
                <p class="text-gray-600 mt-2">Manage your expense categories</p>
            </div>
            <button onclick="openCreateModal()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Add Category
            </button>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Color</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Expenses</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $category->slug }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="w-8 h-8 rounded-full mx-auto border border-gray-200" style="background: {{ $category->color }}"></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                {{ $category->expenses_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($category->is_active)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <button onclick="openEditModal({{ $category->id }})" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteCategory({{ $category->id }})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-tags text-4xl mb-3 block"></i>
                            No categories found. Click "Add Category" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $categories->links() }}
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="category-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="bg-white rounded-xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-title" class="text-xl font-bold text-gray-800">Add Category</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <form id="category-form" method="POST" action="">
            @csrf
            <input type="hidden" id="category-id" name="category_id" value="">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                    <input type="text" id="category-name" name="name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="e.g., Office Supplies">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                    <div class="flex items-center space-x-4">
                        <input type="color" id="category-color" name="color" value="#6b7280"
                               class="w-12 h-12 p-1 border border-gray-300 rounded-lg cursor-pointer">
                        <span id="color-hex" class="text-sm text-gray-500">#6b7280</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="category-description" name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Optional description"></textarea>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="category-active" name="is_active" value="1" checked class="mr-2">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-save mr-2"></i>Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Open create modal
    function openCreateModal() {
        document.getElementById('modal-title').textContent = 'Add Category';
        document.getElementById('category-form').action = '{{ route("expenses.categories.store") }}';
        document.getElementById('category-id').value = '';
        document.getElementById('category-name').value = '';
        document.getElementById('category-description').value = '';
        document.getElementById('category-color').value = '#6b7280';
        document.getElementById('color-hex').textContent = '#6b7280';
        document.getElementById('category-active').checked = true;
        document.getElementById('category-modal').classList.remove('hidden');
        document.getElementById('category-modal').classList.add('flex');
    }

    // Open edit modal
    function openEditModal(id) {
        // Get category data via AJAX
        fetch(`/expenses/categories/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modal-title').textContent = 'Edit Category';
                document.getElementById('category-form').action = `/expenses/categories/${id}`;
                document.getElementById('category-id').value = id;
                document.getElementById('category-name').value = data.name;
                document.getElementById('category-description').value = data.description || '';
                document.getElementById('category-color').value = data.color || '#6b7280';
                document.getElementById('color-hex').textContent = data.color || '#6b7280';
                document.getElementById('category-active').checked = data.is_active == 1;
                document.getElementById('category-modal').classList.remove('hidden');
                document.getElementById('category-modal').classList.add('flex');
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error loading category data', 'error');
            });
    }

    // Close modal
    function closeModal() {
        document.getElementById('category-modal').classList.remove('flex');
        document.getElementById('category-modal').classList.add('hidden');
    }

    // Color picker change
    document.getElementById('category-color').addEventListener('input', function() {
        document.getElementById('color-hex').textContent = this.value;
    });

    // Delete category
    function deleteCategory(id) {
        if (confirm('Are you sure you want to delete this category? This will remove it from all associated expenses.')) {
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

    // Close modal on outside click
    document.getElementById('category-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection