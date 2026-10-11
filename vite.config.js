import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        host: process.env.VITE_DEV_SERVER_HOST,
        hmr: {
            host: process.env.VITE_DEV_SERVER_HOST
        },
    },
    plugins: [
        laravel([
            'resources/js/app.js',
        ]),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Bootstrap 4's SCSS predates Dart Sass's module system and
                // floods the build with deprecation warnings we can't act on.
                quietDeps: true,
                silenceDeprecations: ['import'],
            },
        },
    },
    resolve: {
        alias: [
            {
                // this is required for the SCSS modules
                find: /^~(.*)$/,
                replacement: '$1',
            },
        ],
    },
});