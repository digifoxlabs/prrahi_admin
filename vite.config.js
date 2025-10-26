import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',      // Tailwind CSS (with @tailwind directives)
                'resources/js/app.js',        // JS or Alpine
            ],
            refresh: true,
        }),
        
    ],
});
