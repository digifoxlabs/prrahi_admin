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

        @include('admin.distributors._breadcrump')

        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-700 dark:bg-gray-900 xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-8xl text-center">

                <!-- Header Section -->
                <div
                    class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-700 dark:bg-transparent lg:p-6 bg-white">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">

                            <!-- Profile Image -->
                            <div
                                class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-700 bg-white dark:bg-gray-800">
                                <img src="{{ $distributor->profile_photo ? asset('storage/' . $distributor->profile_photo) : asset('images/user/user-01.jpg') }}"
                                    alt="distributor" class="w-full h-full object-cover" />
                            </div>

                            <!-- Name and Location -->
                            <div class="order-3 xl:order-2">
                                <h4
                                    class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left">
                                    {{ strtoupper($distributor->firstname) }} {{ strtoupper($distributor->lastname) }}
                                </h4>
                                <div
                                    class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">DISTRIBUTOR</p>
                                    <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ strtoupper($distributor->district) . ', ' . strtoupper($distributor->state) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">

                                @if (Auth::guard('admin')->user()->hasPermission('edit_distributors'))

                                    <!-- Profile Image Upload -->
                                    <div x-data="profileImageUploader()">
                                        <button
                                            @click="isProfileImageModal = true"
                                            aria-label="Upload profile image"
                                            class="flex h-11 w-11 items-center justify-center gap-2 rounded-full border bg-white text-sm font-medium text-gray-700 shadow-sm
                                                hover:bg-gray-50 hover:text-gray-900 transform transition duration-150 ease-in-out hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1
                                                dark:border-gray-600 dark:bg-gradient-to-br dark:from-gray-800 dark:to-gray-700 dark:text-gray-100 dark:shadow-md
                                                dark:hover:from-gray-700 dark:hover:to-gray-600 dark:hover:text-gray dark:hover:scale-105">
                                            <!-- camera icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h3l2-3h6l2 3h3v11a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                                <circle cx="12" cy="13" r="3" stroke-width="1.2"></circle>
                                            </svg>
                                        </button>


                                        <!-- Image Crop Modal -->
                                        <div x-show="isProfileImageModal" x-cloak
                                            class="fixed inset-0 z-[99999] flex items-center justify-center p-5">
                                            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="closeModal()"></div>

                                            <div @click.outside="isProfileImageModal = false"
                                                class="relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                                                <button @click="isProfileImageModal = false"
                                                    class="absolute right-5 top-5 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-800
                                                           dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>

                                                <div class="bg-white rounded-lg p-6 w-full max-w-lg relative dark:bg-gray-800">
                                                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white">Profile
                                                        Image</h3>

                                                    <input type="file" @change="handleFile($event)" accept="image/*"
                                                        class="h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800
                                                               focus:ring-2 focus:ring-blue-500 focus:outline-none
                                                               dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-400" />

                                                    <div class="w-full overflow-hidden mb-4 mt-4">
                                                        <img id="crop-image" class="max-h-60 mx-auto block" />
                                                    </div>

                                                    <div class="flex justify-end space-x-2">
                                                        <button @click="closeModal()"
                                                            class="px-4 py-2 rounded-md border bg-gray-100 text-gray-800 hover:bg-gray-200
                                                                   dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                                                            Cancel
                                                        </button>
                                                        <button @click="uploadCroppedImage()"
                                                            class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition">
                                                            Upload
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Update Password -->
                                    <div x-data="updatePasswordComponent()" x-cloak>
                                        <button @click="isPasswordModal = true"
                                            class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 dark:hover:bg-blue-500 transition">
                                            Update Password
                                        </button>

                                        <!-- Modal -->
                                        <div x-show="isPasswordModal" x-cloak
                                            class="fixed inset-0 z-[99999] flex items-center justify-center p-4">
                                            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="isPasswordModal = false"></div>

                                            <div @click.outside="isPasswordModal = false"
                                                class="w-full max-w-md rounded-xl bg-white p-6 relative shadow-lg dark:bg-gray-800">
                                                <button @click="isPasswordModal = false"
                                                    class="absolute top-2 right-3 text-xl text-gray-600 dark:text-white">×</button>

                                                <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Update
                                                    Password</h2>

                                                <form @submit.prevent="submitPassword">
                                                    <div class="mb-4">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 dark:text-white mb-1">New
                                                            Password</label>
                                                        <input type="password" x-model="password"
                                                            class="w-full rounded border px-3 py-2 dark:bg-gray-900 dark:text-white"
                                                            required>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 dark:text-white mb-1">Confirm
                                                            Password</label>
                                                        <input type="password" x-model="confirmPassword"
                                                            class="w-full rounded border px-3 py-2 dark:bg-gray-900 dark:text-white"
                                                            required>
                                                    </div>
                                                    <template x-if="errorMessage">
                                                        <div class="text-red-600 text-sm mb-3" x-text="errorMessage"></div>
                                                    </template>

                                                    <button type="submit"
                                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                                        Update Password
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Button -->

                                 <a href="{{ route('admin.distributors.edit', $distributor->id) }}"
                                    class="rounded-md bg-yellow-500 px-3 py-2 text-white text-sm hover:bg-yellow-600 transition flex items-center justify-center gap-2">

                                        <!-- Icon visible on xs only -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:hidden" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5h-5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.364 5.636a2.121 2.121 0 010 3L12 15l-4 1 1-4 6.364-6.364a2.121 2.121 0 013 0z"/>
                                        </svg>

                                        <!-- Text visible on sm and larger -->
                                        <span class="hidden sm:inline fallback-show-sm">Edit</span>
                                    </a>



                                @endif

                                <!-- Delete Button -->
                                @if (Auth::guard('admin')->user()->hasPermission('delete_distributors'))


                                    <div x-data="{ showDeleteModal: false }">
                                                <!-- Delete Button (triggers modal) -->

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

                                                <!-- Delete Confirmation Modal -->
                                                <div x-show="showDeleteModal" x-transition
                                                    class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 p-4"
                                                    x-cloak>
                                                    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                                                        <div class="p-6">
                                                            <h3 class="text-lg font-medium text-gray-900">Confirm Deletion</h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-600">
                                                                    Are you sure you want to delete this distributor <strong> {{ $distributor->firm_name}}</strong>? This action cannot be
                                                                    undone.
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                            <form action="{{ route('admin.distributors.destroy', $distributor->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                            <button @click="showDeleteModal = false" type="button"
                                                                class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="p-6 mb-8 border rounded-2xl bg-white dark:bg-gray-900 dark:border-gray-700 text-center">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white pb-2">🧾 Basic Information</h3>
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-5 text-sm text-gray-700 dark:text-white text-left">
                        <div><span class="font-semibold">Sales Person:</span> {{ $distributor->salesPerson->name ?? '-' }}</div>
                        <div><span class="font-semibold">Appointment Date:</span> {{ $distributor->appointment_date }}</div>
                        <div><span class="font-semibold">Firm Name:</span> {{ $distributor->firm_name }}</div>
                        <div><span class="font-semibold">Nature of Firm:</span> {{ $distributor->nature_of_firm }}</div>
                        <div><span class="font-semibold">Login ID:</span> {{ $distributor->login_id }}</div>
                        <div><span class="font-semibold">Contact Person:</span> {{ $distributor->contact_person }}</div>
                        <div><span class="font-semibold">Designation:</span> {{ $distributor->designation_contact }}</div>
                        <div><span class="font-semibold">Phone:</span> {{ $distributor->contact_number }}</div>
                        <div><span class="font-semibold">Email:</span> {{ $distributor->email }}</div>
                        <div><span class="font-semibold">GST:</span> {{ $distributor->gst }}</div>
                        <div><span class="font-semibold">Address Line 1:</span> {{ $distributor->address_line_1 }}</div>
                        <div><span class="font-semibold">Address Line 2:</span> {{ $distributor->address_line_2 }}</div>
                        <div><span class="font-semibold">Town:</span> {{ $distributor->town }}</div>
                        <div><span class="font-semibold">District:</span> {{ $distributor->district }}</div>
                        <div><span class="font-semibold">State:</span> {{ $distributor->state }}</div>
                        <div><span class="font-semibold">Pincode:</span> {{ $distributor->pincode }}</div>
                        <div><span class="font-semibold">Landmark:</span> {{ $distributor->landmark }}</div>
                        <div><span class="font-semibold">Date of Birth:</span> {{ $distributor->date_of_birth }}</div>
                        <div><span class="font-semibold">Date of Anniversary:</span> {{ $distributor->date_of_anniversary }}</div>

                        <div>
                            <span class="font-semibold">Latitude:</span> {{ $distributor->latitude }}
                        </div>
                        <div>
                            <span class="font-semibold">Longitude:</span> {{ $distributor->longitude }}
                        </div>

                        <div>
                            <button x-data @click="$dispatch('open-map')"
                                class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-500 transition">
                                📍 View Location on Map
                            </button>
                        </div>
                    </div>
                </div>

