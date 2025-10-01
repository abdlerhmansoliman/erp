<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';

const returnHeaders = [
  { text: 'Return Date', value: 'return_date' },
  { text: 'Return Number', value: 'invoice_number' },
  { text: 'Original Invoice', value: 'original_invoice_number' },
  { text: 'Supplier', value: 'supplier_name' },
  { text: 'Warehouse', value: 'warehouse_name' },
  { text: 'Grand Total', value: 'grand_total' },
  { text: 'Status', value: 'status' },
];
</script>

<template>
<div>
<BaseCrudTable
  endpoint="/returns/purchase"
  :headers="returnHeaders"
  resource-name="purchase-return"
  :show-view="true"
  :show-edit="false"
  :show-delete="false"
  :show-select="false"
  show-route-name="returns-show"
  create-route="/returns/purchase/create"
  search-placeholder="البحث برقم المرتجع أو الفاتورة الأصلية..."
  empty-message="لا توجد بيانات متاحة"
>
  <template #create-button-text>
    Add Purchase Return
  </template>
</BaseCrudTable>
</div>
</template>
