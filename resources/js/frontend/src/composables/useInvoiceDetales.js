// src/composables/useInvoice.js
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/plugins/axios'

export function useInvoice(type = 'purchases') {
  const route = useRoute()
  const router = useRouter()
  const toast = useToast()

  const invoice = ref(null)
  const loading = ref(true)

const invoiceSummary = computed(() => {
  if (!invoice.value || !invoice.value.items) {
    return {
      subtotal: 0,
      total_discount: 0,
      total_tax: 0,
      shipping_amount: 0,
      additional_charges: 0,
      grand_total: 0,
    }
  }

  let subtotal = 0
  let totalDiscount = 0
  let totalTax = 0

  invoice.value.items.forEach(item => {
    subtotal += Number(item.total_price || 0)
    totalDiscount += Number(item.discount || 0)
    totalTax += Number(item.tax_amount || 0)
  })

  return {
    subtotal,
    total_discount: totalDiscount,
    total_tax: totalTax,
    shipping_amount: Number(invoice.value.shipping_amount || 0),
    additional_charges: Number(invoice.value.additional_charges || 0),
    grand_total: Number(invoice.value.grand_total || (subtotal - totalDiscount + totalTax)),
  }
})


  function getStatusClass(status) {
    const classes = {
      pending: 'bg-yellow-100 text-yellow-800',
      completed: 'bg-green-100 text-green-800',
      approved: 'bg-blue-100 text-blue-800',
      cancelled: 'bg-red-100 text-red-800',
      draft: 'bg-gray-100 text-gray-800',
    }
    return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-800'
  }

  function formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(parseFloat(value || 0))
  }

  function goBack() {
    router.push({ name: type }) // route name dynamic (purchases or sales)
  }

  function editInvoice() {
    router.push({ name: `${type}-edit`, params: { id: invoice.value.id } })
  }

  function printInvoice() {
    window.print()
  }

  async function downloadPdf() {
    try {
      const response = await api.get(`${type}/${route.params.id}/pdf`, {
        responseType: 'blob',
      })

      const blob = new Blob([response.data], { type: 'application/pdf' })
      const link = document.createElement('a')
      link.href = URL.createObjectURL(blob)
      link.download = `Invoice-${route.params.id}.pdf`
      link.click()
      URL.revokeObjectURL(link.href)
    } catch (error) {
      console.error('Error downloading PDF:', error)
      toast.error('Failed to download PDF')
    }
  }

  onMounted(async () => {
    try {
      const { data } = await api.get(`/${type}/${route.params.id}`)
      invoice.value = data.data
    } catch (error) {
      console.error(`Error fetching ${type} invoice:`, error)
      toast.error(`Failed to load ${type} invoice`)
    } finally {
      loading.value = false
    }
  })

  return {
    invoice,
    loading,
    invoiceSummary,
    getStatusClass,
    formatCurrency,
    goBack,
    editInvoice,
    printInvoice,
    downloadPdf,
  }
}
