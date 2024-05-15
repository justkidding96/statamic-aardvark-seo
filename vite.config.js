import { defineConfig } from 'vite';
import vue from "@vitejs/plugin-vue2";
import laravel from 'laravel-vite-plugin';
 
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css'
            ],
            publicDirectory: 'resources/dist',
        }),
        vue(),
    ],
});