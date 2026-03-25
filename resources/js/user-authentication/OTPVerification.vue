<template>
  <div class="container mt-5 text-center">
    <h3>Enter OTP sent to your email</h3>

    <input
      v-model="otp"
      placeholder="Enter OTP"
      class="form-control mb-2"
    />

    <button @click="submitOtp" class="btn btn-primary">
      Verify OTP
    </button>

    <!-- RESEND OTP BUTTON -->
    <div class="mt-3">
      <button
        class="btn btn-link"
        :disabled="resendDisabled"
        @click="resendOtp"
      >
        Resend OTP
      </button>

      <small v-if="resendDisabled">
        (Wait {{ timer }}s)
      </small>
    </div>

    <div v-if="error" class="alert alert-danger mt-2">
      {{ error }}
    </div>

    <div v-if="success" class="alert alert-success mt-2">
      {{ success }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const otp = ref('')
const error = ref('')
const success = ref('')

const resendDisabled = ref(true)
const timer = ref(30)

// VERIFY OTP
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

// RESEND OTP
const resendOtp = async () => {
  try {
    const res = await fetch('/api/user/resend-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: route.query.email
      })
    })

    const data = await res.json()

    if (res.ok && data.status) {
      success.value = 'OTP resent successfully'

      startTimer()
    } else {
      error.value = data.message
    }

  } catch {
    error.value = 'Could not resend OTP'
  }
}

// TIMER FUNCTION
const startTimer = () => {
  resendDisabled.value = true
  timer.value = 30

  const interval = setInterval(() => {
    timer.value--

    if (timer.value <= 0) {
      resendDisabled.value = false
      clearInterval(interval)
    }
  }, 1000)
}

// START TIMER ON LOAD
onMounted(() => {
  startTimer()
})
</script>
