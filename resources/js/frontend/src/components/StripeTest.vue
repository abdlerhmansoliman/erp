<template>
  <div>
    <pre>{{ debugInfo }}</pre>
    <!-- Rest of your component -->
  </div>
</template>

<script>
export default {
  data() {
    return {
      debugInfo: {
        stripeKey: import.meta.env.VITE_STRIPE_PUBLISHABLE_KEY ? 'Present' : 'Missing',
        mode: import.meta.env.MODE,
        envVars: import.meta.env
      }
    }
  }
}
</script>