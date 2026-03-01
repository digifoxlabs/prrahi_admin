@extends('admin.admin-layout')
@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.categories._breadcrump')

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

        <div
            class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-6xl">
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white/90">Categories</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Manage segments and sub-categories used in
                            products.</p>
                    </div>
                    @if (Auth::guard('admin')->user()->hasPermission('create_categories'))
                        <a href="{{ route('admin.categories.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                            + Add Category
                        </a>
                    @endif
                </div>

                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.categories.index', ['view' => 'active', 'search' => $search]) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ ($view ?? 'active') === 'active' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                        Active
                    </a>
                    <a href="{{ route('admin.categories.index', ['view' => 'trashed', 'search' => $search]) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ ($view ?? 'active') === 'trashed' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                        Trashed
                    </a>
                    <a href="{{ route('admin.categories.index', ['view' => 'all', 'search' => $search]) }}"
                        class="rounded-lg px-3 py-2 text-sm {{ ($view ?? 'active') === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}">
                        All
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-5">
                    <input type="hidden" name="view" value="{{ $view ?? 'active' }}">
                    <div class="relative w-full sm:max-w-md">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Search category or parent..."
                            class="w-full rounded-lg border border-gray-300 py-2.5 pl-3 pr-10 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-transparent dark:text-white/90" />
                        @if (!empty($search))
                            <a href="{{ route('admin.categories.index', ['view' => $view ?? 'active']) }}"
                                class="absolute inset-y-0 right-3 inline-flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-white">
                            <tr>
                                <th class="w-20 px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Category</th>
                                <th class="w-44 px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Parent Segment</th>
                                @if (($view ?? 'active') !== 'active')
                                    <th class="w-48 px-4 py-3 text-left">Deleted At</th>
                                @endif
                                <th class="w-56 px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($categories as $index => $cat)
                                <tr class="bg-white dark:bg-transparent">
                                    <td class="px-4 py-3">
                                        {{ method_exists($categories, 'firstItem') ? $categories->firstItem() + $index : $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $cat->name }}</td>
                                    <td class="px-4 py-3">
                                        @if ($cat->parent_id)
                                            <span
                                                class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                                Sub Category
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                Segment
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $cat->parent?->name ?? '-' }}</td>
                                    @if (($view ?? 'active') !== 'active')
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                            {{ optional($cat->deleted_at)->format('d M Y h:i A') ?? '-' }}
                                        </td>
                                    @endif
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            @if (! $cat->trashed())
                                                @if (Auth::guard('admin')->user()->hasPermission('edit_categories'))
                                                    <a href="{{ route('admin.categories.edit', $cat) }}"
                                                        class="inline-flex items-center rounded-md border border-blue-200 px-3 py-1.5 text-blue-700 hover:bg-blue-50 dark:border-blue-800 dark:text-blue-300 dark:hover:bg-blue-950/40">
                                                        Edit
                                                    </a>
                                                @endif
                                                @if (Auth::guard('admin')->user()->hasPermission('delete_categories'))
                                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            onclick="return confirm('Move this category to trash?')"
                                                            class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950/40">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                @if (Auth::guard('admin')->user()->hasPermission('delete_categories'))
                                                    <form method="POST" action="{{ route('admin.categories.restore', $cat->id) }}"
                                                        class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                            class="inline-flex items-center rounded-md border border-green-200 px-3 py-1.5 text-green-700 hover:bg-green-50 dark:border-green-800 dark:text-green-300 dark:hover:bg-green-950/40">
                                                            Restore
                                                        </button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('admin.categories.forceDelete', $cat->id) }}"
                                                        class="inline"
                                                        onsubmit="return confirm('Permanently delete this category? This cannot be undone.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center rounded-md border border-red-200 px-3 py-1.5 text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950/40">
                                                            Permanent Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ ($view ?? 'active') !== 'active' ? 6 : 5 }}"
                                        class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($categories, 'hasPages') && $categories->hasPages())
                    <div class="mt-4">
                        {{ $categories->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'categories',
        };
    </script>
@endpush
