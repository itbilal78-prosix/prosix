<template>
  <div class="auth-body">
    <div class="wrapper" :class="{ active: isRegister }">
      <span class="bg-animate"></span>
      <span class="bg-animate2"></span>

      <!-- ===================== LOGIN FORM ===================== -->
      <div class="form-box login">
        <h2 class="animation" style="--i:0; --j:21;">Login</h2>

        <!-- Error Alert -->
        <div v-if="login.error" class="auth-alert error animation" style="--i:1; --j:22;">
          {{ login.error }}
        </div>

        <form @submit.prevent="handleLogin">
          <!-- Email -->
          <div class="input-box animation" style="--i:1; --j:22;">
            <input
              type="email"
              v-model="login.form.email"
              :class="{ invalid: login.errors.email }"
              required
            />
            <label>Email</label>
            <i class="bx bxs-envelope icon-left"></i>
            <span v-if="login.errors.email" class="field-err">{{ login.errors.email }}</span>
          </div>

          <!-- Password -->
          <div class="input-box has-eye animation" style="--i:2; --j:23;">
            <input
              :type="login.showPass ? 'text' : 'password'"
              id="login-pass"
              v-model="login.form.password"
              :class="{ invalid: login.errors.password }"
              required
            />
            <label>Password</label>
            <i class="bx bxs-lock-alt icon-left"></i>
            <button type="button" class="eye-toggle" @click="login.showPass = !login.showPass">
              <i :class="login.showPass ? 'bx bx-show' : 'bx bx-hide'"></i>
            </button>
            <span v-if="login.errors.password" class="field-err">{{ login.errors.password }}</span>
          </div>

          <!-- Remember Me -->
          <div class="remember-box animation" style="--i:3; --j:24;">
            <label class="remember-label">
              <input type="checkbox" v-model="login.form.remember" />
              Remember me
            </label>
          </div>

          <!-- Submit -->
          <button class="btn animation" type="submit" :disabled="login.loading" style="--i:4; --j:25;">
            <span v-if="login.loading" class="spin"></span>
            {{ login.loading ? 'Signing in...' : 'Login' }}
          </button>

          <div class="logreg-link animation" style="--i:5; --j:26;">
            <p>Don't have an account? <br />
              <a href="#" @click.prevent="isRegister = true">Sign up</a>
            </p>
          </div>
        </form>
      </div>

      <!-- LOGIN INFO TEXT -->
      <div class="info-text login">
        <h2 class="animation" style="--i:0; --j:20;">Welcome<br />back!</h2>
        <p class="animation" style="--i:1; --j:21;">We're happy to have you with us again! If you need anything, we're here to help.</p>
      </div>

      <!-- ===================== REGISTER FORM ===================== -->
      <div class="form-box register">
        <h2 class="animation" style="--i:17; --j:0;">Sign up</h2>

        <!-- Error / Success Alert -->
        <div v-if="register.error" class="auth-alert error animation" style="--i:17; --j:0;">
          {{ register.error }}
        </div>
        <div v-if="register.success" class="auth-alert success animation" style="--i:17; --j:0;">
          {{ register.success }}
        </div>

        <form @submit.prevent="handleRegister">
          <!-- Full Name -->
          <div class="input-box animation" style="--i:18; --j:1;">
            <input
              type="text"
              v-model="register.form.name"
              :class="{ invalid: register.errors.name }"
              required
            />
            <label>Full Name</label>
            <i class="bx bxs-user icon-left"></i>
            <span v-if="register.errors.name" class="field-err">{{ register.errors.name }}</span>
          </div>

          <!-- Email -->
          <div class="input-box animation" style="--i:19; --j:2;">
            <input
              type="email"
              v-model="register.form.email"
              :class="{ invalid: register.errors.email }"
              required
            />
            <label>Email</label>
            <i class="bx bxs-envelope icon-left"></i>
            <span v-if="register.errors.email" class="field-err">{{ register.errors.email }}</span>
          </div>

          <!-- Password -->
          <div class="input-box has-eye animation" style="--i:20; --j:3;">
            <input
              :type="register.showPass ? 'text' : 'password'"
              v-model="register.form.password"
              :class="{ invalid: register.errors.password }"
              required
            />
            <label>Password</label>
            <i class="bx bxs-lock-alt icon-left"></i>
            <button type="button" class="eye-toggle" @click="register.showPass = !register.showPass">
              <i :class="register.showPass ? 'bx bx-show' : 'bx bx-hide'"></i>
            </button>
            <span v-if="register.errors.password" class="field-err">{{ register.errors.password }}</span>
          </div>

          <!-- Confirm Password -->
          <div class="input-box has-eye animation" style="--i:21; --j:4;">
            <input
              :type="register.showConfirm ? 'text' : 'password'"
              v-model="register.form.password_confirmation"
              :class="{ invalid: register.errors.password_confirmation }"
              required
            />
            <label>Confirm Password</label>
            <i class="bx bxs-lock icon-left"></i>
            <button type="button" class="eye-toggle" @click="register.showConfirm = !register.showConfirm">
              <i :class="register.showConfirm ? 'bx bx-show' : 'bx bx-hide'"></i>
            </button>
            <span v-if="register.errors.password_confirmation" class="field-err">{{ register.errors.password_confirmation }}</span>
          </div>

          <!-- Submit -->
          <button class="btn animation" type="submit" :disabled="register.loading" style="--i:22; --j:5;">
            <span v-if="register.loading" class="spin"></span>
            {{ register.loading ? 'Creating account...' : 'Sign up' }}
          </button>

          <div class="logreg-link animation" style="--i:23; --j:6;">
            <p>Already have an account? <br />
              <a href="#" @click.prevent="isRegister = false">Login</a>
            </p>
          </div>
        </form>
      </div>

      <!-- REGISTER INFO TEXT -->
      <div class="info-text register">
        <h2 class="animation" style="--i:17; --j:0;">Hello,<br />Friend!</h2>
        <p class="animation" style="--i:18; --j:1;">We're delighted to have you here. If you need any assistance, feel free to reach out.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const route  = useRoute()

