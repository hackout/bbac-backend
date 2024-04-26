import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import AutoImport from 'unplugin-auto-import/vite';

export default defineConfig({
    port: 3000,
    resolve: {
        alias: {
            '@': '/resources/js',
            '@css': '/resources/css',
            '@view': '/resources/views'
        }
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
        AutoImport({
            include: [
                /\.[tj]sx?$/,
                /\.vue$/, /\.vue\?vue/,
                /\.md$/,
            ],
            imports: [
                'vue',
            ],
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: '@import "/resources/css/app.scss";' 
            }
        },
    }
});
