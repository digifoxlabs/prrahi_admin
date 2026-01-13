{{-- resources/views/admin/sales-types/index.blade.php --}}
@extends('admin.admin-layout')

@section('page-content')
<div x-data="salesTypeCrud()" class="max-w-4xl mx-auto p-6 space-y-6">

    {{-- Flash --}}
    @include('partials.flash')

    <div class="flex justify-between items-center">
        <h1 class="text-xl font-semibold">Sales Types</h1>
        <button @click="openCreate()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Add Sales Type
        </button>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Sales Type</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($salesTypes as $type)
                <tr>
                    <td class="p-3">{{ $type->sales_type }}</td>
                    <td class="p-3 text-right space-x-2">
                        <button
                            @click="openEdit({{ $type->id }}, '{{ $type->sales_type }}')"
                            class="text-indigo-600 hover:underline">
                            Edit
                        </button>

                        <form method="POST"
                              action="{{ route('admin.sales-type.destroy', $type) }}"
                              class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete this sales type?')"
                                    class="text-red-600 hover:underline">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL --}}
    <div x-show="showModal"
         x-transition
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
         style="display:none">

        <div @click.outside="close()"
             class="bg-white w-full max-w-md rounded-xl p-6 space-y-4">

            <h2 class="text-lg font-semibold"
                x-text="mode === 'create' ? 'Add Sales Type' : 'Edit Sales Type'"></h2>

            <form method="POST" :action="formAction">
                @csrf
                <template x-if="mode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-sm mb-1">Sales Type</label>
                    <input type="text"
                           name="sales_type"
                           x-model="salesType"
                           required
                           class="w-full border rounded-lg p-2">
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button"
                            @click="close()"
                            class="px-4 py-2 border rounded-lg">
                        Cancel
                    </button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'sales-type',
        };
    </script>  
<script>
function salesTypeCrud() {
    return {
        showModal: false,
        mode: 'create',
        salesType: '',
        formAction: '',

        openCreate() {
            this.mode = 'create';
            this.salesType = '';
            this.formAction = "{{ route('admin.sales-type.store') }}";
            this.showModal = true;
        },

        openEdit(id, name) {
            this.mode = 'edit';
            this.salesType = name;
            this.formAction = `/admin/sales-types/${id}`;
            this.showModal = true;
        },

        close() {
            this.showModal = false;
        }
    }
}
</script>
@endpush
