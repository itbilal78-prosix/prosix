<template>
  <div class="container mt-5 text-center">
    <h3>Enter OTP sent to your email</h3>
    <input v-model="otp" placeholder="Enter OTP" class="form-control mb-2" />
    <button @click="submitOtp" class="btn btn-primary">Verify OTP</button>

    <div v-if="error" class="alert alert-danger mt-2">{{ error }}</div>
    <div v-if="success" class="alert alert-success mt-2">{{ success }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const otp = ref('')
const error = ref('')
const success = ref('')

const submitOtp = async () => {
  try {
    const res = await fetch('/api/user/verify-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: route.query.email,
        otp: otp.value
      })
    })

    const data = await res.json()
    if (res.ok && data.status) {
      success.value = data.message
      setTimeout(() => router.push('/user-login'), 1500)
    } else {
      error.value = data.message
    }
  } catch {
    error.value = 'Network error. Please try again.'
  }
}
</script>
