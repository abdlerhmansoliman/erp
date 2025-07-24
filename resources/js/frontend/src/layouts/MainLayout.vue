<script setup>
import { RouterLink, RouterView } from 'vue-router'
</script>

<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow p-4 hidden md:block">
      <h2 class="text-xl font-bold mb-6">ERP System</h2>
      <nav class="space-y-2">
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/dashboard">Dashboard</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/customers">Customers</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/invoices">Invoices</RouterLink>
        <RouterLink class="block px-3 py-2 rounded hover:bg-gray-200" to="/suppliers">Suppliers</RouterLink>
      </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header class="bg-white shadow p-4">
        <h1 class="text-lg font-semibold">ERP Dashboard</h1>
      </header>

      <!-- Dynamic page -->
      <main class="flex-1 p-6 overflow-y-auto">
        <RouterView />
      </main>
    </div>
  </div>
</template>


