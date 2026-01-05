@extends('admin.admin-layout')


<!-- paste this button where you need it -->
<style>
  /* tiny fallback in case Tailwind responsive utilities are missing/overridden */
  .fallback-show-sm { display: none !important; }
  @media (min-width: 640px) {
    .fallback-show-sm { display: inline !important; }
  }
</style>

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.sales_persons._breadcrump')



        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition
                class="bg-yellow-100 text-red-800 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-700 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-8xl">



                <!-- Header -->
                <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-700 lg:p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">

                            <!-- Profile Image + Info -->
                            <div class="flex flex-col items-center gap-4 xl:flex-row">
                                <div
                                    class="w-20 h-20 rounded-full overflow-hidden border border-gray-300 dark:border-gray-700">
                                    <img src="{{ $salesPerson->profile_photo ? asset('storage/' . $salesPerson->profile_photo) : asset('images/user/user-01.jpg') }}"
                                        alt="Profile">
                                </div>
                                <div class="text-center xl:text-left">
                                    <h2 class="
                                            text-xl font-bold 
                                            text-red-500            {{-- default (xs <640px) --}}
                                            sm:text-green-500       {{-- ≥640px --}}
                                            md:text-blue-500        {{-- ≥768px --}}
                                            lg:text-purple-500      {{-- ≥1024px --}}
                                            xl:text-pink-500        {{-- ≥1280px --}}
                                            2xl:text-amber-500      {{-- ≥1536px --}}
                                            dark:text-white         {{-- fallback text in dark mode --}}
                                        ">
                                        {{ strtoupper($salesPerson->name) }}
                                    </h2>
                                    <p class="text-gray-500 dark:text-gray-300">{{ $salesPerson->designation }}</p>
                                    <p class="text-sm text-gray-400 dark:text-gray-400">{{ $salesPerson->headquarter }}</p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">

                                @if (Auth::guard('admin')->user()->hasPermission('edit_sales'))
                                    <!-- Upload Photo -->
                                    <div x-data="profileImageUploader()">


                                    <button @click="isProfileImageModal = true"
                                        class="rounded-md bg-blue-600 text-white px-3 py-2 text-sm hover:bg-blue-700 transition flex items-center justify-center gap-2">

                                        <!-- Icon visible on xs (hidden from sm upwards) -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 sm:hidden"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M3 7h3l2-3h6l2 3h3v11a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                            <circle cx="12" cy="13" r="3" stroke-width="1.2"></circle>
                                        </svg>

                                        <!-- Text visible from sm and up.
                                            Uses both Tailwind and fallback class to guarantee visibility. -->
                                        <span class="hidden sm:inline fallback-show-sm">Upload Photo</span>
                                    </button>


                                        <!-- Cropper Modal -->
                                        <div x-show="isProfileImageModal"
                                            class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-[99999]"
                                            x-cloak>
                                            <div class="modal-close-btn fixed inset-0 h-full w-full bg-black/40 backdrop-blur-sm"
                                                @click="closeModal()"></div>
                                            <div
                                                class="bg-white dark:bg-gray-900 rounded-xl p-6 relative w-full max-w-lg shadow-lg">
                                                <button @click="isProfileImageModal = false"
                                                    class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white transition">&times;</button>
                                                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Update
                                                    Profile Image</h3>
                                                <input type="file" @change="handleFile($event)" accept="image/*"
                                                    class="mb-4 w-full border rounded p-2 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                                                <div><img id="crop-image" class="max-h-60 mx-auto"></div>
                                                <div class="flex justify-end mt-4 gap-2">
                                                    <button @click="closeModal()"
                                                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                                                        Cancel
                                                    </button>
                                                    <button @click="uploadCroppedImage()"
                                                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                                        Upload
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Update Password -->
                                    <div x-data="updatePasswordComponent()">
                                        <button @click="isPasswordModal = true"
                                            class="rounded-md bg-yellow-500 px-4 py-2 text-white text-sm hover:bg-yellow-600 transition">
                                            Update Password
                                        </button>
                                        <div x-show="isPasswordModal" x-transition
                                            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                                            <div @click.outside="isPasswordModal = false"
                                                class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 p-6 relative shadow-lg">
                                                <button @click="isPasswordModal = false"
                                                    class="absolute top-2 right-2 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white transition">&times;</button>
                                                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Update
                                                    Password</h3>
                                                <input type="password" x-model="password" placeholder="New Password"
                                                    class="w-full mb-3 border rounded p-2 dark:bg-gray-900 dark:text-white dark:border-gray-700">
                                                <input type="password" x-model="confirmPassword"
                                                    placeholder="Confirm Password"
                                                    class="w-full mb-3 border rounded p-2 dark:bg-gray-900 dark:text-white dark:border-gray-700">
                                                <template x-if="errorMessage">
                                                    <div class="text-red-600 text-sm mb-2" x-text="errorMessage"></div>
                                                </template>
                                                <button @click="submitPassword()"
                                                    class="w-full bg-green-600 text-white rounded py-2 hover:bg-green-700 transition">
                                                    Update
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit -->
                                <a href="{{ route('admin.sales-persons.edit', $salesPerson) }}"
                                    class="rounded-md bg-indigo-600 px-3 py-2 text-white text-sm hover:bg-indigo-700 transition flex items-center justify-center gap-2">

                                        <!-- Icon visible on xs only -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:hidden" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5h-5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002 2v-5M18.364 5.636a2.121 2.121 0 010 3L12 15l-4 1 1-4 6.364-6.364a2.121 2.121 0 013 0z"/>
                                        </svg>

                                        <!-- Text visible on sm and larger -->
                                        <span class="hidden sm:inline fallback-show-sm">Edit</span>
                                    </a>
                                @endif

                                <!-- Delete -->
                                <div x-data="{ showDeleteModal: false }">
                                    @if (Auth::guard('admin')->user()->hasPermission('delete_sales'))
                                        <button @click="showDeleteModal = true"
                                            class="rounded-md bg-red-600 px-3 py-2 text-white text-sm hover:bg-red-700 transition flex items-center justify-center gap-2">

                                            <!-- Trash Icon (xs only) -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:hidden" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                            </svg>

                                            <!-- Text (visible on sm and up) -->
                                            <span class="hidden sm:inline fallback-show-sm">Delete</span>
                                        </button>

                                    @endif
                                    <!-- Modal -->
                                    <div x-show="showDeleteModal" x-transition
                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4"
                                        x-cloak>
                                        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-md">
                                            <div class="p-6">
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Confirm
                                                    Deletion</h3>
                                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                                    Are you sure you want to delete this user? This action cannot be undone.
                                                </p>
                                            </div>
                                            <div
                                                class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                <form action="{{ route('admin.sales-persons.destroy', $salesPerson) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition">
                                                        Delete
                                                    </button>
                                                </form>
                                                <button @click="showDeleteModal = false" type="button"
                                                    class="inline-flex w-full justify-center rounded-md bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm font-semibold shadow-sm hover:bg-gray-300 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto transition">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- end buttons -->

                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mt-8 text-gray-800 dark:text-white">
                        <div><strong>Login ID:</strong> {{ $salesPerson->login_id }}</div>
                        <div><strong>Phone:</strong> {{ $salesPerson->phone }}</div>
                        <div><strong>Official Email:</strong> {{ $salesPerson->official_email }}</div>
                        <div><strong>Personal Email:</strong> {{ $salesPerson->personal_email }}</div>
                        <div><strong>Address Line 1:</strong> {{ $salesPerson->address_line_1 }}</div>
                        <div><strong>Address Line 2:</strong> {{ $salesPerson->address_line_2 }}</div>
                        <div><strong>Town:</strong> {{ $salesPerson->town }}</div>
                        <div><strong>District:</strong> {{ $salesPerson->district }}</div>
                        <div><strong>State:</strong> {{ $salesPerson->state }}</div>
                        <div><strong>Pincode:</strong> {{ $salesPerson->pincode }}</div>
                        <div><strong>Zone:</strong> {{ $salesPerson->zone }}</div>
                        <div><strong>States Covered:</strong> {{ $salesPerson->state_covered }}</div>
                        <div><strong>Districts Covered:</strong> {{ $salesPerson->district_covered }}</div>
                        <div><strong>Date of Birth:</strong> {{ $salesPerson->date_of_birth }}</div>
                        <div><strong>Date of Anniversary:</strong> {{ $salesPerson->date_of_anniversary }}</div>
                    </div>
                </div>








