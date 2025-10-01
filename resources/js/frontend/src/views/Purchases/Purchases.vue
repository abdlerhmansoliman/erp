<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import BaseCrudTable from '@/components/BaseCrudTable.vue';

const { t, locale } = useI18n();
const purchaseHeaders = computed(() => [
  { text: t('id'), value: 'id', sortable: true },
  { text: t('invoice_number'), value: 'invoice_number', sortable: true },
  { text: t('supplier_name'), value: 'supplier_name', sortable: true },
  { text: t('invoice_date'), value: 'created_at', sortable: true },
  { text: t('grand_total'), value: 'grand_total', sortable: true },
  { text: t('status'), value: 'status', sortable: true },
]);



</script>
<template>
<div>
    <BaseCrudTable
        endpoint="/purchases"
        :headers="purchaseHeaders"
        resource-name="purchase"
        :show-view="true"
        :show-edit="false"
        :show-delete="false"
        :show-select="false"
        show-route-name="PurchaseShow"  
        create-route="/Purchases/Create"
        search-placeholder="البحث..."
        empty-message="لا توجد بيانات متاحة"
    >
        <!-- Custom create button text if needed -->
        <template #create-button-text>
            Add Purchase
        </template>
    </BaseCrudTable>
</div>

</template>