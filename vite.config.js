import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    css: {
        preprocessorOptions: {
            scss: {
                // Bootstrap 5.x uses deprecated Sass features internally; silence until Bootstrap 6
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/sass/additional.scss',
                'resources/js/app.js',
                
                // homegrown themes
                'resources/sass/excelsior.scss',
                'resources/sass/halloween.scss',
                'resources/sass/hotdog.scss',
                'resources/sass/nexus2.scss',
                'resources/sass/xmas.scss',

            ],
            refresh: true,
        }),
    ],
});