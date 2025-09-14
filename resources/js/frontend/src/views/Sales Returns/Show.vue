<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import InvoiceDetails from '@/components/InvoiceDetails.vue';

const route = useRoute();

// Props from router
const props = defineProps({

  id: { 
    type: [String, Number], 
    required: true,
    validator: (value) => value && !isNaN(value)
  }
});


</script>

<template>
  <div class="min-h-screen bg-gray-50">

    <InvoiceDetails 
      :api-end-point="'/returns/sales'"
      :title="'Purchase Return Invoice'"
      class="px-4 sm:px-6 lg:px-8"
    />
  </div>
</template>
