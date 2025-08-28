<template>
  <div class="p-6 max-w-7xl mx-auto">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      <p class="mt-4 text-gray-600">Loading purchase invoice...</p>
    </div>

    <!-- Invoice Content -->
    <div v-else-if="invoice" class="space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex justify-between items-start mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Purchase Invoice</h1>
            <p class="text-lg text-gray-600 mt-1">#{{ invoice.invoice_number }}</p>
          </div>
          <div class="text-right">
            <span class="inline-block px-3 py-1 text-sm rounded-full font-medium"
                  :class="getStatusClass(invoice.status)">
              {{ invoice.status?.toUpperCase() }}
            </span>
            <p class="text-sm text-gray-500 mt-2">{{ invoice.created_at }}</p>
          </div>
        </div>

        <!-- Invoice Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Supplier Info -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">SUPPLIER</h3>
            <p class="text-lg font-medium text-gray-900">{{ invoice.supplier_name || 'N/A' }}</p>
            <p class="text-sm text-gray-600 mt-1" v-if="invoice.supplier_email">
              {{ invoice.supplier_email }}
            </p>
            <p class="text-sm text-gray-600" v-if="invoice.supplier_phone">
              {{ invoice.supplier_phone }}
            </p>
          </div>

          <!-- Warehouse Info -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">WAREHOUSE</h3>
            <p class="text-lg font-medium text-gray-900">{{ invoice.warehouse_name || 'N/A' }}</p>
          </div>

          <!-- Financial Summary -->
          <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">GRAND TOTAL</h3>
            <p class="text-2xl font-bold text-green-600">${{ formatCurrency(invoice.grand_total) }}</p>
          </div>

          <!-- Additional Info -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">DETAILS</h3>
            <div class="space-y-1">
              <p class="text-sm text-gray-600">
                <span class="font-medium">Items:</span> {{ invoice.items?.length || 0 }}
              </p>
              <p class="text-sm text-gray-600" v-if="invoice.payment_method">
                <span class="font-medium">Payment:</span> {{ invoice.payment_method }}
              </p>
              <p class="text-sm text-gray-600" v-if="invoice.payment_status">
                <span class="font-medium">Status:</span> {{ invoice.payment_status }}
              </p>
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div v-if="invoice.notes" class="mt-6 p-4 bg-yellow-50 rounded-lg">
          <h3 class="text-sm font-semibold text-gray-700 mb-2">NOTES</h3>
          <p class="text-sm text-gray-600">{{ invoice.notes }}</p>
        </div>
      </div>

      <!-- Items Table -->
      <ShowInvoiceTable
        :items="invoice.items || []"
        :summary="invoiceSummary"
        title="Purchase Items"
        :show-tax="true"
        :show-discount="true"
        currency="$"
        empty-message="No items found in this purchase invoice"
      />

      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <button 
          @click="goBack"
          class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
          Back to List
        </button>
        <button 
          @click="printInvoice"
          class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
          Print Invoice
        </button>
        <button 
          @click="editInvoice"
          class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
          Edit Invoice
        </button>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-12">
      <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
        <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714"/>
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-gray-900">Invoice not found</h3>
      <p class="text-gray-600 mt-2">The requested purchase invoice could not be loaded.</p>
      <button 
        @click="goBack"
        class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
        Back to List
      </button>
    </div>
  </div>
</template>

<script setup>
import ShowInvoiceTable from '@/components/ShowInvoiceTable.vue'
import { ref, onMounted, computed } from 'vue'
import api from '@/plugins/axios'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const invoice = ref(null)
const loading = ref(true)

// Computed summary object for the invoice table
const invoiceSummary = computed(() => {
  if (!invoice.value) return null
  
  return {
    subtotal: invoice.value.subtotal || 0,
    total_tax: invoice.value.total_tax || invoice.value.tax_amount || 0,
    total_discount: invoice.value.total_discount || invoice.value.discount_amount || 0,
    shipping_amount: invoice.value.shipping_amount || 0,
    additional_charges: invoice.value.additional_charges || 0,
    grand_total: invoice.value.grand_total || 0
  }
})

// Helper functions
function getStatusClass(status) {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-green-100 text-green-800',
    'approved': 'bg-blue-100 text-blue-800',
    'cancelled': 'bg-red-100 text-red-800',
    'draft': 'bg-gray-100 text-gray-800'
  }
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(parseFloat(value || 0))
}

// Actions
function goBack() {
  router.push({ name: 'purchases' }) // Adjust route name as needed
}

function editInvoice() {
  router.push({ name: 'purchase-edit', params: { id: invoice.value.id } }) // Adjust route name as needed
}

function printInvoice() {
  window.print()
}

// Lifecycle
onMounted(async () => {
  try {
    const { data } = await api.get(`/purchases/${route.params.id}`)
    invoice.value = data.data
    
    // Debug log
    console.log('Invoice loaded:', invoice.value)
    console.log('Items:', invoice.value.items)
  } catch (error) {
    console.error('Error fetching purchase invoice:', error)
    toast.error('Failed to load purchase invoice')
  } finally {
    loading.value = false
  }
})
</script>