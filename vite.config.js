import { defineConfig } from 'vite';

export default defineConfig({
  build: {
    outDir: 'src/assets/dist',
    rollupOptions: {
      input: 'src/resources/css/app.css',
      output: {
        assetFileNames: 'netatmo-weather.[ext]'
      }
    }
  }
});
