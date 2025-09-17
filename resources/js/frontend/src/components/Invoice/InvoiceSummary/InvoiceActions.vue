<script setup>
import { useRouter, useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/plugins/axios'

const props = defineProps({
  invoiceId: { type: [String, Number], required: true },
  type: { type: String, default: 'sales' }, // sales | purchases | returns
  showReturn: { type: Boolean, default: true }
})

const router = useRouter()
const route = useRoute()
const toast = useToast()

// Actions
function goBack() {
  router.back()
}

async function downloadPdf() {
  try {
    const response = await api.get(`/${props.type}/${props.invoiceId}/pdf`, {
      responseType: 'blob'
    })
    const blob = new Blob([response.data], { type: 'application/pdf' })
    const link = document.createElement('a')
    link.href = URL.createObjectURL(blob)
    link.download = `Invoice-${props.invoiceId}.pdf`
    link.click()
    URL.revokeObjectURL(link.href)
  } catch (error) {
    console.error('Error downloading PDF:', error)
    toast.error('Failed to download PDF')
  }
}

function goToCreateReturn() {
  const returnType = props.type.includes('sales') ? 'sales' : 'purchase'
  router.push(`/returns/${returnType}/create/${props.invoiceId}`)
}
</script>

<template>
  <div class="flex justify-end space-x-3">
    <button @click="goBack"
      class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
      Back to List
    </button>

    <button @click="downloadPdf"
      class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
      Download PDF
    </button>

    <button v-if="showReturn" @click="goToCreateReturn"
      class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
      Make Returns
    </button>
  </div>
</template>