{{-- Distributor Section: paste this in place of your existing "NEW: Distributors Section" --}}
@php
    use App\Models\Distributor;
    // eager-load salesPerson relation so assigned name is available
    $allDistributors = Distributor::with('salesPerson')->orderBy('firm_name')->get();
@endphp

<div
    x-data="distributorSection()"
    class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6"
>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Distributors</h3>

        <!-- Map Distributor Button -->
        <div class="flex items-center gap-2">
            <button @click="openMapModal()"
                class="rounded-md bg-green-600 px-3 py-2 text-white text-sm hover:bg-green-700 transition">
                + Map Distributor
            </button>
        </div>
    </div>

    <!-- Map Distributors Modal -->
    <div x-show="showMapModal" x-cloak class="fixed inset-0 z-[999999] flex items-start justify-center overflow-y-auto p-4 pt-24">
        <div class="fixed inset-0 bg-black/40" @click="closeMapModal()"></div>

        <div @click.stop class="relative w-full max-w-4xl rounded-xl bg-white dark:bg-gray-900 shadow-lg p-6">
            <button @click="closeMapModal()"
                class="absolute top-3 right-3 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white transition">&times;</button>

            <h4 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Map Existing Distributors</h4>

            <p class="text-sm text-gray-500 mb-3">Select one or more existing distributors to map to <strong>{{ $salesPerson->name }}</strong>.</p>

            <!-- Search -->
            <div class="mb-3">
                <input id="dist-search" type="search" placeholder="Search by firm name / town / contact"
                    class="w-full md:w-1/2 rounded border p-2 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    x-on:input.debounce.200ms="filterQuery = $event.target.value">
            </div>

            <form action="{{ route('admin.sales-persons.mapDistributors', $salesPerson) }}" method="POST">
                @csrf

                <div class="overflow-x-auto max-h-[50vh] overflow-y-auto border rounded">
                    <table class="w-full min-w-[800px] divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Select</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Firm Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Contact</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Phone</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Town / District</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Assigned To</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($allDistributors as $dist)
                                @php
                                    $assignedToDifferent = $dist->sales_persons_id && $dist->sales_persons_id !== $salesPerson->id;
                                    $assignedToName = $dist->salesPerson ? $dist->salesPerson->name : null;
                                @endphp

                                <tr
                                    class="dist-row"
                                    data-firm="{{ strtolower($dist->firm_name) }}"
                                    data-town="{{ strtolower($dist->town ?? '') }}"
                                    data-contact="{{ strtolower($dist->contact_person ?? '') }}"
                                    x-show="matchesFilter('{{ addslashes(strtolower($dist->firm_name)) }}', '{{ addslashes(strtolower($dist->town ?? '')) }}', '{{ addslashes(strtolower($dist->contact_person ?? '')) }}')"
                                >
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        <label class="inline-flex items-center">
                                            <input
                                                type="checkbox"
                                                name="distributor_ids[]"
                                                value="{{ $dist->id }}"
                                                @if($dist->sales_persons_id === $salesPerson->id) checked @endif
                                                @if($assignedToDifferent) disabled @endif
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >
                                        </label>
                                    </td>

                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->firm_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->contact_person }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->contact_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->town }} / {{ $dist->district }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                        @if($dist->sales_persons_id)
                                            @if($dist->sales_persons_id === $salesPerson->id)
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Mapped to this SP</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">Assigned to: {{ $assignedToName ?? '—' }}</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">Unassigned</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" @click="closeMapModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 transition">Cancel</button>

                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                        Map Selected Distributors
                    </button>
                </div>
            </form>

            <p class="text-xs text-gray-500 mt-2">Note: distributors already mapped to another sales person are disabled here. Unassign them first if you'd like to move them.</p>
        </div>
    </div>
    <!-- end Map Distributors Modal -->


    {{-- Table of mapped distributors --}}
    <div class="overflow-x-auto mt-4">
        <table class="w-full min-w-[900px] divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Firm Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Contact Person</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Phone</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Town / District</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Login ID</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Actions</th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @php $distributors = $salesPerson->distributors ?? collect(); @endphp

                @forelse($distributors as $idx => $dist)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $idx + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->firm_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->contact_person }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->contact_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->town }} / {{ $dist->district }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $dist->login_id }}</td>

                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.distributors.show', $dist) }}" class="text-sm px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 hover:bg-gray-200">View</a>

                                @if(Auth::guard('admin')->user()->hasPermission('edit_distributors'))
                                    <a href="{{ route('admin.distributors.edit', $dist) }}" class="text-sm px-2 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">Edit</a>
                                @endif

                                {{-- Remove / Unmap button (only when mapped to this sales person) --}}
                                @if($dist->sales_persons_id === $salesPerson->id)
              <button
    @click="openUnmapModal({{ $dist->id }}, '{{ addslashes($dist->firm_name) }}')"
    class="text-sm px-2 py-1 rounded bg-red-600 text-white hover:bg-red-700">
    Remove
