import { ref, computed } from 'vue';

export function useInvoice() {
  const invoiceItems = ref([]);
  const invoiceSummary = ref({
    subTotal: 0,
    totalDiscount: 0,
    totalTax: 0,
    grandTotal: 0
  });

  // Computed properties
  const getTotalQuantity = computed(() => {
    return invoiceItems.value.reduce((total, item) => total + (item.qty || 0), 0);
  });

  const getTotalEffectiveQuantity = computed(() => {
    return invoiceItems.value.reduce((total, item) => total + (item.effectiveQty || 0), 0);
  });

  const usedTaxes = computed(() => {
    const taxTotals = {};
    
    // Calculate totals for each tax
    invoiceItems.value.forEach(item => {
      if (item.tax_id) {
        if (!taxTotals[item.tax_id]) {
          taxTotals[item.tax_id] = {
            id: item.tax_id,
            name: item.tax_name,
            rate: item.tax_rate,
            total: 0
          };
        }
        taxTotals[item.tax_id].total += item.tax_amount || 0;
      }
    });
    
    return Object.values(taxTotals).filter(tax => tax.total > 0);
  });

  // Methods
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
    item.effectiveQty = effectiveQty;
  }

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

  function updateItem(id, key, value) {
    const item = invoiceItems.value.find(i => i.id === id);
    if (item) {
      const numValue = Math.max(0, Number(value) || 0);
      item[key] = numValue;
      calculateItemTotals(item);
      calculateInvoiceSummary();
    }
  }

  function removeItem(id) {
    invoiceItems.value = invoiceItems.value.filter(item => item.id !== id);
    calculateInvoiceSummary();
  }

  function addItem(product, tax = null) {
    if (!invoiceItems.value.find(item => item.id === product.id)) {
      const newItem = {
        id: product.id,
        name: product.name,
        product_code: product.product_code,
        qty: 1,
        price: product.price || 0,
        discount: 0,
        tax_id: product.tax_id || null,
        tax_name: tax?.name,
        tax_rate: tax?.rate || 0,
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

  return {
    invoiceItems,
    invoiceSummary,
    getTotalQuantity,
    getTotalEffectiveQuantity,
    usedTaxes,
    calculateItemTotals,
    calculateInvoiceSummary,
    updateItem,
    removeItem,
    addItem
  };
}
