<script setup>
import { ref, onMounted } from 'vue';
import api from '@/plugins/axios';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

const router = useRouter();
const toast = useToast();
const product = ref({
    name: '',
    description: '',
    price: '',
    purchase_price: '',
    category_id: '',
    unit_id: '',
    tax_id: '',
});

const categories = ref([]);
const units = ref([]);
const taxes = ref([]);
const fetchCategories = async () => {
  try {
    const response = await api.get('/categories');
    categories.value = response.data.data; 
  } catch (error) {
    
  }
}


const fetchUnits = async () => {
    try {
        const response = await api.get('/units');
        units.value = response.data.data;
    } catch (error) {
        toast.error('Failed to fetch units:', error);
    }
}
const fetchTaxes = async () => {
    try {
      const response= await api.get('/taxes');
      taxes.value = response.data.data;
    } catch (error) {
      toast.error('Failed to fetch taxes:', error);
    }
}
onMounted(() => {
  fetchCategories();
  fetchUnits();
  fetchTaxes();
});

const submitForm = async () => {
  try {
    const response = await api.post('/products', product.value);
    console.log('Product created:', response.data);
    router.push('/products');
  } catch (error) {
    useToast('Failed to create product:', error.response?.data || error);
  }
};
</script>

<template>
  <div class=" mx-auto p-6 bg-white-100 shadow-md rounded-lg mt-10">
    <h1 class="text-2xl font-semibold mb-6 text-gray-800">Create Product</h1>
    <form @submit.prevent="submitForm" class="space-y-5">

      <!-- Name -->
      <div class="flex flex-col">
        <label class="mb-1 font-medium text-gray-700">Name</label>
        <input 
          v-model="product.name" 
          type="text" 
          placeholder="Enter product name"
          class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
        />
      </div>

      <!-- Description -->
      <div class="flex flex-col">
        <label class="mb-1 font-medium text-gray-700">Description</label>
        <textarea 
          v-model="product.description" 
          placeholder="Enter product description"
          class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition resize-none h-24"
        ></textarea>
      </div>

      <!-- Price -->
      <div class="flex flex-col">
        <label class="mb-1 font-medium text-gray-700">Price</label>
        <input 
          v-model="product.price" 
          type="number" 
          step="0.01"
          placeholder="Enter sale price"
          class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
        />
      </div>

      <!-- Purchase Price -->
      <div class="flex flex-col">
        <label class="mb-1 font-medium text-gray-700">Purchase Price</label>
        <input 
          v-model="product.purchase_price" 
          type="number" 
          step="0.01"
          placeholder="Enter purchase price"
          class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
        />
      </div>

      <!-- Category -->
      <div class="flex flex-col">
        <label class="mb-1 font-medium text-gray-700">Category</label>
        <select 
          v-model="product.category_id" 
          class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
        >
          <option value="">Select Category</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

        <!-- Taxe -->
        <div class="flex flex-col">
        <label class="mb-1 font-medium text-gray-700">Taxe</label>
        <select 
          v-model="product.tax_id" 
          class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
        >
          <option value="">Select Category</option>
          <option v-for="tax in taxes" :key="tax.id" :value="tax.id">
            {{ tax.name }}
          </option>
        </select>
      </div>
      <!-- Unit -->
      <div class="flex flex-col">
        <label class="mb-1 font-medium text-gray-700">Unit</label>
        <select 
          v-model="product.unit_id" 
          class="border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition"
        >
          <option value="">Select Unit</option>
          <option v-for="unit in units" :key="unit.id" :value="unit.id">
            {{ unit.name }}
          </option>
        </select>
      </div>

      <!-- Submit Button -->
      <button 
        type="submit" 
        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 rounded-lg shadow-md transition"
      >
        Create Product
      </button>

    </form>
  </div>
</template>

