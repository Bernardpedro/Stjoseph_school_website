export default defineNuxtConfig({
  // Basic configuration
  components: true,
  css: ['@/assets/css/tailwind.css'],

  // PostCSS configuration
  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    }
  },
    // runtime configuration
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'https://api.stjosephtssnzuki.com',
      siteUrl: process.env.NUXT_PUBLIC_SITE_URL || 'https://stjosephtssnzuki.com'
    }
  },

  routeRules: {
    '/events/event': { redirect: '/events' },
    '/contacts/contact': { redirect: '/contacts' },
    '/academics/academic': { redirect: '/academics' },
    '/cardOverViewP': { redirect: '/card-overview' },
    '/dashboard/admin/projectUpload': { redirect: '/dashboard/admin/project-upload' },
    '/dashboard/admin/usersManagement': { redirect: '/dashboard/admin/users-management' },
  },

  // Vite configuration
  vite: {
    server: {
      proxy: {
        '/api': {
          target: 'http://localhost/beno/SCHOOLS/Stjoseph_stStanislas/Stjoseph_school_website/backend/public',
          changeOrigin: true,
        },
        '/uploads': {
          target: 'http://localhost/beno/SCHOOLS/Stjoseph_stStanislas/Stjoseph_school_website/backend/public',
          changeOrigin: true,
        },
      },
    },
    // Add asset handling for images
    assetsInclude: ['**/*.JPG', '**/*.jpg', '**/*.jpeg', '**/*.png', '**/*.gif', '**/*.webp'],
    
    // Fixed build configuration to resolve Vue manualChunks error
    build: {
      rollupOptions: {
        output: {
          // Remove manual chunking to avoid conflicts with externalized modules
          manualChunks: undefined,
        },
        // Explicitly exclude server-only modules from the client bundle
        external: ['@nuxt/kit', 'nitropack', 'node:fs', 'node:path', 'node:os', 'node:url', 'node:vm']
      }
    }
  },

  // Nitro configuration for static generation
  nitro: {
    preset: 'static'
  },

  // App configuration
  app: {
    head: {
      title: 'Saint Joseph TSS Nzuki',
      meta: [
        { name: 'description', content: 'Saint Joseph TSS Nzuki website' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' }
      ],
      script: [
        {
          innerHTML: `(function(){try{var d=localStorage.getItem('sj_theme')==='dark';document.documentElement.classList.toggle('dark',d);document.documentElement.style.colorScheme=d?'dark':'light';}catch(e){}})();`,
        },
      ],
     link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
    },
  },

  ssr: true,

  modules: [
    '@pinia/nuxt',
    '@nuxtjs/i18n',
  ],

  i18n: {
    locales: [
      { code: 'en', language: 'en-US', name: 'English', file: 'en.json' },
      { code: 'rw', language: 'rw-RW', name: 'Kinyarwanda', file: 'rw.json' },
      { code: 'sw', language: 'sw-TZ', name: 'Kiswahili', file: 'sw.json' },
      { code: 'fr', language: 'fr-FR', name: 'Français', file: 'fr.json' },
      { code: 'de', language: 'de-DE', name: 'Deutsch', file: 'de.json' },
    ],
    langDir: 'lang',
    defaultLocale: process.env.NUXT_PUBLIC_DEFAULT_LOCALE || 'en',
    strategy: 'no_prefix',
    detectBrowserLanguage: {
      useCookie: true,
      cookieKey: 'sj_locale',
      fallbackLocale: 'en',
      alwaysRedirect: false,
      redirectOn: 'root',
    },
  },

  // Disable experimental features
  experimental: {
    payloadExtraction: false,
  },

  // Compatibility date
  compatibilityDate: '2025-02-09',

  // Disable devtools in production
  devtools: {
    enabled: false
  },
  
  // TypeScript configuration
  typescript: {
    strict: false,
    typeCheck: false
  },

  // Minimal build configuration
  build: {
    transpile: []
  },
})
