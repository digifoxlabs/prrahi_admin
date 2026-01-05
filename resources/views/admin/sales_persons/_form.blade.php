<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

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

        <!-- Name -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Name *</label>
            <input type="text" name="name"
                   value="{{ old('name', $salesPerson->name ?? '') }}"
                   required
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Designation -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Designation</label>
            <input type="text" name="designation"
                   value="{{ old('designation', $salesPerson->designation ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Headquarter -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Headquarter</label>
            <input type="text" name="headquarter"
                   value="{{ old('headquarter', $salesPerson->headquarter ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Address Line 1 -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Address Line 1</label>
            <input type="text" name="address_line_1"
                   value="{{ old('address_line_1', $salesPerson->address_line_1 ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Address Line 2 -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Address Line 2</label>
            <input type="text" name="address_line_2"
                   value="{{ old('address_line_2', $salesPerson->address_line_2 ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Town -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Town</label>
            <input type="text" name="town"
                   value="{{ old('town', $salesPerson->town ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- State -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">State *</label>
            <select id="state-select" name="state" required
                    class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
                <option value="">-- Select State --</option>
                @foreach($states as $state)
                    <option value="{{ $state->name }}" @selected(old('state', $salesPerson->state ?? '') == $state->name)>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- District -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">District *</label>
            <select id="district-select" name="district" required
                    class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
                @if(isset($salesPerson))
                    <option value="{{ $salesPerson->district }}" selected>{{ $salesPerson->district }}</option>
                @else
                    <option value="">-- Select District --</option>
                @endif
            </select>
        </div>

        <!-- Pincode -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Pincode</label>
            <input type="text" name="pincode"
                   value="{{ old('pincode', $salesPerson->pincode ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Phone -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Phone</label>
            <input type="text" name="phone"
                   value="{{ old('phone', $salesPerson->phone ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Official Email -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Official Email</label>
            <input type="email" name="official_email"
                   value="{{ old('official_email', $salesPerson->official_email ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Personal Email -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Personal Email</label>
            <input type="email" name="personal_email"
                   value="{{ old('personal_email', $salesPerson->personal_email ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- DOB -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Date of Birth</label>
            <input type="date" name="date_of_birth"
                   value="{{ old('date_of_birth', $salesPerson->date_of_birth ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Anniversary -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Date of Anniversary</label>
            <input type="date" name="date_of_anniversary"
                   value="{{ old('date_of_anniversary', $salesPerson->date_of_anniversary ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Zone -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Zone</label>
            <input type="text" name="zone"
                   value="{{ old('zone', $salesPerson->zone ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- State Covered -->
        <div class="md:col-span-2">
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">States Covered</label>
            <select id="states-covered" name="state_covered[]" multiple
                    class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
                @foreach($states as $state)
                    <option value="{{ $state->name }}"
                        @if(!empty(old('state_covered', $salesPerson->state_covered ?? '')))
                            {{ in_array($state->name, explode(',', old('state_covered', $salesPerson->state_covered ?? ''))) ? 'selected' : '' }}
                        @endif
                    >
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>


            {{-- <select id="states-covered" name="state_covered[]" multiple
    class="w-full p-2 border rounded 
           border-gray-300 dark:border-gray-700 
           bg-white dark:bg-gray-900 
           text-gray-800 dark:text-gray-200 
           focus:outline-none focus:ring-2 focus:ring-blue-500">
    @foreach($states as $state)
        <option value="{{ $state->name }}"
            @if(!empty(old('state_covered', $salesPerson->state_covered ?? '')))
                {{ in_array($state->name, explode(',', old('state_covered', $salesPerson->state_covered ?? ''))) ? 'selected' : '' }}
            @endif
            class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200">
            {{ $state->name }}
        </option>
    @endforeach
</select> --}}

        </div>

        <!-- District Covered -->
        <div class="md:col-span-2">
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Districts Covered</label>
            <select id="districts-covered" name="district_covered[]" multiple
                    class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
                @foreach(\App\Models\District::orderBy('name')->get() as $district)
                    <option value="{{ $district->name }}"
                        @if(!empty(old('district_covered', $salesPerson->district_covered ?? '')))
                            {{ in_array($district->name, explode(',', old('district_covered', $salesPerson->district_covered ?? ''))) ? 'selected' : '' }}
                        @endif
                    >
                        {{ $district->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Towns Covered -->
        <div class="md:col-span-2">
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Town/Village Covered</label>
            <input id="towns-covered" name="town_covered[]"
                   value="{{ old('town_covered', $salesPerson->town_covered ?? '') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Login ID -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">Login ID *</label>
            <input type="text" name="login_id"
                   value="{{ old('login_id', $salesPerson->login_id ?? '') }}"
                   required
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

        <!-- Password -->
        <div>
            <label class="font-medium mb-1 block text-gray-700 dark:text-white">
                Password
                @if(isset($salesPerson))
                    <small class="text-sm text-gray-500 dark:text-gray-400">(Leave blank to keep unchanged)</small>
                @else
                    <small class="text-sm text-gray-500 dark:text-gray-400">(Default password: password@123)</small>
                @endif
            </label>
            <input type="password" name="password"
                   value="{{ old('password', isset($salesPerson) ? '' : 'password@123') }}"
                   class="w-full p-2 border rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-400">
        </div>

    </div>

    <div class="pt-4">
        <button type="submit"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 dark:hover:bg-green-500 transition">
            {{ $buttonText }}
        </button>
<a href="{{ route('admin.sales-persons.index') }}"
   class="ml-3 px-4 py-2 rounded 
          border border-gray-300 dark:border-gray-600 
          bg-gray-100 text-gray-800 
          dark:bg-gray-200 dark:text-amber-500 
          hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-amber-700
          shadow-sm transition duration-200 ease-in-out 
          focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
    Cancel
</a>

    </div>
</form>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet" />
<style>
    /* Dark mode overrides for TomSelect */
    .dark .ts-control, 
    .dark .ts-dropdown, 
    .dark .ts-control input {
        background-color: #1f2937 !important; /* gray-800 */
        color: #e5e7eb !important;           /* gray-200 */
        border-color: #374151 !important;    /* gray-700 */
    }
    .dark .ts-dropdown .ts-option:hover,
    .dark .ts-dropdown .ts-option.active {
        background-color: #2563eb !important; /* blue-600 */
        color: #fff !important;
    }
    .dark .ts-control .item {
        background-color: #374151 !important; /* gray-700 */
        color: #e5e7eb !important;
        border-radius: 0.25rem;
        padding: 0.125rem 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect("#state-select", { create: false, sortField: 'text' });
    const districtSelect = new TomSelect("#district-select", { create: false });

    document.getElementById('state-select').addEventListener('change', function () {
        fetch('{{ route('admin.get-districts') }}?state=' + this.value)
            .then(res => res.json())
            .then(districts => {
                districtSelect.clearOptions();
                districts.forEach(dist => districtSelect.addOption({ value: dist, text: dist }));
                districtSelect.refreshOptions();
            });
    });

    new TomSelect("#states-covered", {
        plugins: ['remove_button'],
        persist: false,
        create: false,
        closeAfterSelect: true
    });

    new TomSelect("#districts-covered", {
        plugins: ['remove_button'],
        persist: false,
        create: false,
        closeAfterSelect: true
    });

    new TomSelect("#towns-covered", {
        plugins: ['remove_button'],
        delimiter: ',',
        persist: false,
        create: true,
        hideSelected: true
    });
</script>






<style>
/* Make Tom Select controls dark in dark mode.
   Place this after the Tom Select CSS so it wins. */

/* Container */
.dark .ts-control,
.dark .ts-control.single,
.dark .ts-control.multi,
.dark .tomselected,
.dark .ts-control .ts-input,
.dark .ts-control input[type="text"] {
  background-color: #0b1220 !important;    /* dark panel */
  color: #e6eef8 !important;               /* light text */
  border-color: #374151 !important;        /* dark border */
}

/* Individual selected items (chips) */
.dark .ts-control .item,
.dark .tomselected .item,
.dark .ts-control .ts-choice {
  background-color: #111827 !important;    /* chip bg */
  color: #e6eef8 !important;
  border-radius: 0.25rem !important;
  padding: 0.125rem 0.5rem !important;
}

/* Remove (x) button on chips */
.dark .ts-control .item .ts-remove,
.dark .ts-control .ts-remove,
.dark .tomselected .ts-remove {
  color: #cbd5e1 !important;
}

/* Input inside control — make transparent so the control's bg shows through */
.dark .ts-control .ts-input,
.dark .ts-control input[type="text"] {
  background: transparent !important;
  color: #e6eef8 !important;
}

/* Dropdown list */
.dark .ts-dropdown,
.dark .ts-dropdown .ts-list {
  background: #071227 !important;   /* dropdown bg */
  color: #e6eef8 !important;
  border-color: #374151 !important;
}

/* Options */
.dark .ts-dropdown .ts-option {
  background: transparent;
  color: inherit;
}

/* Option hover / active */
.dark .ts-dropdown .ts-option:hover,
.dark .ts-dropdown .ts-option.is-active,
.dark .ts-dropdown .ts-option.active {
  background: rgba(99,102,241,0.12) !important; /* slight blue tint */
  color: #ffffff !important;
}

/* Remove button hover on chips */
.dark .ts-control .item .ts-remove:hover {
  color: #ffffff !important;
}

/* Make the placeholder/empty input area consistent */
.dark .ts-control .placeholder {
  color: #9ca3af !important; /* gray-400 */
}

/* z-index just in case dropdown is behind other elements */
.ts-dropdown {
  z-index: 9999 !important;
}

/* Smooth transitions */
.ts-control, .ts-dropdown, .ts-control .item {
  transition: background-color 150ms ease, color 150ms ease, border-color 150ms ease;
}
</style>

@endpush


