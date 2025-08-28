<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  // Items array
  items: {
    type: Array,
    default: () => []
  },
  
  // Summary object (optional - can be calculated from items)
  summary: {
    type: Object,
    default: null
  },
  
  // Display options
  title: {
    type: String,
    default: 'Invoice Items'
  },
  
  showTax: {
    type: Boolean,
    default: true
  },
  
  showDiscount: {
    type: Boolean,
    default: true
  },
  
  // Currency settings
  currency: {
    type: String,
    default: '$'
  },
  
  currencyPosition: {
    type: String,
    default: 'before', // 'before' or 'after'
    validator: (value) => ['before', 'after'].includes(value)
  },
  
  // Localization
  locale: {
    type: String,
    default: 'en-US'
  },
  
  emptyMessage: {
    type: String,
    default: 'No items found'
  }
})

// Computed properties
const columnCount = computed(() => {
  let count = 5 // #, Product, Quantity, Unit Price, Total
  if (props.showTax) count++
  if (props.showDiscount) count++
  return count
})

const calculatedSummary = computed(() => {
  if (props.summary) {
    return {
      subtotal: props.summary.subtotal || 0,
      total_tax: props.summary.total_tax || props.summary.tax_amount || 0,
      total_discount: props.summary.total_discount || props.summary.discount_amount || 0,
      grand_total: props.summary.grand_total || props.summary.total || 0
    }
  }
  
  // Calculate from items if no summary provided
  let subtotal = 0
  let totalTax = 0
  let totalDiscount = 0
  
  props.items.forEach(item => {
    const unitPrice = parseFloat(item.unit_price || item.price || 0)
    const quantity = parseFloat(item.quantity || 0)
    const itemSubtotal = unitPrice * quantity
    
    subtotal += itemSubtotal
    totalTax += parseFloat(item.tax_amount || 0)
    totalDiscount += parseFloat(item.discount_amount || 0)
  })
  
  const grandTotal = subtotal + totalTax - totalDiscount
  
  return {
    subtotal,
    total_tax: totalTax,
    total_discount: totalDiscount,
    grand_total: grandTotal
  }
})

// Helper functions
function getProductName(item) {
  return item.product?.name || 
         item.product_name || 
         item.name || 
         'Unknown Product'
}

function getProductCode(item) {
  return item.product?.code || 
         item.product_code || 
         item.code || 
         null
}

function getProductDescription(item) {
  return item.product?.description || 
         item.product_description || 
         item.description || 
         null
}

function getUnit(item) {
  return item.product?.unit || 
         item.unit || 
         null
}

function calculateLineTotal(item) {
  const unitPrice = parseFloat(item.unit_price || item.price || 0)
  const quantity = parseFloat(item.quantity || 0)
  const taxAmount = parseFloat(item.tax_amount || 0)
  const discountAmount = parseFloat(item.discount_amount || 0)
  
  return (unitPrice * quantity) + taxAmount - discountAmount
}

function formatNumber(value, decimals = 0) {
  return new Intl.NumberFormat(props.locale, {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  }).format(value)
}

function formatCurrency(value) {
  const numValue = parseFloat(value || 0)
  
  if (props.currencyPosition === 'after') {
    return `${formatNumber(numValue, 2)} ${props.currency}`
  }
  
  return `${props.currency}${formatNumber(numValue, 2)}`
}
</script>



