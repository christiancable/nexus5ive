import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/legacy.scss', // legacy bootstrap
                'resources/sass/extra.scss', // legacy extras
                'resources/css/app.css', // default laravel, unused
                'resources/js/app.js',

                // homegrown themes
                'resources/sass/app.scss',
                'resources/sass/excelsior.scss',
                'resources/sass/extra.scss',
                'resources/sass/halloween.scss',
                'resources/sass/hotdog.scss',
                'resources/sass/xmas.scss',
                'resources/sass/legacy.scss',
            ],
            refresh: true,
        }),
    ],
});
