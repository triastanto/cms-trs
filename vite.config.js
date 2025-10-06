import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

// Environment-aware configuration
const isProduction = process.env.NODE_ENV === 'production';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Code splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    // Separate vendor chunks for better caching
                    'vendor': ['axios'],
                },
                // Clean chunk file names
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: ({name}) => {
                    if (/\.(gif|jpe?g|png|svg|webp)$/.test(name ?? '')) {
                        return 'images/[name]-[hash][extname]';
                    }
                    if (/\.css$/.test(name ?? '')) {
                        return 'css/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
        // Chunk size warnings
        chunkSizeWarningLimit: 1000,
        // Enable minification only in production
        minify: isProduction ? 'terser' : false,
        terserOptions: isProduction ? {
            compress: {
                drop_console: true, // Remove console.logs in production only
                drop_debugger: true,
            },
        } : {},
        // Enable source maps in development for debugging
        sourcemap: !isProduction,
        // Optimize CSS
        cssMinify: isProduction,
        // Report compressed size
        reportCompressedSize: isProduction,
    },
    server: {
        cors: true,
        hmr: {
            host: 'localhost',
        },
    },
});