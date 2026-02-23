<template>
  <div class="container min-vh-100 d-flex justify-content-center align-items-center">
    <div class="col-12 col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <!-- Logo -->
          <div class="text-center mb-4">
            <router-link to="/" class="d-flex justify-content-center align-items-center mb-2 text-decoration-none">
              <img src="/public/assets/images/P LOGO BLACK.png" alt="Prosix Logo" class="me-2" style="height: 40px; width: auto;" />
              <span class="fs-4 fw-bold text-dark">Prosix</span>
            </router-link>
          </div>

          <!-- Heading -->
          <h4 class="text-center mb-3">Create your account</h4>
          <p class="text-center text-muted mb-4">
            Or
            <router-link to="/user-login">sign in to your existing account</router-link>
          </p>

          <!-- Error / Success Messages -->
          <div v-if="registerError" class="alert alert-danger">
            {{ registerError }}
          </div>
          <div v-if="successMessage" class="alert alert-success">
            {{ successMessage }}
          </div>

          <!-- Register Form -->
          <form @submit.prevent="handleRegister">
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" id="name" class="form-control" v-model="form.name" :class="{ 'is-invalid': errors.name }" placeholder="Enter your full name">
              <div class="invalid-feedback">{{ errors.name }}</div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" id="email" class="form-control" v-model="form.email" :class="{ 'is-invalid': errors.email }" placeholder="Enter your email">
              <div class="invalid-feedback">{{ errors.email }}</div>
            </div>

            <div class="mb-3 position-relative">
              <label for="password" class="form-label">Password</label>
              <input :type="showPassword ? 'text' : 'password'" id="password" class="form-control" v-model="form.password" :class="{ 'is-invalid': errors.password }" placeholder="Create a password">
              <button type="button" @click="showPassword = !showPassword" class="btn btn-outline-secondary btn-sm position-absolute top-50 end-0 translate-middle-y me-2">
                {{ showPassword ? 'Hide' : 'Show' }}
              </button>
              <div class="invalid-feedback">{{ errors.password }}</div>
            </div>

            <div class="mb-3 position-relative">
              <label for="password_confirmation" class="form-label">Confirm Password</label>
              <input :type="showConfirmPassword ? 'text' : 'password'" id="password_confirmation" class="form-control" v-model="form.password_confirmation" :class="{ 'is-invalid': errors.password_confirmation }" placeholder="Confirm your password">
              <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="btn btn-outline-secondary btn-sm position-absolute top-50 end-0 translate-middle-y me-2">
                {{ showConfirmPassword ? 'Hide' : 'Show' }}
              </button>
              <div class="invalid-feedback">{{ errors.password_confirmation }}</div>
            </div>

            <button :disabled="loading" type="submit" class="btn btn-primary w-100">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'Creating account...' : 'Create account' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const registerError = ref('')
const successMessage = ref('')

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const validateForm = () => {
  Object.keys(errors).forEach(k => errors[k] = '')
  let valid = true

  if (!form.name) { errors.name = 'Name is required'; valid = false }
  if (!form.email) { errors.email = 'Email is required'; valid = false }
  if (!form.password) { errors.password = 'Password is required'; valid = false }
  if (form.password !== form.password_confirmation) { errors.password_confirmation = 'Passwords do not match'; valid = false }

  return valid
}

const handleRegister = async () => {
  if (!validateForm()) return
  loading.value = true
  registerError.value = ''
  successMessage.value = ''

  try {
    const res = await fetch('/api/user/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(form)
    })
    const data = await res.json() // ✅ parse JSON correctly
if (data.status) {
  router.push({ path: '/otp-verification', query: { email: data.email } })
}

else {
      registerError.value = data.message || 'Registration failed'

      // ✅ Show field specific errors from backend
      if (data.errors) {
        Object.keys(data.errors).forEach(key => {
          if (errors[key] !== undefined) errors[key] = data.errors[key][0]
        })
      }
    }
  } catch (e) {
    registerError.value = 'Network error. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
