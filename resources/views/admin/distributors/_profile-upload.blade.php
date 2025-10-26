<div x-data="profileImageUploader()" x-cloak>
    <button @click="isProfileImageModal = true" class="px-4 py-2 rounded-md border bg-white hover:bg-gray-100">Update Photo</button>

    <div x-show="isProfileImageModal" class="fixed inset-0 flex items-center justify-center p-5 z-50 bg-black/50" x-cloak>
        <div @click.outside="closeModal" class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Upload Profile Photo</h3>
            <input type="file" @change="handleFile($event)" accept="image/*" class="w-full mb-4">
            <div class="overflow-hidden mb-4">
                <img id="crop-image" class="max-h-60 mx-auto">
            </div>
            <div class="flex justify-end space-x-2">
                <button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                <button @click="uploadCroppedImage('{{ route('admin.distributor.updateProfileImage', $distributor) }}')" class="px-4 py-2 bg-blue-600 text-white rounded">Upload</button>
            </div>
        </div>
    </div>
</div>
