<script setup>
import { defineProps, defineEmits } from 'vue';

defineProps({
  items: {
    type: Array,
    required: true
  },
  taxes: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['update-item', 'remove-item', 'tax-change']);

function handleItemUpdate(id, key, value) {
  emit('update-item', id, key, value);
}

function handleTaxChange(itemId, taxId) {
  emit('tax-change', itemId, taxId);
}

function handleRemoveItem(id) {
  emit('remove-item', id);
}
</script>

<template>
  <div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="w-full table-fixed divide-y divide-gray-200">
      <thead class="bg-white divide-y divide-gray-200">
        <tr class="">
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 20%">اسم المنتج</th>
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 12%">كود المنتج</th>
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 10%">الكمية</th>
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 10%">السعر</th>
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 12%">الخصم</th>
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 15%">الضريبة</th>
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 8%">المجموع</th>
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 8%">الإجمالي</th>
          <th class="px-4 py-3 text-xl font-medium text-gray-500 uppercase tracking-wider" style="width: 5%">الإجراءات</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50 transition-colors duration-150">
          <td class="px-4 text-center  py-4 text-xl text-gray-900" style="width: 20%">
            <div class="font-medium text-gray-500">{{ item.name }}</div>
          </td>
          <td class="px-4 text-center py-4 text-xl text-gray-600" style="width: 12%">
            <div class="font-mono">{{ item.product_code }}</div>
          </td>
          <td class="px-4 text-center py-4" style="width: 10%">
            <input 
              type="number" 
              class="block w-full px-3 py-1.5 text-xl border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150"
              :value="item.qty"
              min="1"
              @input="handleItemUpdate(item.id, 'qty', $event.target.value)" 
            />
          </td>
          <td class="px-4 text-center py-4" style="width: 10%">
            <input 
              type="number" 
              class="block w-full px-3 py-1.5 text-xl border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150"
              :value="item.price"
              min="0"
              step="0.01"
              @input="handleItemUpdate(item.id, 'price', $event.target.value)" 
            />
          </td>
          <td class="px-4 text-center py-4" style="width: 12%">
            <div class="space-y-1">
              <input 
                type="number" 
                class="block w-full px-3 py-1.5 text-xl border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150"
                :value="item.discount"
                min="0"
                :max="item.qty"
                @input="handleItemUpdate(item.id, 'discount', $event.target.value)" 
              />
              <div class="text-sx    text-gray-500">
                الكمية الفعلية: <span class="font-medium">{{ item.effectiveQty }}</span>
              </div>
            </div>
          </td>
          <td class="px-4 text-center py-4" style="width: 15%">
            <div class="space-y-1">
              <select 
                :value="item.tax_id" 
                class="block w-full px-3 py-1.5 text-xl border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-150"
                @change="handleTaxChange(item.id, $event.target.value)"
              >
                <option :value="null">بدون ضريبة</option>
                <option v-for="tax in taxes" :key="tax.id" :value="tax.id">
                  {{ tax.name }} ({{ tax.rate }}%)
                </option>
              </select>
              <div class="text-xl text-gray-500 mt-1" v-if="item.tax_amount">
                مبلغ الضريبة: {{ item.tax_amount.toFixed(2) }}
              </div>
            </div>
          </td>
          <td class="px-4 text-center py-4 text-xl text-gray-900 text-center" style="width: 8%">
            {{ item.subtotal.toFixed(2) }}
          </td>
          <td class="px-4 text-center py-4 text-xl text-gray-900 text-center" style="width: 8%">
            {{ item.total.toFixed(2) }}
          </td>
          <td class="px-4 text-center py-4 text-center" style="width: 5%">
            <button 
              @click="handleRemoveItem(item.id)" 
              class="inline-flex items-center px-2 py-1 text-xl text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors duration-150"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
