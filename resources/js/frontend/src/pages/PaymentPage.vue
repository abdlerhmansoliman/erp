<template>
  <div class="p-6">
    <div class="max-w-3xl mx-auto">
      <div class="bg-white shadow rounded p-6">
        <h1 class="text-2xl font-semibold mb-4">Payment</h1>

        <div class="mb-4">
          <div class="text-gray-700">Total Amount</div>
          <div class="text-3xl font-bold">{{ formatCurrency(totalAmount) }}</div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
          <div class="flex items-center gap-6">
            <label class="inline-flex items-center">
              <input type="radio" class="form-radio" value="cash" v-model="method" />
              <span class="ml-2">Cash</span>
            </label>
            <label class="inline-flex items-center">
              <input type="radio" class="form-radio" value="visa" v-model="method" />
              <span class="ml-2">Visa / Stripe</span>
            </label>
          </div>
        </div>

        <div v-if="method === 'cash'" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
            <input v-model.number="cashAmount" type="number" min="0" :max="totalAmount" step="0.01" class="w-full border rounded px-3 py-2" />
          </div>
          <button @click="submitCash" :disabled="loading" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-50">
            <span v-if="!loading">Confirm Cash Payment</span>
            <span v-else>Processing...</span>
          </button>
        </div>

        <div v-if="method === 'visa'" class="space-y-4">
          <div id="card-element" class="border rounded px-3 py-2 focus-within:ring-2 focus-within:ring-indigo-500"></div>
          <button @click="submitStripe" :disabled="loading || !stripe || !elements" class="px-4 py-2 bg-indigo-600 text-white rounded disabled:opacity-50">
            <span v-if="!loading">Pay with Card</span>
            <span v-else>Processing...</span>
          </button>
        </div>

        <div v-if="message" class="mt-6">
          <div :class="messageClass" class="p-3 rounded">
            {{ message }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js'
import api from '@/plugins/axios'

export default {
  name: 'PaymentPage',
  data() {
    return {
      invoiceId: this.$route.query.invoice_id ? Number(this.$route.query.invoice_id) : null,
      totalAmount: 0,
      paymentId: null,
      method: 'visa',
      cashAmount: 0,
      stripe: null,
      elements: null,
      cardElement: null,
      clientSecret: null,
      loading: false,
      message: '',
      messageType: 'info',
      pollTimer: null,
      paymentMethodId: 7,
    }
  },
  computed: {
    messageClass() {
      return {
        'bg-green-100 text-green-800': this.messageType === 'success',
        'bg-red-100 text-red-800': this.messageType === 'error',
        'bg-blue-100 text-blue-800': this.messageType === 'info',
      }
    }
  },
  methods: {
    formatCurrency(v) {
      return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(v || 0))
    },
    async fetchInvoice() {
      if (!this.invoiceId) return
      const { data } = await api.get(`/sales/${this.invoiceId}`)
      this.totalAmount = Number(data.grand_total || 0)
      this.cashAmount = this.totalAmount
    },
    async initStripe() {
      // Use transactions module to create a transaction and get client_secret
      const payload = {
        payable_type: 'App\\Models\\SalesInvoice',
        payable_id: this.invoiceId,
        amount: this.totalAmount,
        payment_method: 'Stripe',
      }
      const { data } = await api.post('/transactions/pay', payload)
      this.clientSecret = data.client_secret
      this.transactionId = data.transaction_id
      this.stripe = await loadStripe(data.publishable_key)
      this.elements = this.stripe.elements({ appearance: { theme: 'stripe' } })
      if (this.cardElement) {
        try { this.cardElement.unmount() } catch (_) {}
        this.cardElement = null
      }
      this.$nextTick(() => {
        this.cardElement = this.elements.create('card', {
          hidePostalCode: true,
        })
        this.cardElement.mount('#card-element')
        // Try focusing card to allow typing immediately
        setTimeout(() => {
          const iframe = document.querySelector('#card-element iframe')
          if (iframe) iframe.focus()
        }, 50)
      })
    },
    async submitCash() {
      try {
        this.loading = true
        const { data } = await api.post('/transactions/cash', {
          payable_type: 'App\\Models\\SalesInvoice',
          payable_id: this.invoiceId,
          amount: this.cashAmount,
        })
        this.message = 'Payment recorded successfully.'
        this.messageType = 'success'
      } catch (e) {
        this.message = e.response?.data?.message || 'Cash payment failed'
        this.messageType = 'error'
      } finally {
        this.loading = false
      }
    },
    async submitStripe() {
      if (!this.clientSecret || !this.stripe || !this.cardElement) return
      try {
        this.loading = true
        const { error, paymentIntent } = await this.stripe.confirmCardPayment(this.clientSecret, {
          payment_method: {
            card: this.cardElement,
          },
        })
        if (error) {
          this.message = error.message || 'Payment failed'
          this.messageType = 'error'
          return
        }
        // Confirm endpoint if needed by backend
        try {
          await api.post(`/transactions/${this.transactionId}/confirm`, {
            payment_intent_id: paymentIntent.id,
            status: paymentIntent.status,
          })
        } catch (_) {}
        this.message = 'Payment processing...'
        this.messageType = 'info'
        this.startPolling()
      } catch (e) {
        this.message = e.message || 'Payment failed'
        this.messageType = 'error'
      } finally {
        this.loading = false
      }
    },
    async fetchPaymentStatus() {
      if (!this.transactionId) return
      const { data } = await api.get(`/transactions/${this.transactionId}`)
      const status = data.status
      if (status === 'succeeded') {
        this.message = 'Payment Successful'
        this.messageType = 'success'
        this.stopPolling()
      } else if (status === 'failed') {
        this.message = 'Payment Failed'
        this.messageType = 'error'
        this.stopPolling()
      }
    },
    startPolling() {
      this.stopPolling()
      this.pollTimer = setInterval(this.fetchPaymentStatus, 3000)
    },
    stopPolling() {
      if (this.pollTimer) {
        clearInterval(this.pollTimer)
        this.pollTimer = null
      }
    }
  },
  watch: {
    method(newVal) {
      if (newVal === 'visa') {
        this.initStripe()
      }
    }
  },
  async mounted() {
    await this.fetchInvoice()
    if (this.method === 'visa') {
      await this.initStripe()
    }
  },
  beforeUnmount() {
    this.stopPolling()
    if (this.cardElement) {
      try { this.cardElement.unmount() } catch (_) {}
    }
  }
}
</script>

<style scoped>
</style>


