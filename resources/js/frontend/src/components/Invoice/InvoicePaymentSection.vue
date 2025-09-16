<script setup>
import { computed } from 'vue'

const props = defineProps({
  shippingCost: {
    type: Number,
    default: 0
  },
  paymentStatus: {
    type: String,
    default: 'paid'
  },
  dueDate: {
    type: String,
    default: () => new Date().toISOString().slice(0,10)
  },
  paidAmount: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits([
  'update:shippingCost',
  'update:paymentStatus',
  'update:dueDate',
  'update:paidAmount'
])

// تحويل props إلى قيم قابلة للتعديل عبر v-model
const shippingCostLocal = computed({
  get: () => props.shippingCost,
  set: (val) => emit('update:shippingCost', +val)
})

const paymentStatusLocal = computed({
  get: () => props.paymentStatus,
  set: (val) => emit('update:paymentStatus', val)
})

const dueDateLocal = computed({
  get: () => props.dueDate,
  set: (val) => emit('update:dueDate', val)
})

const paidAmountLocal = computed({
  get: () => props.paidAmount,
  set: (val) => emit('update:paidAmount', +val)
})
</script>

<template>
  <div class="space-y-4 border bg-white rounded p-4">
    <!-- Shipping Cost -->
    <div class="flex items-center space-x-2">
      <label class="font-semibold w-40">تكلفة التوصيل:</label>
      <input 
        type="number" 
        min="0" 
        v-model="shippingCostLocal"
        class="border rounded p-2 w-32" 
      />
    </div>

    <!-- Payment Status -->
    <div class="flex items-center space-x-2">
      <label class="font-semibold w-40">حالة الدفع:</label>
      <select 
        v-model="paymentStatusLocal"
        class="border rounded p-2"
      >
        <option value="draft">مسودة</option>
        <option value="due">غير مدفوعة</option>
        <option value="partial">مدفوعة جزئيًا</option>
        <option value="paid">مدفوعة بالكامل</option>
      </select>
    </div>

    <!-- Paid Amount (يظهر فقط في حالة جزئي) -->
    <div 
      class="flex items-center space-x-2"
      v-if="paymentStatusLocal === 'partial'"
    >
      <label class="font-semibold w-40">المبلغ المدفوع:</label>
      <input 
        type="number" 
        min="0" 
        v-model="paidAmountLocal"
        class="border rounded p-2 w-32"
      />
    </div>

    <!-- Due Date (يظهر في جزئي أو غير مدفوعة) -->
    <div 
      class="flex items-center space-x-2"
      v-if="['partial','due'].includes(paymentStatusLocal)"
    >
      <label class="font-semibold w-40">تاريخ الاستحقاق:</label>
      <input 
        type="date"
        v-model="dueDateLocal"
        class="border rounded p-2"
      />
    </div>
  </div>
</template>
