<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '@/plugins/axios';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const invoiceId = route.params.id;

const props = defineProps({
  apiEndPoint: { type: String, required: true },
  title: { type: String, required: true },
  currency: { type: String, default: 'USD' },
  showTax:{ type: Boolean, default: false },
  showDiscount:{ type: Boolean, default: true },

});

// State
const invoice = ref(null);
const loading = ref(false);
const error = ref('');

const showTax = ref(true);
const showDiscount = ref(true);
const emptyMessage = ref('No items found');

// Formatters
const formatCurrency = (amount) => {
  if (amount == null || isNaN(amount)) return `$0.00`;
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: props.currency,
    minimumFractionDigits: 2
  }).format(Number(amount));
};

const formatNumber = (num) => (num == null || isNaN(num) ? '0' : Number(num).toLocaleString());
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Fetch invoice
const fetchInvoice = async () => {
  if (!invoiceId) {
    error.value = 'Invalid invoice ID';
    return;
  }

  loading.value = true;
  error.value = '';

  try {
    const response = await api.get(`${props.apiEndPoint}/${invoiceId}`);
    if (response.data?.success && response.data?.data) {
      invoice.value = response.data.data;
    } else if (response.data?.data) {
      invoice.value = response.data.data;
    } else {
      invoice.value = response.data;
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load invoice';
    toast.error(error.value);
  } finally {
    loading.value = false;
  }
};

// Computed
const items = computed(() => invoice.value?.items || []);

const calculatedSummary = computed(() => ({
  subtotal: Number(invoice.value?.sub_total || 0),
  total_tax: Number(invoice.value?.tax_amount || 0),
  total_discount: Number(invoice.value?.discount_amount || 0),
  shipping_amount: Number(invoice.value?.shipping_cost || 0),
  additional_charges: Number(invoice.value?.additional_charges || 0),
  grand_total: Number(invoice.value?.grand_total || 0)
}));
// Helpers for items
const getProductName = (item) => item.product?.name || item.name || 'N/A';
const getProductCode = (item) => item.product?.code || item.code || '';
const getProductDescription = (item) => item.product?.description || item.description || '';
const getUnit = (item) => item.unit?.name || item.unit_name || '';

// Actions
const goBack = () => router.back();

onMounted(fetchInvoice);
defineExpose({ fetchInvoice, invoice });
</script>

<template>
  <div class="min-h-screen bg-gray-50 p-4 sm:p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex justify-between items-start mb-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ title }}</h1>
            <p class="text-lg text-gray-600 mt-1">
              #{{ invoice?.invoice_number || invoice?.id }}
            </p>
          </div>
          <div class="text-right">
            <span class="inline-block px-3 py-1 text-sm rounded-full font-medium">
              {{ invoice?.status?.toUpperCase() }}
            </span>
            <p class="text-sm text-gray-500 mt-2">
              {{ formatDate(invoice?.created_at) }}
            </p>
          </div>
        </div>

        <!-- Invoice Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Supplier</h3>
            <p class="text-lg font-medium text-gray-900">{{ invoice?.customer_name || 'N/A' }}</p>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Warehouse</h3>
            <p class="text-lg font-medium text-gray-900">{{ invoice?.warehouse_name || 'N/A' }}</p>
          </div>

          <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Grand Total</h3>
            <p class="text-2xl font-bold text-green-600">{{ formatCurrency(invoice?.grand_total) }}</p>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Details</h3>
            <p class="text-sm text-gray-600"><span class="font-medium">Items:</span> {{ items.length }}</p>
          </div>
            <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Shipping Cost</h3>
            <p class="text-2xl font-bold text-green-600">{{ formatCurrency(invoice?.shipping_cost) }}</p>
          </div>
        </div>
        
        <!-- Notes -->
        <div v-if="invoice?.notes" class="mt-6 p-4 bg-yellow-50 rounded-lg">
          <h3 class="text-sm font-semibold text-gray-700 mb-2">Notes</h3>
          <p class="text-sm text-gray-600">{{ invoice.notes }}</p>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="bg-white rounded-lg shadow p-8 text-center">
        <p class="text-gray-600">Loading invoice details...</p>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-red-600 mb-4">{{ error }}</p>
        <button @click="fetchInvoice" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
          Try Again
        </button>
      </div>

      <!-- Invoice Table -->
      <div v-else-if="invoice" class="bg-white rounded-lg shadow p-6 space-y-6">
        <div>
          <h2 class="text-lg font-semibold">Invoice #{{ invoice?.invoice_number || invoice?.id }}</h2>
          <div class="overflow-x-auto mt-4">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                  <th v-if="props.showTax" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                  <th v-if="props.showDiscount" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="items.length === 0">
                  <td :colspan="showTax && showDiscount ? 7 : showTax || showDiscount ? 6 : 5" class="px-6 py-8 text-center text-gray-500">{{ emptyMessage }}</td>
                </tr>
                <tr v-else v-for="(item, index) in items" :key="item.id || index" class="hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900">
                    <div> {{ item.product_name }}</div>
                    <div v-if="getProductCode(item)" class="text-xs text-gray-500">Code: {{ getProductCode(item) }}</div>
                    <div v-if="getProductDescription(item)" class="text-xs text-gray-400 mt-1">{{ getProductDescription(item) }}</div>
                  </td>
                  <td class="px-6 py-4 text-right text-sm text-gray-900 font-medium">{{ formatNumber(item.quantity) }} <span v-if="getUnit(item)" class="text-xs text-gray-500 ml-1">{{ getUnit(item) }}</span></td>
                  <td v-if="showTax"class="px-6 py-4 text-right text-sm text-gray-900">{{ formatCurrency(item.unit_price || item.price) }}</td>
                  <td v-if="showDiscount" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ formatCurrency(item.tax_amount || item.tax_amount) }}</td>
                  <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ formatCurrency(item.discount_amount || item.discount) }}</td>
                  <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ formatCurrency(item.total_price || item.line_total) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Invoice Summary -->
          <div class="border-t bg-gray-50 px-6 py-4 mt-4">
            <div class="flex justify-end">
              <div class="w-full max-w-sm space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Subtotal:</span>
                  <span class="font-medium">{{ formatCurrency(calculatedSummary.subtotal) }}</span>
                </div>
                <div v-if="showTax" class="flex justify-between text-sm">
                  <span class="text-gray-600">Total Tax:</span>
                  <span class="font-medium">{{ formatCurrency(calculatedSummary.total_tax) }}</span>
                </div>
                <div v-if="showDiscount" class="flex justify-between text-sm text-red-600">
                  <span>Total Discount:</span>
                  <span class="font-medium">-{{ formatCurrency(calculatedSummary.total_discount) }}</span>
                </div>
                <div  class="flex justify-between text-sm">
                  <span class="text-gray-600">Shipping:</span>
                  <span class="font-medium">{{ formatCurrency(calculatedSummary.shipping_amount) }}</span>
                </div>
                <div v-if="calculatedSummary.additional_charges > 0" class="flex justify-between text-sm">
                  <span class="text-gray-600">Additional Charges:</span>
                  <span class="font-medium">{{ formatCurrency(calculatedSummary.additional_charges) }}</span>
                </div>
                <hr class="border-gray-300">
                <div class="flex justify-between text-lg font-bold">
                  <span class="text-gray-900">Grand Total:</span>
                  <span class="text-green-600">{{ formatCurrency(calculatedSummary.grand_total) }}</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>