</button>

                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No distributors assigned to this sales person yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Unmap Confirmation Modal -->
    <div x-show="showUnmapModal" x-cloak
         class="fixed inset-0 z-[90] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40" @click="closeUnmapModal()"></div>

        <div @click.stop class="relative w-full max-w-md rounded-xl bg-white dark:bg-gray-900 shadow-lg p-6">
            <button @click="closeUnmapModal()" class="absolute top-3 right-3 text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white transition">&times;</button>

            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Confirm Remove</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Are you sure you want to remove <strong x-text="unmapDistributorName"></strong> from <strong>{{ $salesPerson->name }}</strong>?
                This will unassign the distributor (it will not delete the distributor record).
            </p>

            <form method="POST" :action="`{{ route('admin.sales-persons.unmapDistributor', $salesPerson) }}`" class="flex justify-end gap-2">
                @csrf
                <input type="hidden" name="distributor_id" :value="unmapDistributorId">

                <button type="button" @click="closeUnmapModal()"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 transition">Cancel</button>

                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                    Yes, Remove
                </button>
            </form>
        </div>
    </div>
    <!-- end Unmap Confirmation Modal -->
</div>



















            </div>
        </div>
    @endsection


    @push('scripts')
        <script>
            window.pageXData = {
                page: 'sales-persons',
                isProfileImageModal: false,
                isPasswordModal: false,
                isProfileInfoModal: false,
            };
        </script>
    @endpush

    @section('scripts')
        <script>
            function profileImageUploader() {
                return {
                    isProfileImageModal: false,
                    cropper: null,

                    handleFile(event) {
                        const file = event.target.files[0];
                        if (!file) {
                            toastr.warning('Please select an image file');
                            return;
                        }

                        const image = document.getElementById('crop-image');
                        image.src = URL.createObjectURL(file);

                        this.$nextTick(() => {
                            if (this.cropper) this.cropper.destroy();
                            this.cropper = new Cropper(image, {
                                aspectRatio: 1,
                                viewMode: 1,
                                movable: true,
                                cropBoxResizable: true,
                            });
                        });
                    },

                    uploadCroppedImage() {
                        if (!this.cropper) {
                            toastr.error('Please select and crop an image first');
                            return;
                        }

                        this.cropper.getCroppedCanvas().toBlob((blob) => {
                            const formData = new FormData();
                            formData.append('image', blob);
                            formData.append('_token', '{{ csrf_token() }}');

                            fetch('{{ route('admin.sales-persons.profile.upload', $salesPerson) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                })
                                .then(res => {
                                    if (!res.ok) throw new Error('Network error');
                                    return res.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        this.isProfileImageModal = false;
                                        toastr.success('Profile image uploaded successfully', 'Success');
                                        setTimeout(() => window.location.reload(), 1500);
                                    } else {
                                        toastr.warning('Failed to upload image', 'Warning');
                                    }
                                })
                                .catch(error => {
                                    toastr.error('Error occurred while uploading', 'Error');
                                });
                        }, 'image/jpeg');
                    },

                    closeModal() {
                        this.isProfileImageModal = false;
                        if (this.cropper) this.cropper.destroy();
                        this.cropper = null;
                        document.getElementById('crop-image').src = '';
                    }
                }
            }


            function updatePasswordComponent() {
                return {
                    isPasswordModal: false,
                    password: '',
                    confirmPassword: '',
                    errorMessage: '',

                    async submitPassword() {
                        this.errorMessage = '';

                        if (!this.password || !this.confirmPassword) {
                            this.errorMessage = 'Both password fields are required';
                            return;
                        }
                        if (this.password !== this.confirmPassword) {
                            this.errorMessage = 'Passwords do not match';
                            return;
                        }
                        if (this.password.length < 8) {
                            this.errorMessage = 'Password must be at least 8 characters long';
                            return;
                        }

                        try {
                            const res = await fetch("{{ route('admin.sales-persons.updatePassword') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json', // 👈 force JSON
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    sales_person_id: {{ $salesPerson->id }},
                                    password: this.password, 
                                    password_confirmation: this.confirmPassword,
                                }),
                            });

                            const data = await res.json(); // if error page, this line fails

                            if (data.success) {
                                this.password = '';
                                this.confirmPassword = '';
                                this.isPasswordModal = false;
                                toastr?.success(data.message) || alert(data.message);
                            } else {
                                this.errorMessage = data.message || 'Failed to update password';
                            }
                        } catch (error) {
                            console.error('Fetch error:', error);
                            this.errorMessage = 'Server returned an invalid response.';
                        }
                    },
                }
            }
        </script>





