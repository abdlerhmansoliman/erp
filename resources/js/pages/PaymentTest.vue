<template>
  <div class="container py-5">
    <h2 class="mb-4">Payment Test Page</h2>
    
    <StripePayment
      payable-type="App\Models\SalesInvoice"
      :payable-id="123"
      :payment-method-id="1"
      @payment-success="onPaymentSuccess"
    />

    <div v-if="webhookEvents.length" class="mt-5">
      <h3>Webhook Events</h3>
      <div class="webhook-events">
        <div v-for="event in webhookEvents" :key="event.id" class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">{{ event.type }}</h5>
            <pre class="card-text">{{ JSON.stringify(event.data, null, 2) }}</pre>
            <small class="text-muted">{{ new Date(event.created_at).toLocaleString() }}</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import StripePayment from '@/components/StripePayment.vue'

export default {
  name: 'PaymentTest',
  
  components: {
    StripePayment
  },

  data() {
    return {
      webhookEvents: []
    }
  },

  methods: {
    onPaymentSuccess(paymentIntent) {
      console.log('Payment successful:', paymentIntent);
    },

    // Poll for webhook events (in real app, use websockets instead)
    async pollWebhookEvents() {
      try {
        const { data } = await axios.get('/api/webhook-events');
        this.webhookEvents = data;
      } catch (error) {
        console.error('Error fetching webhook events:', error);
      }
    }
  },

  mounted() {
    // Poll every 5 seconds
    this.pollInterval = setInterval(this.pollWebhookEvents, 5000);
  },

  beforeUnmount() {
    if (this.pollInterval) {
      clearInterval(this.pollInterval);
    }
  }
}
</script>