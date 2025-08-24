<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue';
const porductHeaders = [
    { text: 'Name', value: 'name', sortable: true },
    { text: 'Category', value: 'category_name', sortable: true },
    {text:'Description', value:'description', sortable: false},
    { text: 'Product Code', value: 'product_code', sortable: true },
    { text: 'Price', value: 'price', sortable: true },
    {text:'unit', value:'unit_name', sortable: true},
]
</script>

<template>
<div>
    <BaseCrudTable
        endpoint="/products"
        :headers="porductHeaders"
        resource-name="product"
        edit-route-name="ProductEdit"
        create-route="/Products/Create"
        search-placeholder="البحث..."
        empty-message="لا توجد بيانات متاحة"
        delete-confirmation-key="product_id"
    >
        <!-- Custom create button text if needed -->
        <template #create-button-text>
            Add Product
        </template>
    </BaseCrudTable>
</div>
</template>
