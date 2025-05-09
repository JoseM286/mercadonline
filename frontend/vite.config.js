import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    host: '0.0.0.0',  // Asegura que escuche en todas las interfaces
    port: 3000,
    watch: {
      usePolling: true,  // Necesario para Docker y WSL2
      interval: 1000,    // Intervalo de polling en ms
      ignored: ['!**/node_modules/**'],  // No ignorar node_modules
    },
    hmr: {
      overlay: true,     // Muestra errores como overlay
      clientPort: 3000,  // Puerto para conexiones HMR desde el cliente
      host: 'localhost', // Host para conexiones HMR
    }
  }
})







