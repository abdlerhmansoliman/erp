<script setup>
import ReusableDataTable from '@/components/BaseCrudTable.vue'

const customerHeaders = [
  { text: 'Name', value: 'name', sortable: true },
  { text: 'Email', value: 'email', sortable: true },
  { text: 'Phone', value: 'phone', sortable: true },
  { text: 'Address', value: 'address', sortable: true },
]

// Handle custom events if needed
const handleItemSelected = (event) => {}

const handleCustomAction = (event) => {}
</script>

<template>
  <div>
    <ReusableDataTable
      endpoint="/customers"
      :headers="customerHeaders"
      resource-name="customer"
      edit-route-name="CustomerEdit"
      create-route="/customers/Create"
      search-placeholder="البحث..."
      empty-message="لا توجد بيانات متاحة"
      delete-confirmation-key="name"
      @item-selected="handleItemSelected"
      @custom-action="handleCustomAction"
    >
      <!-- Custom create button text if needed -->
      <template #create-button-text>
        Add Customer
      </template>
    </ReusableDataTable>
  </div>
</template>