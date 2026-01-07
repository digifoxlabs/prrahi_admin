@extends('sales.layout')

@section('page-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
     



    {{-- Flash --}}
    @if (session('success'))
        <div x-data="{ show:true }" x-init="setTimeout(()=>show=false,3000)" x-show="show" x-transition
             class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show:true }" x-init="setTimeout(()=>show=false,3000)" x-show="show" x-transition
             class="bg-yellow-100 text-red-800 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white
                px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">

        <div class="mx-auto w-full max-w-8xl" x-data="retailersTableComponent()">

            {{-- ACTION BAR --}}
            <div class="mb-4 rounded-xl border border-gray-200 bg-white/90 backdrop-blur
                        dark:border-gray-800 dark:bg-black md:border-0 md:bg-transparent md:p-0 p-3">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    {{-- Search --}}
                    <div class="w-full sm:flex-1">
                        <div class="relative w-full sm:w-72 md:w-80 lg:w-96">
                            <input type="text" x-model="search" @input="updateQuery"
                                   placeholder="Search retailers..."
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-10 text-sm
                                          focus:ring-2 focus:ring-blue-500
                                          dark:border-gray-700 dark:bg-transparent dark:text-white/90">
                            <button type="button" x-show="search"
                                    @click="search=''; updateQuery()"
                                    class="absolute inset-y-0 right-0 pr-3 text-gray-500">
                                ✕
                            </button>
                        </div>
                    </div>

                    {{-- Right actions --}}
                    <div class="flex items-center gap-3 shrink-0">           

                        {{-- Create --}}
                        <a href="{{ route('sales.retailers.create') }}"
                           class="inline-flex items-center rounded-lg bg-blue-600
                                  px-3 py-2 text-sm text-white hover:bg-blue-700">
                            + Create
                        </a>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto">
                <table id="retailersTable"
                       class="min-w-full border text-sm table-auto md:table-fixed
                              border-gray-200 dark:border-gray-700
                              text-gray-900 dark:text-white/90">

                    <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase">
                        <tr class="text-left">
                            <th class="p-2">#</th>
                            <th class="p-2">Retailer</th>
                            <th class="p-2">Contact</th>
                            <th class="p-2">Phone</th>
                            <th class="p-2">District</th>
                            <th class="p-2">Town</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($retailers as $retailer)
                        <tr class="border-t dark:border-gray-700"
                            x-data="{ open:false, showModal:false, deleteUrl:'' }">

                            <td class="p-2">{{ $loop->iteration }}</td>
                            <td class="p-2 font-medium">{{ $retailer->retailer_name }}</td>
                            <td class="p-2">{{ $retailer->contact_person }}</td>
                            <td class="p-2">{{ $retailer->contact_number }}</td>
                            <td class="p-2">{{ $retailer->district ?? '-' }}</td>
                            <td class="p-2">{{ $retailer->town ?? '-' }}</td>


                            {{-- ACTIONS (SAME AS SALESPERSON) --}}
                            <td class="p-2 relative"
                                x-data="{
                                    open:false,
                                    dropdownStyle:'',
                                    toggle($refs){
                                        this.open=!this.open;
                                        if(this.open){
                                            this.$nextTick(()=>{
                                                const r=$refs.btn.getBoundingClientRect();
                                                const w=160, m=8;
                                                let l=Math.max(m,r.right-w);
                                                let t=r.bottom+6;
                                                this.dropdownStyle=`left:${l}px;top:${t}px;width:${w}px`;
                                                const onScroll=()=>{this.open=false;window.removeEventListener('scroll',onScroll,true)};
                                                window.addEventListener('scroll',onScroll,true);
                                            });
                                        }
                                    }
                                }"
                                @keydown.escape.window="open=false">

                                <button type="button" x-ref="btn"
                                        @click.prevent.stop="toggle($refs)"
                                        class="bg-gray-200 dark:bg-gray-700
                                               px-3 py-1 text-sm rounded
                                               hover:bg-gray-300 dark:hover:bg-gray-600">
                                    Actions ▾
                                </button>

                                <div x-cloak x-show="open" x-transition
                                     @click.away="open=false"
                                     :style="dropdownStyle"
                                     class="fixed z-[100] bg-white dark:bg-gray-800
                                            border border-gray-200 dark:border-gray-700
                                            rounded shadow-lg">

                                    <a href="{{ route('sales.retailers.show',$retailer) }}"
                                       class="block px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                        👁️ View
                                    </a>

                   
                                    <a href="{{ route('sales.retailers.edit',$retailer) }}"
                                       class="block px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                        ✏️ Edit
                                    </a>
                                  

                                    <button
                                        @click.prevent="showModal=true; deleteUrl='{{ route('sales.retailers.destroy',$retailer) }}'; open=false"
                                        class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        🗑️ Delete
                                    </button>
                                </div>

                                {{-- DELETE MODAL --}}
                                <template x-if="showModal">
                                    <div class="fixed inset-0 flex items-center justify-center z-[200]">
                                        <div class="absolute inset-0 bg-black/50" @click="showModal=false"></div>
                                        <div class="bg-white dark:bg-gray-900 rounded-lg p-6 w-full max-w-md z-[201]">
                                            <h3 class="text-lg font-semibold mb-4 text-red-600">Confirm Deletion</h3>
                                            <p class="mb-6">Delete <strong>{{ $retailer->retailer_name }}</strong>?</p>
                                            <div class="flex justify-end gap-3">
                                                <button @click="showModal=false"
                                                        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                                                    Cancel
                                                </button>
                                                <form :action="deleteUrl" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                                        Yes, Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-400">No retailers found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $retailers->links() }}</div>
            </div>

        </div>
    </div>


    
    </div>
@endsection

    @push('scripts')
        <script>
            window.pageXData = {
                page: 'dashboard',
            };
        </script>


<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
function retailersTableComponent(){
    return{
        search:'{{ request('search','') }}',
        debounceTimeout:null,
        updateQuery(){
            clearTimeout(this.debounceTimeout);
            this.debounceTimeout=setTimeout(()=>{
                const base='{{ route('admin.retailers.index') }}';
                const q=this.search.trim()?`?search=${encodeURIComponent(this.search)}`:'';
                window.location.href=base+q;
            },500);
        },
        exportToExcel(){
            const table=document.getElementById('retailersTable');
            const wb=XLSX.utils.table_to_book(table,{sheet:'Retailers'});
            XLSX.writeFile(wb,'retailers.xlsx');
        }
    }
}
</script>








    @endpush