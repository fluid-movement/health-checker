import {defineConfig} from 'vite';
import path from 'path';
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    tailwindcss(),
  ],
  root: path.resolve(__dirname, 'src/View'), // Set the root to the source directory
  build: {
    outDir: path.resolve(__dirname, 'public', 'dist'), // Output directory for built assets
    emptyOutDir: true, // Clear the output directory before each build
    manifest: true, // Generate manifest.json for asset mapping
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'src/View', 'js', 'app.js'), // Main entry point
      },
    },
  },
  server: {
    host: 'health-checker.test', // Set the development server to use your custom domain
    port: 5173, // Ensure this port matches your application's port
    cors: true,
    hmr: {
      host: 'health-checker.test',
    },
  },
});
