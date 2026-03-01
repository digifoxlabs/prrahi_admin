@extends('admin.admin-layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
        @include('admin.attendance-registers._breadcrump', ['pageName' => 'Create Attendance Register'])

        <div
            class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-6xl">
                <h2 class="mb-4 text-xl font-semibold text-gray-800 dark:text-white/90">Create Attendance Register</h2>

                @include('admin.attendance-registers._form', [
                    'action' => route('admin.attendance-registers.store'),
                    'method' => 'POST',
                    'buttonText' => 'Create Register',
                    'attendanceRegister' => null,
                    'weekdayRules' => collect(),
                ])
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
