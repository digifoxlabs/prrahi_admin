@extends('admin.admin-layout')

@section('page-content')

<div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">

    <div class="rounded-2xl border border-gray-200 bg-white shadow dark:border-gray-800 dark:bg-gray-900 overflow-hidden">

        <div class="flex h-[85vh]">

            {{-- ================= SIDEBAR ================= --}}
            <div class="w-80 border-r border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900 flex flex-col">

                {{-- Header --}}
                <div class="p-4 border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        Sales Visits
                    </h2>

                    {{-- Search --}}
                    <div class="mt-3">
                        <input type="text"
                               id="searchSales"
                               placeholder="Search Sales Person..."
                               class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500">
                    </div>
                </div>

                {{-- Sales Person List --}}
                <div id="salesList"
                     class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">

                    @foreach($chatGroups as $group)

                        @php
                            $salesPerson = $group['sales_person'];
                            if(!$salesPerson) continue;
                        @endphp

                        <a href="{{ route('admin.visits.index', ['sales_person_id' => $salesPerson->id]) }}"
                           class="block px-4 py-3 transition bg-transparent dark:bg-gray-900 hover:bg-blue-50 dark:hover:bg-gray-800
                           {{ $selectedSalesPersonId == $salesPerson->id ? 'bg-blue-100 dark:bg-gray-800' : '' }}">

                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                        {{ $salesPerson->name }}
                                    </p>

                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate w-52">
                                        {{ $group['last_message'] ?? 'No messages' }}
                                    </p>
                                </div>

                                @if($group['last_time'])
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ \Carbon\Carbon::parse($group['last_time'])->diffForHumans() }}
                                    </span>
                                @endif
                            </div>

                        </a>

                    @endforeach

                </div>

            </div>
            {{-- ================= END SIDEBAR ================= --}}



            {{-- ================= CHAT AREA ================= --}}
            <div class="flex-1 bg-gray-100 dark:bg-gray-900">

                @if($selectedSalesPerson)
                    @include('admin.visits.partials.messages')
                @else
                    <div class="h-full flex items-center justify-center text-gray-400 dark:text-gray-500 text-lg">
                        Select a Sales Person to view visits
                    </div>
                @endif

            </div>
            {{-- ================= END CHAT AREA ================= --}}

        </div>
    </div>

</div>


<!-- DELETE MODAL -->
<div id="deleteModal"
     class="fixed inset-0 bg-black/60 hidden items-center justify-center z-999999">

    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 w-full max-w-md shadow-xl">

        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">
            Delete Chat
        </h3>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Are you sure you want to delete this message?
        </p>

        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                Cancel
            </button>

            <button id="confirmDeleteBtn"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 dark:hover:bg-red-500">
                Delete
            </button>
        </div>
    </div>
</div>


@endsection


@push('scripts')

    <script>
        window.pageXData = {
            page: 'sales-visits',
        };
    </script>


<script>

    // ================= SEARCH FILTER =================
    document.getElementById('searchSales').addEventListener('keyup', function() {

        let value = this.value.toLowerCase();
        let items = document.querySelectorAll('#salesList a');

        items.forEach(function(item) {
            let name = item.innerText.toLowerCase();
            item.style.display = name.includes(value) ? '' : 'none';
        });
    });


    // ================= INFINITE SCROLL =================
    document.addEventListener("DOMContentLoaded", function() {

        let container = document.getElementById('chatContainer');

        if (!container) return;

        container.addEventListener('scroll', function() {

            if (container.scrollTop === 0) {

                let nextPage = "{{ $visits->nextPageUrl() }}";

                if (nextPage) {
                    fetch(nextPage)
                        .then(res => res.text())
                        .then(html => {
                            let parser = new DOMParser();
                            let doc = parser.parseFromString(html, 'text/html');
                            let newMessages = doc.querySelector('#chatContainer').innerHTML;
                            container.insertAdjacentHTML('afterbegin', newMessages);
                        });
                }
            }

        });
    });



let deleteVisitId = null;

function openDeleteModal(id) {
    deleteVisitId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    deleteVisitId = null;
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function () {

    if (!deleteVisitId) return;

    fetch(`{{ url('admin/visits') }}/${deleteVisitId}`, {


        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {

            // Remove chat bubble smoothly
            let messageCard = document.getElementById('visit-' + deleteVisitId);
            if (messageCard) {
                messageCard.remove();
            }

            closeDeleteModal();
        }
    })
    .catch(err => {
        console.error(err);
        closeDeleteModal();
    });
});


</script>



@endpush
