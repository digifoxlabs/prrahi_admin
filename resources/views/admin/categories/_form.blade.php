<form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if(isset($category))
        @method('PUT')
    @endif

    <label class="block mb-2">Category Name</label>
    <input name="name" value="{{ old('name', $category->name ?? '') }}"
           class="w-full border p-2 mb-4 rounded" required>

    <label class="block mb-2">Parent Segment (Optional)</label>
    <select name="parent_id" class="w-full border p-2 mb-4 rounded">
        <option value="">None</option>
        @foreach($segments as $seg)
            <option value="{{ $seg->id }}"
                @selected((old('parent_id') ?? $category->parent_id ?? null) == $seg->id)>
                {{ $seg->name }}
            </option>
        @endforeach
    </select>

    <div class="flex items-center gap-4 mt-4">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ isset($category) ? 'Update Category' : 'Create Category' }}
        </button>

        <a href="{{ route('admin.categories.index') }}"
           class="text-gray-700 border border-gray-300 px-4 py-2 rounded hover:bg-gray-100">
            Cancel
        </a>
    </div>
</form>
