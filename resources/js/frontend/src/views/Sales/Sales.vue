<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
const salesHeaders = [
    { text: 'Sales ID', value: 'id', sortable: true },
    { text: 'Customer', value: 'customer_name', sortable: true },
    { text: 'Date', value: 'created_at', sortable: true },
    { text: 'Total Amount', value: 'grand_total', sortable: true },
]
const handleItemSelected = (event) => {
    console.log('Item selected:', event);
    // Handle create/edit events if not using routes
};
const handleCustomAction = (event) => {
    console.log('Custom action:', event);
    // Handle any custom actions
};
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
    edit-route-name="SaleEdit"
    create-route="/sales/Create"
    search-placeholder="البحث..."
    empty-message="لا توجد بيانات متاحة"
    delete-confirmation-key="sale_id"
    @item-selected="handleItemSelected"
    @custom-action="handleCustomAction"
>
    <!-- Custom create button text if needed -->
    <template #create-button-text>
        Add Sale
    </template>
</BaseCrudTable>
</div>
</template>