<template>
  <div class="bg-white rounded-lg shadow-sm border">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b bg-gray-50">
      <h3 class="text-lg font-semibold text-gray-900">
        {{ title || 'Invoice Items' }}
      </h3>
      <p class="text-sm text-gray-600 mt-1" v-if="items && items.length > 0">
        {{ items.length }} item{{ items.length !== 1 ? 's' : '' }}
      </p>
    </div>

    <!-- Items Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              #
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Product
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Quantity
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Unit Price
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" v-if="showTax">
              Tax Amount
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" v-if="showDiscount">
              Discount
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Total
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <!-- Empty State -->
          <tr v-if="!items || items.length === 0">
            <td :colspan="columnCount" class="px-6 py-8 text-center">
              <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.712-3.714M14 40v-4a9.971 9.971 0 01.712-3.714M8 16a6 6 0 1112 0v3h3a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2h3V16z" />
                </svg>
                <p class="text-lg font-medium">{{ emptyMessage }}</p>
              </div>
            </td>
          </tr>
          
          <!-- Items -->
          <tr v-else v-for="(item, index) in items" :key="item.id || index" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ index + 1 }}
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-col">
                <div class="text-sm font-medium text-gray-900">
                  {{ getProductName(item) }}
                </div>
                <div class="text-sm text-gray-500" v-if="getProductCode(item)">
                  Code: {{ getProductCode(item) }}
                </div>
                <div class="text-xs text-gray-400 mt-1" v-if="getProductDescription(item)">
                  {{ getProductDescription(item) }}
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
              {{ formatNumber(item.quantity || 0) }}
              <span class="text-xs text-gray-500 ml-1" v-if="getUnit(item)">
                {{ getUnit(item) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
              {{ formatCurrency(item.unit_price || item.price || 0) }}
            </td>
            <td v-if="showTax" class="px-6 py-4 whitespace-nowrap text-sm text-right">
              <span class="text-gray-900">{{ formatCurrency(item.tax_amount || 0) }}</span>
              <div class="text-xs text-gray-500" v-if="item.tax_rate">
                ({{ item.tax_rate }}%)
              </div>
            </td>
            <td v-if="showDiscount" class="px-6 py-4 whitespace-nowrap text-sm text-right">
              <span class="text-red-600">{{ formatCurrency(item.discount_amount || 0) }}</span>
              <div class="text-xs text-gray-500" v-if="item.discount_rate">
                ({{ item.discount_rate }}%)
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
              {{ formatCurrency(item.total || item.line_total || calculateLineTotal(item)) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Invoice Summary -->
    <div class="border-t bg-gray-50 px-6 py-4" v-if="summary || items.length > 0">
      <div class="flex justify-end">
        <div class="w-full max-w-sm">
          <div class="space-y-2">
            <!-- Subtotal -->
            <div class="flex justify-between text-sm">
              <span class="text-gray-600">Subtotal:</span>
              <span class="font-medium">{{ formatCurrency(calculatedSummary.subtotal) }}</span>
            </div>

            <!-- Total Tax -->
            <div v-if="showTax && calculatedSummary.total_tax > 0" class="flex justify-between text-sm">
              <span class="text-gray-600">Total Tax:</span>
              <span class="font-medium">{{ formatCurrency(calculatedSummary.total_tax) }}</span>
            </div>

            <!-- Total Discount -->
            <div v-if="showDiscount && calculatedSummary.total_discount > 0" class="flex justify-between text-sm text-red-600">
              <span>Total Discount:</span>
              <span class="font-medium">-{{ formatCurrency(calculatedSummary.total_discount) }}</span>
            </div>

            <!-- Shipping (if applicable) -->
            <div v-if="summary && summary.shipping_amount > 0" class="flex justify-between text-sm">
              <span class="text-gray-600">Shipping:</span>
              <span class="font-medium">{{ formatCurrency(summary.shipping_amount) }}</span>
            </div>

            <!-- Additional Charges -->
            <div v-if="summary && summary.additional_charges > 0" class="flex justify-between text-sm">
              <span class="text-gray-600">Additional Charges:</span>
              <span class="font-medium">{{ formatCurrency(summary.additional_charges) }}</span>
            </div>

            <hr class="border-gray-300">

            <!-- Grand Total -->
            <div class="flex justify-between text-lg font-bold">
              <span class="text-gray-900">Grand Total:</span>
              <span class="text-green-600">{{ formatCurrency(calculatedSummary.grand_total) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

