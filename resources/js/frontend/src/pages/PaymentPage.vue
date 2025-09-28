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
          <button @click="submitCash" :disabled="loading || !paymentId" class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-50">
            <span v-if="!loading">Confirm Cash Payment</span>
            <span v-else>Processing...</span>
          </button>
        </div>

        <div v-if="method === 'visa'" class="space-y-4">
          <div id="card-element" class="border rounded px-3 py-2 focus-within:ring-2 focus-within:ring-indigo-500"></div>
          <button @click="submitStripe" :disabled="loading || !stripe || !elements || !paymentId" class="px-4 py-2 bg-indigo-600 text-white rounded disabled:opacity-50">
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
      transactionId: null,
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
      if (!this.invoiceId) {
        this.message = 'Invalid invoice ID'
        this.messageType = 'error'
        return
      }
      
      try {
        const { data } = await api.get(`/sales/${this.invoiceId}`)
        
        // The actual invoice data is nested inside data.data
        const invoice = data.data || data
        
        // Debug: log the actual invoice object
        console.log('Full invoice data:', invoice)
        
        // Try different possible field names for the total amount
        this.totalAmount = Number(
          invoice.grand_total || 
          invoice.total_amount || 
          invoice.grandTotal || 
          invoice.totalAmount ||
          invoice.total ||
          0
        )
        
        this.cashAmount = this.totalAmount
        
        console.log('Invoice loaded:', { 
          id: this.invoiceId, 
          totalAmount: this.totalAmount,
          grand_total: invoice.grand_total,
          total_amount: invoice.total_amount,
          availableFields: Object.keys(invoice)
        })
        
        if (this.totalAmount < 0.01) {
          this.message = `Invoice amount is invalid (${this.totalAmount}). Please check the invoice data.`
          this.messageType = 'error'
          return
        }
        
        // Get payment record for this invoice
        await this.createPaymentRecord()
      } catch (error) {
        console.error('Error fetching invoice:', error)
        this.message = 'Failed to load invoice data'
        this.messageType = 'error'
      }
    },

    async createPaymentRecord() {
      try {
        // Get the payment_id for this invoice from your payment table
        // Assuming your SaleInvoiceService creates payments similar to PurchaseInvoiceService
        const { data } = await api.get(`/payments/by-invoice/${this.invoiceId}`)
        this.paymentId = data.id
        console.log('Found payment ID:', this.paymentId)
      } catch (error) {
        if (error.response?.status === 404) {
          console.error('Payment not found for invoice:', this.invoiceId)
          this.message = 'Payment record not found. Please ensure the invoice was saved with payment information.'
          this.messageType = 'error'
        } else {
          console.error('Error fetching payment record:', error)
          this.message = 'Error loading payment data'
          this.messageType = 'error'
        }
      }
    },

    async initStripe() {
      if (!this.paymentId) {
        console.error('Payment ID is required for Stripe initialization')
        return
      }

      if (!this.totalAmount || this.totalAmount < 0.01) {
        console.error('Invalid amount for Stripe initialization:', this.totalAmount)
        this.message = 'Invalid payment amount'
        this.messageType = 'error'
        return
      }

      try {
        const payload = {
          payment_id: this.paymentId,
          amount: this.totalAmount,
          payment_method_id: this.paymentMethodId,
        }

        console.log('Sending payload:', payload)

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
          this.cardElement = this.elements.create('card', { hidePostalCode: true })
          this.cardElement.mount('#card-element')
        })
      } catch (error) {
        console.error('Error initializing Stripe:', error)
        console.error('Error details:', error.response?.data)
        this.message = error.response?.data?.message || 'Failed to initialize payment'
        this.messageType = 'error'
      }
    },

    async submitCash() {
      if (!this.paymentId) {
        this.message = 'Payment ID not found'
        this.messageType = 'error'
        return
      }

      try {
        this.loading = true
        const payload = {
          payment_id: this.paymentId,
          amount: this.cashAmount,
          payment_method_id: this.paymentMethodId,
        }

        const { data } = await api.post('/transactions/cash', payload)

        this.message = 'Cash payment recorded successfully.'
        this.messageType = 'success'
        
        // Optionally redirect or update UI
        setTimeout(() => {
          this.$router.push('/sales') // Adjust route as needed
        }, 2000)
        
      } catch (e) {
        console.error('Cash payment error:', e)
        this.message = e.response?.data?.message || 'Cash payment failed'
        this.messageType = 'error'
      } finally {
        this.loading = false
      }
    },

    async submitStripe() {
      if (!this.clientSecret || !this.stripe || !this.cardElement) {
        this.message = 'Payment not properly initialized'
        this.messageType = 'error'
        return
      }
      
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
        console.error('Stripe payment error:', e)
        this.message = e.message || 'Payment failed'
        this.messageType = 'error'
      } finally {
        this.loading = false
      }
    },

    async fetchPaymentStatus() {
      if (!this.transactionId) return
      
      try {
        const { data } = await api.get(`/transactions/${this.transactionId}`)
        const status = data.status
        
        if (status === 'succeeded') {
          this.message = 'Payment Successful'
          this.messageType = 'success'
          this.stopPolling()
          
          // Optionally redirect after successful payment
          setTimeout(() => {
            this.$router.push('/sales') // Adjust route as needed
          }, 2000)
        } else if (status === 'failed') {
          this.message = 'Payment Failed'
          this.messageType = 'error'
          this.stopPolling()
        }
      } catch (error) {
        console.error('Error fetching payment status:', error)
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
      if (newVal === 'visa' && this.paymentId && this.totalAmount >= 0.01) {
        this.initStripe()
      }
    }
  },

  async mounted() {
    await this.fetchInvoice()
    
    // Only initialize Stripe if we have valid data
    if (this.method === 'visa' && this.paymentId && this.totalAmount >= 0.01) {
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