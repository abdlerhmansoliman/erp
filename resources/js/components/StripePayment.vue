<template>
  <div class="payment-form">
    <div v-if="loading" class="loading">
      Processing payment...
    </div>
    
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <div v-if="success" class="alert alert-success">
      Payment successful!
    </div>

    <form @submit.prevent="handleSubmit" v-if="!success">
      <div class="form-group mb-3">
        <label>Amount</label>
        <input 
          type="number" 
          class="form-control" 
          v-model="amount" 
          :disabled="loading"
          step="0.01"
          min="0.01"
          required
        >
      </div>

      <div class="card-element mb-3 p-3 border rounded">
        <!-- Stripe Card Element will be mounted here -->
      </div>

      <button 
        type="submit" 
        class="btn btn-primary" 
        :disabled="loading || !stripe"
      >
        Pay Now
      </button>
    </form>

    <!-- Payment Status -->
    <div v-if="paymentStatus" class="mt-3">
      <h4>Payment Status</h4>
      <pre>{{ paymentStatus }}</pre>
    </div>
  </div>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js';

export default {
  name: 'StripePayment',
  
  props: {
    payableType: {
      type: String,
      required: true
    },
    payableId: {
      type: [Number, String],
      required: true
    },
    paymentMethodId: {
      type: [Number, String],
      required: true
    }
  },

  data() {
    return {
      loading: false,
      error: null,
      success: false,
      amount: '',
      stripe: null,
      card: null,
      paymentStatus: null
    }
  },

  async mounted() {
    // Initialize Stripe
    this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);
    const elements = this.stripe.elements();
    
    // Create card element
    this.card = elements.create('card');
    this.card.mount('.card-element');

    // Handle validation errors
    this.card.addEventListener('change', (event) => {
      if (event.error) {
        this.error = event.error.message;
      } else {
        this.error = null;
      }
    });
  },

  methods: {
    async handleSubmit() {
      this.loading = true;
      this.error = null;
      
      try {
        // 1. Create payment intent
        const { data: paymentIntent } = await axios.post('/api/payments', {
          payable_type: this.payableType,
          payable_id: this.payableId,
          amount: parseFloat(this.amount),
          payment_method_id: this.paymentMethodId
        });

        // 2. Confirm card payment
        const result = await this.stripe.confirmCardPayment(paymentIntent.client_secret, {
          payment_method: {
            card: this.card,
          }
        });

        if (result.error) {
          throw new Error(result.error.message);
        }

        // 3. Handle result
        if (result.paymentIntent.status === 'succeeded') {
          this.success = true;
          this.paymentStatus = result.paymentIntent;
          this.$emit('payment-success', result.paymentIntent);
        }

      } catch (err) {
        this.error = err.message || 'Payment failed. Please try again.';
      } finally {
        this.loading = false;
      }
    }
  },

  beforeUnmount() {
    if (this.card) {
      this.card.destroy();
    }
  }
}
</script>

<style scoped>
.payment-form {
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
}

.card-element {
  background: white;
}

.loading {
  text-align: center;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 4px;
  margin-bottom: 20px;
}
</style>