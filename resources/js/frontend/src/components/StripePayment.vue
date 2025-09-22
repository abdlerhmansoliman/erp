<template>
  <div class="payment-form">
    <!-- Loading State -->
    <div v-if="loading" class="loading-overlay">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Processing payment...</span>
      </div>
      <p class="mt-2">Processing payment...</p>
    </div>
    
    <!-- Error Message -->
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <!-- Success Message -->
    <div v-if="success" class="alert alert-success">
      <i class="fas fa-check-circle me-2"></i>
      Payment successful!
    </div>

    <!-- Payment Form -->
    <form @submit.prevent="handleSubmit" v-if="!success">
      <!-- Amount Input -->
      <div class="mb-4">
        <label class="form-label">Amount ($)</label>
        <div class="input-group">
          <span class="input-group-text">$</span>
          <input 
            type="number" 
            v-model="amount" 
            :disabled="loading"
            class="form-control"
            step="0.01"
            min="0.01"
            required
            placeholder="0.00"
          >
        </div>
      </div>

      <!-- Card Element -->
      <div class="mb-4">
        <label class="form-label">Card Information</label>
        <div 
          ref="cardElement" 
          class="stripe-element"
        ></div>
        <div v-if="cardError" class="invalid-feedback d-block">
          {{ cardError }}
        </div>
        <div class="form-text mt-2">
          Test Card: 4242 4242 4242 4242 (any future date, any 3 digits for CVC)
        </div>
      </div>

      <!-- Submit Button -->
      <button 
        type="submit" 
        class="btn btn-primary w-100"
        :disabled="loading || !stripe || !amount || cardError"
      >
        <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
        {{ loading ? 'Processing...' : `Pay $${amount || '0.00'}` }}
      </button>
    </form>
  </div>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js'
import axios from 'axios'

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

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
      default: 7 // Default to Stripe payment method
    },
    presetAmount: {
      type: Number,
      default: null
    }
  },

  data() {
    return {
      loading: false,
      error: null,
      cardError: null,
      success: false,
      amount: '',
      stripe: null,
      card: null,
      paymentStatus: null,
      isDev: process.env.NODE_ENV === 'development'
    }
  },

  async mounted() {
    try {
      // Initialize Stripe
      this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY);

      const elements = this.stripe.elements();
      
      // Create card element
      this.card = elements.create('card', {
        style: {
          base: {
            color: '#495057',
            fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: '#6c757d'
            }
          },
          invalid: {
            color: '#dc3545',
            iconColor: '#dc3545'
          }
        },
        hidePostalCode: true,
        classes: {
          base: 'stripe-element',
          focus: 'is-focused',
          invalid: 'is-invalid',
          complete: 'is-complete'
        }
      });

      // Mount card element
      if (this.$refs.cardElement) {
        this.card.mount(this.$refs.cardElement);

        // Handle validation errors
        this.card.addEventListener('change', (event) => {
          if (event.error) {
            this.cardError = event.error.message;
          } else {
            this.cardError = null;
          }
        });
      } else {
        throw new Error('Card element mount point not found');
      }
    } catch (err) {
      const errorMessage = err.message || 'Failed to load payment form. Please try again.';
      this.error = errorMessage;
      console.error('Stripe initialization error:', {
        message: err.message,
        stripeKey: import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY ? 'Present' : 'Missing',
        cardElement: this.$refs.cardElement ? 'Present' : 'Missing'
      });
    }
  },

  methods: {
    async handleSubmit() {
      this.loading = true
      this.error = null
      this.cardError = null
      
      try {
        // 1. Create payment
        const modelName = this.payableType.replace('App\\Models\\', '');
        const paymentData = {
          payable_type: `App\\Models\\${modelName}`,
          payable_id: 123, // Using the newly created test invoice ID
          amount: parseFloat(this.amount),
          payment_method_id: 7 // Using the existing Stripe payment method ID
        };
        
        console.log('Payment request:', paymentData);

        const response = await axios.post('/api/payments/pay', paymentData).catch(error => {
          if (error.response) {
            throw new Error(error.response.data.message || 'Failed to create payment intent');
          }
          throw error;
        });

        const { client_secret, payment_id } = response.data;

        // 2. Confirm card payment
        const result = await this.stripe.confirmCardPayment(client_secret, {
          payment_method: {
            card: this.card,
            billing_details: {
              name: 'Test Customer' // You might want to make this dynamic
            }
          }
        });

        // 3. Confirm with backend
        if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
          const backendResult = await axios.post(`/api/payments/${payment_id}/confirm`, {
            payment_intent_id: result.paymentIntent.id,
            status: result.paymentIntent.status
          }).catch(error => {
            console.error('Backend confirmation error:', error.response?.data);
            throw new Error(error.response?.data?.message || 'Failed to confirm payment with backend');
          });
          
          this.success = true;
          this.paymentStatus = result.paymentIntent;
          
          // Emit success with payment details
          this.$emit('payment-success', {
            success: true,
            payment: backendResult.data,
            paymentIntent: result.paymentIntent
          });
        } else if (result.error) {
          throw result.error;
        }

      } catch (err) {
        if (err.response && err.response.data) {
          // Backend error
          this.error = err.response.data.message || 'Server error occurred';
          console.error('Backend error:', err.response.data);
        } else if (err.type === 'card_error' || err.type === 'validation_error') {
          // Stripe card error
          this.cardError = err.message;
        } else {
          // Other errors
          this.error = err.message || 'Payment failed. Please try again.';
        }
        
        console.error('Payment error:', {
          message: err.message,
          type: err.type,
          code: err.code,
          response: err.response ? err.response.data : null
        });
      } finally {
        this.loading = false;
      }
    }
  },

  beforeUnmount() {
    if (this.card) {
      this.card.destroy()
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

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.stripe-element {
  padding: 0.75rem;
  font-size: 1rem;
  font-weight: 400;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  min-height: 48px;
}

.stripe-element.is-focused {
  border-color: #80bdff;
  outline: 0;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.stripe-element.is-invalid {
  border-color: #dc3545;
  padding-right: calc(1.5em + 0.75rem);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right calc(0.375em + 0.1875rem) center;
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.stripe-element.is-complete {
  border-color: #28a745;
  padding-right: calc(1.5em + 0.75rem);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right calc(0.375em + 0.1875rem) center;
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 80%;
  color: #dc3545;
}

.form-text {
  color: #6c757d;
  font-size: 0.875rem;
}

.btn.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.btn.btn-primary:hover {
  background-color: #0b5ed7;
  border-color: #0a58ca;
}

.btn.btn-primary:disabled {
  background-color: #0d6efd;
  border-color: #0d6efd;
  opacity: 0.65;
}
</style>