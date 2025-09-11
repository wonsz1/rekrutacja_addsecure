import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue2'
export default defineConfig({
  plugins: [vue()],
  build: {
    outDir: "./public",
    rollupOptions: {
      input: './resources/main.js',
      output: {
        manualChunks: false,
        inlineDynamicImports: true,
        entryFileNames: '[name].js',
        assetFileNames: '[name].[ext]',
      },
    }
  },
})
