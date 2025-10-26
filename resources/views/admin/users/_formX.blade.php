@php
    $isEdit = isset($user);
@endphp

<div class="grid md:grid-cols-2 gap-4">

    <div>
        <label for="fname" class="block font-medium">First Name*</label>
        <input type="text" id="fname" name="fname" value="{{ old('fname', $user->fname ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label for="lname" class="block font-medium">Last Name*</label>
        <input type="text" id="lname" name="lname" value="{{ old('lname', $user->lname ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label for="email" class="block font-medium">Email*</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label for="mobile_number" class="block font-medium">Mobile Number*</label>
        <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    @if (!$isEdit)
        <div>
            <label for="password" class="block font-medium">Password*</label>
            <input type="password" id="password" name="password"
                   class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label for="password_confirmation" class="block font-medium">Confirm Password*</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    @endif

    <div class="md:col-span-2">
        <label for="address" class="block font-medium">Address</label>
        <input type="text" id="address" name="address" value="{{ old('address', $user->address ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label for="district" class="block font-medium">District</label>
        <input type="text" id="district" name="district" value="{{ old('district', $user->district ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label for="city_town" class="block font-medium">City/Town</label>
        <input type="text" id="city_town" name="city_town" value="{{ old('city_town', $user->city_town ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label for="state" class="block font-medium">State</label>
        <input type="text" id="state" name="state" value="{{ old('state', $user->state ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label for="country" class="block font-medium">Country</label>
        <input type="text" id="country" name="country" value="{{ old('country', $user->country ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label for="pincode" class="block font-medium">Pincode</label>
        <input type="text" id="pincode" name="pincode" value="{{ old('pincode', $user->pincode ?? '') }}"
               class="w-full border border-gray-300 bg-gray-100 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

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
    </div>
</div>
