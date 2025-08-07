<script setup>
import api from '@/plugins/axios';
import { ref, watch, onMounted } from 'vue';
import EasyDataTable from 'vue3-easy-data-table';
import { useRouter } from 'vue-router'
import { useConfirmDialog } from '@/composables/useConfirmDialog'
import { useToast } from 'vue-toastification';


const { confirmDelete } = useConfirmDialog()
const toast = useToast()
const customers = ref([]);
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
async function fetchCustomers() {
    loading.value = true;
    try {
        const response = await api.get('/customers', {
            params: {
                page: currentPage.value,
                perPage: rowsPerPage.value,
                search: search.value,
            }
        });
if (response.data && Array.isArray(response.data.data)) {
    customers.value = response.data.data;
    total.value = response.data.total;
}
        else {
            customers.value = [];
            total.value = 0;
        }
    }
    catch (error) {
        customers.value = [];
        total.value = 0;
    }
    finally {
        loading.value = false;
    }

}
const router = useRouter()
function onSearch() {
    currentPage.value = 1;
    fetchCustomers()
}
const deleteCustomer=async(item)=>{
    if (!item || !item.id) {
        toast.error('Error: Could not find customer ID');
        return;
    }
  const confirmed = await confirmDelete(`Customer: ${item.name}`);

    if (confirmed) {
        try {
            await api.delete(`/customers/${item.id}`);
            toast.error('Customer deleted successfully!');
            fetchCustomers();
        } catch (error) {
            console.error('Delete error:', error);
            toast.error('Error deleting customer.');
        }
    }
}
function goToEdit(item) {
  router.push({ name: 'CustomerEdit', params: { id: item.id } });
}
watch ([currentPage, rowsPerPage], () => {
    fetchCustomers();
});
onMounted(() => {
    fetchCustomers();
});

</script>

<template>
  <div class="">
    <!-- Search input -->
    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
    <!-- حقل البحث -->
    <input
        v-model="search"
        type="text"
        placeholder="البحث..."
        class="px-4 py-2 border rounded-md w-full max-w-md"
        @input="onSearch"
    />

    <!-- زر الإضافة -->
  <router-link
    to="/customers/Create" 
    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-center"
  >
    Add
  </router-link>
    </div>
    <EasyDataTable
      :headers="headers"
      :items="customers"
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
            @click="() => deleteCustomer(item)"
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
</template>
<style>
@import "vue3-easy-data-table/dist/style.css";
</style>