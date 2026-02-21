@extends('admin.admin-layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="mb-4 rounded bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="mb-4 rounded bg-yellow-100 p-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                <strong>Please fix the following:</strong>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-6xl" x-data="salesTypeCrud()">

                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold">Sales Types</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Create and manage sales type master data.</p>
                    </div>

                    <button @click="openCreate()"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                        + Add Sales Type
                    </button>
                </div>

                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.sales-type.index', ['view' => 'active']) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ ($view ?? 'active') === 'active' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                        Active
                    </a>
                    <a href="{{ route('admin.sales-type.index', ['view' => 'trashed']) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ ($view ?? 'active') === 'trashed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                        Trashed
                    </a>
                    <a href="{{ route('admin.sales-type.index', ['view' => 'all']) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ ($view ?? 'active') === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                        All
                    </a>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-white">
                            <tr>
                                <th class="w-20 px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Sales Type</th>
                                @if (($view ?? 'active') !== 'active')
                                    <th class="w-52 px-4 py-3 text-left">Deleted At</th>
                                @endif
                                <th class="w-64 px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($salesTypes as $index => $type)
                                <tr class="bg-white dark:bg-transparent">
                                    <td class="px-4 py-3">
                                        {{ method_exists($salesTypes, 'firstItem') ? $salesTypes->firstItem() + $index : $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 font-medium">{{ $type->sales_type }}</td>
                                    @if (($view ?? 'active') !== 'active')
                                        <td class="px-4 py-3">{{ optional($type->deleted_at)->format('d M Y h:i A') ?? '-' }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-right space-x-2">
                                        @if (! $type->trashed())
                                            <button @click='openEdit({{ $type->id }}, @json($type->sales_type))'
                                                class="inline-flex items-center rounded-md border border-blue-200 px-3 py-1.5 text-blue-700 hover:bg-blue-50 dark:border-blue-800 dark:text-blue-400 dark:hover:bg-blue-950/40">
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.sales-type.destroy', $type) }}"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Move this sales type to trash?')"
                                                    class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950/40">
                                                    Delete
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.sales-type.restore', $type->id) }}"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-md border border-green-200 px-3 py-1.5 text-green-700 hover:bg-green-50 dark:border-green-800 dark:text-green-400 dark:hover:bg-green-950/40">
                                                    Restore
                                                </button>
                                            </form>

                                            <form method="POST"
                                                action="{{ route('admin.sales-type.forceDelete', $type->id) }}"
                                                class="inline"
                                                onsubmit="return confirm('Permanently delete this sales type? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-950/40">
                                                    Permanent Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($view ?? 'active') !== 'active' ? 4 : 3 }}"
                                        class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No sales types found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($salesTypes, 'hasPages') && $salesTypes->hasPages())
                    <div class="mt-4">
                        {{ $salesTypes->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                @endif

                <div x-show="showModal" x-transition
                    class="fixed inset-0 z-[110] flex items-center justify-center bg-black/50 p-4" style="display:none">
                    <div @click.outside="close()"
                        class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900">

                        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white"
                            x-text="mode === 'create' ? 'Add Sales Type' : 'Edit Sales Type'"></h2>

                        <form method="POST" :action="formAction">
                            @csrf
                            <template x-if="mode === 'edit'">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Sales
                                    Type</label>
                                <input type="text" name="sales_type" x-model="salesType" required
                                    class="w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-transparent dark:text-white/90">
                            </div>

                            <div class="mt-5 flex justify-end gap-3">
                                <button type="button" @click="close()"
                                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

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
            const isValidationEdit = @json(old('_method') === 'PUT');
            return {
                showModal: @json($errors->any()),
                mode: isValidationEdit ? 'edit' : 'create',
                salesType: @json(old('sales_type', '')),
                formAction: isValidationEdit ? @json(url()->current()) : "{{ route('admin.sales-type.store') }}",
                editRouteTemplate: "{{ route('admin.sales-type.update', ['sales_type' => '__ID__']) }}",

                openCreate() {
                    this.mode = 'create';
                    this.salesType = '';
                    this.formAction = "{{ route('admin.sales-type.store') }}";
                    this.showModal = true;
                },

                openEdit(id, name) {
                    this.mode = 'edit';
                    this.salesType = name || '';
                    this.formAction = this.editRouteTemplate.replace('__ID__', id);
                    this.showModal = true;
                },

                close() {
                    this.showModal = false;
                }
            }
        }
    </script>
@endpush
