<script setup>
import { defineProps } from 'vue';

defineProps({
  items: {
    type: Array,
    required: true
  },
  totalQuantity: {
    type: Number,
    required: true
  },
  totalEffectiveQuantity: {
    type: Number,
    required: true
  }
});
</script>

<template>
  <div class="bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-2">الكمية الإجمالية</h3>
    <div class="space-y-2">
      <div v-for="item in items" :key="item.id" class="flex justify-between">
        <span>{{ item.name }}</span>
        <div class="text-left">
          <div>الكمية: {{ item.qty }}</div>
          <div>الخصم: {{ item.discount || 0 }}</div>
          <div class="font-semibold">الفعلي: {{ item.effectiveQty }}</div>
        </div>
      </div>
      <div class="border-t pt-2">
        <div class="flex justify-between font-bold">
          <span>الإجمالي:</span>
          <div class="text-left">
            <div>الكمية: {{ totalQuantity }}</div>
            <div>الفعلي: {{ totalEffectiveQuantity }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
