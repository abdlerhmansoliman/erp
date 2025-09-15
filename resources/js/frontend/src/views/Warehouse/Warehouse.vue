<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
const warehouseHeaders = [
  { text: 'Name', value: 'name', sortable: true },
  { text: 'Address', value: 'address', sortable: true },
  { text: 'Email' ,value: 'email', sortable: true },
  { text: 'Phone', value: 'phone', sortable: true },
  { text: 'Products Count', value: 'product_count', sortable: false },
  {text: 'Total Quantity', value: 'total_quantity', sortable: false}, 
];
</script>
<template>
    <div>
        <BaseCrudTable
            endpoint="/warehouses"
            :headers="warehouseHeaders"
            :show-view="true"
            resource-name="warehouse"
            edit-route-name="WarehouseEdit"
            create-route="/warehouses/Create"
            search-placeholder="البحث..."
            show-route-name="WarehouseShow"
            empty-message="لا توجد بيانات متاحة"
            delete-confirmation-key="name"
        >
            <!-- Custom create button text if needed -->
            <template #create-button-text>
            Create Warehouse
            </template>
        </BaseCrudTable>
    </div>
</template>