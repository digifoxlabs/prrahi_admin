@extends('admin.admin-layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

        @include('components.alerts')
 
        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `Profile` }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName"></h2>

                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="{{ route('admin.dashboard') }}">
                                Dashboard
                                <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke=""
                                        stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName"></li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Breadcrumb End -->

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">
                Profile
            </h3>

            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">


                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

                    <div class="flex flex-col items-center w-full gap-6 xl:flex-row">

                        <div class="w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
                            <img src="{{ auth('admin')->user()->profile_image ? asset('storage/' . $user->profile_image) : asset('images/user/user-01.jpg') }}"
                                alt="user" />
                        </div>

                        <div class="order-3 xl:order-2">
                            <h4
                                class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left">
                                {{ strtoupper(auth('admin')->user()->fname) }}
                                {{ strtoupper(auth('admin')->user()->lname) }}
                            </h4>
                            <div class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @foreach (auth('admin')->user()->roles as $role)
                                        {{ strtoupper($role->name) }}
                                    @endforeach
                                </p>
                                <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ strtoupper($user->district) . ',' . strtoupper($user->state) }}
                                </p>
                            </div>
                        </div>


                        <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">



                            <button @click="isProfileImageModal = true"
                                class="flex h-11 w-11 items-center justify-center gap-2 rounded-full border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <circle cx="12" cy="13" r="3" stroke="#1C274C" stroke-width="1.5">
                                        </circle>
                                        <path
                                            d="M3.0001 12.9999C3.0001 10.191 2.99995 8.78673 3.67407 7.77783C3.96591 7.34107 4.34091 6.96607 4.77767 6.67423C5.78656 6.00011 7.19103 6.00011 9.99995 6.00011H14C16.8089 6.00011 18.2133 6.00011 19.2222 6.67423C19.659 6.96607 20.034 7.34107 20.3258 7.77783C21 8.78673 21.0001 10.191 21.0001 12.9999C21.0001 15.8088 21.0001 17.2133 20.326 18.2222C20.0341 18.6589 19.6591 19.0339 19.2224 19.3258C18.2135 19.9999 16.809 19.9999 14.0001 19.9999H10.0001C7.19117 19.9999 5.78671 19.9999 4.77782 19.3258C4.34106 19.0339 3.96605 18.6589 3.67422 18.2222C3.44239 17.8752 3.29028 17.4815 3.19049 16.9999"
                                            stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path>
                                        <path d="M18 10H17.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round">
                                        </path>
                                        <path d="M14.5 3.5H9.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round">
                                        </path>
                                    </g>
                                </svg>
                            </button>


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

                                        <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Update Password
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

                        </div>



                        <!-- Image Crop Modal -->
                        <div x-data="profileImageUploader()" class="p-6">

                            <div x-show="isProfileImageModal"
                                class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-99999" x-cloak>

                                <div
                                    class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
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
                            </div>
                        </div>
                        {{-- Image Crop Modal End --}}

                    </div>
                </div>
            </div>


            
            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                
                

                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
                                Personal Information
                            </h4>

                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32 pb-4">
                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        First Name
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper($user->fname) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        Last Name
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper($user->lname) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        Email address
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper($user->email) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        Phone
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper($user->mobile_number) }}
                                    </p>
                                </div>
                            </div>

                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
                                Address
                            </h4>

                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-7 2xl:gap-x-32">
                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        Address
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper($user->address) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        District
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper($user->district) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        City/Town
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper($user->city_town) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        State
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper($user->state) }}

                                    </p>
                                </div>

                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        Country
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper(auth('admin')->user()->country) }}
                                    </p>
                                </div>
                                <div>
                                    <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                                        Postal Code
                                    </p>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                        {{ strtoupper(auth('admin')->user()->pincode) }}
                                    </p>
                                </div>

                            </div>


                        </div>



                        <div x-data="profileModalComponent()" >

                        <button @click="isProfileInfoModal = true; loadProfile({{ auth('admin')->user()->toJson() }})"
                            class="flex w-full items-center justify-center gap-2 rounded-full border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 lg:inline-flex lg:w-auto">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z"
                                    fill="" />
                            </svg>
                            Edit
                        </button>




            {{-- Edit Modal Start --}}
                <div x-show="isProfileInfoModal"
                    class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-99999">
                    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"></div>
                    <div @click.outside="isProfileInfoModal = false"
                        class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                        <!-- close btn -->
                        <button @click="isProfileInfoModal = false"
                            class="transition-color absolute right-5 top-5 z-999 flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-700 dark:bg-white/[0.05] dark:text-gray-400 dark:hover:bg-white/[0.07] dark:hover:text-gray-300">
                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.04289 16.5418C5.65237 16.9323 5.65237 17.5655 6.04289 17.956C6.43342 18.3465 7.06658 18.3465 7.45711 17.956L11.9987 13.4144L16.5408 17.9565C16.9313 18.347 17.5645 18.347 17.955 17.9565C18.3455 17.566 18.3455 16.9328 17.955 16.5423L13.4129 12.0002L17.955 7.45808C18.3455 7.06756 18.3455 6.43439 17.955 6.04387C17.5645 5.65335 16.9313 5.65335 16.5408 6.04387L11.9987 10.586L7.45711 6.04439C7.06658 5.65386 6.43342 5.65386 6.04289 6.04439C5.65237 6.43491 5.65237 7.06808 6.04289 7.4586L10.5845 12.0002L6.04289 16.5418Z"
                                    fill="" />
                            </svg>
                        </button>
                        <div class="px-2 pr-14">
                            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                                Edit Personal Information
                            </h4>
                            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
                                Update your details to keep your profile up-to-date.
                            </p>
                        </div>
                        <form class="flex flex-col">
                            <div class="custom-scrollbar h-[450px] overflow-y-auto px-2">

                                <div class="mt-7">
                                    <h5 class="mb-5 text-lg font-medium text-gray-800 dark:text-white/90 lg:mb-6">
                                        Personal Information
                                    </h5>

                                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                                        <div class="col-span-2 lg:col-span-1">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                First Name
                                            </label>
                                            <input type="text" x-model="profile.fname" name="fname"
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                        </div>

                                        <div class="col-span-2 lg:col-span-1">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Last Name
                                            </label>
                                            <input type="text" x-model="profile.lname"
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                        </div>

                                        <div class="col-span-2 lg:col-span-1">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Email Address
                                            </label>
                                            <input type="text" x-model="profile.email" readonly
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 cursor-not-allowed bg-gray-100 dark:bg-gray-800" />

                                        </div>

                                        <div class="col-span-2 lg:col-span-1">
                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Phone
                                            </label>
                                            <input type="text" x-model="profile.mobile_number"
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                        </div>



                                        <div class="col-span-2 lg:col-span-1">

                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Address</label>
                                            <input type="text" x-model="profile.address"
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                                        </div>

                                        <div class="col-span-2 lg:col-span-1">

                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">District</label>
                                            <input type="text" x-model="profile.district"
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                                        </div>

                                        <div class="col-span-2 lg:col-span-1">

                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">City/Town</label>
                                            <input type="text" x-model="profile.city_town"
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                                        </div>

                                        <div class="col-span-2 lg:col-span-1">

                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">State</label>
                                            <input type="text" x-model="profile.state"
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                                        </div>

                                        <div class="col-span-2 lg:col-span-1">

                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Country</label>
                                            <input type="text" x-model="profile.country"
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                                        </div>

                                        <div class="col-span-2 lg:col-span-1">

                                            <label
                                                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pincode</label>
                                            <input type="text" x-model="profile.pincode"
                                                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">
                                <button @click="isProfileInfoModal = false" type="button"
                                    class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                                    Close
                                </button>
                                <button @click="saveProfile()" type="button"
                                    class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div> {{-- Edit Modal End --}}





            </div>

      



            <div x-show="alert.show" x-transition x-data @keydown.escape.window="alert.show = false"
                class="fixed inset-0 flex items-center justify-center z-[999999] bg-black/40 backdrop-blur-sm"
                style="display: none;">
                <div :class="{
                    'bg-green-100 border-green-500 text-green-700': alert.type === 'success',
                    'bg-yellow-100 border-yellow-500 text-yellow-700': alert.type === 'warning'
                }"
                    class="relative max-w-sm w-full rounded-xl border p-5 shadow-lg">
                    <!-- Close Button -->
                    <button @click="alert.show = false"
                        class="absolute top-2 right-2 text-lg font-bold text-gray-500 hover:text-gray-700">
                        ×
                    </button>

                    <!-- Alert Message -->
                    <div class="text-sm font-medium" x-text="alert.message"></div>
                </div>
            </div>




    </div>
