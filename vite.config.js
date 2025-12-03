import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/index.css',
                'resources/css/registrar.css',
                'resources/css/perfil.css',
                'resources/js/app.js',
                'resources/css/login.css',
                'resources/css/eventos.css',
                'resources/css/participation.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
