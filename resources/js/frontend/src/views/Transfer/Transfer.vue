<script setup>
import BaseCrudTable from '@/components/BaseCrudTable.vue'

const transferHeaders = [
  { text: 'ID', value: 'id', sortable: true },
  { text: 'From Warehouse', value: 'from_warehouse.name', sortable: true },
  { text: 'To Warehouse', value: 'to_warehouse.name', sortable: true },
  { text: 'Transfer Date', value: 'transfer_date', sortable: true },
  { text: 'Created By', value: 'created_by', sortable: true },
]
</script>

<template>
  <div>
    <BaseCrudTable
      endpoint="/transfers"
      :headers="transferHeaders"
      resource-name="transfer"
      edit-route-name="TransferEdit"
      create-route="/transfer/create"
      search-placeholder="ابحث عن تحويل..."
      empty-message="لا توجد تحويلات متاحة"
      delete-confirmation-key="id"
    >
      <template #create-button-text>
        Add Transfer
      </template>
    </BaseCrudTable>
  </div>
</template>
