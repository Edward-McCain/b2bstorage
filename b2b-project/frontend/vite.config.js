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
    },
  },
  server: {
    headers: {
      'Permissions-Policy': 'microphone=*, camera=*, geolocation=*',
      'Cross-Origin-Embedder-Policy': 'unsafe-none',
      'Cross-Origin-Opener-Policy': 'unsafe-none',
      'Content-Security-Policy': "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'; connect-src 'self' http://localhost:* http://127.0.0.1:* https: wss: https://webrtc.b2bsklad.uz wss://webrtc.b2bsklad.uz; frame-src 'self' https://webrtc.b2bsklad.uz/; media-src 'self' https://webrtc.b2bsklad.uz/; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://webrtc.b2bsklad.uz/;"
    },
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
        configure: (proxy, options) => {
          proxy.on('error', (err, req, res) => {
            console.log('proxy error', err);
          });
          proxy.on('proxyReq', (proxyReq, req, res) => {
            console.log('Sending Request to the Target:', req.method, req.url);
          });
          proxy.on('proxyRes', (proxyRes, req, res) => {
            console.log('Received Response from the Target:', proxyRes.statusCode, req.url);
          });
        },
        bypass: (req, res) => {
          // Не проксировать /docs_api, пусть Vue Router обработает
          if (req.url === '/docs_api') {
            return req.url;
          }
        }
      }
    }
  }
})
