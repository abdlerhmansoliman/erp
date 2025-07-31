<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import EasyDataTable from 'vue3-easy-data-table';
import { useRouter } from 'vue-router'
import { useConfirmDialog } from '@/composables/useConfirmDialog'
import { useToast } from 'vue-toastification'


const { confirmDelete } = useConfirmDialog()
const toast = useToast()
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

const router = useRouter()

function onSearch() {
  currentPage.value = 1;
  fetchSuppliers();
}

const deleteSupplier = async (item) => {
  if (!item || !item.id) {
    toast.error('Error: Could not find supplier');
    return;
  }

  const confirmed = await confirmDelete(`Supplier: ${item.name}`);
  if (!confirmed) return;

  try {
    await axios.delete(`http://localhost:8000/api/suppliers/${item.id}`);
    toast.error('The supplier deleted successfully!');
    fetchSuppliers(); // تأكد إن دي موجودة في الكومبوننت
  } catch (error) {
    console.error('Delete error:', error);
    toast.error('Error deleting supplier.');
  }
};
function goToEdit(item) {
  router.push({ name: 'SupplierEdit', params: { id: item.id } });
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
    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
    <input
        v-model="search"
        type="text"
        placeholder="البحث..."
        class="px-4 py-2 border rounded-md w-full max-w-md"
        @input="onSearch"
    />

  <router-link
    to="/suppliers/Create" 
    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-center"
  >
    Add
  </router-link>
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
      <template #item-controller="item">
        <div class="flex gap-2">
          <button
            @click="() => goToEdit(item)"
            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Edit
          </button>
          <button
            @click="() => deleteSupplier(item)"
            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
          >
            Delete
          </button>
        </div>
      </template>

      <template #empty-message>
        <div class="text-center p-4">
          لا توجد بيانات متاحة
        </div>
        
      </template>
    </EasyDataTable>

  </div>
  <!-- Edit Form -->

</template>

<style>
</style>