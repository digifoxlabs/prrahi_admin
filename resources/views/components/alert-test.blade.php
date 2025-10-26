<div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Alert System Test</h2>
    
    <div class="space-y-4">
        <!-- Success Alert Test -->
        <button @click="Alpine.store('alert').flash('Success message!', 'success')"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
            Test Success Alert
        </button>
        
        <!-- Warning Alert Test -->
        <button @click="Alpine.store('alert').flash('Warning message!', 'warning')"
                class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition">
            Test Warning Alert
        </button>
        
        <!-- Error Alert Test -->
        <button @click="Alpine.store('alert').flash('Error message!', 'error')"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
            Test Error Alert
        </button>
    </div>
    
    <!-- Debug Information -->
    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded">
        <h3 class="font-medium mb-2">Debug Information</h3>
        <p class="text-sm">Alpine Store Status: <span x-text="typeof Alpine !== 'undefined' ? 'Loaded' : 'Not loaded'"></span></p>
        <p class="text-sm">Alert Store: <span x-text="typeof Alpine !== 'undefined' ? (Alpine.store('alert') ? 'Exists' : 'Missing') : 'N/A'"></span></p>
    </div>
</div>