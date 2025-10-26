@php
    $isEdit = isset($user);
@endphp

<form method="POST" action="{{ $isEdit ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid md:grid-cols-2 gap-4">

        <!-- First Name -->
        <div>
            <label for="fname" class="block font-medium">First Name*</label>
            <input type="text" id="fname" name="fname" value="{{ old('fname', $user->fname ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('fname')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Last Name -->
        <div>
            <label for="lname" class="block font-medium">Last Name*</label>
            <input type="text" id="lname" name="lname" value="{{ old('lname', $user->lname ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('lname')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block font-medium">Email*</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Mobile -->
        <div>
            <label for="mobile_number" class="block font-medium">Mobile Number*</label>
            <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('mobile_number')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        @if (!$isEdit)
            <div>
                <label for="password" class="block font-medium">Password*</label>
                <input type="password" id="password" name="password"
                    class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block font-medium">Confirm Password*</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        @else
            <div class="md:col-span-2">
                <label for="password" class="block font-medium">New Password <span class="text-sm text-gray-500">(leave blank to keep unchanged)</span></label>
                <input type="password" id="password" name="password"
                    class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <!-- Address -->
        <div class="md:col-span-2">
            <label for="address" class="block font-medium">Address</label>
            <input type="text" id="address" name="address" value="{{ old('address', $user->address ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('address')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- District -->
        <div>
            <label for="district" class="block font-medium">District</label>
            <input type="text" id="district" name="district" value="{{ old('district', $user->district ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('district')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- City/Town -->
        <div>
            <label for="city_town" class="block font-medium">City/Town</label>
            <input type="text" id="city_town" name="city_town" value="{{ old('city_town', $user->city_town ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('city_town')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- State -->
        <div>
            <label for="state" class="block font-medium">State</label>
            <input type="text" id="state" name="state" value="{{ old('state', $user->state ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('state')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Country -->
        <div>
            <label for="country" class="block font-medium">Country</label>
            <input type="text" id="country" name="country" value="{{ old('country', $user->country ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('country')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pincode -->
        <div>
            <label for="pincode" class="block font-medium">Pincode</label>
            <input type="text" id="pincode" name="pincode" value="{{ old('pincode', $user->pincode ?? '') }}"
                class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('pincode')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Roles -->
        <div class="md:col-span-2">
            <label class="block font-medium mb-1">Assign Roles</label>
            @foreach ($roles as $role)
                <label class="inline-flex items-center mr-4 mb-2">
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                        {{ (isset($user) && $user->roles->contains($role->id)) || (is_array(old('roles')) && in_array($role->id, old('roles', []))) ? 'checked' : '' }}
                        class="rounded border-gray-300">
                    <span class="ml-2">{{ $role->name }}</span>
                </label>
            @endforeach
            @error('roles')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

    </div>

    <div class="mt-6">
        <button type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update' : 'Create' }} User
        </button>
        <a href="{{ route('admin.users.index')}}"  class="text-gray-700 border border-gray-300 px-4 py-2 rounded hover:bg-gray-100">Back</a>
    </div>
</form>
