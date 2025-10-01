<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import BaseCrudTable from '@/components/BaseCrudTable.vue';

const { t } = useI18n();

const salesHeaders = computed(() => [
    { text: t('id'), value: 'id', sortable: true },
    { text: t('invoice_number'), value: 'invoice_number', sortable: true },
    { text: t('customer_name'), value: 'customer_name', sortable: true },
    { text: t('invoice_date'), value: 'created_at', sortable: true },
    { text: t('grand_total'), value: 'grand_total', sortable: true },
])

</script>
<template>
<div>
<BaseCrudTable
    endpoint="/sales"
    :headers="salesHeaders"
    resource-name="sale"
    :show-view="true"
    :show-edit="false"
    :show-delete="false"
    :show-select="false"
    show-route-name="SalesShow"
    create-route="/sales/Create"
    search-placeholder="البحث..."
    empty-message="لا توجد بيانات متاحة"
    delete-confirmation-key="sale_id"
>
    <!-- Custom create button text if needed -->
    <template #create-button-text>
        Add Sale
    </template>
</BaseCrudTable>
</div>
</template>