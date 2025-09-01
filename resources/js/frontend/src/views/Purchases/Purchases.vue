<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
const purchaseHeaders = [
  { text: 'Purchase ID', value: 'id', sortable: true },
  { text: 'Supplier', value: 'supplier_name', sortable: true },
  { text: 'Date', value: 'created_at', sortable: true },
  { text: 'Total Amount', value: 'grand_total', sortable: true },
  { text: 'Status', value: 'status', sortable: true },
];



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
        show-route-name="PurchaseShow"  
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