// resources/js/frontend/src/main.js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import { i18n } from './i18n'

// Stores
import { useAuthStore } from '@/stores/authStore'

// Directives
import permissionDirectives from '@/directives/permissions'

// Toast
import Toast from "vue-toastification"
import "vue-toastification/dist/index.css"

// Styles
import './assets/main.css'
import 'vue3-easy-data-table/dist/style.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(permissionDirectives)
app.use(Toast)
app.use(i18n) 

const authStore = useAuthStore(pinia)
authStore.initializeAuth()

app.config.errorHandler = (err, vm, info) => {
  console.error('Vue Error:', err)
  console.error('Component:', vm)
  console.error('Info:', info)
}

app.mount('#app')
