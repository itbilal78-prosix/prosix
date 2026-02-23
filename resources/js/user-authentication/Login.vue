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
          <h4 class="text-center mb-3">Sign in to your account</h4>
          <p class="text-center text-muted mb-4">
            Or <router-link to="/register">create a new account</router-link>
          </p>

          <!-- Error Message -->
          <div v-if="loginError" class="alert alert-danger">
            {{ loginError }}
          </div>

          <!-- Login Form -->
          <form @submit.prevent="handleLogin">
            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input
                type="email"
                id="email"
                class="form-control"
                v-model="form.email"
                :class="{ 'is-invalid': errors.email }"
                placeholder="Enter your email"
                required
              />
              <div class="invalid-feedback">{{ errors.email }}</div>
            </div>

            <!-- Password -->
            <div class="mb-3 position-relative">
              <label for="password" class="form-label">Password</label>
              <input
                :type="showPassword ? 'text' : 'password'"
                id="password"
                class="form-control"
                v-model="form.password"
                :class="{ 'is-invalid': errors.password }"
                placeholder="Enter your password"
                required
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2"
              >
                {{ showPassword ? 'Hide' : 'Show' }}
              </button>
              <div class="invalid-feedback">{{ errors.password }}</div>
            </div>

            <!-- Remember Me -->
            <div class="mb-3 form-check">
              <input type="checkbox" id="remember-me" class="form-check-input" v-model="form.remember" />
              <label for="remember-me" class="form-check-label">Remember me</label>
            </div>

            <!-- Submit Button -->
            <button :disabled="loading" type="submit" class="btn btn-primary w-100">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'Signing in...' : 'Sign in' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route = useRoute()

// Reactive form data
const form = reactive({
  email: '',
  password: '',
  remember: false
})

// Error messages
const errors = reactive({
  email: '',
  password: ''
})

const loginError = ref('')
const loading = ref(false)
const showPassword = ref(false)

// Login handler
const handleLogin = async () => {
  loading.value = true
  loginError.value = ''
  errors.email = ''
  errors.password = ''

  try {
    const res = await axios.post('/api/user/login', {
      email: form.email,
      password: form.password
    })

    if (res.data.status) {
      // ===== TOKEN SAVE =====
      // Agar backend token deta hai toh woh save karo
      // Nahi deta toh 'logged_in' rakhein (fallback)
      const token = res.data.token || 'logged_in'
      localStorage.setItem('auth_token', token)

      // ===== REDIRECT =====
      // Agar pehle kisi protected page sai aaya tha (e.g. /models/5)
      // toh wahan wapas jaao, nahi toh dashboard
      const redirectTo = route.query.redirect || '/dashboard'
      router.push(redirectTo)
    }
  } catch (e) {
    if (e.response && e.response.data) {
      const data = e.response.data
      loginError.value = data.message || 'Login failed'

      if (data.errors) {
        errors.email = data.errors.email ? data.errors.email[0] : ''
        errors.password = data.errors.password ? data.errors.password[0] : ''
      }
    } else {
      loginError.value = 'Network or server error'
    }
  } finally {
    loading.value = false
  }
}
</script>