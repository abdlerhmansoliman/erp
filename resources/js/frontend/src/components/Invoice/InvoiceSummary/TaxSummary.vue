<script setup>
import { defineProps } from 'vue';

defineProps({
  usedTaxes: {
    type: Array,
    required: true
  },
  totalTax: {
    type: Number,
    required: true
  }
});
</script>

<template>
  <div class="bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-2">الضرائب</h3>
    <div class="space-y-2">
      <div v-for="tax in usedTaxes" :key="tax.id" class="flex justify-between">
        <span>{{ tax.name }}</span>
        <span>{{ tax.total.toFixed(2) }}</span>
      </div>
      <div class="border-t pt-2 font-bold">
        <span>إجمالي الضرائب: {{ totalTax.toFixed(2) }}</span>
      </div>
    </div>
  </div>
</template>
