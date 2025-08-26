<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '@/plugins/axios';
import InvoiceHeader from '@/components/Invoice/InvoiceHeader.vue';
import ProductSearch from '@/components/Invoice/ProductSearch.vue';
import InvoiceItemsTable from '@/components/Invoice/InvoiceItemsTable.vue';
import InvoiceSummary from '@/components/Invoice/InvoiceSummary/InvoiceSummary.vue';
import { useToast } from "vue-toastification"
import { useRouter } from 'vue-router'


const router = useRouter()
const toast = useToast()
const selectedSupplier = ref(null);
const selectedWarehouse = ref(null);
const date = ref(new Date().toISOString().slice(0,10));
const status = ref('draft');
const invoiceNumber = ref('');
const invoiceItems = ref([]);
const suppliers = ref([]);
const warehouses = ref([]);
const taxes = ref([]);

// Define available statuses
const availableStatuses = [
  { value: 'draft', label: 'مسودة' },
  { value: 'ordered', label: 'تم الطلب' },
  { value: 'received', label: 'تم الاستلام' },
  { value: 'cancelled', label: 'ملغي' }
];

// Invoice totals
const invoiceSummary = ref({
  subTotal: 0,
  totalDiscount: 0,
  totalTax: 0,
  grandTotal: 0
});

const headers = [
  { text: "اسم المنتج", value: "name" },
  { text: "كود المنتج", value: "product_code" },
  { text: "الكمية", value: "qty" },
  { text: "السعر", value: "price" },
  { text: "الخصم", value: "discount" },
  { text: "قيمة الخصم", value: "discount_amount" },
  { text: "الضريبة", value: "tax" },
  { text: "قيمة الضريبة", value: "tax_amount" },
  { text: "المجموع الفرعي", value: "subtotal" },
  { text: "الإجمالي", value: "total" },
  { text: "الإجراءات", value: "actions" }
];

// جلب الموردين
onMounted(async () => {
  try {
    // Fetch initial data from create endpoint
    const { data } = await api.get('/purchases/create');
    suppliers.value = data.data.suppliers;
    warehouses.value = data.data.warehouses;
    // Set default warehouse if available
    if (warehouses.value.length > 0) {
      selectedWarehouse.value = warehouses.value[0];
    }
  } catch (error) { 
    toast.error('Error fetching initial data:', error); 
  }

  // Fetch taxes
  try {
    const { data } = await api.get('/taxes');
    taxes.value = data.data;
  } catch (error) {
    toast.error('Error fetching taxes:', error);
  }
});

function handleSelectProduct(product) {
  if (!invoiceItems.value.find(item => item.id === product.id)) {
    // Get the product's assigned tax if it has one
    const productTax = taxes.value.find(tax => tax.id === product.tax_id);
    
    const newItem = {
      id: product.id,
      name: product.name,
      product_code: product.product_code,
      qty: 1,
      price: product.price || 0,
      discount: 0,
      tax_id: product.tax_id || null,
      tax_rate: productTax ? productTax.rate : 0,
      tax_amount: 0,
      discount_amount: 0,
      subtotal: product.price || 0,
      total: product.price || 0
    };
    
    invoiceItems.value.push(newItem);
    calculateItemTotals(newItem);
    calculateInvoiceSummary();
  }
}

function removeItem(id) {
  invoiceItems.value = invoiceItems.value.filter(item => item.id !== id);
}

// تحديث قيمة داخل الجدول (qty/price/discount/tax)
function updateItem(id, key, value) {
  const item = invoiceItems.value.find(i => i.id === id);
  if (item) {
    // Ensure the value is a number and not less than 0
    const numValue = Math.max(0, Number(value) || 0);
    item[key] = numValue;
    
    calculateItemTotals(item);
    calculateInvoiceSummary();
  }
}

function handleTaxChange(itemId, taxId) {
  const item = invoiceItems.value.find(i => i.id === itemId);
  const selectedTax = taxes.value.find(t => t.id === Number(taxId));
  
  if (item) {
    item.tax_id = selectedTax ? selectedTax.id : null;
    item.tax_rate = selectedTax ? selectedTax.rate : 0;
    calculateItemTotals(item);
    calculateInvoiceSummary();
  }
}

function calculateItemTotals(item) {
  // Get the effective quantity after discount
  const effectiveQty = Math.max(0, (item.qty || 0) - (item.discount || 0));
  
  // Calculate subtotal based on effective quantity
  const subtotal = (item.price || 0) * effectiveQty;
  
  // Calculate the discount amount
  const discount_amount = (item.price || 0) * (item.discount || 0);
  
  // Calculate tax amount on the subtotal
  const tax_amount = item.tax_rate ? (subtotal * item.tax_rate) / 100 : 0;
  
  // Update item totals
  item.subtotal = subtotal;
  item.total = subtotal - discount_amount + tax_amount;
  item.discount_amount = discount_amount;
  item.tax_amount = tax_amount;
  item.effectiveQty = effectiveQty; // Store effective quantity for display
}


