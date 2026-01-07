<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    {{-- Global Errors --}}
    @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 p-3 rounded mb-4 space-y-1">
            <strong class="block">Please fix the following errors:</strong>
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-2 gap-4">

        <!-- Retailer Name -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Retailer Name *</label>
            <input type="text" name="retailer_name" required
                    placeholder="M/S MAA ENTERPRISE"
                   value="{{ old('name', $retailer->retailer_name ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Contact Person -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Contact Person *</label>
            <input type="text" name="contact_person" required
                    placeholder="Full Name"
                   value="{{ old('contact_person', $retailer->contact_person ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Contact Number -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Contact Number *</label>
            <input type="numeric" name="contact_number" required
                    placeholder="10 digit mobile number"
                   value="{{ old('contact_number', $retailer->contact_number ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Email -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Email</label>
            <input type="email" name="email"
                    placeholder="example@gmail.com"
                   value="{{ old('email', $retailer->email ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- GST -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">GST No (Optional)</label>
            <input type="text" name="gst"
                   value="{{ old('gst', $retailer->gst ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Nature of Outlet -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Nature of Outlet</label>
            <input type="text" name="nature_of_outlet"
                   value="{{ old('nature_of_outlet', $retailer->nature_of_outlet ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Address Line 1 -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Address Line 1 *</label>
            <input type="text" name="address_line_1"
                    required
                   value="{{ old('address_line1', $retailer->address_line_1 ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Address Line 2 -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Address Line 2</label>
            <input type="text" name="address_line_2"
                   value="{{ old('address_line2', $retailer->address_line_2 ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Town -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Town</label>
            <input type="text" name="town"
                   value="{{ old('town', $retailer->town ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Landmark -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Landmark</label>
            <input type="text" name="landmark"
                   value="{{ old('town', $retailer->landmark ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- State -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">State *</label>
            <select id="state-select" name="state" required
                    class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                           bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
                <option value="">-- Select State --</option>
                @foreach($states as $state)
                    <option value="{{ $state->name }}"
                        @selected(old('state', $retailer->state ?? '') == $state->name)>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- District -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">District *</label>
            <select id="district-select" name="district" required
                    class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                        bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
                @if(isset($retailer))
                    <option value="{{ $retailer->district }}" selected>
                        {{ $retailer->district }}
                    </option>
                @else
                    <option value="">-- Select District --</option>
                @endif
            </select>
        </div>

        <!-- Pincode -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Pincode *</label>
            <input type="text" name="pincode"
                   value="{{ old('pincode', $retailer->pincode ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                          bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Distributor -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Distributor</label>
            <select name="distributor_id"
                    class="w-full p-2 border rounded border-gray-300 dark:border-gray-700
                           bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
                <option value="">-- Select Distributor --</option>
                @foreach($distributors as $d)
                    <option value="{{ $d->id }}"
                        @selected(old('distributor_id', $retailer->distributor_id ?? '') == $d->id)>
                        {{ $d->firm_name }}
                    </option>
                @endforeach
            </select>
        </div>

        
        <!-- Date of Birth -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">
                Date of Birth
            </label>

            <div class="relative">
                <input type="date"
                    name="date_of_birth"
                    value="{{ old('date_of_birth', $retailer->date_of_birth ?? '') }}"
                    class="w-full border rounded-lg p-2 pr-10 text-sm
                            border-gray-300 dark:border-gray-700
                            bg-white dark:bg-gray-900
                            text-gray-800 dark:text-gray-400">

                <button type="button"
                        onclick="this.previousElementSibling.showPicker && this.previousElementSibling.showPicker()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                    📅
                </button>
            </div>
        </div>


        <!-- Date of Anniversary -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">
                Date of Anniversary
            </label>

            <div class="relative">
                <input type="date"
                    name="date_of_anniversary"
                    value="{{ old('date_of_anniversary', $retailer->date_of_anniversary ?? '') }}"
                    class="w-full border rounded-lg p-2 pr-10 text-sm
                            border-gray-300 dark:border-gray-700
                            bg-white dark:bg-gray-900
                            text-gray-800 dark:text-gray-400">

                <button type="button"
                        onclick="this.previousElementSibling.showPicker && this.previousElementSibling.showPicker()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                    📅
                </button>
            </div>
        </div>

        {{-- Appointment Date --}}

        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">
                Appointment Date
            </label>

            <div class="relative">
                <input type="date"
                    name="appointment_date"
                    required
                    value="{{ old(
                        'appointment_date',
                        optional($retailer)->appointment_date
                            ? \Illuminate\Support\Carbon::parse(optional($retailer)->appointment_date)->toDateString()
                            : now()->toDateString()
                    ) }}"
                    class="w-full border rounded-lg p-2 pr-10 text-sm
                            border-gray-300 dark:border-gray-700
                            bg-white dark:bg-gray-900
                            text-gray-800 dark:text-gray-400">

                <button type="button"
                        onclick="this.previousElementSibling.showPicker && this.previousElementSibling.showPicker()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500">
                    📅
                </button>
            </div>
        </div>





    </div>

    <!-- ACTIONS -->
    <div class="pt-4">
        <button type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            {{ $buttonText }}
        </button>

        <a href="{{ route($returnURL) }}"
           class="ml-3 px-4 py-2 rounded border border-gray-300 bg-gray-100
                  hover:bg-gray-200 transition">
            Cancel
        </a>
    </div>
</form>



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>


    new TomSelect("#state-select", { create: false, sortField: 'text' });
    const districtSelect = new TomSelect("#district-select", { create: false });

    document.getElementById('state-select').addEventListener('change', function () {
        fetch('{{ route('all.get-districts') }}?state=' + this.value)
            .then(res => res.json())
            .then(districts => {
                districtSelect.clearOptions();
                districts.forEach(dist => districtSelect.addOption({ value: dist, text: dist }));
                districtSelect.refreshOptions();
            });
    });

</script>


<script>
    flatpickr("#dob", {
        dateFormat: "Y-m-d",
        allowInput: true
    });

    flatpickr("#anniversary", {
        dateFormat: "Y-m-d",
        allowInput: true
    });
</script>



@endpush
