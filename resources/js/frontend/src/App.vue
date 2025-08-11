<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()

onMounted(async () => {
  // Initialize auth when app starts
  await authStore.initializeAuth()
})
</script>

<template>
  <div id="app">
    <!-- Your app content -->
    <router-view />
  </div>
</template>