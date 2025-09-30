<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import api from '@/plugins/axios'
import { useToast } from 'vue-toastification'
import { useRouter } from 'vue-router'

const toast = useToast()
const router = useRouter()

// Form state
const fromWarehouseId = ref(null)
const toWarehouseId = ref(null)
const transferDate = ref(new Date().toISOString().slice(0, 10))
const status = ref('draft')

// Options/data
const warehouses = ref([])
const allProducts = ref([])

// UI state
const loadingInit = ref(false)
const loadingProducts = ref(false)
const saving = ref(false)
const searchQuery = ref('')

// Filtered products with quantity field
const filteredProducts = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return []
  
  return allProducts.value
    .filter(p => (p.name || '').toLowerCase().includes(q) || String(p.id).includes(q))
})

function resetSelection() {
  allProducts.value = []
  searchQuery.value = ''
}

async function fetchInit() {
  loadingInit.value = true
  try {
    const { data } = await api.get('/transfers/create')
    warehouses.value = data.warehouses || []
    transferDate.value = data?.defaults?.transfer_date || transferDate.value
    status.value = 'draft'
  } catch (e) {
    toast.error('Failed to load transfer form data')
  } finally {
    loadingInit.value = false
  }
}

async function fetchProductsForWarehouse() {
  if (!fromWarehouseId.value) {
    resetSelection()
    return
  }
  loadingProducts.value = true
  try {
    const { data } = await api.get('/transfers/create', { params: { from_warehouse_id: fromWarehouseId.value } })
    allProducts.value = (data.products || []).map(p => ({ ...p, quantity: 0 }))
  } catch (e) {
    allProducts.value = []
    toast.error('Failed to load products for warehouse')
  } finally {
    loadingProducts.value = false
  }
}

function updateQuantity(index, value) {
  const qty = Number(value || 0)
  if (Number.isNaN(qty) || qty < 0) {
    allProducts.value[index].quantity = 0
  } else if (qty > allProducts.value[index].available_qty) {
    allProducts.value[index].quantity = allProducts.value[index].available_qty
  } else {
    allProducts.value[index].quantity = qty
  }
}

async function submit() {
  if (!fromWarehouseId.value || !toWarehouseId.value) return toast.error('Select both warehouses')
  if (fromWarehouseId.value === toWarehouseId.value) return toast.error('Warehouses must be different')
  
  const items = allProducts.value
    .filter(p => p.quantity > 0)
    .map(p => ({ product_id: p.id, quantity: p.quantity }))
  
  if (items.length === 0) return toast.error('Set quantity for at least one product')

  const payload = {
    from_warehouse_id: fromWarehouseId.value,
    to_warehouse_id: toWarehouseId.value,
    transfer_date: transferDate.value,
    status: status.value,
    items
  }

  saving.value = true
  try {
    const res = await api.post('/transfers', payload)
    
    // Check if response indicates success
    if (res.data && (res.data.success === true || res.data.data || res.status === 200 || res.status === 201)) {
      toast.success('Transfer created successfully')
      router.push({ name: 'TransferIndex' }).catch(() => {})
    } else {
      // Response exists but doesn't indicate success
      const errorMsg = res.data?.message || 'Failed to create transfer'
      toast.error(errorMsg)
    }
  } catch (e) {
    // Only show error if it's a real failure
    if (e?.response?.status >= 400) {
      toast.error(e?.response?.data?.message || 'Failed to create transfer')
    }
  } finally {
    saving.value = false
  }
}

watch(fromWarehouseId, () => {
  resetSelection()
  fetchProductsForWarehouse()
})

onMounted(() => {
  fetchInit()
})
</script>

<template>
  <div class="p-4 max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Create Transfer</h2>

    <div v-if="loadingInit" class="p-4 bg-gray-50 rounded border">Loading...</div>

    <div v-else>
      <!-- Warehouse Selection -->
      <div class="mb-4 border p-4 rounded bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block mb-1 font-semibold">From Warehouse</label>
            <select v-model="fromWarehouseId" class="w-full border rounded px-3 py-2">
              <option :value="null" disabled>Select warehouse</option>
              <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 font-semibold">To Warehouse</label>
            <select v-model="toWarehouseId" class="w-full border rounded px-3 py-2">
              <option :value="null" disabled>Select warehouse</option>
              <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 font-semibold">Transfer Date</label>
            <input type="date" v-model="transferDate" class="w-full border rounded px-3 py-2" />
          </div>
          <div>
            <label class="block mb-1 font-semibold">Status</label>
            <select v-model="status" class="w-full border rounded px-3 py-2">
              <option value="draft">Draft</option>
              <option value="ordered">Ordered</option>
              <option value="received">Received</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Product Search -->
      <div class="mb-4">
        <input
          v-model="searchQuery"
          :disabled="!fromWarehouseId || loadingProducts"
          type="text"
          placeholder="Search product in selected warehouse..."
          class="w-full border rounded px-3 py-2"
        />
        <span v-if="loadingProducts" class="text-sm text-gray-500 mt-1 block">Loading products...</span>
      </div>

      <!-- Products Table -->
      <table class="w-full border mb-4 text-left">
        <thead class="bg-gray-100">
          <tr>
            <th class="border px-2 py-1">Product</th>
            <th class="border px-2 py-1">Available Quantity</th>
            <th class="border px-2 py-1">Unit Price</th>
            <th class="border px-2 py-1">Transfer Quantity</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!searchQuery || filteredProducts.length === 0">
            <td colspan="4" class="border px-2 py-4 text-center text-gray-500">
              {{ !fromWarehouseId ? 'Please select a warehouse first' : !searchQuery ? 'Type to search products' : 'No products found' }}
            </td>
          </tr>
          <tr v-for="(product, index) in filteredProducts" :key="product.id">
            <td class="border px-2 py-1">{{ product.name }}</td>
            <td class="border px-2 py-1">{{ product.available_qty }}</td>
            <td class="border px-2 py-1">{{ Number(product.net_unit_price || 0).toFixed(2) }}</td>
            <td class="border px-2 py-1">
              <input
                type="number"
                min="0"
                :max="product.available_qty"
                v-model.number="product.quantity"
                @input="updateQuantity(index, $event.target.value)"
                class="border p-1 w-20"
              />
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Summary -->
      <div class="mb-4 p-4 bg-gray-50 rounded">
        <p class="text-lg font-bold">
          <strong>Total Items to Transfer:</strong> 
          {{ allProducts.filter(p => p.quantity > 0).length }}
        </p>
        <p class="text-lg font-bold">
          <strong>Total Quantity:</strong> 
          {{ allProducts.reduce((sum, p) => sum + (p.quantity || 0), 0) }}
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2">
        <button @click="router.back()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
          Cancel
        </button>
        <button 
          @click="submit" 
          :disabled="saving"
          class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:bg-gray-400"
        >
          {{ saving ? 'Saving...' : 'Create Transfer' }}
        </button>
      </div>
    </div>
  </div>
</template>