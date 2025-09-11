<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';

const returnHeaders = [
  { text: 'Return Date', value: 'return_date', sortable: true },
  { text: 'Return Number', value: 'invoice_number', sortable: true },
  { text: 'Original Invoice', value: 'original_invoice_number', sortable: true }, // الفاتورة الأصلية
  { text: 'Customer', value: 'customer.name', sortable: true },
  { text: 'Warehouse', value: 'warehouse.name', sortable: true },
  { text: 'Grand Total', value: 'grand_total', sortable: true },
  { text: 'Status', value: 'status', sortable: true },
];

</script>

<template>
<div>
<BaseCrudTable
  endpoint="/returns/sales"
  :headers="returnHeaders"
  resource-name="sales-return"
  :show-view="true"
  :show-edit="false"
  :show-delete="false"
  :show-select="false"
  show-route-name="returns-show"
  create-route="/returns/sales/create"
  search-placeholder="البحث برقم المرتجع أو الفاتورة الأصلية..."
  empty-message="لا توجد بيانات متاحة"
>
  <template #create-button-text>
    Add sales Return
  </template>
</BaseCrudTable>
</div>
</template>