// Toggle between login / register panel
const isRegister = ref(false)

// ─── LOGIN STATE ──────────────────────────────────────────────
const login = reactive({
  loading : false,
  showPass: false,
  error   : '',
  form    : { email: '', password: '', remember: false },
  errors  : { email: '', password: '' }
})

const handleLogin = async () => {
  login.loading = true
  login.error   = ''
  login.errors.email    = ''
  login.errors.password = ''

  try {
    const res = await axios.post('/api/user/login', {
      email   : login.form.email,
      password: login.form.password
    })

    if (res.data.status) {
      const token = res.data.token || 'logged_in'
      localStorage.setItem('auth_token', token)
      const redirectTo = route.query.redirect || '/dashboard'
      router.push(redirectTo)
    }
  } catch (e) {
    if (e.response?.data) {
      const data = e.response.data
      login.error = data.message || 'Login failed'
      if (data.errors) {
        login.errors.email    = data.errors.email?.[0]    || ''
        login.errors.password = data.errors.password?.[0] || ''
      }
    } else {
      login.error = 'Network or server error'
    }
  } finally {
    login.loading = false
  }
}

// ─── REGISTER STATE ───────────────────────────────────────────
const register = reactive({
  loading    : false,
  showPass   : false,
  showConfirm: false,
  error      : '',
  success    : '',
  form       : { name: '', email: '', password: '', password_confirmation: '' },
  errors     : { name: '', email: '', password: '', password_confirmation: '' }
})

const validateRegister = () => {
  Object.keys(register.errors).forEach(k => (register.errors[k] = ''))
  let valid = true
  if (!register.form.name)     { register.errors.name     = 'Name is required';  valid = false }
  if (!register.form.email)    { register.errors.email    = 'Email is required'; valid = false }
  if (!register.form.password) { register.errors.password = 'Password is required'; valid = false }
  if (register.form.password !== register.form.password_confirmation) {
    register.errors.password_confirmation = 'Passwords do not match'; valid = false
  }
  return valid
}

