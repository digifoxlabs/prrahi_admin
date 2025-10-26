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
                                                d="M11 5h-5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.364 5.636a2.121 2.121 0 010 3L12 15l-4 1 1-4 6.364-6.364a2.121 2.121 0 013 0z"/>
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
    @endsection
