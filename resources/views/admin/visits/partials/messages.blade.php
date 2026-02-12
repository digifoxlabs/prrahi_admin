@if($selectedSalesPerson)

<div
    class="flex flex-col h-[85vh] bg-gray-50 dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">

    {{-- Header --}}
    <div
        class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ $selectedSalesPerson->name }}
            </h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Sales Visit Chat History
            </p>
        </div>
    </div>

    {{-- Messages Area --}}
    <div id="chatContainer" class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-800 px-8 py-6 space-y-6">

        @php
        $currentDate = null;
        @endphp

        @forelse($visits as $visit)

        {{-- Date Grouping --}}
        @if($currentDate !== $visit->created_at->format('Y-m-d'))
        @php $currentDate = $visit->created_at->format('Y-m-d'); @endphp

        <div class="flex justify-center">
            <span class="text-xs bg-gray-200 text-gray-600 dark:bg-gray-800 dark:text-gray-300 px-3 py-1 rounded-full">
                {{ \Carbon\Carbon::parse($currentDate)->format('d M Y') }}
            </span>
        </div>
        @endif

        @php
        $entityLabel = null;
        $entityName = null;

        if ($visit->entity_type === 'retailer') {
        $entityLabel = 'Retailer';
        $entityName = $visit->retailer->retailer_name;
        }

        if ($visit->entity_type === 'distributor') {
        $entityLabel = 'Distributor';
        $entityName = $visit->distributor->firm_name;
        }
        @endphp

        {{-- Chat Bubble --}}
        <div class="flex justify-start group" id="visit-{{ $visit->id }}">

            <div class="w-full max-w-4xl">

                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 relative">

                    <button onclick="openDeleteModal({{ $visit->id }})"
                        class="absolute top-3 right-3 text-gray-400 dark:text-gray-500 hover:text-red-500 text-sm opacity-0 group-hover:opacity-100 transition">
                        🗑
                    </button>

                    {{-- Entity Info --}}
                    @if($entityLabel && $entityName)
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">
                            {{ $entityLabel }}
                        </span>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                            {{ $entityName }}
                        </span>
                    </div>
                    @endif

                    {{-- Message --}}
                    @if($visit->message)
                    <div class="text-sm text-gray-800 dark:text-white/90 whitespace-pre-line">
                        {{ $visit->message }}
                    </div>
                    @endif

                    {{-- Location Icon + Mini Map --}}
                    @if($visit->latitude && $visit->longitude)
                    <div class="mt-3">

                        <button onclick="openMapPreview({{ $visit->latitude }}, {{ $visit->longitude }})"
                            class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                            📍
                            <span>View Location</span>
                        </button>

                    </div>
                    @endif

                    {{-- Documents --}}
                    @if($visit->documents->count())
                    <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($visit->documents as $doc)

                        @if(Str::contains($doc->file_type, 'image'))
                        <img src="{{ $doc->file_path }}" onclick="openLightbox('{{ $doc->file_path }}')"
                            class="rounded-lg cursor-pointer hover:scale-105 transition shadow">
                        @else
                        <a href="{{ $doc->file_path }}" target="_blank"
                            class="text-sm text-blue-600 dark:text-blue-400 underline">
                            View Document
                        </a>
                        @endif

                        @endforeach
                    </div>
                    @endif

                    {{-- Time --}}
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-3 text-right">
                        {{ $visit->created_at->format('h:i A') }}
                    </div>

                </div>
            </div>
        </div>

        @empty
        <div class="text-center text-gray-400 dark:text-gray-500">
            No visits found.
        </div>
        @endforelse

    </div>
</div>

