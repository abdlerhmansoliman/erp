<script setup>
import { ref } from 'vue';

const props = defineProps({
  invoiceData: { type: Object, required: true }, // كل الداتا من API
  type: { type: String, required: true }, // purchase | sales | return
});

const invoice = ref(props.invoiceData.invoice);
const items = ref(props.invoiceData.items);

function goBack() {
  history.back();
}

function downloadPdf() {
  // مثال على تحميل PDF
  console.log('Download PDF for invoice', invoice.value.id);
}
</script>

<template>
  <div v-if="invoice && items">
    <button @click="goBack">Back</button>
    <h2>{{ type }} Invoice #{{ invoice.id }}</h2>
    <p>Supplier/Warehouse: {{ invoice.supplier.name }} / {{ invoice.warehouse.name }}</p>
    <p>Status: {{ invoice.status }}</p>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id">
          <td>{{ item.product_name }}</td>
          <td>{{ item.quantity }}</td>
          <td>{{ item.price }}</td>
          <td>{{ item.total }}</td>
        </tr>
      </tbody>
    </table>
    <p>Total: {{ invoice.total }}</p>
    <p>Tax: {{ invoice.tax }}</p>
    <p>Grand Total: {{ invoice.grand_total }}</p>
    <button @click="downloadPdf">Download PDF</button>
  </div>

  <div v-else>
    Loading...
  </div>
</template>

