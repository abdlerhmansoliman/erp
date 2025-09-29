<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import StripePayment from '@/components/StripePayment.vue'
import { useToast } from 'vue-toastification'

axios.defaults.withCredentials = true

const toast = useToast()
const route = useRoute()
const router = useRouter()

const invoiceId = computed(() => route.query.invoice_id)
const loading = ref(false)
const error = ref(null)
const payment = ref(null)

const methods = ref([])
const selectedMethodId = ref(null)
const cashAmount = ref('')

async function loadPayment() {
  if (!invoiceId.value) {
    error.value = 'Missing invoice_id'
    return
  }
  loading.value = true
  error.value = null
  try {
    const [{ data: pay }, { data: methodList }] = await Promise.all([
      axios.get(`/api/payments/by-invoice/${invoiceId.value}`),
      axios.get('/api/payment-methods')
    ])
    payment.value = pay
    // If already completed, inform and redirect immediately to avoid backend 409
    if ((payment.value?.status || '').toLowerCase() === 'succeeded') {
      redirectToSalesWithToast('error', 'Payment already completed for this invoice')
      return
    }
    methods.value = methodList
    const stripe = methods.value.find(m => (m.name || '').toLowerCase() === 'stripe')
    const cash = methods.value.find(m => (m.name || '').toLowerCase() === 'cash')
    selectedMethodId.value = stripe?.id || cash?.id || methods.value[0]?.id || null
    cashAmount.value = pay?.amount != null ? Number(pay.amount) : ''
  } catch (e) {
    error.value = e?.response?.data?.error || e?.message || 'Failed to load payment'
  } finally {
    loading.value = false
  }
}

function redirectToSalesWithToast(type, message) {
  if (type === 'success') {
    toast.success(message)
  } else {
    toast.error(message)
  }
  router.push({ name: 'sales' })
}

function handleSuccess(evt) {
  redirectToSalesWithToast('success', 'Payment completed successfully')
}

function handleStripeFailed(evt) {
  redirectToSalesWithToast('error', evt?.message || 'Payment failed')
}

async function payCash() {
  if (!payment.value) return
  const method = methods.value.find(m => m.id === selectedMethodId.value)
  if (!method || (method.name || '').toLowerCase() !== 'cash') return
  loading.value = true
  error.value = null
  try {
    const payload = {
      payment_id: payment.value.id,
      amount: Number(cashAmount.value),
      payment_method_id: selectedMethodId.value
    }
    const { data } = await axios.post('/api/transactions/pay', payload)
    if (data?.success) {
      redirectToSalesWithToast('success', 'Cash payment completed successfully')
    } else {
      throw new Error('Cash payment failed')
    }
  } catch (e) {
    const msg = e?.response?.data?.message || e?.message || 'Cash payment failed'
    redirectToSalesWithToast('error', msg)
  } finally {
    loading.value = false
  }
}

const isStripeSelected = computed(() => {
  const method = methods.value.find(m => m.id === selectedMethodId.value)
  return (method?.name || '').toLowerCase() === 'stripe'
})

onMounted(loadPayment)
</script>

<template>
  <div class="p-4 space-y-4">
    <h2 class="text-xl font-semibold">Payment</h2>

    <div v-if="loading">Loading...</div>
    <div v-else-if="error" class="text-red-600">{{ error }}</div>

    <div v-else-if="payment">
      <div class="mb-4 p-3 border rounded space-y-2">
        <div class="font-medium">Invoice ID: {{ invoiceId }}</div>
        <div>Amount due: {{ payment.amount }}</div>
        <div>Status: {{ payment.status }}</div>
        <div class="flex items-center gap-2">
          <label class="font-medium">Method</label>
          <select v-model="selectedMethodId" class="border rounded px-2 py-1">
            <option v-for="m in methods" :key="m.id" :value="m.id">{{ m.name }}</option>
          </select>
        </div>
      </div>

      <div v-if="isStripeSelected">
        <StripePayment
          :payable-type="'App\\Models\\SalesInvoice'"
          :payable-id="invoiceId"
          :payment-id="payment.id"
          :preset-amount="Number(payment.amount)"
          @payment-success="handleSuccess"
          @payment-failed="handleStripeFailed"
        />
      </div>

      <div v-else class="max-w-md">
        <div class="mb-3">
          <label class="block mb-1">Amount</label>
          <input type="number" v-model="cashAmount" min="0.01" step="0.01" class="border rounded px-2 py-2 w-full" />
        </div>
        <button @click="payCash" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" :disabled="loading || !cashAmount">
          {{ loading ? 'Processing...' : 'Pay Cash' }}
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
