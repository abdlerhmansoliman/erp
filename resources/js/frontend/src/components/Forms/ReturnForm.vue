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
    // Validate form data
    if (!form?.items || !Array.isArray(form.items)) {
      toast.error('Invalid form data: items not found');
      return;
    }

    // Filter and map items to return
    const itemsToReturn = form.items
      .filter(item => item.quantity && item.quantity > 0)
      .map((item, index) => {
        if (!item.product_id) {
          throw new Error(`Item at index ${index} missing product_id`);
        }
        
        if (!itemsWithTotal.value || !itemsWithTotal.value[index]) {
          throw new Error(`Missing calculated values for item at index ${index}`);
        }

        return {
          product_id: item.product_id,
          quantity: parseFloat(item.quantity) || 0,
          unit_price: parseFloat(item.unit_price) || 0,
          total_price: parseFloat(itemsWithTotal.value[index].totalPrice) || 0,
          tax_amount: parseFloat(itemsWithTotal.value[index].tax) || 0,
          discount_amount: parseFloat(itemsWithTotal.value[index].discount) || 0,
        };
      });

    // Validate at least one item selected
    if (!itemsToReturn.length) {
      toast.error('Please enter return quantity for at least one item.');
      return;
    }

    // Validate required dependencies
    if (!invoice.value?.id) {
      toast.error('Invoice ID not found');
      return;
    }

    if (!props?.type) {
      toast.error('Return type not specified');
      return;
    }

    // Prepare payload
    const payload = {
      [`${props.type}_invoice_id`]: invoice.value.id,
      return_date: new Date().toISOString().split('T')[0],
      sub_total: parseFloat(subTotal.value) || 0,
      tax_amount: parseFloat(taxTotal.value) || 0,
      discount_amount: parseFloat(discountTotal.value) || 0,
      grand_total: parseFloat(grandTotal.value) || 0,
      items: itemsToReturn,
    };

    // Make API call
    const res = await api.post(`/returns/${props.type}`, payload);

    // Handle response - check for success based on actual response structure
    if (res.data && (res.data.success === true || res.data.data)) {
      const successMessage = `${labels.value?.title || 'Return created'} successfully`;
      toast.success(successMessage);
      
      // Navigate to list page
      if (labels.value?.listRoute) {
        try {
          await router.push({ name: labels.value.listRoute });
        } catch (routeError) {
          // Try common alternative route names
          const alternativeRoutes = [
            'sales-returns',
            'sales-return-list', 
            'returns',
            'return-list',
            'sales_returns',
            'salesReturns'
          ];
          
          let routeFound = false;
          for (const altRoute of alternativeRoutes) {
            try {
              await router.push({ name: altRoute });
              routeFound = true;
              break;
            } catch (e) {
              // Continue trying other routes
            }
          }
          
          if (!routeFound) {
            toast.info('Return created successfully. Please navigate to the returns list manually.');
          }
        }
      }
    } else {
      // Handle failure
      let errorMessage = 'Failed to create return';
      
      if (res.data?.message) {
        errorMessage = res.data.message;
      } else if (res.data?.errors) {
        if (Array.isArray(res.data.errors)) {
          errorMessage = res.data.errors.join(', ');
        } else if (typeof res.data.errors === 'object') {
          errorMessage = Object.values(res.data.errors).flat().join(', ');
        }
      }

      toast.error(errorMessage);
    }

  } catch (error) {
    // Handle different types of errors
    if (error.response) {
      if (error.response.status === 400) {
        const errorMsg = error.response.data?.message || 'Bad request - please check your input';
        toast.error(errorMsg);
      } else if (error.response.status === 401) {
        toast.error('Authentication required. Please log in again.');
      } else if (error.response.status === 403) {
        toast.error('You do not have permission to perform this action.');
      } else if (error.response.status === 404) {
        toast.error('The requested resource was not found.');
      } else if (error.response.status === 422) {
        const errorMsg = error.response.data?.message || 'Validation failed';
        toast.error(errorMsg);
      } else if (error.response.status >= 500) {
        toast.error('Server error. Please try again later.');
      } else {
        const errorMsg = error.response.data?.message || `HTTP Error ${error.response.status}`;
        toast.error(errorMsg);
      }
    } else if (error.request) {
      toast.error('Network error. Please check your connection and try again.');
    } else {
      toast.error(error.message || 'An unexpected error occurred');
    }
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