// Computed property for total quantity
const getTotalQuantity = computed(() => {
  return invoiceItems.value.reduce((total, item) => total + (item.qty || 0), 0);
});

const getTotalEffectiveQuantity = computed(() => {
  return invoiceItems.value.reduce((total, item) => total + (item.effectiveQty || 0), 0);
});

// Computed property for used taxes with their totals
const usedTaxes = computed(() => {
  const taxTotals = {};
  
  // Initialize tax totals
  taxes.value.forEach(tax => {
    taxTotals[tax.id] = {
      id: tax.id,
      name: tax.name,
      rate: tax.rate,
      total: 0
    };
  });
  
  // Calculate totals for each tax
  invoiceItems.value.forEach(item => {
    if (item.tax_id && taxTotals[item.tax_id]) {
      taxTotals[item.tax_id].total += item.tax_amount || 0;
    }
  });
  
  // Return only used taxes
  return Object.values(taxTotals).filter(tax => tax.total > 0);
});

function calculateInvoiceSummary() {
  let subTotal = 0;
  let totalDiscount = 0;
  let totalTax = 0;
  
  invoiceItems.value.forEach(item => {
    subTotal += item.subtotal || 0;
    totalDiscount += item.discount_amount || 0;
    totalTax += item.tax_amount || 0;
  });
  
  invoiceSummary.value = {
    subTotal,
    totalDiscount,
    totalTax,
    grandTotal: subTotal - totalDiscount + totalTax
  };
}

async function savePurchase() {
  if (!selectedSupplier.value) {
    toast.error('الرجاء اختيار المورد');
    return;
  }

  if (!selectedWarehouse.value) {
    toast.error('الرجاء اختيار المستودع');
    return;
  }

  if (!invoiceItems.value.length) {
    toast.error('الرجاء إضافة منتجات للفاتورة');
    return;
  }

  try {
    const purchaseData = {
      supplier_id: selectedSupplier.value.id,
      warehouse_id: selectedWarehouse.value.id,
      status: status.value,
      date: date.value,
      sub_total: invoiceSummary.value.subTotal,
      discount_amount: invoiceSummary.value.totalDiscount,
      tax_amount: invoiceSummary.value.totalTax,
      grand_total: invoiceSummary.value.grandTotal,
      items: invoiceItems.value.map(item => ({
        product_id: item.id,
        quantity: item.qty,
        unit_price: item.price,
        discount_amount: item.discount_amount,
        tax_id: item.tax_id,
        tax_amount: item.tax_amount,
        total_price: item.total,
        net_price: item.subtotal - item.discount_amount + item.tax_amount
      }))
    };

    const { data } = await api.post('/purchases', purchaseData);
    
    if (data.status === 'success') {
      toast.success(data.message || 'تم حفظ الفاتورة بنجاح');
      // Reset form
      selectedSupplier.value = null;
      date.value = new Date().toISOString().slice(0,10);
      status.value = 'draft';
      invoiceItems.value = [];
      calculateInvoiceSummary();
      router.push('/purchases')
    } else {
      toast.error(data.message || 'حدث خطأ أثناء حفظ الفاتورة');
    }
  } catch (error) {
    console.error('Error saving purchase:', error);
    if (error.response?.data?.message) {
      toast.error(error.response.data.message);
    } else {
      toast.error('حدث خطأ أثناء حفظ الفاتورة');
    }
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
      :selected-warehouse="selectedWarehouse"
      :date="date"
      :status="status"
      @update-selected-party="selectedSupplier = $event"
      @update-selected-warehouse="selectedWarehouse = $event"
      @update-date="date = $event"
      @update-status="status = $event"
    />

    <!-- Product Search -->
    <ProductSearch
      api-url="/api/products/search"
      @select-product="handleSelectProduct"
    />

    <!-- Products Table -->
    <div v-if="invoiceItems.length" class="space-y-4">
      <InvoiceItemsTable
        :items="invoiceItems"
        :taxes="taxes"
        @update-item="updateItem"
        @remove-item="removeItem"
        @tax-change="handleTaxChange"
      />

      <!-- Summary Cards -->
      <InvoiceSummary
        :invoice-items="invoiceItems"
        :summary="invoiceSummary"
        :used-taxes="usedTaxes"
        :total-quantity="getTotalQuantity"
        :total-effective-quantity="getTotalEffectiveQuantity"
      />
      
      <!-- Submit Button -->
      <div class="flex justify-end mt-4">
        <button 
          @click="savePurchase"
          class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          حفظ الفاتورة
        </button>
      </div>
    </div>
  </div>
</template>
