<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';

const returnHeaders = [
  { text: 'Return Date', value: 'return_date', sortable: true },
  { text: 'Return Number', value: 'invoice_number', sortable: true },
  { text: 'Original Invoice', value: 'invoice.invoice_number', sortable: true },
  { text: 'Supplier', value: 'invoice.supplier.name', sortable: true },
{ text: 'Warehouse', value: 'invoice.warehouse.name', sortable: true },
  { text: 'Grand Total', value: 'grand_total', sortable: true },
  { text: 'Status', value: 'status', sortable: true },
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
