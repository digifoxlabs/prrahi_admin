<!DOCTYPE html>
<html>
<head>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div x-data>
        <!-- Alert -->
        <div x-show="$store.alert.show" x-text="$store.alert.message" 
             style="position:fixed; top:20px; right:20px; background:red; color:white; padding:20px;">
        </div>
        
        <!-- Button -->
        <button @click="$store.alert.show = true; $store.alert.message = 'TEST ALERT'; setTimeout(() => $store.alert.show = false, 2000)"
                style="padding: 10px; background: blue; color: white;">
            SHOW ALERT
        </button>
    </div>
    
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('alert', {
            show: false,
            message: ''
        });
    });
    </script>
</body>
</html>