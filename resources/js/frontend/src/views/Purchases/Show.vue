<script setup>
import { useRoute } from 'vue-router';
import InvoiceDetails from '@/components/InvoiceDetails.vue';
import InvoiceActions from '@/components/Invoice/InvoiceSummary/InvoiceActions.vue';

const route = useRoute();
</script>

<template>
<InvoiceDetails

  :api-end-point="'/purchases'"
  :title="'Purchase Return Invoice'"
  :show-tax="true"
  :show-discount="true"
  class="px-4 sm:px-6 lg:px-8"
/>

  <InvoiceActions
  :invoice-id="route.params.id"
  type="sales"
/>
</template>
