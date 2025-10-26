<div x-data="passwordUpdater()" x-cloak>
    <button @click="showPasswordModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-md">Update Password</button>

    <div x-show="showPasswordModal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4" x-cloak>
        <div @click.outside="showPasswordModal = false" class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Update Password</h3>
            <input type="password" x-model="password" placeholder="New Password" class="w-full mb-3 p-2 border rounded">
            <input type="password" x-model="confirmPassword" placeholder="Confirm Password" class="w-full mb-3 p-2 border rounded">
            <template x-if="error"><p class="text-sm text-red-500 mb-3" x-text="error"></p></template>
            <div class="flex justify-end space-x-2">
                <button @click="showPasswordModal = false" class="px-4 py-2 rounded bg-gray-200">Cancel</button>
                <button @click="updatePassword('{{ route('admin.distributor.updatePassword', $distributor) }}')" class="px-4 py-2 rounded bg-blue-600 text-white">Update</button>
            </div>
        </div>
    </div>
</div>
