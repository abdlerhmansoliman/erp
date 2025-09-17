<script setup>
import { defineProps } from 'vue';

defineProps({
  invoiceItems: {
    type: Array,
    required: true
  },
  summary: {
    type: Object,
    required: true
  },
  usedTaxes: {
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
  },
  shippingCost: {
    type: Number,
    required: 0
  }
});

const emit = defineEmits(['save']);
</script>

<template>
  <div class="grid grid-cols-3 gap-4">
    <!-- Quantity Summary -->
    <div class="bg-white p-4 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-2">الكمية الإجمالية</h3>
      <div class="space-y-2">
        <div v-for="item in invoiceItems" :key="item.id" class="flex justify-between">
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

    <!-- Taxes Summary -->
    <div class="bg-white p-4 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-2">الضرائب</h3>
      <div class="space-y-2">
        <div v-for="tax in usedTaxes" :key="tax.id" class="flex justify-between">
          <span>{{ tax.name }}</span>
          <span>{{ tax.total.toFixed(2) }}</span>
        </div>
        <div class="border-t pt-2 font-bold">
          <span>إجمالي الضرائب: {{ summary.totalTax.toFixed(2) }}</span>
        </div>
      </div>
    </div>

    <!-- Total Summary -->
    <div class="bg-white p-4 rounded-lg shadow">
      <h3 class="text-lg font-semibold mb-2">الإجمالي</h3>
      <div class="space-y-2">
<div class="flex justify-between">
  <span>المجموع الفرعي:</span>
  <span>{{ (summary.subTotal || 0).toFixed(2) }}</span>
</div>
<div class="flex justify-between">
  <span>إجمالي الخصم:</span>
  <span>{{ (summary.totalDiscount || 0).toFixed(2) }}</span>
</div>
<div class="flex justify-between">
  <span>إجمالي الضرائب:</span>
  <span>{{ (summary.totalTax || 0).toFixed(2) }}</span>
</div>
<div class="flex justify-between">
  <span>تكلفة التوصيل:</span>
  <span>{{ (shippingCost || 0).toFixed(2) }}</span>
</div>
<div class="border-t pt-2 font-bold flex justify-between">
  <span>الإجمالي النهائي:</span>
  <span>{{ (summary.grandTotal || 0).toFixed(2) }}</span>
</div>
        
        <!-- Save Button -->

      </div>
    </div>
  </div>
</template>
