# Payment API Documentation

## Endpoints

### Create Payment
POST /api/payments

Creates a new payment intent and returns the client secret for Stripe processing.

**Request Body:**
```json
{
    "payable_type": "App\\Models\\SalesInvoice",
    "payable_id": 1,
    "amount": 100.00,
    "payment_method_id": 1
}
```

**Response:**
```json
{
    "client_secret": "pi_...",
    "payment_id": 1,
    "publishable_key": "pk_..."
}
```

### Confirm Payment
POST /api/payments/{payment}/confirm

Confirms a payment after client-side processing.

**Request Body:**
```json
{
    "payment_intent": "pi_...",
    "payment_method": "pm_..."
}
```

**Response:**
```json
{
    "id": 1,
    "status": "succeeded",
    "amount": 100.00,
    ...
}
```

### Webhook
POST /stripe/webhook

Handles Stripe webhook events for payment status updates.

**Headers:**
- Stripe-Signature: Required for webhook validation

## Error Codes

- 400: Invalid request data
- 401: Unauthorized
- 402: Payment Required (payment failed)
- 403: Forbidden
- 404: Resource not found
- 409: Payment already processed
- 500: Server error

## Testing

Use Stripe test cards for development:
- 4242 4242 4242 4242: Successful payment
- 4000 0000 0000 9995: Failed payment
- 4000 0000 0000 3220: 3D Secure required

## Implementation Example

```javascript
// Frontend implementation with Stripe.js
const stripe = Stripe('your_publishable_key');
const elements = stripe.elements();

// Create payment
const response = await fetch('/api/payments', {
    method: 'POST',
    body: JSON.stringify({
        payable_type: 'App\\Models\\SalesInvoice',
        payable_id: invoiceId,
        amount: total,
        payment_method_id: 7
    })
});

const { client_secret } = await response.json();

// Confirm payment
const result = await stripe.confirmCardPayment(client_secret, {
    payment_method: {
        card: elements.getElement('card'),
        billing_details: {
            name: 'Customer Name'
        }
    }
});

if (result.error) {
    // Handle error
} else {
    // Payment successful
}
```