<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .alert-transition {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Alert System Test</h1>
        
        <!-- Alert Container -->
        <div x-data="alertComponent">
            <!-- Alert Display -->
            <div x-show="alert.show"
                 x-transition:enter="alert-transition"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="alert-transition"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 :class="{
                     'bg-green-100 border-green-400 text-green-700': alert.type === 'success',
                     'bg-yellow-100 border-yellow-400 text-yellow-700': alert.type === 'warning',
                     'bg-red-100 border-red-400 text-red-700': alert.type === 'error'
                 }"
                 class="fixed top-4 right-4 border-l-4 p-4 rounded shadow-lg z-50 max-w-sm">
                <div class="flex items-center">
                    <span x-text="alert.message" class="mr-3"></span>
                    <button @click="alert.show = false" class="ml-auto text-lg">&times;</button>
                </div>
            </div>
            
            <!-- Test Buttons -->
            <div class="space-y-3">
                <button @click="$store.alert.flash('Success message!', 'success')"
                        class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Show Success Alert
                </button>
                
                <button @click="$store.alert.flash('Warning message!', 'warning')"
                        class="w-full px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                    Show Warning Alert
                </button>
                
                <button @click="$store.alert.flash('Error message!', 'error')"
                        class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Show Error Alert
                </button>
            </div>
        </div>
    </div>
</body>
</html>