<script>
    function distributorSection() {
        return {
            // map modal state
            showMapModal: false,
            // unmap modal state
            showUnmapModal: false,
            unmapDistributorId: null,
            unmapDistributorName: '',
            // client-side search
            filterQuery: '',

            openMapModal() {
                this.showMapModal = true;
                this.filterQuery = '';
                // clear search input if present
                const input = document.getElementById('dist-search');
                if (input) input.value = '';
            },
            closeMapModal() {
                this.showMapModal = false;
            },

            openUnmapModal(id, name) {
                this.unmapDistributorId = id;
                this.unmapDistributorName = name;
                this.showUnmapModal = true;
            },
            closeUnmapModal() {
                this.showUnmapModal = false;
                this.unmapDistributorId = null;
                this.unmapDistributorName = '';
            },

            // used in x-show of rows in modal to avoid direct DOM queries
            matchesFilter(firm, town, contact) {
                const q = (this.filterQuery || '').trim().toLowerCase();
                if (!q) return true;
                const combined = (firm || '') + ' ' + (town || '') + ' ' + (contact || '');
                return combined.indexOf(q) !== -1;
            },
        }
    }

    // Provide a fallback generic search function if someone prefers DOM filtering
    function filterDistributorRows(q) {
        q = (q || '').trim().toLowerCase();
        document.querySelectorAll('.dist-row').forEach(row => {
            const firm = row.getAttribute('data-firm') || '';
            const town = row.getAttribute('data-town') || '';
            const contact = row.getAttribute('data-contact') || '';
            const combined = firm + ' ' + town + ' ' + contact;
            if (!q || combined.indexOf(q) !== -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>









    @endsection
