<form
    action="{{ isset($permission) ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}"
    method="POST"
    class="space-y-4"
>
    @csrf
    @if(isset($permission))
        @method('PUT')
    @endif

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Permission Name</label>
        <input
            type="text"
            name="name"
            id="name"
            value="{{ old('name', $permission->name ?? '') }}"
            class="mt-1 block w-full border border-gray-300 rounded px-3 py-2"
            required
        />
        @error('name')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end space-x-2">
        <a href="{{ route('admin.permissions.index') }}"  class="text-gray-700 border border-gray-300 px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
        <button
            type="submit"
            class="px-4 py-2 bg-{{ isset($permission) ? 'green' : 'blue' }}-600 text-white rounded hover:bg-{{ isset($permission) ? 'green' : 'blue' }}-700"
        >
            {{ isset($permission) ? 'Update' : 'Create' }}
        </button>
    </div>
</form>
