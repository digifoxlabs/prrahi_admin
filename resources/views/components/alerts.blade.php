
{{-- @if (session('success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3"
    >
        {{ session('success') }}
    </div>
@endif --}}

{{-- <div x-data x-show="$store.page.alert.show" x-transition class="mb-4">
    <div :class="{
        'bg-green-100 text-green-800': $store.page.alert.type === 'success',
        'bg-yellow-100 text-yellow-800': $store.page.alert.type === 'warning',
    }"
        class="rounded p-4">
        <div class="flex justify-between items-center">
            <span x-text="$store.page.alert.message"></span>
            <button @click="$store.page.alert.show = false" class="ml-4 font-bold">×</button>
        </div>
    </div>
</div> --}}

{{-- <div x-data x-show="$store.alert.show" x-transition.duration.300ms>
    <div
        :class="{
            'bg-green-100 border-green-300 text-green-700': $store.alert.type === 'success',
            'bg-yellow-100 border-yellow-300 text-yellow-700': $store.alert.type === 'warning'
        }"
        class="border px-4 py-3 rounded relative"
        role="alert"
    >
        <strong class="font-bold capitalize" x-text="$store.alert.type + '!'"></strong>
        <span class="block sm:inline" x-text="$store.alert.message"></span>
        <span @click="$store.alert.show = false"
              class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <svg class="fill-current h-6 w-6 text-gray-700" role="button"
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path
                    d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 10-1.414 1.414L8.586 10l-2.934 2.934a1 1 0 101.414 1.414L10 11.414l2.934 2.934a1 1 0 001.414-1.414L11.414 10l2.934-2.934a1 1 0 000-1.414z"/>
            </svg>
        </span>
    </div>
</div> --}}


{{-- <div 
    x-data 
    x-show="$store.alert.show"
    x-transition.duration.300ms 
    class="mb-4"
>
    <div 
        :class="{
            'bg-green-100 border border-green-400 text-green-700': $store.alert.type === 'success',
            'bg-yellow-100 border border-yellow-400 text-yellow-700': $store.alert.type === 'warning',
            'bg-red-100 border border-red-400 text-red-700': $store.alert.type === 'error'
        }"
        class="px-4 py-3 rounded relative"
        role="alert"
    >
        <strong class="font-bold capitalize" x-text="$store.alert.type + '!'"></strong>
        <span class="block sm:inline ml-2" x-text="$store.alert.message"></span>
        <span 
            @click="$store.alert.show = false" 
            class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer"
        >
            <svg class="fill-current h-6 w-6 text-gray-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 10-1.414 1.414L8.586 10l-2.934 2.934a1 1 0 101.414 1.414L10 11.414l2.934 2.934a1 1 0 001.414-1.414L11.414 10l2.934-2.934a1 1 0 000-1.414z"/>
            </svg>
        </span>
    </div>
</div> --}}


<div x-data 
     x-show="$store.alert.show" 
     x-transition
     class="fixed top-4 right-4 z-50">
    <div x-bind:class="{
        'bg-green-100 border-green-400 text-green-700': $store.alert.type === 'success',
        'bg-yellow-100 border-yellow-400 text-yellow-700': $store.alert.type === 'warning',
        'bg-red-100 border-red-400 text-red-700': $store.alert.type === 'error'
    }" 
    class="border px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline" x-text="$store.alert.message"></span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="$store.alert.show = false">
            <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </span>
    </div>
</div>
