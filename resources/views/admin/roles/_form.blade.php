<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <!-- Role Name -->
    <div class="mb-4">
        <label for="name" class="block text-left text-sm font-bold text-gray-700">Role Name</label>
        <input
            type="text"
            name="name"
            id="name"
            value="{{ old('name', $role->name ?? '') }}"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2"
        >
        @error('name')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Permissions Checkboxes -->
    <div class="mb-4">
        <label class="block text-sm font-bold text-gray-700 mb-2 text-left">Permissions</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
            @foreach ($permissions as $permission)
                <label class="inline-flex items-center space-x-2">
                    <input
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $permission->id }}"
                        @if(isset($role) && $role->permissions->contains($permission->id)) checked @endif
                        class="rounded text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                </label>
            @endforeach
        </div>
        @error('permissions')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div>
        <a href="{{ route('admin.roles.index') }}"  class="text-gray-700 border border-gray-300 px-4 py-2 rounded hover:bg-gray-100"> Cancel</a>
        <button
            type="submit"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
            {{ $buttonText }}
        </button>
    </div>
</form>
