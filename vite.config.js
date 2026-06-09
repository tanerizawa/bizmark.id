import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/public.css',
                'resources/css/admin.css',
                'resources/css/landing.css',
                'resources/css/landing-theme.css',
                'resources/css/client.css',
                'resources/css/new.css',
                'resources/js/app.js',
                'resources/js/client.js',
            ],
            refresh: true,
        }),
    ],
});
