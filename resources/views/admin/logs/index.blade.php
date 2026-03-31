@extends('admin.admin-layout')

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ $title }}</h1>
            <p class="text-sm text-gray-500">View recent log entries and clear log files from the admin panel.</p>
        </div>
    </div>

    @include('partials.flash')

    <div class="grid gap-4 lg:grid-cols-12">
        <div class="lg:col-span-3">
            <div class="rounded-xl border bg-white p-4">
                <h2 class="mb-3 text-sm font-semibold text-gray-700">Log Files</h2>

                @if($logFiles->isEmpty())
                    <p class="text-sm text-gray-500">No log files found.</p>
                @else
                    <div class="space-y-2">
                        @foreach($logFiles as $logFile)
                            @php($relativePath = str_replace('\\', '/', ltrim(str_replace(storage_path('logs'), '', $logFile), DIRECTORY_SEPARATOR)))
                            <a href="{{ route('admin.logs.index', ['file' => $relativePath]) }}"
                               class="block rounded-lg border px-3 py-2 text-sm transition {{ $selectedFile === str_replace('\\', DIRECTORY_SEPARATOR, ltrim(str_replace(storage_path('logs'), '', $logFile), DIRECTORY_SEPARATOR)) ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 hover:bg-gray-50' }}">
                                {{ $relativePath }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-9">
            <div class="rounded-xl border bg-white">
                <div class="flex flex-col gap-3 border-b p-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-700">Log Preview</h2>
                        <p class="text-xs text-gray-500">
                            {{ $selectedFile ? $selectedFile : 'Select a log file to inspect.' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if($selectedFile)
                            <form method="POST" action="{{ route('admin.logs.clear') }}">
                                @csrf
                                <input type="hidden" name="scope" value="selected">
                                <input type="hidden" name="file" value="{{ str_replace('\\', '/', $selectedFile) }}">
                                <button type="submit"
                                        class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                                        onclick="return confirm('Clear the selected log file?')">
                                    Clear Selected
                                </button>
                            </form>
                        @endif

                        @if($logFiles->isNotEmpty())
                            <form method="POST" action="{{ route('admin.logs.clear') }}">
                                @csrf
                                <input type="hidden" name="scope" value="all">
                                <button type="submit"
                                        class="rounded-lg border border-red-300 px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                                        onclick="return confirm('Clear all log files in storage/logs?')">
                                    Clear All Logs
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="p-4">
                    <pre class="max-h-[70vh] overflow-auto rounded-lg bg-slate-950 p-4 text-xs text-slate-100 whitespace-pre-wrap">{{ $logContents !== '' ? $logContents : 'No log content available.' }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.pageXData = {
        page: 'logs',
    };
</script>
@endpush
