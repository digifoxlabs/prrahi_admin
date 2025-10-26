<form method="POST" action="{{ isset($fragrance) ? route('admin.fragrances.update', $fragrance) : route('admin.fragrances.store') }}">
    @csrf
    @if(isset($fragrance))
        @method('PUT')
    @endif

    <label class="block mb-2">Fragrance Name</label>
    <input name="name" value="{{ old('name', $fragrance->name ?? '') }}"
           class="w-full border p-2 mb-4 rounded" required>


    <div class="flex items-center gap-4 mt-4">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ isset($fragrance) ? 'Update Fragrance' : 'Create Fragrance' }}
        </button>

        <a href="{{ route('admin.fragrances.index') }}"
           class="text-gray-700 border border-gray-300 px-4 py-2 rounded hover:bg-gray-100">
            Cancel
        </a>
    </div>
</form>
