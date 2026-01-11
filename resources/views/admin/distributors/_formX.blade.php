@props(['distributor', 'action', 'method', 'salesPersons'])

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      class="space-y-10  p-8   text-left transition">
    @csrf
    @if ($method === 'PUT') @method('PUT') @endif

    {{-- Error messages --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 border border-red-300 dark:border-red-700 rounded-lg">
            <h3 class="font-semibold mb-2">Please fix the following errors:</h3>
            <ul class="list-disc list-inside space-y-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Basic Info --}}
    <div class="border rounded-xl shadow bg-gray-50 dark:bg-gray-800 p-6 transition">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">🧾 Basic Information</h3>

        <div class="grid grid-cols-2 gap-6">
            {{-- Sales Person --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Sales Person</label>
                <select name="sales_persons_id"
                        class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">-- Select Sales Person --</option>
                    @foreach($salesPersons as $salesPerson)
                        <option value="{{ $salesPerson->id }}" {{ old('sales_persons_id', $distributor->sales_persons_id ?? '') == $salesPerson->id ? 'selected' : '' }}>
                            {{ $salesPerson->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Appointment Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Appointment Date *</label>
                <input type="text" id="appointment_date" name="appointment_date"
                       value="{{ old('appointment_date', isset($distributor) && $distributor->appointment_date ? $distributor->appointment_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Firm Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Firm Name *</label>
                <input type="text" name="firm_name" value="{{ old('firm_name', $distributor->firm_name ?? '') }}"
                       class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            {{-- Nature of Firm --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Nature of Firm *</label>
                <select name="nature_of_firm"
                        class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">-- Select --</option>
                    @foreach (['Proprietorship', 'Partnership', 'LLP', 'Pvt Ltd', 'Ltd'] as $type)
                        <option value="{{ $type }}" {{ old('nature_of_firm', $distributor->nature_of_firm ?? '') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Repeated fields --}}
            @foreach ([
                'address_line_1', 'address_line_2', 'town', 'district', 'state', 'pincode', 'landmark',
                'contact_person', 'designation_contact', 'contact_number', 'email', 'gst',
                'date_of_birth', 'date_of_anniversary'
            ] as $field)
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                        {{ ucwords(str_replace('_', ' ', $field)) }}
                    </label>
                    <input type="{{ in_array($field, ['date_of_birth','date_of_anniversary']) ? 'date' : 'text' }}"
                           name="{{ $field }}"
                           value="{{ old($field, $distributor->$field ?? '') }}"
                           class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>
            @endforeach
        </div>
    </div>

    {{-- Location --}}
    <div class="border rounded-xl shadow bg-gray-50 dark:bg-gray-800 p-6 transition">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">📍 Location Information</h3>
        <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Latitude</label>
                <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $distributor->latitude ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400" readonly>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Longitude</label>
                <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $distributor->longitude ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400" readonly>
            </div>
            <div class="md:col-span-2 text-right">
                <button type="button" onclick="openMapModal()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-500 transition">
                    📌 Set Location on Map
                </button>
            </div>
        </div>
    </div>

    @include('admin.distributors.map-modal')

    {{-- Dynamic Sections --}}
    @php
        function getSectionData($key, $distributor) {
            return old($key) ?: ($distributor ? $distributor->{$key}->toArray() : []);
        }
        $dynamicSections = [
            ['title' => 'Distributor Companies', 'key' => 'companies', 'fields' => [
                ['label' => 'Company Name', 'name' => 'company_name'],
                ['label' => 'Segment', 'name' => 'segment'],
                ['label' => 'Brand Name', 'name' => 'brand_name'],
                ['label' => 'Products', 'name' => 'products'],
                ['label' => 'Working As', 'name' => 'working_as'],
                ['label' => 'Margin', 'name' => 'margin'],
                ['label' => 'Payment Terms', 'name' => 'payment_terms'],
                ['label' => 'Working Since', 'name' => 'working_since'],
                ['label' => 'Area of Operation', 'name' => 'area_operation'],
                ['label' => 'Monthly TO', 'name' => 'monthly_to'],
                ['label' => 'DSR No', 'name' => 'dsr_no'],
                ['label' => 'Details', 'name' => 'details'],
            ]],
            ['title' => 'Bank Details', 'key' => 'banks', 'fields' => [
                ['label' => 'Bank Name', 'name' => 'bank_name'],
                ['label' => 'Branch Name', 'name' => 'branch_name'],
                ['label' => 'Current A/C', 'name' => 'current_ac'],
                ['label' => 'IFSC Code', 'name' => 'ifsc'],
            ]],
            ['title' => 'Godown Details', 'key' => 'godowns', 'fields' => [
                ['label' => 'No. of Godowns', 'name' => 'no_godown'],
                ['label' => 'Godown Size', 'name' => 'godown_size'],
            ]],
            ['title' => 'Manpower', 'key' => 'manpowers', 'fields' => [
                ['label' => 'Sales Staff', 'name' => 'sales'],
                ['label' => 'Accounts Staff', 'name' => 'accounts'],
                ['label' => 'Godown Staff', 'name' => 'godown'],
            ]],
            ['title' => 'Vehicles', 'key' => 'vehicles', 'fields' => [
                ['label' => 'Two Wheeler', 'name' => 'two_wheeler'],
                ['label' => 'Three Wheeler', 'name' => 'three_wheeler'],
                ['label' => 'Four Wheeler', 'name' => 'four_wheeler'],
            ]],
        ];
    @endphp

    @foreach ($dynamicSections as $section)
        @php $sectionData = getSectionData($section['key'], $distributor ?? null); @endphp

        <div x-data="{
                items: @json($sectionData),
                addItem() { this.items.push({}); },
                removeItem(index) { this.items.splice(index, 1); }
            }"
            class="border rounded-xl bg-gray-50 dark:bg-gray-800 p-6 mb-8 transition">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $section['title'] }}</h3>
                <button type="button" @click="addItem()"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-500 transition">+ Add</button>
            </div>

            <template x-for="(item, index) in items" :key="index">
                <div class="border p-4 mb-4 rounded-lg bg-white dark:bg-gray-900 shadow-sm relative transition">
                    <button type="button" @click="removeItem(index)" class="absolute top-2 right-2 text-red-600 dark:text-red-400 text-xl">&times;</button>
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                        @foreach ($section['fields'] as $field)
                            <div>
                                <label class="text-sm text-gray-700 dark:text-gray-400">{{ $field['label'] }}</label>
                                <input type="text"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400 focus:ring-blue-500 focus:border-blue-500 transition"
                                       :name="'{{ $section['key'] }}[' + index + '][{{ $field['name'] }}]'"
                                       x-model="item['{{ $field['name'] }}']">
                            </div>
                        @endforeach
                    </div>
                </div>
            </template>

            <div x-show="items.length === 0" class="text-sm text-gray-500 dark:text-gray-400">
                No {{ strtolower($section['title']) }} added yet.
            </div>
        </div>
    @endforeach

    {{-- Login --}}
    <div class="border rounded-xl bg-gray-50 dark:bg-gray-800 p-6 transition">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">🔐 Login Credentials</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Login ID *</label>
                <input type="text" name="login_id" value="{{ old('login_id', $distributor->login_id ?? '') }}"
                       class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                    Password <small>
                        @if(isset($distributor))
                            (leave blank if not changing)
                        @else
                            (default: password@123)
                        @endif
                    </small>
                </label>
                <input type="text" name="password"
                       value="{{ old('password', isset($distributor) ? '' : 'password@123') }}"
                       placeholder="{{ isset($distributor) ? 'Keep blank if not changing' : '' }}"
                       class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>
        </div>
    </div>

    {{-- Actions --}}

<div class="flex justify-end gap-4">
    <a href="{{ route('admin.distributors.index') }}"
       class="px-6 py-3 rounded-lg 
              bg-gray-200 text-gray-800 hover:bg-gray-300 
              dark:bg-gray-700 dark:text-gray-400/95 dark:hover:bg-gray-600 dark:hover:text-white
              border border-gray-300 dark:border-gray-600 
              shadow-sm transition
              focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
        Cancel
    </a>
    <button type="submit"
            class="px-6 py-3 rounded-lg 
                   bg-blue-600 text-white hover:bg-blue-700 
                   dark:hover:bg-blue-500 
                   shadow-sm transition
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Save Distributor
    </button>
</div>



</form>

<script>
    flatpickr("#appointment_date", {
        dateFormat: "Y-m-d",
        defaultDate: "{{ old('appointment_date', isset($distributor) && $distributor->appointment_date ? $distributor->appointment_date->format('Y-m-d') : now()->format('Y-m-d')) }}",
    });
</script>
