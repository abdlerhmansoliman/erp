<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import InvoiceHeader from '@/components/Invoice/InvoiceHeader.vue';
import ProductSearch from '@/components/Invoice/ProductSearch.vue';
import EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

const selectedSupplier = ref(null);
const date = ref(new Date().toISOString().slice(0,10));
const status = ref('draft');
const invoiceItems = ref([]);

const suppliers = ref([]);
const warehouses = ref([]);

// جلب الموردين
onMounted(async () => {
  try {
    const { data } = await axios.get('/api/suppliers');
    suppliers.value = data.data;
  } catch (error) { console.error(error); }

  // لو محتاج تجيب المخازن من API
  try {
    const { data } = await axios.get('/api/warehouses');
    warehouses.value = data.data;
  } catch (error) { console.error(error); }
});

// إضافة منتج من البحث
function handleSelectProduct(product) {
  const exists = invoiceItems.value.find(item => item.id === product.id);
  if (!exists) {
    invoiceItems.value.push({
      ...product,
      qty: 1,
      price: product.price,
      discount: 0,
      tax: 0,
      subtotal: product.price,
      total: product.price
    });
  }
}

// حذف منتج
function removeItem(id) {
  invoiceItems.value = invoiceItems.value.filter(item => item.id !== id);
}

// تحديث قيمة داخل الجدول (qty/price/discount/tax)
function updateItem(id, key, value) {
  const item = invoiceItems.value.find(i => i.id === id);
  if (item) {
    item[key] = Number(value);
  }
}
</script>

<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <InvoiceHeader
      :party-list="suppliers"
      :selected-party="selectedSupplier"
      :warehouse-list="warehouses"
      :selected-warehouse="warehouses[0]"
      :date="date"
      :status="status"
      @update-selected-party="selectedSupplier = $event"
      @update-date="date = $event"
      @update-status="status = $event"
    />

    <!-- Product Search -->
    <ProductSearch
      api-url="/api/products/search"
      @select-product="handleSelectProduct"
    />

    <!-- Easy Data Table -->
    <div v-if="invoiceItems.length">
      <EasyDataTable :data="invoiceItems">
        <template #default="{ row }">
          <tr>
            <td>{{ row.name }}</td>
            <td>{{ row.product_code }}</td>
            <td>
              <input type="number" class="border p-1 w-16"
                     v-model.number="row.qty"
                     @input="updateItem(row.id, 'qty', row.qty)" />
            </td>
            <td>
              <input type="number" class="border p-1 w-20"
                     v-model.number="row.price"
                     @input="updateItem(row.id, 'price', row.price)" />
            </td>
            <td>
              <input type="number" class="border p-1 w-16"
                     v-model.number="row.discount"
                     @input="updateItem(row.id, 'discount', row.discount)" />
            </td>
            <td>
              <input type="number" class="border p-1 w-16"
                     v-model.number="row.tax"
                     @input="updateItem(row.id, 'tax', row.tax)" />
            </td>
            <td>{{ row.subtotal }}</td>
            <td>{{ row.total }}</td>
            <td>
              <button @click="removeItem(row.id)" class="text-red-500">حذف</button>
            </td>
          </tr>
        </template>
      </EasyDataTable>
    </div>
  </div>
</template>