{{-- ================= MAP MODAL ================= --}}
<div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-999999">

    <div id="mapModalPanel"
        class="bg-white dark:bg-gray-900 rounded-xl w-[92vw] max-w-6xl h-[80vh] shadow-2xl overflow-hidden flex flex-col border border-gray-200 dark:border-gray-800">

        <div
            class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-white/90">Location Preview</h3>

            <div class="flex items-center gap-2">
                <button id="mapFullscreenBtn" type="button" onclick="toggleMapFullScreen()"
                    class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-gray-700 dark:text-white">
                    Full Screen
                </button>

                <button type="button" onclick="closeMapPreview()"
                    class="px-3 py-1.5 text-xs font-medium bg-red-600 hover:bg-red-700 rounded-lg text-white">
                    Close
                </button>
            </div>
        </div>

        <div id="miniMap" class="w-full flex-1"></div>

    </div>
</div>

{{-- ================= LIGHTBOX ================= --}}
<div id="lightbox" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-999999">

    <img id="lightboxImage" class="max-h-[90vh] max-w-[90vw] rounded-lg shadow-lg">

</div>

{{-- ================= SCRIPT SECTION ================= --}}
<script>
    let miniMapInstance = null;
    let isMapFullScreen = false;

    function mountVisitOverlaysToBody() {
        const mapModal = document.getElementById('mapModal');
        const lightbox = document.getElementById('lightbox');
        if (mapModal && mapModal.parentElement !== document.body) {
            document.body.appendChild(mapModal);
        }
        if (lightbox && lightbox.parentElement !== document.body) {
            document.body.appendChild(lightbox);
        }
    }
    mountVisitOverlaysToBody();
    document.addEventListener('DOMContentLoaded', mountVisitOverlaysToBody);

    function openMapPreview(lat, lng) {
        mountVisitOverlaysToBody();
        document.getElementById('mapModal').classList.remove('hidden');
        document.getElementById('mapModal').classList.add('flex');
        const panel = document.getElementById('mapModalPanel');
        const fullScreenBtn = document.getElementById('mapFullscreenBtn');
        panel.classList.remove('w-screen', 'h-screen', 'max-w-none', 'rounded-none');
        panel.classList.add('w-[92vw]', 'max-w-6xl', 'h-[80vh]');
        fullScreenBtn.innerText = 'Full Screen';
        isMapFullScreen = false;
        setTimeout(function() {
            if (miniMapInstance) {
                miniMapInstance.remove();
            }
            miniMapInstance = L.map('miniMap').setView([lat, lng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(miniMapInstance);
            L.marker([lat, lng]).addTo(miniMapInstance);
        }, 200);
    }

    function closeMapPreview() {
        document.getElementById('mapModal').classList.add('hidden');
        document.getElementById('mapModal').classList.remove('flex');
        if (miniMapInstance) {
            miniMapInstance.remove();
            miniMapInstance = null;
        }
    }

    function toggleMapFullScreen() {
        const panel = document.getElementById('mapModalPanel');
        const btn = document.getElementById('mapFullscreenBtn');
        isMapFullScreen = !isMapFullScreen;
        panel.classList.toggle('w-screen', isMapFullScreen);
        panel.classList.toggle('h-screen', isMapFullScreen);
        panel.classList.toggle('max-w-none', isMapFullScreen);
        panel.classList.toggle('rounded-none', isMapFullScreen);
        panel.classList.toggle('w-[92vw]', !isMapFullScreen);
        panel.classList.toggle('max-w-6xl', !isMapFullScreen);
        panel.classList.toggle('h-[80vh]', !isMapFullScreen);
        btn.innerText = isMapFullScreen ? 'Exit Full Screen' : 'Full Screen';
        if (miniMapInstance) {
            setTimeout(function() {
                miniMapInstance.invalidateSize();
            }, 200);
        }
    }

    function openLightbox(src) {
        document.getElementById('lightboxImage').src = src;
        document.getElementById('lightbox').classList.remove('hidden');
        document.getElementById('lightbox').classList.add('flex');
    }
    document.getElementById('lightbox').addEventListener('click', function() {
        this.classList.add('hidden');
    });
</script>

@endif