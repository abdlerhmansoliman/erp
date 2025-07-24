<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import EasyDataTable from 'vue3-easy-data-table';

const suppliers = ref([]);
const total = ref(0);
const loading = ref(false);
const currentPage = ref(1);
const rowsPerPage = ref(10);
const search = ref('');

const headers = [
  { text: 'Name', value: 'name', sortable: true },
  { text: 'Email', value: 'email', sortable: true },
  { text: 'Phone', value: 'phone', sortable: true },
  { text: 'Address', value: 'address', sortable: true },
{ text: 'Actions', value: 'controller', sortable: false },
];

async function fetchSuppliers() {
  loading.value = true;
  
  try {
    const response = await axios.get('http://localhost:8000/api/suppliers', {
      params: {
        page: currentPage.value,
        perPage: rowsPerPage.value,
        search: search.value,
      },
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }
    });

    const data = response.data;

    if (data.data && Array.isArray(data.data)) {
      suppliers.value = data.data;
      total.value = data.total;
    } else {
      suppliers.value = [];
      total.value = 0;
    }
  } catch (error) {
    suppliers.value = [];
    total.value = 0;
  } finally {
    loading.value = false;
  }
}

function onSearch() {
  currentPage.value = 1;
  fetchSuppliers();
}

watch([currentPage, rowsPerPage], () => {
  fetchSuppliers();
});

onMounted(() => {
  fetchSuppliers();
});
</script>

<template>
  <div class="p-4">
    <!-- Search input -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="البحث..."
        class="px-4 py-2 border rounded-md w-full max-w-md"
        @input="onSearch"
      />
    </div>

    <!-- Easy Data Table -->
    <EasyDataTable
      :headers="headers"
      :items="suppliers"
      :loading="loading"
      :rows-per-page="rowsPerPage"
      :current-page="currentPage"
      :total-items="total"
      show-index
      @update:current-page="currentPage = $event"
      @update:rows-per-page="rowsPerPage = $event"
    >
    <template #item-controller="{ item }">
      <button @click="editSupplier(item)" class="text-white bg-blue-700 hover:bg-blue-800  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 ">Edit</button>
      <button @click="deleteSupplier(item.id)" class="text-white bg-red-700 hover:bg-red-800  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Delete</button>
    </template>
      <template #empty-message>
        <div class="text-center p-4">
          لا توجد بيانات متاحة
        </div>
      </template>
    </EasyDataTable>
  </div>
</template>



<style>
@import "vue3-easy-data-table/dist/style.css";
</style>
