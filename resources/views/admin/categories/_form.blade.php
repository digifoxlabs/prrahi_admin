@php
    $isEdit = isset($category);
    $selectedParentId = old('parent_id', $category->parent_id ?? '');
    $adminUser = Auth::guard('admin')->user();
    $canSubmit = $isEdit
        ? ($adminUser && $adminUser->hasPermission('edit_categories'))
        : ($adminUser && $adminUser->hasPermission('create_categories'));
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
    class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div>
        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Category Name</label>
        <input id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required
            class="w-full rounded-lg border px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none dark:bg-transparent dark:text-white/90 {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}"
            placeholder="e.g. Personal Care" />
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="parent_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Parent Segment
            (Optional)</label>
        <select id="parent_id" name="parent_id"
            x-init="$nextTick(() => { if (window.TomSelect && !$el.tomselect) { new TomSelect($el, { create: false, allowEmptyOption: true, placeholder: 'Choose parent segment' }); } })"
            class="w-full rounded-lg border px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none dark:bg-transparent dark:text-white/90 {{ $errors->has('parent_id') ? 'border-red-500' : 'border-gray-300 dark:border-gray-700' }}">
            <option value="">None (Segment)</option>
            @foreach ($segments as $seg)
                <option value="{{ $seg->id }}" @selected((string) $selectedParentId === (string) $seg->id)>
                    {{ $seg->name }}
                </option>
            @endforeach
        </select>
        @error('parent_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
        @if ($canSubmit)
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                {{ $isEdit ? 'Update Category' : 'Create Category' }}
            </button>
        @endif

        <a href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            Cancel
        </a>
    </div>
</form>
