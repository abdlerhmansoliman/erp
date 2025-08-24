<script setup>
import ReusableDataTable from '@/components/BaseCrudTable.vue'

// Define headers for suppliers
const supplierHeaders = [
  { text: 'Name', value: 'name', sortable: true },
  { text: 'Email', value: 'email', sortable: true },
  { text: 'Phone', value: 'phone', sortable: true },
  { text: 'Address', value: 'address', sortable: true },
]

// Handle custom events if needed
const handleItemSelected = (event) => {
  console.log('Item selected:', event)
  // Handle create/edit events if not using routes
}

const handleCustomAction = (event) => {
  console.log('Custom action:', event)
  // Handle any custom actions
}
</script>

<template>
  <div>
    <ReusableDataTable
      endpoint="/suppliers"
      :headers="supplierHeaders"
      resource-name="supplier"
      edit-route-name="SupplierEdit"
      create-route="/suppliers/Create"
      search-placeholder="البحث..."
      empty-message="لا توجد بيانات متاحة"
      delete-confirmation-key="name"
      @item-selected="handleItemSelected"
      @custom-action="handleCustomAction"
    >
      <!-- Custom create button text if needed -->
      <template #create-button-text>
        Add Supplier
      </template>
    </ReusableDataTable>
  </div>
</template>