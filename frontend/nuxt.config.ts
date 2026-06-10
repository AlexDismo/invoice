import tailwindcss from '@tailwindcss/vite'

export default defineNuxtConfig({
  compatibilityDate: '2026-06-09',
  devtools: { enabled: false },
  css: ['~/assets/css/main.css'],
  modules: ['@vee-validate/nuxt', '@element-plus/nuxt'],
  elementPlus: {
    importStyle: 'css',
  },
  runtimeConfig: {
    apiBase: `${process.env.NUXT_API_PROXY_TARGET || 'http://backend:8000'}/api`,
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8000/api',
    },
  },
  vite: {
    plugins: [tailwindcss()],
    optimizeDeps: {
      include: [
        '@element-plus/icons-vue',
        'dayjs',
        'dayjs/plugin/customParseFormat.js',
        'dayjs/plugin/localeData.js',
        'dayjs/plugin/relativeTime.js',
        'lodash-unified',
        'zod',
        '@vee-validate/zod',
      ],
    },
  },
})