const handleRegister = async () => {
  if (!validateRegister()) return
  register.loading = true
  register.error   = ''
  register.success = ''

  try {
    const res  = await fetch('/api/user/register', {
      method : 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body   : JSON.stringify(register.form)
    })
    const data = await res.json()

    if (data.status) {
      router.push({ path: '/otp-verification', query: { email: data.email } })
    } else {
      register.error = data.message || 'Registration failed'
      if (data.errors) {
        Object.keys(data.errors).forEach(key => {
          if (register.errors[key] !== undefined)
            register.errors[key] = data.errors[key][0]
        })
      }
    }
  } catch (e) {
    register.error = 'Network error. Please try again.'
  } finally {
    register.loading = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
@import url('https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css');

/* ── full-page centering ── */
.auth-body {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #081b29;
  font-family: 'Poppins', sans-serif;
}

/* ── card ── */
.wrapper {
  position: relative;
  width: 750px;
  height: 480px;
  background: transparent;
  border: 2px solid #0ef;
  overflow: hidden;
  box-shadow: 0 0 25px #0ef;
}

/* ─────────────────────── FORM BOX ─────────────────────── */
.wrapper .form-box {
  position: absolute;
  top: 0;
  width: 50%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.wrapper .form-box.login {
  left: 0;
  padding: 0 50px 0 36px;
}

/* LOGIN animations */
.wrapper .form-box.login .animation {
  transform: translateX(0);
  opacity: 1;
  filter: blur(0);
  transition: 0.7s ease;
  transition-delay: calc(0.1s * var(--j));
}
.wrapper.active .form-box.login .animation {
  transform: translateX(-120%);
  opacity: 0;
  filter: blur(10px);
  transition-delay: calc(0.1s * var(--i));
}

.wrapper .form-box.register {
  right: 0;
  padding: 0 36px 0 50px;
  pointer-events: none;
}
.wrapper.active .form-box.register {
  pointer-events: auto;
}

/* REGISTER animations */
.wrapper .form-box.register .animation {
  transform: translateX(120%);
  opacity: 0;
  filter: blur(10px);
  transition: 0.7s ease;
}
.wrapper.active .form-box.register .animation {
  transform: translateX(0);
  opacity: 1;
  filter: blur(0);
  transition-delay: calc(0.1s * var(--i));
}

/* ─────────────────────── HEADINGS ─────────────────────── */
.form-box h2 {
  font-size: 28px;
  color: #fff;
  text-align: center;
  margin-bottom: 4px;
}

/* ─────────────────────── ALERTS ─────────────────────── */
.auth-alert {
  font-size: 12.5px;
  padding: 7px 12px;
  border-radius: 6px;
  margin-bottom: 8px;
  text-align: center;
}
.auth-alert.error   { background: rgba(255,80,80,0.15); border: 1px solid rgba(255,80,80,0.4); color: #ff6b6b; }
.auth-alert.success { background: rgba(0,239,239,0.1);  border: 1px solid rgba(0,239,239,0.4); color: #0ef; }

/* ─────────────────────── INPUT BOX ─────────────────────── */
.form-box .input-box {
  position: relative;
  width: 100%;
  height: 48px;
  margin: 16px 0;
}

.input-box input {
  width: 100%;
  height: 100%;
  background: transparent;
  border: none;
  outline: none;
  border-bottom: 2px solid #fff;
  padding-right: 26px;
  font-size: 15px;
  color: #fff;
  font-weight: 500;
  transition: 0.5s;
  font-family: 'Poppins', sans-serif;
}

.input-box input:focus,
.input-box input:valid            { border-bottom-color: #0ef; }
.input-box input.invalid          { border-bottom-color: #ff6b6b !important; }

.input-box label {
  position: absolute;
  top: 50%;
  left: 0;
  transform: translateY(-50%);
  font-size: 15px;
  color: #fff;
  pointer-events: none;
  transition: 0.5s;
}

.input-box input:focus ~ label,
.input-box input:valid ~ label    { top: -5px; font-size: 12px; color: #0ef; }

/* field icon (lock / envelope / user) */
.icon-left {
  position: absolute;
  top: 50%;
  right: 0;
  transform: translateY(-50%);
  font-size: 18px;
  color: #fff;
  pointer-events: none;
  transition: 0.5s;
}
.input-box input:focus ~ .icon-left,
.input-box input:valid ~ .icon-left { color: #0ef; }

/* has-eye: make room for both icons */
.input-box.has-eye input    { padding-right: 50px; }
.input-box.has-eye .icon-left { right: 26px; }

/* eye toggle button */
.eye-toggle {
  position: absolute;
  top: 50%;
  right: 0;
  transform: translateY(-50%);
  background: none;
  border: none;
  outline: none;
  cursor: pointer;
  color: #fff;
  font-size: 20px;
  padding: 0;
  line-height: 1;
  transition: color 0.3s;
  z-index: 5;
}
.eye-toggle:hover { color: #0ef; }

/* field-level error text */
.field-err {
  position: absolute;
  bottom: -16px;
  left: 0;
  font-size: 10.5px;
  color: #ff6b6b;
}

/* ─────────────────────── REMEMBER ME ─────────────────────── */
.remember-box {
  margin: -6px 0 4px;
}
.remember-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: #fff;
  cursor: pointer;
}
.remember-label input[type='checkbox'] {
  accent-color: #0ef;
  width: 14px;
  height: 14px;
  cursor: pointer;
}

/* ─────────────────────── BUTTON ─────────────────────── */
.btn {
  position: relative;
  width: 100%;
  height: 44px;
  background: transparent;
  border: 2px solid #0ef;
  outline: none;
  border-radius: 40px;
  cursor: pointer;
  font-size: 15px;
  color: #fff;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  z-index: 1;
  overflow: hidden;
  margin-top: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.btn::before {
  content: '';
  position: absolute;
  top: -100%;
  left: 0;
  width: 100%;
  height: 300%;
  background: linear-gradient(#081b29, #0ef, #081b29, #0ef);
  z-index: -1;
  transition: 0.5s;
}
.btn:hover:not(:disabled)::before { top: 0; }
.btn:disabled { opacity: 0.6; cursor: not-allowed; }

/* spinner inside button */
.spin {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255,255,255,0.35);
  border-top-color: #fff;
  border-radius: 50%;
  animation: _spin 0.6s linear infinite;
}
@keyframes _spin { to { transform: rotate(360deg); } }

/* ─────────────────────── LINK TEXT ─────────────────────── */
.form-box .logreg-link {
  font-size: 13.5px;
  color: #fff;
  text-align: center;
  margin-top: 12px;
}
.logreg-link p a {
  color: #0ef;
  text-decoration: none;
  font-weight: 600;
}
.logreg-link p a:hover { text-decoration: underline; }

/* ─────────────────────── INFO TEXT ─────────────────────── */
.wrapper .info-text {
  position: absolute;
  top: 0;
  width: 50%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.wrapper .info-text.login {
  right: 0;
  text-align: right;
  padding: 0 40px 60px 140px;
}
.wrapper .info-text.login .animation {
  transform: translateX(0);
  opacity: 1;
  filter: blur(0);
  transition: 0.7s ease;
  transition-delay: calc(0.1s * var(--j));
}
.wrapper.active .info-text.login .animation {
  transform: translateX(120%);
  opacity: 0;
  filter: blur(10px);
  transition-delay: calc(0.1s * var(--i));
}

.wrapper .info-text.register {
  left: 0;
  text-align: left;
  padding: 0 140px 60px 40px;
  pointer-events: none;
}
.wrapper.active .info-text.register {
  pointer-events: auto;
}
.wrapper .info-text.register .animation {
  transform: translateX(-120%);
  opacity: 0;
  filter: blur(10px);
  transition: 0.7s ease;
  transition-delay: calc(0.1s * var(--j));
}
.wrapper.active .info-text.register .animation {
  transform: translateX(0);
  opacity: 1;
  filter: blur(0);
  transition-delay: calc(0.1s * var(--i));
}

.info-text h2 {
  font-size: 34px;
  color: #fff;
  line-height: 1.3;
  text-transform: uppercase;
}
.info-text p {
  font-size: 14.5px;
  color: #fff;
}

/* ─────────────────────── BG ANIMATIONS ─────────────────────── */
.wrapper .bg-animate {
  position: absolute;
  top: 0;
  right: 0;
  width: 850px;
  height: 600px;
  background: linear-gradient(45deg, #081b29, #0ef);
  border-bottom: 3px solid #0ef;
  transform: rotate(10deg) skewY(40deg);
  transform-origin: bottom right;
  transition: 1.5s ease;
  transition-delay: 1.6s;
}
.wrapper.active .bg-animate {
  transform: rotate(0) skewY(0);
  transition-delay: 0.5s;
}

.wrapper .bg-animate2 {
  position: absolute;
  top: 100%;
  left: 250px;
  width: 850px;
  height: 700px;
  background: #081b29;
  border-top: 3px solid #0ef;
  transform: rotate(0) skewY(0);
  transform-origin: bottom left;
  transition: 1.5s ease;
  transition-delay: 0.5s;
}
.wrapper.active .bg-animate2 {
  transform: rotate(-11deg) skewY(-41deg);
  transition-delay: 1.2s;
}

/* ─────────────────────── RESPONSIVE ─────────────────────── */
@media (max-width: 800px) {
  .wrapper {
    width: 95vw;
    height: auto;
    min-height: 460px;
  }
  .wrapper .info-text { display: none; }
  .wrapper .form-box  { width: 100%; padding: 40px 28px !important; }
  .wrapper .form-box.register { right: unset; left: 0; }
  /* on mobile keep register hidden until active */
  .wrapper .form-box.register { display: none; }
  .wrapper.active .form-box.register { display: flex; }
  .wrapper .form-box.login  { display: flex; }
  .wrapper.active .form-box.login  { display: none; }
}
</style>
