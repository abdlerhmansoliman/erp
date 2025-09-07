<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
const salesHeaders = [
    { text: 'Sales ID', value: 'id', sortable: true },
    { text: 'Customer', value: 'customer_name', sortable: true },
    { text: 'Date', value: 'created_at', sortable: true },
    { text: 'Total Amount', value: 'grand_total', sortable: true },
]

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