<!-- Map Modal (with fullscreen + close) -->
<div x-data="{ open: false }" 
     x-on:open-map.window="open = true" 
     x-on:keydown.escape.window="open = false" 
     x-cloak>

    <div x-show="open"
         class="fixed inset-0 flex items-center justify-center bg-black/60 z-[999999] p-4"
         x-transition>
         
        <div @click.outside="open = false"
             class="relative bg-white rounded-lg overflow-hidden shadow-lg w-full max-w-3xl dark:bg-gray-900">
             
            <!-- Header -->
            <div class="flex justify-between items-center px-4 py-2 border-b dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">📌 Map Location</h2>
            </div>

            <!-- Control Buttons (absolute top-right) -->
            <div class="absolute top-3 right-3 flex space-x-2 z-[10000]">
                <!-- Fullscreen -->
                <button 
                    @click="let el=$refs.mapFrame; 
                            if(el.requestFullscreen){el.requestFullscreen();} 
                            else if(el.webkitRequestFullscreen){el.webkitRequestFullscreen();}" 
                    class="h-9 w-9 flex items-center justify-center rounded-full
                        bg-gray-200 text-gray-700 shadow-sm
                        hover:bg-gray-300 hover:text-gray-900
                        dark:bg-gradient-to-br dark:from-gray-700 dark:to-gray-800 
                        dark:text-gray-300 
                        dark:hover:from-gray-600 dark:hover:to-gray-700 dark:hover:text-white
                        transition duration-200 ease-in-out">
                    ⛶
                </button>

                <!-- Close -->
                <button @click="open = false"
                        class="h-9 w-9 flex items-center justify-center rounded-full 
                            bg-gray-200 text-gray-700 shadow-sm
                            hover:bg-red-500 hover:text-white
                            dark:bg-gradient-to-br dark:from-gray-700 dark:to-gray-800 
                            dark:text-gray-300 
                            dark:hover:from-red-600 dark:hover:to-red-700 dark:hover:text-white
                            transition duration-200 ease-in-out text-lg font-bold">
                    &times;
                </button>

            </div>

            <!-- Map -->
            <div class="w-full h-[500px]">
                <iframe 
                    x-ref="mapFrame"
                    width="100%" height="100%" 
                    frameborder="0" style="border:0"
                    src="https://www.google.com/maps?q={{ $distributor->latitude }},{{ $distributor->longitude }}&hl=es;z=14&output=embed"
                    allowfullscreen
                    class="dark:bg-gray-800">
                </iframe>
            </div>
        </div>
    </div>
