<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import api from '@/plugins/axios';
import { useToast } from 'vue-toastification';
import { useRouter, useRoute } from 'vue-router';

// Props
const props = defineProps({
  type: { type: String, required: true } // 'purchase' | 'sales'
});

const toast = useToast();
const router = useRouter();
const route = useRoute();

const invoice = ref(null);

const form = reactive({
  items: [],
});

// ✅ dynamic labels
const labels = computed(() => {
  return props.type === 'purchase'
    ? { title: 'Create Purchase Return', partner: 'Supplier', listRoute: 'PurchaseReturnList' }
    : { title: 'Create Sales Return', partner: 'Customer', listRoute: 'SalesReturnList' };
});

// ✅ fetch invoice data
onMounted(async () => {
  const invoiceId = route.params.id;
  const res = await api.get(`/returns/${props.type}/create/${invoiceId}`);
  invoice.value = res.data.data;

  form.items = invoice.value.items.map(item => ({
    product_id: item.product_id,
    product_name: item.product?.name || 'N/A',
    quantity: 0,
    unit_price: parseFloat(item.unit_price),
    original_quantity: item.quantity,
    available_quantity: item.available_quantity,
    total_price: 0,
    tax_amount: parseFloat(item.tax_amount || 0),
    discount_amount: parseFloat(item.discount_amount || 0),
  }));
});

// ✅ computed items with totals
const itemsWithTotal = computed(() =>
  form.items.map(item => {
    const quantity = item.quantity;
    const taxPerUnit = item.tax_amount / item.original_quantity;
    const discountPerUnit = item.discount_amount / item.original_quantity;

    const tax = taxPerUnit * quantity;
    const discount = discountPerUnit * quantity;
    const totalPrice = item.unit_price * quantity + tax - discount;

    return { ...item, tax, discount, totalPrice };
  })
);

const subTotal = computed(() =>
  itemsWithTotal.value.reduce((sum, i) => sum + i.unit_price * i.quantity, 0)
);
const taxTotal = computed(() =>
  itemsWithTotal.value.reduce((sum, i) => sum + i.tax, 0)
);
const discountTotal = computed(() =>
  itemsWithTotal.value.reduce((sum, i) => sum + i.discount, 0)
);
const grandTotal = computed(() => subTotal.value + taxTotal.value - discountTotal.value);

// ✅ submit return
async function submitReturn() {
  try {
    const itemsToReturn = form.items
      .filter(i => i.quantity > 0)
      .map((i, index) => ({
        product_id: i.product_id,
        quantity: i.quantity,
        unit_price: i.unit_price,
        total_price: itemsWithTotal.value[index].totalPrice,
        tax_amount: itemsWithTotal.value[index].tax,
        discount_amount: itemsWithTotal.value[index].discount,
      }));

    if (!itemsToReturn.length) {
      toast.error('Please enter return quantity for at least one item.');
      return;
    }

    const res = await api.post(`/returns/${props.type}`, {
      [`${props.type}_invoice_id`]: invoice.value.id,
      return_date: new Date().toISOString().split('T')[0],
      sub_total: subTotal.value,
      tax_amount: taxTotal.value,
      discount_amount: discountTotal.value,
      grand_total: grandTotal.value,
      items: itemsToReturn,
    });

    if (res.data.success) {
      toast.success(`${labels.value.title} successfully`);
      router.push({ name: labels.value.listRoute });
    } else {
      toast.error(res.data.message || 'Failed to create return');
    }

  } catch (error) {
    if (error.response?.status === 400) {
      toast.error(error.response.data.message || 'Quantity exceeds available amount');
      return;
    }
    console.error(error);
    toast.error(error.response?.data?.message || 'Failed to create return');
  }
}
</script>

<template>
  <div class="p-4 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">{{ labels.title }}</h2>

    <div class="mb-4 border p-4 rounded bg-gray-50">
      <p><strong>Invoice Number:</strong> {{ invoice?.invoice_number }}</p>
      <p><strong>{{ labels.partner }}:</strong> {{ invoice?.[props.type === 'purchase' ? 'supplier' : 'customer']?.name }}</p>
      <p><strong>Warehouse:</strong> {{ invoice?.warehouse?.name }}</p>
      <p><strong>Date:</strong> {{ invoice?.created_at }}</p>
    </div>

    <table class="w-full border mb-4 text-left">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-2 py-1">Product</th>
          <th class="border px-2 py-1">Unit Price</th>
          <th class="border px-2 py-1">Original Quantity</th>
          <th class="border px-2 py-1">Return Quantity</th>
          <th class="border px-2 py-1">Tax</th>
          <th class="border px-2 py-1">Discount</th>
          <th class="border px-2 py-1">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in itemsWithTotal" :key="index">
          <td class="border px-2 py-1">{{ item.product_name }}</td>
          <td class="border px-2 py-1">{{ item.unit_price.toFixed(2) }}</td>
          <td class="border px-2 py-1">{{ item.original_quantity }}</td>
          <td class="border px-2 py-1">
            <input type="number" min="0" :max="item.available_quantity" v-model.number="form.items[index].quantity"
                   class="border p-1 w-20"/>
          </td>
          <td class="border px-2 py-1">{{ item.tax.toFixed(2) }}</td>
          <td class="border px-2 py-1">{{ item.discount.toFixed(2) }}</td>
          <td class="border px-2 py-1">{{ item.totalPrice.toFixed(2) }}</td>
        </tr>
      </tbody>
    </table>

    <div class="mb-4 p-4 bg-gray-50 rounded">
      <p><strong>Sub Total:</strong> {{ subTotal.toFixed(2) }}</p>
      <p><strong>Tax:</strong> {{ taxTotal.toFixed(2) }}</p>
      <p><strong>Discount:</strong> {{ discountTotal.toFixed(2) }}</p>
      <p class="text-lg font-bold"><strong>Grand Total:</strong> {{ grandTotal.toFixed(2) }}</p>
    </div>

    <button @click="submitReturn" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
      Create Return
    </button>
  </div>
</template>
