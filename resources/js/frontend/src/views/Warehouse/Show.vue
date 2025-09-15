<script setup>
import { onMounted, ref, watch } from 'vue';
import api from '@/plugins/axios';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useI18n } from 'vue-i18n';
import EasyDataTable from 'vue3-easy-data-table';

const { t } = useI18n();
const route = useRoute();
const router = useRouter();
const toast = useToast();

// Data refs
const warehouse = ref(null);
const items = ref([]);
const loading = ref(false);

// Server-side options for EasyDataTable
const serverOptions = ref({
  page: 1,
  rowsPerPage: 10,
  sortBy: null,
  sortType: null,
});

// Search ref
const searchValue = ref('');

// Server items length (total count)
const serverItemsLength = ref(0);

// Headers for the table
const headers = [
  { text: t('product_name'), value: 'product_name', sortable: true },
  { text: t('quantity'), value: 'qty', sortable: true },
  { text: t('remaining'), value: 'remaining', sortable: true },
  { text: t('unit_cost'), value: 'unit_price', sortable: true },
  { text: t('total_price'), value: 'total_price', sortable: true },
];

// Fetch warehouse data from server
const fetchWarehouse = async () => {
  loading.value = true;
  try {
    const params = {
      page: serverOptions.value.page,
      perPage: serverOptions.value.rowsPerPage,
    };

    // Add search if exists
    if (searchValue.value) {
      params.search = searchValue.value;
    }

    // Add sorting if exists
    if (serverOptions.value.sortBy) {
      params.sortBy = serverOptions.value.sortBy;
      params.sortDirection = serverOptions.value.sortType || 'asc';
    }

    const { data } = await api.get(`/warehouses/${route.params.id}`, { params });

    // Update warehouse info
    warehouse.value = data.warehouse;
    
    // Update table data
    items.value = data.data;
    serverItemsLength.value = data.total;

  } catch (error) {
    toast.error('Error fetching warehouse data');
    if (error.response?.status === 404) {
      router.push({ name: 'warehouses' });
    }
  } finally {
    loading.value = false;
  }
};

// Watch for server options changes (pagination, sorting)
watch(serverOptions, () => {
  fetchWarehouse();
}, { deep: true });

// Watch for search changes with debounce
let searchTimeout;
watch(searchValue, (newVal, oldVal) => {
  if (newVal !== oldVal) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      // Reset to page 1 when searching
      serverOptions.value.page = 1;
      fetchWarehouse();
    }, 500); // 500ms debounce
  }
});

// Initial load
onMounted(() => {
  fetchWarehouse();
});

// Format currency for display
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(value);
};
</script>

<template>
  <div>
    <h1 class="text-xl font-bold mb-4">{{ t('warehouse') }}</h1>

    <!-- Warehouse Information Card -->
    <div class="bg-white shadow rounded-lg mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">{{ t('warehouse_details') }}</h2>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 mb-1">{{ t('name') }}</span>
            <span class="font-semibold">{{ warehouse?.name || '-' }}</span>
          </div>
          
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 mb-1">{{ t('address') }}</span>
            <span class="font-semibold">{{ warehouse?.address || '-' }}</span>
          </div>
          
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 mb-1">{{ t('phone') }}</span>
            <span class="font-semibold">{{ warehouse?.phone || '-' }}</span>
          </div>
          
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 mb-1">{{ t('email') }}</span>
            <span class="font-semibold">{{ warehouse?.email || '-' }}</span>
          </div>
          
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 mb-1">{{ t('product_count') }}</span>
            <span class="font-semibold text-blue-600">{{ warehouse?.product_count || 0 }}</span>
          </div>
          
          <div class="flex flex-col">
            <span class="text-sm text-gray-500 mb-1">{{ t('total_quantity') }}</span>
            <span class="font-semibold text-green-600">{{ warehouse?.total_quantity || 0 }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Section -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold">{{ t('products') }}</h2>
        
        <!-- Search Input -->
        <div class="flex items-center gap-2">
          <div class="relative">
            <input
              v-model="searchValue"
              type="text"
              :placeholder="t('search_products')"
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>
      
      <div class="p-6">
        <!-- EasyDataTable with server-side features -->
        <EasyDataTable
          :headers="headers"
          :items="items"
          :loading="loading"
          :server-options="serverOptions"
          :server-items-length="serverItemsLength"
          v-model:server-options="serverOptions"
          show-index
          alternating
          border-cell
          buttons-pagination
          :rows-items="[10, 25, 50, 100]"
          class="easy-data-table--custom"
        >
          <!-- Custom slots for better formatting -->
          <template #item-product_name="item">
            <span class="font-medium text-gray-900">{{ item.product_name }}</span>
          </template>
          
          <template #item-qty="item">
            <span class="text-blue-600 font-medium">{{ item.qty }}</span>
          </template>
          
          <template #item-remaining="item">
            <span class="text-green-600 font-medium">{{ item.remaining }}</span>
          </template>
          
          <template #item-unit_price="item">
            <span class="text-gray-900 font-mono">{{ formatCurrency(item.unit_price) }}</span>
          </template>
          
          <template #item-total_price="item">
            <span class="text-indigo-600 font-semibold font-mono">{{ formatCurrency(item.total_price) }}</span>
          </template>
          
          <!-- Loading state -->
          <template #loading>
            <div class="flex items-center justify-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
              <span class="ml-2 text-gray-600">{{ t('loading') }}...</span>
            </div>
          </template>
          
          <!-- Empty state -->
          <template #empty>
            <div class="text-center py-8 text-gray-500">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m-2 0v5a2 2 0 002 2h2M4 13V8a2 2 0 012-2h2"></path>
              </svg>
              <p class="mt-2">{{ t('no_products_found') }}</p>
            </div>
          </template>
        </EasyDataTable>
      </div>
    </div>
  </div>
</template>

<style scoped>
.easy-data-table--custom {
  --easy-table-border: 1px solid #e5e7eb;
  --easy-table-header-bg: #f9fafb;
  --easy-table-header-font-size: 14px;
  --easy-table-body-font-size: 14px;
  --easy-table-row-border: 1px solid #f3f4f6;
  --easy-table-header-font-weight: 600;
  --easy-table-header-text-color: #374151;
  --easy-table-body-text-color: #6b7280;
}

.easy-data-table--custom .header-text {
  font-weight: 600;
}

.easy-data-table--custom .easy-data-table__main {
  border-radius: 0.5rem;
}

/* Custom pagination styling */
.easy-data-table--custom .pagination__rows-per-page {
  margin-right: 1rem;
}

.easy-data-table--custom .pagination__button {
  margin: 0 2px;
  padding: 8px 12px;
  border-radius: 6px;
  transition: all 0.2s;
}

.easy-data-table--custom .pagination__button:hover {
  background-color: #f3f4f6;
}

.easy-data-table--custom .pagination__button.active {
  background-color: #3b82f6;
  color: white;
}
</style>