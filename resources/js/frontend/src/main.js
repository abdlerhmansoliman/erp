// main.js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'

// Import stores
import { useAuthStore } from '@/stores/authStore'

// Import permission directives
import permissionDirectives from '@/directives/permissions'

// Import Toast
import Toast from "vue-toastification"
import "vue-toastification/dist/index.css"

// Import global styles
import './assets/main.css'
import 'vue3-easy-data-table/dist/style.css'

const app = createApp(App)
const pinia = createPinia()

// Use Pinia for state management
app.use(pinia)

// Use Vue Router
app.use(router)

// Register permission directives
app.use(permissionDirectives)

// Use Toast notifications
app.use(Toast)

// Initialize auth store
const authStore = useAuthStore()
authStore.initializeAuth()

// Global error handler
app.config.errorHandler = (err, vm, info) => {
  console.error('Vue Error:', err)
  console.error('Component:', vm)
  console.error('Info:', info)
}

// Mount the app
app.mount('#app')