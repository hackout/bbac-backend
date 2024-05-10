import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import AutoImport from 'unplugin-auto-import/vite';
import { compression } from 'vite-plugin-compression2';

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
        compression({
            threshold: 2000,
            deleteOriginalAssets: false,
            skipIfLargerOrEqual: true,
        })
    ],
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: '@import "/resources/css/app.scss";'
            }
        },
    },
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                }
            }
        }
    }
});
