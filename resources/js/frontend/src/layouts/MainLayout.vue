<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'  // عدل المسار حسب مشروعك
import { useI18n } from 'vue-i18n'
import LanguageSelector from '@/components/LanguageSelector.vue'

const { t } = useI18n()
const authStore = useAuthStore()
const router = useRouter()

async function logoutUser() {
  await authStore.logout()
  router.push('/login') // أو أي صفحة تحب ترجع لها بعد اللوج آوت
}
</script>

<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow p-4 hidden md:block">
      <h2 class="text-xl font-bold mb-6">ERP System</h2>
      <nav class="space-y-2">
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/dashboard">{{t('dashboard')}}</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/customers">{{t('customers')}}</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/suppliers">{{t('suppliers')}}</RouterLink>
      </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
<header class="bg-white shadow p-4 flex justify-between items-center">
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
      <main class="flex-1 p-6 overflow-y-auto">
        <RouterView />
      </main>
    </div>
  </div>
</template>
