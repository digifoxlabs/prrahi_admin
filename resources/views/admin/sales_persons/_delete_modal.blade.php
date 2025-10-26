<div x-data="{ showDeleteModal: false }">
    <button @click="showDeleteModal = true" class="px-4 py-2 bg-red-600 text-white rounded-md">Delete</button>
    <div x-show="showDeleteModal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4" x-cloak>
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4 text-red-600">Confirm Deletion</h3>
            <p class="mb-6">Are you sure you want to delete <strong>{{ $salesPerson->name }}</strong>?</p>
            <div class="flex justify-end space-x-3">
                <button @click="showDeleteModal = false" class="px-4 py-2 rounded bg-gray-200">Cancel</button>
                <form action="{{ route('admin.sales-persons.destroy', $salesPerson) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
