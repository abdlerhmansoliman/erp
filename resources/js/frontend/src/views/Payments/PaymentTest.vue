<template>
  <div class="payment-test">
    <div class="container py-4">
      <div class="row">
        <!-- Payment Form -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Test Payment</h5>
            </div>
            <div class="card-body">
              <StripePayment
                payable-type="App\Models\SalesInvoice"
                :payable-id="123"
                :payment-method-id="7"
                @payment-success="onPaymentSuccess"
              />
            </div>
          </div>
        </div>

        <!-- Webhook Events -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Webhook Events</h5>
            </div>
            <div class="card-body">
              <div v-if="webhookEvents.length" class="webhook-events">
                <div v-for="event in webhookEvents" :key="event.id" class="webhook-event mb-3">
                  <div class="d-flex justify-content-between">
                    <strong>{{ event.type }}</strong>
                    <small>{{ formatDate(event.created_at) }}</small>
                  </div>
                  <pre class="mt-2"><code>{{ JSON.stringify(event.data, null, 2) }}</code></pre>
                </div>
              </div>
              <div v-else class="text-center py-3">
                No webhook events yet
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import StripePayment from '@/components/StripePayment.vue'
import axios from 'axios'

export default {
  name: 'PaymentTest',
  
  components: {
    StripePayment
  },

  data() {
    return {
      webhookEvents: [],
      isDev: process.env.NODE_ENV === 'development',
      pollInterval: null
    }
  },

  methods: {
    onPaymentSuccess(result) {
      console.log('Payment success event received:', result);
      
      // Add safety check for result
      if (!result) {
        console.error('Payment success event received but result is undefined');
        return;
      }
      
      // Show success toast
      this.$toast.success('Payment processed successfully!');
      
      // Safely get payment details from the result
      const payment = result.payment || {};
      const paymentIntent = result.paymentIntent || {};
      
      console.log('Payment details:', { payment, paymentIntent });
      
      // Refresh webhook events if available
      if (this.pollInterval) {
        this.fetchWebhookEvents();
      }
      
      // Redirect to invoice after a short delay
      if (payment.payable_id) {
        setTimeout(() => {
          this.$router.push({ 
            name: 'SalesShow', 
            params: { id: payment.payable_id }
          });
        }, 2000);
      } else {
        console.warn('No payable_id found in payment object');
      }
    },

    async fetchWebhookEvents() {
      try {
        const { data } = await axios.get('/api/webhook-events')
        this.webhookEvents = data
      } catch (error) {
        console.error('Error fetching webhook events:', error)
      }
    },

    getEventBadgeClass(eventType) {
      const classes = {
        'payment_intent.succeeded': 'event-success',
        'payment_intent.payment_failed': 'event-error',
        'charge.succeeded': 'event-info',
        'payment_intent.created': 'event-warning',
        'payment_intent.processing': 'event-processing'
      }
      return classes[eventType] || 'event-default'
    },

    formatEventType(type) {
      return type.split('.').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
      ).join(' ')
    },

    formatDate(date) {
      return new Intl.DateTimeFormat('en-US', {
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        hour12: true
      }).format(new Date(date))
    }
  },

  mounted() {
    this.fetchWebhookEvents()
    this.pollInterval = setInterval(this.fetchWebhookEvents, 5000)
  },

  beforeUnmount() {
    if (this.pollInterval) {
      clearInterval(this.pollInterval)
    }
  }
}
</script>

<style scoped>
.webhook-events {
  max-height: 500px;
  overflow-y: auto;
}

.webhook-event pre {
  background: #f8f9fa;
  padding: 10px;
  border-radius: 4px;
  font-size: 12px;
  margin: 0;
}
</style>