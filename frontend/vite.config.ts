import path from 'path';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// Build output goes to ../site (the IIS web root). Only the `assets/` folder and
// index.html are regenerated; api/, data/, uploads/ and web.config are left alone.
export default defineConfig({
  server: { port: 3000, host: '0.0.0.0', allowedHosts: true as const, proxy: { '/api': 'http://127.0.0.1:8090', '/uploads': 'http://127.0.0.1:8090' } },
  plugins: [react()],
  resolve: { alias: { '@': path.resolve(__dirname, '.') } },
  build: {
    outDir: '../site',
    emptyOutDir: false,
    rollupOptions: {
      output: {
        manualChunks: { 'react-vendor': ['react', 'react-dom', 'react-router-dom'] },
      },
    },
    minify: 'esbuild',
    chunkSizeWarningLimit: 1200,
    target: 'es2017',
    cssCodeSplit: true,
    assetsInlineLimit: 8192,
  },
  preview: { port: 4173, host: '0.0.0.0' },
});
