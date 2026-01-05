@extends('admin.admin-layout')

@section('page-content')
<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

    @include('admin.retailers._breadcrump')

    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show:true }" x-init="setTimeout(()=>show=false,3000)"
             x-show="show" x-transition
             class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show:true }" x-init="setTimeout(()=>show=false,3000)"
             x-show="show" x-transition
             class="bg-yellow-100 text-red-800 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif


    <div class="mb-2 flex items-center justify-end">
    <a href="{{ route('admin.retailers.index') }}"
       class="inline-flex items-center gap-2
              rounded-lg border border-gray-300
              bg-white px-3 py-2 text-sm
              text-gray-700 hover:bg-gray-100
              dark:border-gray-700 dark:bg-gray-900
              dark:text-gray-300 dark:hover:bg-gray-800 transition">
        ← Back
    </a>

  
        {{-- <a href="{{ route('admin.retailers.edit', $retailer) }}"
           class="inline-flex items-center gap-2
                  rounded-lg bg-indigo-600
                  px-4 py-2 text-sm font-medium
                  text-white hover:bg-indigo-700 transition">
            ✏️ Edit
        </a> --}}
   
    </div>


    <div
        class="min-h-screen rounded-2xl border border-gray-200 bg-white
               px-5 py-7 dark:border-gray-700 dark:bg-white/[0.03]
               xl:px-10 xl:py-12">

        <div class="mx-auto w-full max-w-8xl">

            {{-- HEADER CARD --}}
            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-700 lg:p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

                    {{-- Retailer Info --}}
                    <div class="flex flex-col gap-2">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ strtoupper($retailer->retailer_name) }}
                        </h2>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Contact Person: {{ $retailer->contact_person ?? '-' }}
                        </p>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Phone: {{ $retailer->contact_number ?? '-' }}
                        </p>


                    <div>
                        <strong>Appointed By:</strong>

                        @php
                            $appointedBy = $retailer->appointedBy;
                            $appointedLabel = '—';
                            $appointedName  = '—';

                            if ($appointedBy) {
                                switch (class_basename($appointedBy)) {
                                    case 'User': // Admin
                                        $appointedLabel = 'Admin';
                                        $appointedName  = $appointedBy->fname ?? '—';
                                        break;

                                    case 'Distributor':
                                        $appointedLabel = 'Distributor';
                                        $appointedName  = $appointedBy->firm_name ?? '—';
                                        break;

                                    case 'SalesPerson':
                                        $appointedLabel = 'Sales Person';
                                        $appointedName  = $appointedBy->name ?? '—';
                                        break;
                                }
                            }
                        @endphp

                        <span>
                            {{ $appointedLabel }}
                            @if($appointedName !== '—')
                                – <span class="font-medium">{{ $appointedName }}</span>
                            @endif
                        </span>
                    </div>


                    </div>


                    {{-- ACTION BUTTONS --}}
                    <div class="flex items-center gap-2">

                        {{-- Edit --}}
                      
                            <a href="{{ route('admin.retailers.edit', $retailer) }}"
                               class="inline-flex items-center gap-2
                                      rounded-md bg-indigo-600
                                      px-4 py-2 text-sm text-white
                                      hover:bg-indigo-700 transition">
                                ✏️ Edit
                            </a>
                        

                        {{-- Delete --}}
                       
                            <div x-data="{ showDelete:false }">
                                <button @click="showDelete=true"
                                        class="inline-flex items-center gap-2
                                               rounded-md bg-red-600
                                               px-4 py-2 text-sm text-white
                                               hover:bg-red-700 transition">
                                    🗑️ Delete
                                </button>

                                {{-- Delete Modal --}}
                                <div x-show="showDelete" x-cloak x-transition
                                     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4">
                                    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-md">
                                        <div class="p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                Confirm Deletion
                                            </h3>
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                                Are you sure you want to delete
                                                <strong>{{ $retailer->retailer_name }}</strong>?
                                                This action cannot be undone.
                                            </p>
                                        </div>
                                        <div
                                            class="bg-gray-50 dark:bg-gray-800 px-4 py-3
                                                   flex justify-end gap-3">

                                            <button @click="showDelete=false"
                                                class="px-4 py-2 rounded
                                                       bg-gray-200 dark:bg-gray-700
                                                       text-gray-800 dark:text-white
                                                       hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                                Cancel
                                            </button>

                                            <form action="{{ route('admin.retailers.destroy', $retailer) }}"
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 rounded
                                                           bg-red-600 text-white
                                                           hover:bg-red-700 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                   

                    </div>
                </div>
            </div>

            {{-- RETAILER DETAILS --}}
            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">
                    Retailer Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 dark:text-gray-300">

                    <div><strong>Retailer Name:</strong> {{ $retailer->retailer_name }}</div>
                    <div><strong>Contact Person:</strong> {{ $retailer->contact_person ?? '-' }}</div>
                    <div><strong>Phone:</strong> {{ $retailer->contact_number ?? '-' }}</div>
                    <div><strong>Email:</strong> {{ $retailer->email ?? '-' }}</div>

                    <div><strong>Town:</strong> {{ $retailer->town ?? '-' }}</div>
                    <div><strong>District:</strong> {{ $retailer->district ?? '-' }}</div>
                    <div><strong>State:</strong> {{ $retailer->state ?? '-' }}</div>
                    <div><strong>Pincode:</strong> {{ $retailer->pincode ?? '-' }}</div>

                    <div class="md:col-span-2">
                        <strong>Address:</strong>
                        {{ $retailer->address ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- DISTRIBUTOR INFO --}}
            <div class="p-5 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">
                    Assigned Distributor
                </h3>

                @if($retailer->distributor)
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <p><strong>Firm Name:</strong> {{ $retailer->distributor->firm_name }}</p>
                        <p><strong>Contact:</strong> {{ $retailer->distributor->contact_person }}</p>
                        <p><strong>Phone:</strong> {{ $retailer->distributor->contact_number }}</p>

                        <div class="mt-3">
                            <a href="{{ route('admin.distributors.show', $retailer->distributor) }}"
                               class="inline-flex items-center rounded-md
                                      bg-gray-100 dark:bg-gray-800
                                      px-3 py-1 text-sm
                                      hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                View Distributor
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        No distributor assigned.
                    </p>
                @endif
            </div>

        </div>
    </div>
    
</div>
@endsection

@push('scripts')

    <script>
        window.pageXData = {
            page: 'retailer',
        };
    </script>
    
@endpush
