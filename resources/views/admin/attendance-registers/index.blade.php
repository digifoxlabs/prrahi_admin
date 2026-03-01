@extends('admin.admin-layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        @include('admin.attendance-registers._breadcrump', ['pageName' => 'Attendance Registers'])

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
            <div class="mx-auto w-full max-w-7xl">
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <form method="GET" action="{{ route('admin.attendance-registers.index') }}" class="w-full sm:max-w-md">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search registers..."
                            class="w-full rounded-lg border border-gray-300 py-2.5 pl-3 pr-10 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-transparent dark:text-white/90" />
                    </form>

                    @if (Auth::guard('admin')->user()->hasPermission('create_attendance'))
                        <a href="{{ route('admin.attendance-registers.create') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                            + Create Register
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full table-auto text-sm">
                        <thead class="bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-white">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Start Date</th>
                                <th class="px-4 py-3 text-left">End Date</th>
                                <th class="px-4 py-3 text-left">Participants</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($registers as $index => $register)
                                <tr>
                                    <td class="px-4 py-3">
                                        {{ method_exists($registers, 'firstItem') ? $registers->firstItem() + $index : $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $register->name }}</td>
                                    <td class="px-4 py-3">{{ $register->start_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3">{{ $register->end_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3">{{ $register->participants_count }}</td>
                                    <td class="px-4 py-3">
                                        @if ($register->is_active)
                                            <span
                                                class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Active</span>
                                        @else
                                            <span
                                                class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            @if (Auth::guard('admin')->user()->hasPermission('view_attendance'))
                                                <a href="{{ route('admin.attendance-registers.show', $register) }}"
                                                    class="rounded-md border border-blue-200 px-3 py-1.5 text-blue-700 hover:bg-blue-50 dark:border-blue-800 dark:text-blue-300 dark:hover:bg-blue-950/40">
                                                    Open
                                                </a>
                                            @endif
                                            @if (Auth::guard('admin')->user()->hasPermission('edit_attendance'))
                                                <a href="{{ route('admin.attendance-registers.edit', $register) }}"
                                                    class="rounded-md border border-green-200 px-3 py-1.5 text-green-700 hover:bg-green-50 dark:border-green-800 dark:text-green-300 dark:hover:bg-green-950/40">
                                                    Edit
                                                </a>
                                            @endif
                                            @if (Auth::guard('admin')->user()->hasPermission('delete_attendance'))
                                                <form method="POST"
                                                    action="{{ route('admin.attendance-registers.destroy', $register) }}"
                                                    class="inline" onsubmit="return confirm('Delete this attendance register?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="rounded-md border border-red-200 px-3 py-1.5 text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950/40">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No
                                        attendance registers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($registers, 'hasPages') && $registers->hasPages())
                    <div class="mt-4">
                        {{ $registers->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'attendance-registers',
        };
    </script>
@endpush
