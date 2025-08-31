<script setup>
import ShowInvoiceTable from '@/components/ShowInvoiceTable.vue'

const props = defineProps({
  type: { type: String, required: true }, // 'purchase' or 'sales'
  invoice: { type: Object, required: true },
  loading: { type: Boolean, default: false },
  onBack: { type: Function, required: true },
  onEdit: { type: Function, required: true },
  onDownload: { type: Function, required: true }
})

function getStatusClass(status) {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    approved: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-red-100 text-red-800',
    draft: 'bg-gray-100 text-gray-800'
  }
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(parseFloat(value || 0))
}
</script>

<template>
  <div class="p-6 max-w-7xl mx-auto">
    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      <p class="mt-4 text-gray-600">Loading {{ type }} invoice...</p>
    </div>

    <!-- Invoice -->
    <div v-else-if="invoice" class="space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex justify-between items-start mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              {{ type === 'purchase' ? 'Purchase Invoice' : 'Sales Invoice' }}
            </h1>
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

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Party -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">
              {{ type === 'purchase' ? 'SUPPLIER' : 'CUSTOMER' }}
            </h3>
            <p class="text-lg font-medium text-gray-900">
              {{ type === 'purchase' ? invoice.supplier_name : invoice.customer_name }}
            </p>
          </div>

          <!-- Warehouse -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">WAREHOUSE</h3>
            <p class="text-lg font-medium text-gray-900">{{ invoice.warehouse_name || 'N/A' }}</p>
          </div>

          <!-- Total -->
          <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">GRAND TOTAL</h3>
            <p class="text-2xl font-bold text-green-600">${{ formatCurrency(invoice.grand_total) }}</p>
          </div>
        </div>
      </div>

      <!-- Items -->
      <ShowInvoiceTable
        :items="invoice.items || []"
        :summary="{
          subtotal: invoice.subtotal || 0,
          total_tax: invoice.total_tax || invoice.tax_amount || 0,
          total_discount: invoice.total_discount || invoice.discount_amount || 0,
          shipping_amount: invoice.shipping_amount || 0,
          additional_charges: invoice.additional_charges || 0,
          grand_total: invoice.grand_total || 0
        }"
        :title="type === 'purchase' ? 'Purchase Items' : 'Sales Items'"
        :show-tax="true"
        :show-discount="true"
        currency="$"
        empty-message="No items found"
      />

      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <button @click="onBack" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md">Back</button>
        <button @click="onDownload" class="px-6 py-2 bg-blue-600 text-white rounded-md">Download PDF</button>
        <button @click="onEdit" class="px-6 py-2 bg-green-600 text-white rounded-md">Edit</button>
      </div>
    </div>

    <!-- Error -->
    <div v-else class="text-center py-12">
      <h3 class="text-lg font-semibold text-gray-900">{{ type }} Invoice not found</h3>
      <button @click="onBack" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md">Back</button>
    </div>
  </div>
</template>
