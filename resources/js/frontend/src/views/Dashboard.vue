<script setup>
import { ref, onMounted } from 'vue'
import api from '@/plugins/axios'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const stats = ref({})
const last7Sales = ref([])
const last7Purchases = ref([])
const recentSales = ref([])
const recentPurchases = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get('/dashboard/overview')
    stats.value = res.data.stats
    last7Sales.value = res.data.last_7_days_sales
    last7Purchases.value = res.data.last_7_days_purchases
    recentSales.value = res.data.recent_sales
    recentPurchases.value = res.data.recent_purchases
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold">Dashboard</h1>

    <div v-if="loading" class="text-gray-500">Loading...</div>

    <template v-else>

      <!-- Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="p-4 rounded-xl shadow bg-blue-400 hover:bg-blue-500 transition">
          <h3 class="text-sm">{{t('sales')}}</h3>
          <p class="text-2xl font-bold">{{ stats.total_sales }}</p>
        </div>
        <div class="p-4 rounded-xl shadow bg-green-400 hover:bg-green-500 transition">
          <h3 class="text-sm">{{ t('purchases') }}</h3>
          <p class="text-2xl font-bold">{{ stats.total_purchases }}</p>
        </div>
        <div class="p-4 rounded-xl shadow bg-yellow-300 hover:bg-yellow-400 transition">
          <h3 class="text-sm">{{ t('products') }}</h3>
          <p class="text-2xl font-bold">{{ stats.total_products }}</p>
        </div>
        <div class="p-4 rounded-xl shadow bg-red-400 hover:bg-red-500 transition">
          <h3 class="text-sm">{{ t('customers') }}</h3>
          <p class="text-2xl font-bold">{{ stats.total_customers }}</p>
        </div>
        <div class="p-4 rounded-xl shadow bg-purple-400 hover:bg-purple-500 transition">
          <h3 class="text-sm">{{ t('suppliers') }}</h3>
          <p class="text-2xl font-bold">{{ stats.total_suppliers }}</p>
        </div>
      </div>

      <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="p-4 rounded-xl shadow bg-white">
            <h3 class="mb-2 font-semibold">{{ t('sales') }} ({{ t('last_7_days') }})</h3>
            <ul class="text-sm bg-gray-50 p-2 rounded divide-y">
            <li
                v-for="item in last7Sales"
                :key="item.date"
                class="py-1 flex justify-between"
            >
                <span>{{ item.date }}</span>
                <span class="font-medium">{{ item.total }}</span>
            </li>
            </ul>
        </div>

        <div class="p-4 rounded-xl shadow bg-white">
            <h3 class="mb-2 font-semibold">{{ t('purchases') }} ({{ t('last_7_days') }})</h3>
            <ul class="text-sm bg-gray-50 p-2 rounded divide-y">
            <li
                v-for="item in last7Purchases"
                :key="item.date"
                class="py-1 flex justify-between"
            >
                <span>{{ item.date }}</span>
                <span class="font-medium">{{ item.total }}</span>
            </li>
            </ul>
        </div>
        </div>


      <!-- Recent Invoices -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="p-4 rounded-xl shadow bg-white">
          <h3 class="mb-2 font-semibold">{{ t('recent_sales') }}</h3>
          <ul class="text-sm divide-y">
            <li v-for="s in recentSales" :key="s.id" class="py-1 flex justify-between">
              <span>{{ s.invoice_number }}</span>
              <span class="font-medium">{{ s.grand_total }}</span>
            </li>
          </ul>
        </div>
        <div class="p-4 rounded-xl shadow bg-white">
          <h3 class="mb-2 font-semibold">{{ t('recent_purchases') }}</h3>
          <ul class="text-sm divide-y">
            <li v-for="p in recentPurchases" :key="p.id" class="py-1 flex justify-between">
              <span>{{ p.invoice_number }}</span>
              <span class="font-medium">{{ p.grand_total }}</span>
            </li>
          </ul>
        </div>
      </div>
    </template>
  </div>
</template>
