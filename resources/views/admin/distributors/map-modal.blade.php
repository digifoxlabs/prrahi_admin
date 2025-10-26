<!-- Leaflet Modal -->
<div id="mapModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-60 flex items-center justify-center">
    <div class="bg-white w-full max-w-3xl rounded-lg shadow-xl relative">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Select Location</h2>
           <button type="button" onclick="closeMapModal()" class="text-gray-500 hover:text-gray-700 text-xl">
    &times;
</button>

        </div>

        <div id="map" class="h-96 w-full"></div>

        <div class="flex justify-end px-6 py-4 border-t">
                <button type="button" onclick="useSelectedLocation()"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                Use This Location
            </button>
        </div>
    </div>
</div>

{{-- Leaflet CDN --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<script>
    let map, marker;

    function openMapModal() {
        document.getElementById('mapModal').classList.remove('hidden');

        setTimeout(() => {
            if (!map) {
                initMap();
            } else {
                map.invalidateSize();
            }
        }, 200);
    }

    function closeMapModal() {
        document.getElementById('mapModal').classList.add('hidden');
    }

    function initMap() {
        let latInput = document.getElementById('latitude').value;
        let lngInput = document.getElementById('longitude').value;

        let lat = parseFloat(latInput);
        let lng = parseFloat(lngInput);

        if (!isNaN(lat) && !isNaN(lng)) {
            // Use saved lat/lng
            setupMap(lat, lng, 14);
        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                setupMap(position.coords.latitude, position.coords.longitude, 14);
            }, () => {
                setupMap(20.5937, 78.9629, 5); // Default: India
            });
        } else {
            setupMap(20.5937, 78.9629, 5); // Default: India
        }
    }

    function setupMap(lat, lng, zoomLevel) {
        map = L.map('map').setView([lat, lng], zoomLevel);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    }

    function useSelectedLocation() {
        const position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat.toFixed(7);
        document.getElementById('longitude').value = position.lng.toFixed(7);
        closeMapModal();
    }
</script>
