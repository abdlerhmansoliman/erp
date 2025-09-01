<script setup>
import GenericInvoice from '@/components/Invoice/GenericInvoice.vue'
import { useInvoice } from '@/composables/useInvoiceDetales'

const {
  invoice,
  loading,
  invoiceSummary,
  getStatusClass,
  formatCurrency,
  goBack,
  editInvoice,
  downloadPdf,
} = useInvoice('sales')
</script>

<template>
  <GenericInvoice
    type="customer"
    title="Sales Invoice"
    party-label="CUSTOMER"
    :invoice="invoice"
    :loading="loading"
    :invoice-summary="invoiceSummary"
    :get-status-class="getStatusClass"
    :format-currency="formatCurrency"
    :go-back="goBack"
    :edit-invoice="editInvoice"
    :download-pdf="downloadPdf"
  />
</template>