</div>



                <!-- Dynamic Sections -->
                @foreach (['companies' => 'Distributor Companies', 'banks' => 'Bank Details', 'godowns' => 'Godown Details', 'manpowers' => 'Manpower', 'vehicles' => 'Vehicles'] as $relation => $title)
                    <div class="p-6 mb-8 border border-gray-300 rounded-2xl shadow-sm bg-white dark:bg-gray-900 dark:border-gray-700">
                        <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white">{{ $title }}</h3>
                        @forelse($distributor->$relation as $item)
                            <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-5 p-5 rounded-xl bg-gray-50 border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                                @foreach ($item->getAttributes() as $key => $value)
                                    @if(!in_array($key, ['id', 'distributor_id', 'created_at', 'updated_at']))
                                        <div class="text-sm text-gray-700 dark:text-white">
                                            <span class="font-semibold">{{ ucwords(str_replace('_', ' ', $key)) }}:</span> {{ $value }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm italic dark:text-gray-400">No {{ strtolower($title) }} added.</p>
                        @endforelse
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'distributors',
            isProfileImageModal: false,
            isPasswordModal: false,
            isProfileInfoModal: false,
        };
    </script>
@endpush

@section('scripts')

    <!-- AlpineJS Components -->
    <script>
        function profileImageUploader() {
            return {
                isProfileImageModal: false,
                cropper: null,
                imageFile: null,

                handleFile(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.imageFile = file;

                        // Initialize Cropper
                        const image = document.getElementById('crop-image');
                        const reader = new FileReader();

                        reader.onload = (e) => {
                            image.src = e.target.result;

                            if (this.cropper) {
                                this.cropper.destroy();
                            }

                            this.cropper = new Cropper(image, {
                                aspectRatio: 1,
                                viewMode: 1,
                                autoCropArea: 1,
                            });
                        };

                        reader.readAsDataURL(file);
                    }
                },

                uploadCroppedImage() {
                    if (this.cropper) {
                        this.cropper.getCroppedCanvas().toBlob((blob) => {
                            const formData = new FormData();
                            formData.append('profile_photo', blob, 'profile.jpg');
                            formData.append('_token', '{{ csrf_token() }}');

                            fetch('{{ route('admin.distributors.updateProfileImage', $distributor->id) }}', {
                                method: 'POST',
                                body: formData,
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.reload();
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        });
                    }
                },

                closeModal() {
                    this.isProfileImageModal = false;
                    if (this.cropper) {
                        this.cropper.destroy();
                        this.cropper = null;
                    }
                }
            };
        }

        // Update Password Component
        function updatePasswordComponent() {
            return {
                isPasswordModal: false,
                password: '',
                confirmPassword: '',
                errorMessage: '',

                async submitPassword() {
                    this.errorMessage = '';

                    if (this.password !== this.confirmPassword) {
                        this.errorMessage = 'Passwords do not match';
                        return;
                    }

                    if (this.password.length < 8) {
                        this.errorMessage = 'Password must be at least 8 characters';
                        return;
                    }

                    try {
                        const res = await fetch("{{ route('admin.distributors.updatePassword') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                distributor_id: {{ $distributor->id }},
                                password: this.password,
                                password_confirmation: this.confirmPassword
                            })
                        });

                        const data = await res.json();

                        if (data.success) {
                            this.password = '';
                            this.confirmPassword = '';
                            this.isPasswordModal = false;

                            // Optional: Toastr for success
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Password updated successfully');
                            } else {
                                alert('Password updated successfully');
                            }

                        } else {
                            this.errorMessage = data.message || 'Failed to update password';
                        }

                    } catch (error) {
                        this.errorMessage = 'Something went wrong';
                        console.error('Password update error:', error);
                    }
                }
            };
        }
    </script>

@endsection
