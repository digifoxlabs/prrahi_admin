@extends('admin.admin-layout')


@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('admin.users._breadcrump')

        <div
            class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
            <div class="mx-auto w-full max-w-8xl">



                <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">


                            <div class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
                                <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('images/user/user-01.jpg') }}"
                                    alt="user" />
                            </div>



                            <div class="order-3 xl:order-2">
                                <h4
                                    class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left">
                                    {{ strtoupper($user->fname) }} {{ strtoupper($user->lname) }}
                                </h4>
                                <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @foreach ($user->roles as $role)
                                            {{ strtoupper($role->name) }}
                                        @endforeach
                                    </p>
                                    <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ strtoupper($user->district) . ', ' . strtoupper($user->state) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">


                                <div x-data="profileImageUploader()">

                                    <button @click="isProfileImageModal = true"
                                        class="flex h-11 w-11 items-center justify-center gap-2 rounded-full border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round">
                                            </g>
                                            <g id="SVGRepo_iconCarrier">
                                                <circle cx="12" cy="13" r="3" stroke="#1C274C"
                                                    stroke-width="1.5">
                                                </circle>
                                                <path
                                                    d="M3.0001 12.9999C3.0001 10.191 2.99995 8.78673 3.67407 7.77783C3.96591 7.34107 4.34091 6.96607 4.77767 6.67423C5.78656 6.00011 7.19103 6.00011 9.99995 6.00011H14C16.8089 6.00011 18.2133 6.00011 19.2222 6.67423C19.659 6.96607 20.034 7.34107 20.3258 7.77783C21 8.78673 21.0001 10.191 21.0001 12.9999C21.0001 15.8088 21.0001 17.2133 20.326 18.2222C20.0341 18.6589 19.6591 19.0339 19.2224 19.3258C18.2135 19.9999 16.809 19.9999 14.0001 19.9999H10.0001C7.19117 19.9999 5.78671 19.9999 4.77782 19.3258C4.34106 19.0339 3.96605 18.6589 3.67422 18.2222C3.44239 17.8752 3.29028 17.4815 3.19049 16.9999"
                                                    stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path>
                                                <path d="M18 10H17.5" stroke="#1C274C" stroke-width="1.5"
                                                    stroke-linecap="round">
                                                </path>
                                                <path d="M14.5 3.5H9.5" stroke="#1C274C" stroke-width="1.5"
                                                    stroke-linecap="round">
                                                </path>
                                            </g>
                                        </svg>
                                    </button>


                                    <!-- Image Crop Modal -->


                                    <div x-show="isProfileImageModal"
                                        class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-99999"
                                        x-cloak>

                                        <div
                                            class="modal-close-btn fixed inset-0 h-full w-full  bg-black/40 backdrop-blur-sm">
                                        </div>

                                        <div @click.outside="isProfileInfoModal = false"
                                            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                                            <!-- close btn -->
                                            <button @click="isProfileImageModal = false"
                                                class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
                                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                                                        fill="" />
                                                </svg>
                                            </button>

                                            <div class="bg-white rounded-lg p-6 w-full max-w-lg relative">

                                                <h3 class="text-lg font-bold mb-4">Profile Image</h3>

                                                <input type="file" @change="handleFile($event)" accept="image/*"
                                                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                                                <div class="w-full overflow-hidden mb-4">
                                                    <img id="crop-image" class="max-h-60 mx-auto" />
                                                </div>

                                                <div class="flex justify-end space-x-2">
                                                    <button @click="closeModal"
                                                        class="btn btn-secondary hover:text-red-900">Cancel</button>
                                                    <button @click="uploadCroppedImage"
                                                        class="btn btn-primary hover:text-violet-600">Upload</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div> {{-- Image Crop Modal End --}}
                                </div>



                                <div x-data="updatePasswordComponent()" x-cloak>
                                    <button @click="isPasswordModal = true"
                                        class="rounded-md bg-blue-600 px-2 py-2 text-white hover:bg-blue-700">
                                        Update Password
                                    </button>

                                    <!-- Modal -->
                                    <div x-show="isPasswordModal" x-transition
                                        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
                                        <div @click.outside="isPasswordModal = false"
                                            class="w-full max-w-md rounded-xl bg-white dark:bg-gray-800 p-6 relative shadow-lg">

                                            <!-- Close button -->
                                          
                                            <button @click="isPasswordModal = false"
                                                class="absolute top-2 right-3 text-xl text-gray-600 dark:text-gray-300">×</button>

                                            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Update
                                                Password
                                            </h2>

                                            <form @submit.prevent="submitPassword">
                                                <!-- Password -->
                                                <div class="mb-4">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New
                                                        Password</label>
                                                    <input type="password" x-model="password"
                                                        class="w-full rounded border px-3 py-2 dark:bg-gray-900 dark:text-white"
                                                        required>
                                                </div>

                                                <!-- Confirm Password -->
                                                <div class="mb-4">
                                                    <label
                                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm
                                                        Password</label>
                                                    <input type="password" x-model="confirmPassword"
                                                        class="w-full rounded border px-3 py-2 dark:bg-gray-900 dark:text-white"
                                                        required>
                                                </div>

                                                <!-- Error Message -->
                                                <template x-if="errorMessage">
                                                    <div class="text-red-600 text-sm mb-3" x-text="errorMessage"></div>
                                                </template>

                                                <!-- Submit -->
                                                <button type="submit"
                                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                                    Update Password
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>



                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="rounded-md bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600">
                                    Edit
                                </a>

                                {{-- <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                    class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                                    Delete
                                    </button>
                                    </form> --}}





                                <!-- Replace your current delete form with this code -->
                                <div x-data="{ showDeleteModal: false }">
                                    <!-- Delete Button (triggers modal) -->
                                    <button @click="showDeleteModal = true"
                                        class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                                        Delete
                                    </button>

                                    <!-- Delete Confirmation Modal -->
                                    <div x-show="showDeleteModal" x-transition
                                        class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 p-4"
                                        x-cloak>
                                        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                                            <div class="p-6">
                                                <h3 class="text-lg font-medium text-gray-900">Confirm Deletion</h3>
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-600">
                                                        Are you sure you want to delete this user? This action cannot be
                                                        undone.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                <form action="{{ route('admin.users.destroy', $user->id) }}"
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



                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Info Section -->
                <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">Personal Information</h4>
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32 pb-4">
                        <div>
                            <p class="text-xs text-gray-500">First Name</p>
                            <p class="text-sm font-medium">{{ strtoupper($user->fname) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Last Name</p>
                            <p class="text-sm font-medium">{{ strtoupper($user->lname) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Phone</p>
                            <p class="text-sm font-medium">{{ $user->mobile_number }}</p>
                        </div>
                    </div>

                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">Address</h4>
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
                        <div>
                            <p class="text-xs text-gray-500">Address</p>
                            <p class="text-sm font-medium">{{ strtoupper($user->address) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">District</p>
                            <p class="text-sm font-medium">{{ strtoupper($user->district) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">City/Town</p>
                            <p class="text-sm font-medium">{{ strtoupper($user->city_town) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">State</p>
                            <p class="text-sm font-medium">{{ strtoupper($user->state) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Country</p>
                            <p class="text-sm font-medium">{{ strtoupper($user->country) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Postal Code</p>
                            <p class="text-sm font-medium">{{ strtoupper($user->pincode) }}</p>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.pageXData = {
            page: 'users',
            isProfileImageModal: false,
            isPasswordModal: false,
            isProfileInfoModal: false,
        };
    </script>
@endpush



@section('scripts')
    <!-- Alpine Logic -->
    <script>
        function profileImageUploader() {
            return {
                open: false,
                cropper: null,
                imageUrl: @json($user->profile_image ? asset('storage/' . $user->profile_image) : '/default-avatar.png'),

                handleFile(event) {
                    const file = event.target.files[0];
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
                    
                    this.cropper.getCroppedCanvas().toBlob((blob) => {
                        const formData = new FormData();
                        formData.append('image', blob);
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('user_id', '{{ $user->id }}');

                        fetch('{{ route('admin.users.profile.upload') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.isProfileImageModal = false;

                                    // Show success toast
                                    toastr.success('Profile image uploaded successfully', 'Success');

                                    // Refresh the image after a short delay
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    toastr.warning('Failed to upload image', 'Warning');
                                }
                            })
                            .catch(error => {
                                toastr.error('Error occurred while uploading', 'Error');
                            });
                    }, 'image/jpeg');
                },

                removeImage() {
                    if (!confirm('Are you sure you want to remove the profile image?')) return;

                    fetch('{{ route('admin.profile.removeImage') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.imageUrl = '/default-avatar.png';
                            } else {
                                alert('Failed to remove image.');
                            }

                        });
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

                    if (this.password !== this.confirmPassword) {
                        this.errorMessage = 'Passwords do not match';
                        return;
                    }

                    try {
                        const res = await fetch("{{ route('admin.users.profile.password.update') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                password: this.password,
                                password_confirmation: this.confirmPassword,
                                user_id: {{ $user->id }}
                            })
                        });

                        const data = await res.json();

                        if (data.success) {

                            // alert('ok');

                            this.password = '';
                            this.confirmPassword = '';
                            this.isPasswordModal = false;
                            this.isProfileInfoModal = false;

                            // Show success toast
                            toastr.success('Password updated successfully', 'Success');

                            setTimeout(() => {

                                window.location.href =
                                    '{{ route('admin.users.show', ['user' => '__USER_ID__']) }}'.replace(
                                        '__USER_ID__', {{ $user->id }});
                            }, 3000);



                        } else {
                            toastr.error('Failed to update password', 'Error');
                        }

                    } catch (error) {
                        this.errorMessage = 'Something went wrong';
                    }
                }
            }
        }
    </script>
@endsection
