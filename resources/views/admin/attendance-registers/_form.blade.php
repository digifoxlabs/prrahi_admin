@php
    $rulesSource = old('weekday_rules', []);
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (($method ?? 'POST') === 'PUT')
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="rounded bg-red-100 p-3 text-red-800">
            <strong class="mb-1 block">Please fix the following errors:</strong>
            <ul class="list-inside list-disc text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="mb-1 block font-medium text-gray-700 dark:text-white">Register Name *</label>
            <input type="text" name="name" value="{{ old('name', $attendanceRegister->name ?? '') }}" required
                class="w-full rounded border border-gray-300 bg-white p-2 text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
        </div>

        <div class="flex items-end">
            <label class="inline-flex items-center gap-2 rounded border border-gray-300 px-3 py-2 dark:border-gray-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                    {{ (string) old('is_active', isset($attendanceRegister) ? (int) $attendanceRegister->is_active : 1) === '1' ? 'checked' : '' }}>
                <span class="text-sm text-gray-700 dark:text-gray-200">Set as active register</span>
            </label>
        </div>

        <div>
            <label class="mb-1 block font-medium text-gray-700 dark:text-white">Start Date *</label>
            <input type="date" name="start_date"
                value="{{ old('start_date', isset($attendanceRegister) ? $attendanceRegister->start_date->format('Y-m-d') : '') }}"
                required
                class="w-full rounded border border-gray-300 bg-white p-2 text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
        </div>

        <div>
            <label class="mb-1 block font-medium text-gray-700 dark:text-white">End Date *</label>
            <input type="date" name="end_date"
                value="{{ old('end_date', isset($attendanceRegister) ? $attendanceRegister->end_date->format('Y-m-d') : '') }}"
                required
                class="w-full rounded border border-gray-300 bg-white p-2 text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
        </div>
    </div>

    <div>
        <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white/90">Default Weekly IN/OUT Times</h3>
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left">Day</th>
                        <th class="px-4 py-2 text-left">Default IN</th>
                        <th class="px-4 py-2 text-left">Default OUT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($weekdayNames as $dayIndex => $dayName)
                        @php
                            $modelRule = $weekdayRules[$dayIndex] ?? null;
                            $inTime = $rulesSource[$dayIndex]['in_time'] ?? ($modelRule && $modelRule->default_in_time ? substr($modelRule->default_in_time, 0, 5) : '');
                            $outTime = $rulesSource[$dayIndex]['out_time'] ?? ($modelRule && $modelRule->default_out_time ? substr($modelRule->default_out_time, 0, 5) : '');
                        @endphp
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-700 dark:text-gray-200">{{ $dayName }}</td>
                            <td class="px-4 py-2">
                                <input type="time" name="weekday_rules[{{ $dayIndex }}][in_time]"
                                    value="{{ $inTime }}"
                                    class="w-full rounded border border-gray-300 bg-white p-2 text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                            </td>
                            <td class="px-4 py-2">
                                <input type="time" name="weekday_rules[{{ $dayIndex }}][out_time]"
                                    value="{{ $outTime }}"
                                    class="w-full rounded border border-gray-300 bg-white p-2 text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-2">
        <button type="submit"
            class="rounded bg-green-600 px-4 py-2 text-white transition hover:bg-green-700">{{ $buttonText }}</button>
        <a href="{{ route('admin.attendance-registers.index') }}"
            class="ml-3 rounded border border-gray-300 bg-gray-100 px-4 py-2 text-gray-800 transition hover:bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">Cancel</a>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pickerInputs = document.querySelectorAll('input[type="date"], input[type="time"]');

            pickerInputs.forEach(function(input) {
                if (typeof input.showPicker !== 'function') {
                    return;
                }

                const openPicker = function() {
                    try {
                        input.showPicker();
                    } catch (e) {
                        // ignore browser restrictions when picker cannot open
                    }
                };

                input.addEventListener('focus', openPicker);
                input.addEventListener('click', openPicker);
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ' || event.key === 'ArrowDown') {
                        event.preventDefault();
                        openPicker();
                    }
                });
            });
        });
    </script>
@endpush
