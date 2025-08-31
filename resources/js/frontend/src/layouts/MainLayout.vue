<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'  
import { useI18n } from 'vue-i18n'
import LanguageSelector from '@/components/LanguageSelector.vue'
import { ref } from 'vue'

const { t } = useI18n()
const authStore = useAuthStore()
const router = useRouter()

const showSidebar = ref(false)

async function logoutUser() {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar for Desktop -->
    <aside class="w-64 bg-white shadow p-4 hidden md:block">
      <h2 class="text-xl font-bold mb-6">ERP System</h2>
      <nav class="space-y-2">
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/dashboard">{{t('dashboard')}}</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/customers">{{t('customers')}}</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/suppliers">{{t('suppliers')}}</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/purchases">{{t('purchases')}}</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/products">{{t('products')}}</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/categories">{{t('Categories')}}</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/sales">{{t('Sales')}}</RouterLink>
      </nav>
    </aside>

    <!-- Sidebar for Mobile (Drawer) -->
    <transition name="fade">
      <aside
        v-if="showSidebar"
        class="fixed inset-0 bg-black bg-opacity-50 z-40 flex"
        @click.self="showSidebar = false"
      >
        <div class="w-64 bg-white shadow p-4 z-50">
          <button class="mb-4 text-red-500 font-bold" @click="showSidebar = false">
            ✕ Close
          </button>
          <h2 class="text-xl font-bold mb-6">ERP System</h2>
          <nav class="space-y-2">
            <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/dashboard" @click="showSidebar = false">{{t('dashboard')}}</RouterLink>
            <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/customers" @click="showSidebar = false">{{t('customers')}}</RouterLink>
            <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/suppliers" @click="showSidebar = false">{{t('suppliers')}}</RouterLink>
            <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/purchases" @click="showSidebar = false">{{t('purchases')}}</RouterLink>
            <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/products" @click="showSidebar = false">{{t('products')}}</RouterLink>
          </nav>
        </div>
      </aside>
    </transition>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-white shadow p-4 flex justify-between items-center">
        <!-- Left: Mobile menu button -->
        <button class="md:hidden text-gray-700" @click="showSidebar = true">
          ☰
        </button>

        <h1 class="text-lg font-semibold">ERP Dashboard</h1>

        <!-- Right side items -->
        <div class="flex items-center gap-4">
          <button
            @click="logoutUser"
            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
          >
            {{ t('logout') }}
          </button>
          <LanguageSelector />
        </div>
      </header>

      <!-- Dynamic page -->
      <main class="bg-gray-50 flex-1 overflow-y-auto p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
