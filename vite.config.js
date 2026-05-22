import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'
import fs from 'fs'
import path from 'path'

/** Copies node_modules/tinymce → public/vendor/tinymce once on each Vite start/build */
function copyTinyMce() {
    return {
        name: 'copy-tinymce',
        buildStart() {
            const src  = path.resolve('node_modules/tinymce')
            const dest = path.resolve('public/vendor/tinymce')
            if (fs.existsSync(src) && !fs.existsSync(dest)) {
                fs.cpSync(src, dest, { recursive: true })
            }
        },
    }
}

export default defineConfig({
    plugins: [
        copyTinyMce(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/main.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        hmr: {
            host: 'localhost',
            protocol: 'ws',
        },
    },
})
