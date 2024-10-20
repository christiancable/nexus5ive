import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/legacy.scss', // legacy bootstrap
                'resources/sass/extra.scss', // legacy extras
                'resources/css/app.css', // default
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
