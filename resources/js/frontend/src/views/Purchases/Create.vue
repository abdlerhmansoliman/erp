<script setup>
import { onMounted } from 'vue';
import InvoiceHeader from '@/components/Invoice/InvoiceHeader.vue';
import ProductSearch from '@/components/Invoice/ProductSearch.vue';
import InvoiceItemsTable from '@/components/Invoice/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/Invoice/InvoiceSummary/InvoiceSummary.vue';
import { useInvoice } from '@/composables/useInvoice';
import InvoicePaymentSection from '@/components/Invoice/InvoicePaymentSection.vue';

// Props
const props = defineProps({
  type: {
    type: String,
    default: 'purchase', // 'purchase' or 'sale'
    validator: (value) => ['purchase', 'sale'].includes(value)
  }
});

// Use the invoice composable
const {
  selectedSupplier, // This will be selectedCustomer for sales
  selectedWarehouse,
  date,
  status,
  invoiceItems,
  suppliers, // This will be customers for sales
  warehouses,
  taxes,
  shippingCost,
  paymentStatus,
  paidAmount,
  dueDate,
  // Computed
  config,
  invoiceSummary,
  getTotalQuantity,
  getTotalEffectiveQuantity,
  usedTaxes,
  
  // Methods
  initializeData,
  handleSelectProduct,
  removeItem,
  updateItem,
  handleTaxChange,
  saveInvoice
} = useInvoice(props.type);

// Initialize data on component mount
onMounted(initializeData);
</script>

<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <InvoiceHeader
      :party-list="suppliers"
      :selected-party="selectedSupplier"
      :warehouse-list="warehouses"
      :selected-warehouse="selectedWarehouse"
      :date="date"
      :status="status"
      :available-statuses="config.availableStatuses"
      :party-label="config.partyLabel"
      @update-selected-party="selectedSupplier = $event"
      @update-selected-warehouse="selectedWarehouse = $event"
      @update-date="date = $event"
      @update-status="status = $event"
    />

    <!-- Product Search -->
    <ProductSearch
      :api-url="config.productSearchUrl"
      @select-product="handleSelectProduct"
    />

    <!-- Products Table -->
    <div v-if="invoiceItems.length" class="space-y-4">
      <InvoiceItemsTable
        :items="invoiceItems"
        :taxes="taxes"
        @update-item="updateItem"
        @remove-item="removeItem"
        @tax-change="handleTaxChange"
      />
      <InvoicePaymentSection
    v-model:shippingCost="shippingCost"
    v-model:paymentStatus="paymentStatus"
    v-model:dueDate="dueDate"
    v-model:paidAmount="paidAmount"
      />
      <!-- Summary Cards -->
      <InvoiceSummary
        :invoice-items="invoiceItems"
        :summary="invoiceSummary"
        :used-taxes="usedTaxes"
        :total-quantity="getTotalQuantity"
        :total-effective-quantity="getTotalEffectiveQuantity"
      />
      
      <!-- Submit Button -->
      <div class="flex justify-end mt-4">
        <button 
          @click="saveInvoice"
          class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          {{ config.saveButtonText }}
        </button>
      </div>
    </div>
  </div>
</template>