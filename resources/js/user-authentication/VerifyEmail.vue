<template>
  <div class="container text-center mt-5">
    <h3 v-if="loading">Verifying your email...</h3>

    <div v-if="success" class="alert alert-success">
      Email verified successfully ✅  
      Redirecting to login...
    </div>

    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const success = ref(false)
const error = ref('')

onMounted(async () => {
  try {
    const res = await fetch(
      `/api/email/verify/${route.query.id}/${route.query.hash}`,
      { headers: { Accept: 'application/json' } }
    )

    const data = await res.json()

    if (res.ok && data.status) {
      success.value = true
      setTimeout(() => router.push('/user-login'), 2000)
    } else {
      error.value = data.message || 'Verification failed'
    }
  } catch (e) {
    error.value = 'Something went wrong'
  } finally {
    loading.value = false
  }
})
</script>
