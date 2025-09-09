<template>
  <div id="google-signin-button"></div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import axios from 'axios'

const router = useRouter()
const authStore = useAuthStore()
const GOOGLE_CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID

function handleCredentialResponse(response) {
  const idToken = response.credential
  authStore.loginWithGoogle(idToken)
      .then(res => {
          if(res.success){
              router.push('/dashboard')
          } else {
              alert(res.message)
          }
      })
}


onMounted(() => {
  google.accounts.id.initialize({
    client_id: GOOGLE_CLIENT_ID,
    callback: handleCredentialResponse
  })

  google.accounts.id.renderButton(
    document.getElementById('google-signin-button'),
    { theme: 'outline', size: 'large', width: 250 }
  )
})
</script>