@endsection


@push('scripts')
    <script>
        window.pageXData = {
            page: 'profile',
            isProfileInfoModal: false,
            isProfileAddressModal: false,
            isProfileImageModal: false,
            isPasswordModal: false,
            profile: {
                id: '',
                fname: '',
                lname: '',
                email: '',
                mobile_number: '',
                roles: [],
                address: '',
                dustrict: '',
                city_town: '',
                state: '',
                country: '',
                pincode: '',

            },
            loadProfile(data) {
                this.profile = data;
                this.open = true;
            },
            alert: {
                show: false,
                message: '',
                type: 'success', // or 'warning'
            },
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

                        fetch('{{ route('admin.profile.upload') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {


                                    this.isProfileInfoModal = false;
                                    this.alert.message = 'Image Uploaded Successfully';
                                    this.alert.type = 'success';
                                    this.alert.show = true;
                                    setTimeout(() => this.alert.show = false, 3000);


                                    setTimeout(() => {
                                        window.location.href =
                                        '{{ route('admin.profile') }}'; // ✅ Redirect to profile page
                                    }, 500);


                                } else {

                                    this.alert.message = 'Update failed!';
                                    this.alert.type = 'warning';
                                    this.alert.show = true;
                                }
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
                        const res = await fetch("{{ route('admin.profile.password.update') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                password: this.password,
                                password_confirmation: this.confirmPassword
                            })
                        });

                        const data = await res.json();

                        if (data.success) {
                            this.isPasswordModal = false;
                            this.password = '';
                            this.confirmPassword = '';
                            this.isProfileInfoModal = false;
                            this.alert.message = 'Password updated successfully';
                            this.alert.type = 'success';
                            this.alert.show = true;
                            setTimeout(() => this.alert.show = false, 3000);


                        } else {
                            this.errorMessage = data.message || 'Failed to update password';
                        }

                    } catch (error) {
                        this.errorMessage = 'Something went wrong';
                    }
                }
            }
        }
    </script>
@endsection

@push('scripts')
    <script>
        function profileModalComponent() {
            return {
                isProfileInfoModal: false,

                async saveProfile() {
                    try {
                        const response = await fetch('{{ route('admin.profile.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.profile)
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.isProfileInfoModal = false;
                            // alert("Profile updated!");

                            this.isProfileInfoModal = false;
                            this.alert.message = 'Profile updated!';
                            this.alert.type = 'success';
                            this.alert.show = true;
                            setTimeout(() => this.alert.show = false, 3000);


                            setTimeout(() => {
                                window.location.href =
                                '{{ route('admin.profile') }}'; // ✅ Redirect to profile page
                            }, 500);


                        } else {

                            this.alert.message = 'Update failed!';
                            this.alert.type = 'warning';
                            this.alert.show = true;
                        }
                    } catch (err) {
                        this.alert.message = 'Something went wrong!';
                        this.alert.type = 'warning';
                        this.alert.show = true;
                    }
                }
            }
        }
    </script>
@endpush
