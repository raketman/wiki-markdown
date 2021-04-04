export default {
  axios: {
    baseURL: 'http://localhost:8000', // Used as fallback if no runtime config is provided
  },

  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  ssr: false,

  // Target: https://go.nuxtjs.dev/config-target
  target: 'static',

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    title: 'app',
    htmlAttrs: {
      lang: 'en'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
  ],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    '@nuxtjs/axios',
    'bootstrap-vue/nuxt'
  ],

  // Build Configuration: https://go.nuxtjs.dev/config-build
  buildDir: './../public/nuxt',
  build: {
    filenames: {
      app: ({ isDev, isModern }) => `[name].js`,
      chunk: ({ isDev, isModern }) => `[name].js`,
      css: ({ isDev }) => '[name].css',
      img: ({ isDev }) => '[path][name].[ext]' ,
      font: ({ isDev }) => '[path][name].[ext]',
      video: ({ isDev }) => '[path][name].[ext]'
    }
  }
}
