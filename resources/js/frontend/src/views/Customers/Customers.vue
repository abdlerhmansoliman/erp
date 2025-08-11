<script setup>
import { ref } from 'vue'
import BaseCrudTable from '@/components/BaseCrudTable.vue'

const tableRef = ref()

const fields = [
  { value: 'name' },
  { value: 'email' },
  { value: 'phone' },
  { value: 'address' },
  { value: 'controller', sortable: false }
]

function editRow(item) {
  tableRef.value.goToEdit(item)
}
function deleteRow(item) {
  tableRef.value.deleteItem(item)
}
</script>

<template>
  <BaseCrudTable
    ref="tableRef"
    :fields="fields"
    endpoint="/customers"
    item-label="customer"
    edit-route="CustomerEdit"
    create-route="/customers/create"
  >
    <template #item-controller="{ item }">
      <button @click="editRow(item)" class="bg-blue-500 text-white px-2 py-1 rounded">
        Edit
      </button>
      <button @click="deleteRow(item)" class="bg-red-500 text-white px-2 py-1 rounded">
        Delete
      </button>
    </template>
  </BaseCrudTable>
</template>
