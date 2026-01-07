<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
      {{ $title }} | Prrahi Admin Dashboard
    </title>
  <link rel="icon" href="favicon.ico">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  
    <!-- Cropper.js CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.5.13/dist/cropper.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>

    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet"/>


    <!-- ✅ jQuery: MUST be loaded first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- ✅ Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>








</head>
  <body
    x-data="loadPageData()"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark bg-gray-900': darkMode === true}"
  >



{{-- Preloader --}}
@include ('admin.partials.preloader')

<!-- ===== Page Wrapper Start ===== -->
<div class="flex h-screen overflow-hidden">

{{-- SideBar --}}
@include('admin.partials.sidebar')

<!-- ===== Content Area Start ===== -->
<div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">

{{-- Small device Overlay --}}
@include('admin.partials.overlay')


{{-- Header --}}
@include('admin.partials.header')

{{-- Main Content --}}

<main> 


  @env('local1')
<div id="breakpoint-debug"
     class="fixed bottom-4 right-4 z-[99999] rounded-lg bg-blue-600 text-white text-base font-bold px-4 py-2 shadow-lg pointer-events-none">
    <!-- Extra Small (default <640px) -->
    <span class="block sm:hidden">📱 XS &lt; 640px</span>

    <!-- Small (≥640px <768px) -->
    <span class="hidden sm:block md:hidden">📱 SM ≥ 640px</span>

    <!-- Medium (≥768px <1024px) -->
    <span class="hidden md:block lg:hidden">💻 MD ≥ 768px</span>

    <!-- Large (≥1024px <1280px) -->
    <span class="hidden lg:block xl:hidden">🖥️ LG ≥ 1024px</span>

    <!-- Extra Large (≥1280px <1536px) -->
    <span class="hidden xl:block 2xl:hidden">🖥️ XL ≥ 1280px</span>

    <!-- 2XL (≥1536px) -->
    <span class="hidden 2xl:block">🖥️ 2XL ≥ 1536px</span>
</div>
@endenv

  
  @yield('page-content')
</main>
     </div>
  <!-- ===== Content Area End ===== -->
</div>
<!-- ===== Page Wrapper End ===== -->




@yield('scripts')

  <script>
    function loadPageData() {
        return Object.assign({           
            loaded: true,
            darkMode: false,
            stickyMenu: false,
            sidebarToggle: false,
            scrollTop: false
        }, window.pageXData || {});
    }

    function initPage() {
        console.log("Alpine page data:", $data);
    }
</script>

@stack('scripts')

  
  <script>
      window.openLogoutModal = function() {
          const modal = document.getElementById('logout-modal');
          modal.style.display = 'flex';

          // Disable scroll
          document.body.style.overflow = 'hidden';
          document.documentElement.style.overflow = 'hidden';
      }

      window.closeLogoutModal = function() {
          const modal = document.getElementById('logout-modal');
          modal.style.display = 'none';

          // Restore scroll
          document.body.style.overflow = '';
          document.documentElement.style.overflow = '';
      }

      window.confirmLogout = function() {
          document.getElementById('logout-form').submit();
      }
  </script>




</body>
</html>