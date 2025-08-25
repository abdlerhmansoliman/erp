<script setup>
import {  defineEmits, ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  apiUrl: { type: String, required: true }  // endpoint البحث
});

const emit = defineEmits(['select-product']);

const searchQuery = ref('');
const searchResults = ref([]);
const loading = ref(false);

// تابع البحث
async function searchProducts() {
  if (!searchQuery.value) {
    searchResults.value = [];
    return;
  }

  loading.value = true;
  try {
    const { data } = await axios.get(props.apiUrl, { params: { q: searchQuery.value } });
    searchResults.value = data.data || [];
  } catch (error) {
    console.error('Error fetching products:', error);
  } finally {
    loading.value = false;
  }
}

// متابعة الـ input + debounce صغير
watch(searchQuery, () => {
  searchProducts();
});

// عند اختيار منتج
function selectProduct(product) {
  emit('select-product', product);
  searchQuery.value = '';
  searchResults.value = [];
}
</script>

<template>
  <div class="mb-4">
    <input
      type="text"
      v-model="searchQuery"
      placeholder="ابحث عن منتج"
      class="border p-2 rounded w-full"
    />

    <div v-if="loading" class="mt-2">جاري البحث...</div>

<ul v-if="searchResults.length" class="border rounded mt-2 max-h-60 overflow-auto">
  <li
    v-for="product in searchResults"
    :key="product.id"
    @click="selectProduct(product)"
    class="p-2 hover:bg-gray-100 cursor-pointer"
  >
    {{ product.name }} - {{ product.product_code }}
  </li>
</ul>
  </div>
</template>
