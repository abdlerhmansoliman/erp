<!-- src/components/SidebarMenu.vue -->
<script setup>
import { ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const route = useRoute()


const menuItems = [
  { label: 'dashboard', path: '/dashboard' },

    {
        label: 'People',
        children: [
        { label: 'customers', path: '/customers' },
        { label: 'suppliers', path: '/suppliers' },
        ],
    },
    { label: 'products', path: '/products' },
    {
        label: 'purchases & Sales',
        children: [
        { label: 'purchases', path: '/purchases' },
        { label: 'Sales', path: '/sales' },
        ],
    },

    {
    label: 'returns',
    children: [
      { label: 'purchase_returns', path: '/returns' },
      { label: 'sales_returns', path: '/returns/sales' },
        ],
    },

  { label: 'Categories', path: '/categories' },
]

// لحفظ القوائم المفتوحة
const openGroups = ref({})
const toggleGroup = (label) => {
  openGroups.value[label] = !openGroups.value[label]
}
</script>

<template>
  <nav class="space-y-1 ">
    <div v-for="item in menuItems" :key="item.label">
      <!-- عنصر رئيسي بدون أبناء -->
      <RouterLink
        v-if="!item.children"
        :to="item.path"
        class="block px-3 py-2 rounded hover:bg-gray-200"
        :class="{ 'bg-gray-200': route.path.startsWith(item.path) }"
      >
        {{ t(item.label) }}
      </RouterLink>

      <!-- عنصر رئيسي له أبناء -->
      <div v-else>
        <button
          @click="toggleGroup(item.label)"
          class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-200"
        >
          <span>{{ t(item.label) }}</span>
          <span class="text-sm">{{ openGroups[item.label] ? '▲' : '▼' }}</span>
        </button>
        <div
          v-show="openGroups[item.label]"
          class="pl-4 space-y-1 mt-1"
        >
          <RouterLink
            v-for="child in item.children"
            :key="child.path"
            :to="child.path"
            class="block px-3 py-2 rounded hover:bg-gray-100 text-sm"
            :class="{ 'bg-gray-200': route.path.startsWith(child.path) }"
          >
            {{ t(child.label) }}
          </RouterLink>
        </div>
      </div>
    </div>
  </nav>
</template>
