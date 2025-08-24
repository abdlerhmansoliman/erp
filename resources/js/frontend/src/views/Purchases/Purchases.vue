<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
const purchaseHeaders = [
  { text: 'Purchase ID', value: 'id', sortable: true },
  { text: 'Supplier', value: 'supplier_name', sortable: true },
  { text: 'Date', value: 'created_at', sortable: true },
  { text: 'Total Amount', value: 'grand_total', sortable: true },
  { text: 'Status', value: 'status', sortable: true },
];

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
        endpoint="/purchases"
        :headers="purchaseHeaders"
        resource-name="purchase"
        edit-route-name="PurchaseEdit"
        create-route="/Purchases/Create"
        search-placeholder="البحث..."
        empty-message="لا توجد بيانات متاحة"
        delete-confirmation-key="purchase_id"
        @item-selected="handleItemSelected"
        @custom-action="handleCustomAction"
    >
        <!-- Custom create button text if needed -->
        <template #create-button-text>
            Add Purchase
        </template>
    </BaseCrudTable>
</div>

</template>