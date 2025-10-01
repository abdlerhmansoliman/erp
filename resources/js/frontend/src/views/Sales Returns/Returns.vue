<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const returnHeaders = computed(() => [
  { text: t('return_date'), value: 'return_date' },
  { text: t('return_numper'), value: 'invoice_number' },
  { text: t('orginal_numper'), value: 'original_invoice_number' },
  { text: t('customer_name'), value: 'customer_name' },
  { text: t('warehouse'), value: 'warehouse_name' },
  { text: t('grand_total'), value: 'grand_total' },
  { text: t('status'), value: 'status' },
]);

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
  show-route-name="sales-returns-show"
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
