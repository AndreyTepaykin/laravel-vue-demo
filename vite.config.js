import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve('resources/js/argon'),
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
                // Argon ships Bootstrap 5.1 SCSS; modern Sass warns about legacy APIs there.
                silenceDeprecations: [
                    'import',
                    'global-builtin',
                    'color-functions',
                    'if-function',
                    'function-units',
                    'slash-div',
                    'abs-percent',
                ],
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
