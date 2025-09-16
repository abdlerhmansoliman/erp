// composables/useInvoice.js
import { ref, computed } from 'vue';
import api from '@/plugins/axios';
import { useToast } from "vue-toastification";
import { useRouter } from 'vue-router';

export function useInvoice(invoiceType = 'purchase') {
  const router = useRouter();
  const toast = useToast();

  // Reactive State
  const selectedSupplier = ref(null);
  const selectedWarehouse = ref(null);
  const date = ref(new Date().toISOString().slice(0,10));
  const status = ref('draft');
  const invoiceNumber = ref('');
  const invoiceItems = ref([]);
  const suppliers = ref([]);
  const warehouses = ref([]);
  const taxes = ref([]);
  const shippingCost = ref(0);
  const paymentStatus = ref('paid');
  const dueDate = ref(new Date().toISOString().slice(0,10));
  const paidAmount = ref(0);

  // Configuration based on invoice type
  const config = computed(() => {
    const isPurchase = invoiceType === 'purchase';
    
    return {
      createEndpoint: isPurchase ? '/purchases/create' : '/sales/create',
      saveEndpoint: isPurchase ? '/purchases' : '/sales',
      productSearchUrl: '/api/products/search',
      partyLabel: isPurchase ? 'المورد' : 'العميل',
      saveButtonText: isPurchase ? 'حفظ فاتورة الشراء' : 'حفظ فاتورة البيع',
      successMessage: isPurchase ? 'تم حفظ فاتورة الشراء بنجاح' : 'تم حفظ فاتورة البيع بنجاح',
      partyRequiredMessage: isPurchase ? 'الرجاء اختيار المورد' : 'الرجاء اختيار العميل',
      redirectPath: isPurchase ? '/purchases' : '/sales',
      partyIdKey: isPurchase ? 'supplier_id' : 'customer_id',
      partiesKey: isPurchase ? 'suppliers' : 'customers',
      availableStatuses: isPurchase ? [
        { value: 'draft', label: 'مسودة' },
        { value: 'ordered', label: 'تم الطلب' },
        { value: 'received', label: 'تم الاستلام' },
        { value: 'cancelled', label: 'ملغي' }
      ] : [
        { value: 'draft', label: 'مسودة' },
        { value: 'pending', label: 'في الانتظار' },
        { value: 'confirmed', label: 'مؤكد' },
        { value: 'shipped', label: 'تم الشحن' },
        { value: 'delivered', label: 'تم التسليم' },
        { value: 'cancelled', label: 'ملغي' }
      ]
    };
  });

  // Computed Properties
  const invoiceSummary = computed(() => {
    let subTotal = 0;
    let totalDiscount = 0;
    let totalTax = 0;
    
    invoiceItems.value.forEach(item => {
      subTotal += item.subtotal || 0;
      totalDiscount += item.discount_amount || 0;
      totalTax += item.tax_amount || 0;
    });
    
    return {
      subTotal,
      totalDiscount,
      totalTax,
      grandTotal: subTotal - totalDiscount + totalTax + (Number(shippingCost.value) || 0)
    };
  });

  const getTotalQuantity = computed(() => {
    return invoiceItems.value.reduce((total, item) => total + (item.qty || 0), 0);
  });

  const getTotalEffectiveQuantity = computed(() => {
    return invoiceItems.value.reduce((total, item) => total + (item.effectiveQty || 0), 0);
  });

  const usedTaxes = computed(() => {
    const taxTotals = {};
    
    taxes.value.forEach(tax => {
      taxTotals[tax.id] = {
        id: tax.id,
        name: tax.name,
        rate: tax.rate,
        total: 0
      };
    });

    invoiceItems.value.forEach(item => {
      if (item.tax_id && taxTotals[item.tax_id]) {
        taxTotals[item.tax_id].total += item.tax_amount || 0;
      }
    });
    
    return Object.values(taxTotals).filter(tax => tax.total > 0);
  });

  // Methods
  const initializeData = async () => {
    try {
      
      const { data } = await api.get(config.value.createEndpoint);
      
      
      // Handle different response structures
      if (data.data) {
        suppliers.value = data.data[config.value.partiesKey] || [];
        warehouses.value = data.data.warehouses || [];
      } else {
        suppliers.value = data[config.value.partiesKey] || [];
        warehouses.value = data.warehouses || [];
      }


      if (warehouses.value.length > 0) {
        selectedWarehouse.value = warehouses.value[0];
      }
    } catch (error) { 
      console.error('Error fetching initial data:', error);
      toast.error('Error fetching initial data: ' + error.message); 
    }

    try {
      const { data } = await api.get('/taxes');
      taxes.value = data.data || data || [];
    } catch (error) {
      console.error('Error fetching taxes:', error);
      toast.error('Error fetching taxes: ' + error.message);
    }
  };

  const handleSelectProduct = (product) => {
    if (!invoiceItems.value.find(item => item.id === product.id)) {
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
    }
  };

  const removeItem = (id) => {
    invoiceItems.value = invoiceItems.value.filter(item => item.id !== id);
  };

  const updateItem = (id, key, value) => {
    const item = invoiceItems.value.find(i => i.id === id);
    if (item) {
      const numValue = Math.max(0, Number(value) || 0);
      item[key] = numValue;
      calculateItemTotals(item);
    }
  };

  const handleTaxChange = (itemId, taxId) => {
    const item = invoiceItems.value.find(i => i.id === itemId);
    const selectedTax = taxes.value.find(t => t.id === Number(taxId));
    
    if (item) {
      item.tax_id = selectedTax ? selectedTax.id : null;
      item.tax_rate = selectedTax ? selectedTax.rate : 0;
      calculateItemTotals(item);
    }
  };

  const calculateItemTotals = (item) => {
    const qty = Math.max(0, item.qty || 0);
    const unitPrice = item.price || 0;

    const subtotal = unitPrice * qty;
    const tax_amount = item.tax_rate ? (subtotal * item.tax_rate) / 100 : 0;
    const discount_amount = Math.min(item.discount || 0, subtotal + tax_amount);
    const total = subtotal + tax_amount - discount_amount;

    item.subtotal = subtotal;
    item.tax_amount = tax_amount;
    item.discount_amount = discount_amount;
    item.total = total;
  };

const saveInvoice = async () => {
  if (!selectedSupplier.value) {
    toast.error(config.value.partyRequiredMessage);
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
const invoiceData = {
  [config.value.partyIdKey]: selectedSupplier.value.id,
  warehouse_id: selectedWarehouse.value.id,
  status: status.value,
  date: date.value,
  sub_total: invoiceSummary.value.subTotal,
  discount_amount: invoiceSummary.value.totalDiscount,
  tax_amount: invoiceSummary.value.totalTax,
  grand_total: invoiceSummary.value.grandTotal,
  shipping_cost: Number(shippingCost.value) || 0,
  payment_status: paymentStatus.value,
  due_date: ['partial', 'due'].includes(paymentStatus.value) ? dueDate.value : null,
  paid_amount: paymentStatus.value === 'paid' ? invoiceSummary.value.grandTotal : (paymentStatus.value === 'partial' ? Number(paidAmount.value) : 0),
  payment_date: ['paid', 'partial'].includes(paymentStatus.value) ? date.value : null,
  items: invoiceItems.value.map(item => ({
    product_id: item.id,
    quantity: item.qty,
    unit_price: item.price,
    discount_amount: item.discount_amount || 0,
    tax_id: item.tax_id || null,
    tax_amount: item.tax_amount || 0,
    total_price: item.total,
    net_price: item.subtotal - (item.discount_amount || 0) + (item.tax_amount || 0)
  }))
};


    const { data } = await api.post(config.value.saveEndpoint, invoiceData);

    if (data.status === 'success' || (data.data && data.data.id)) {
      toast.success(data.message || config.value.successMessage);
      resetForm();
      router.push(config.value.redirectPath);
    } else {
      toast.error(data.message || 'حدث خطأ أثناء حفظ الفاتورة');
    }
  } catch (error) {
    // عرض رسالة OutOfStockException فقط
    if (error.response?.data?.message) {
      toast.error(error.response.data.message);
    } else {
      toast.error(error.message || 'حدث خطأ أثناء حفظ الفاتورة');
    }
  }
};


  const resetForm = () => {
    selectedSupplier.value = null;
    date.value = new Date().toISOString().slice(0,10);
    status.value = 'draft';
    invoiceItems.value = [];
  };

  return {
    // State
    selectedSupplier,
    selectedWarehouse,
    date,
    status,
    invoiceNumber,
    invoiceItems,
    suppliers,
    warehouses,
    taxes,
    shippingCost,
    paymentStatus,
    dueDate,
    paidAmount,

    // Computed
    config,
    invoiceSummary,
    getTotalQuantity,
    getTotalEffectiveQuantity,
    usedTaxes,
    
    // Methods
    initializeData,
    handleSelectProduct,
    removeItem,
    updateItem,
    handleTaxChange,
    calculateItemTotals,
    saveInvoice,
    resetForm
  };
}