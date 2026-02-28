<template>
  <div class="auth-body">
    <div class="wrapper" :class="{ active: isRegister }">
      <span class="bg-animate"></span>
      <span class="bg-animate2"></span>

      <!-- ===================== LOGIN FORM ===================== -->
      <div class="form-box login">
        <h2 class="animation" style="--i:0; --j:21;">Login</h2>

        <div v-if="login.error" class="auth-alert error animation" style="--i:1; --j:22;">
          {{ login.error }}
        </div>

        <form @submit.prevent="handleLogin">

          <div class="input-box animation" style="--i:1; --j:22;">
            <input
              type="email"
              v-model="login.form.email"
              :class="{ invalid: login.errors.email }"
              placeholder=" "
              required
            />
            <label>Email</label>
            <i class="bx bxs-envelope icon-left"></i>
            <span v-if="login.errors.email" class="field-err">{{ login.errors.email }}</span>
          </div>

          <div class="input-box has-eye animation" style="--i:2; --j:23;">
            <input
              :type="login.showPass ? 'text' : 'password'"
              v-model="login.form.password"
              :class="{ invalid: login.errors.password }"
              placeholder=" "
              required
            />
            <label>Password</label>
            <button type="button" class="eye-toggle" @click="login.showPass = !login.showPass">
              <i :class="login.showPass ? 'bx bx-show' : 'bx bx-hide'"></i>
            </button>
            <span v-if="login.errors.password" class="field-err">{{ login.errors.password }}</span>
          </div>

          <div class="remember-box animation" style="--i:3; --j:24;">
            <label class="remember-label">
              <input type="checkbox" v-model="login.form.remember" />
              Remember me
            </label>
          </div>

          <button class="btn animation" type="submit" :disabled="login.loading" style="--i:4; --j:25;">
            <span v-if="login.loading" class="spin"></span>
            {{ login.loading ? 'Signing in...' : 'Login' }}
          </button>

          <div class="logreg-link animation" style="--i:5; --j:26;">
            <p>Don't have an account?<br />
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

        <div v-if="register.error" class="auth-alert error animation" style="--i:17; --j:0;">
          {{ register.error }}
        </div>
        <div v-if="register.success" class="auth-alert success animation" style="--i:17; --j:0;">
          {{ register.success }}
        </div>

        <form @submit.prevent="handleRegister">

          <div class="input-box animation" style="--i:18; --j:1;">
            <input
              type="text"
              v-model="register.form.name"
              :class="{ invalid: register.errors.name }"
              placeholder=" "
              required
            />
            <label>Full Name</label>
            <i class="bx bxs-user icon-left"></i>
            <span v-if="register.errors.name" class="field-err">{{ register.errors.name }}</span>
          </div>

          <div class="input-box animation" style="--i:19; --j:2;">
            <input
              type="email"
              v-model="register.form.email"
              :class="{ invalid: register.errors.email }"
              placeholder=" "
              required
            />
            <label>Email</label>
            <i class="bx bxs-envelope icon-left"></i>
            <span v-if="register.errors.email" class="field-err">{{ register.errors.email }}</span>
          </div>

          <div class="input-box has-eye animation" style="--i:20; --j:3;">
            <input
              :type="register.showPass ? 'text' : 'password'"
              v-model="register.form.password"
              :class="{ invalid: register.errors.password }"
              placeholder=" "
              required
            />
            <label>Password</label>
            <button type="button" class="eye-toggle" @click="register.showPass = !register.showPass">
              <i :class="register.showPass ? 'bx bx-show' : 'bx bx-hide'"></i>
            </button>
            <span v-if="register.errors.password" class="field-err">{{ register.errors.password }}</span>
          </div>

          <div class="input-box has-eye animation" style="--i:21; --j:4;">
            <input
              :type="register.showConfirm ? 'text' : 'password'"
              v-model="register.form.password_confirmation"
              :class="{ invalid: register.errors.password_confirmation }"
              placeholder=" "
              required
            />
            <label>Confirm Password</label>
            <button type="button" class="eye-toggle" @click="register.showConfirm = !register.showConfirm">
              <i :class="register.showConfirm ? 'bx bx-show' : 'bx bx-hide'"></i>
            </button>
            <span v-if="register.errors.password_confirmation" class="field-err">{{ register.errors.password_confirmation }}</span>
          </div>

          <button class="btn animation" type="submit" :disabled="register.loading" style="--i:22; --j:5;">
            <span v-if="register.loading" class="spin"></span>
            {{ register.loading ? 'Creating account...' : 'Sign up' }}
          </button>

          <div class="logreg-link animation" style="--i:23; --j:6;">
            <p>Already have an account?<br />
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

const isRegister = ref(false)

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
      localStorage.setItem('auth_token', res.data.token || 'logged_in')
      router.push(route.query.redirect || '/dashboard')
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

/* ── PAGE ── */
.auth-body {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #ffffff;                        /* WHITE page background */
  font-family: 'Poppins', sans-serif;
}

/* ── CARD ── */
.wrapper {
  position: relative;
  width: 750px;
  height: 480px;
  background: transparent;
  border: 2px solid #333333;                  /* dark border on white */
  overflow: hidden;
  box-shadow: 0 8px 40px rgba(0,0,0,0.18);
  border-radius: 4px;
}

/* ─────────────── FORM BOX ─────────────── */
.wrapper .form-box {
  position: absolute;
  top: 0;
  width: 50%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.wrapper .form-box.login  { left: 0;  padding: 0 50px 0 36px; }
.wrapper .form-box.register { right: 0; padding: 0 36px 0 50px; pointer-events: none; }
.wrapper.active .form-box.register { pointer-events: auto; }

/* LOGIN slide animations */
.wrapper .form-box.login .animation {
  transform: translateX(0); opacity: 1; filter: blur(0);
  transition: 0.7s ease;
  transition-delay: calc(0.1s * var(--j));
}
.wrapper.active .form-box.login .animation {
  transform: translateX(-120%); opacity: 0; filter: blur(10px);
  transition-delay: calc(0.1s * var(--i));
}

/* REGISTER slide animations */
.wrapper .form-box.register .animation {
  transform: translateX(120%); opacity: 0; filter: blur(10px);
  transition: 0.7s ease;
}
.wrapper.active .form-box.register .animation {
  transform: translateX(0); opacity: 1; filter: blur(0);
  transition-delay: calc(0.1s * var(--i));
}

/* ─────────────── HEADINGS ─────────────── */
.form-box h2 {
  font-size: 26px;
  font-weight: 700;
  color: #111111;
  text-align: center;
  margin-bottom: 6px;
}

/* ─────────────── ALERTS ─────────────── */
.auth-alert {
  font-size: 12px;
  padding: 7px 12px;
  border-radius: 6px;
  margin-bottom: 8px;
  text-align: center;
}
.auth-alert.error   { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; }
.auth-alert.success { background: #f0fdf4; border: 1px solid #86efac; color: #16a34a; }

/* ─────────────── INPUT BOX ─────────────── */
.form-box .input-box {
  position: relative;
  width: 100%;
  height: 50px;
  margin: 18px 0;
}

.input-box input {
  width: 100%;
  height: 100%;
  background: transparent;
  border: none;
  outline: none;
  border-bottom: 2px solid #aaaaaa;
  padding: 18px 30px 0 6px;
  font-size: 14px;
  color: #111111;
  font-weight: 500;
  transition: border-color 0.3s;
  font-family: 'Poppins', sans-serif;
}

/* focus & filled state */
.input-box input:focus       { border-bottom-color: #111111; }
.input-box input.invalid     { border-bottom-color: #ef4444 !important; }

/* FLOATING LABEL — uses placeholder=" " trick */
.input-box label {
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  color: #888888;
  pointer-events: none;
  transition: 0.25s ease;
  background: transparent;
}
/* float up when input has content (not-placeholder-shown) OR is focused */
.input-box input:focus          ~ label,
.input-box input:not(:placeholder-shown) ~ label {
  top: 4px;
  transform: translateY(0);
  font-size: 10.5px;
  padding: 0 0 0 6px;
  color: #555555;
  font-weight: 600;
  letter-spacing: 0.04em;
}

/* ── right-side icon (lock / envelope / user) ── */
.icon-left {
  position: absolute;
  top: 50%;
  right: 2px;
  padding: 0 6px 0 0;
  transform: translateY(-50%);
  font-size: 17px;
  color: #aaaaaa;
  pointer-events: none;
  transition: color 0.3s;
  line-height: 1;
}
.input-box input:focus          ~ .icon-left,
.input-box input:not(:placeholder-shown) ~ .icon-left { color: #111111; }

/* has-eye: shift field icon left to make room for eye button */
.input-box.has-eye input     { padding-right: 54px; }
.input-box.has-eye .icon-left { right: 28px; }

/* eye toggle */
.eye-toggle {
  position: absolute;
  top: 50%;
  right: 0;
  transform: translateY(-50%);
  background: none;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 0;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #aaaaaa;
  font-size: 19px;
  transition: color 0.3s;
  z-index: 5;
}
.eye-toggle:hover { color: #111111; }

/* field error */
.field-err {
  position: absolute;
  bottom: -16px;
  left: 0;
  font-size: 10px;
  color: #ef4444;
}

/* ─────────────── REMEMBER ME ─────────────── */
.remember-box { margin: -4px 0 6px; }
.remember-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12.5px;
  color: #555555;
  cursor: pointer;
}
.remember-label input[type='checkbox'] {
  accent-color: #111111;
  width: 13px; height: 13px; cursor: pointer;
}

/* ─────────────── BUTTON ─────────────── */
.btn {
  position: relative;
  width: 100%;
  height: 44px;
  background: #111111;          /* black background */
  border: 2px solid #111111;
  outline: none;
  border-radius: 40px;
  cursor: pointer;
  font-size: 14px;
  color: #ffffff;               /* white text */
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  margin-top: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background 0.3s, color 0.3s;
}
.btn:hover:not(:disabled) {
  background: #ffffff;          /* white background on hover */
  color: #111111;               /* black text on hover */
}
.btn:disabled { opacity: 0.5; cursor: not-allowed; }

.spin {
  width: 13px; height: 13px;
  border: 2px solid rgba(0,0,0,0.2);
  border-top-color: currentColor;
  border-radius: 50%;
  animation: _spin 0.6s linear infinite;
}
@keyframes _spin { to { transform: rotate(360deg); } }

/* ─────────────── LINK TEXT ─────────────── */
.form-box .logreg-link {
  font-size: 12.5px;
  color: #666666;
  text-align: center;
  margin-top: 12px;
}
.logreg-link p a {
  color: #111111;
  text-decoration: none;
  font-weight: 700;
}
.logreg-link p a:hover { text-decoration: underline; }

/* ─────────────── INFO TEXT ─────────────── */
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
  transform: translateX(0); opacity: 1; filter: blur(0);
  transition: 0.7s ease;
  transition-delay: calc(0.1s * var(--j));
}
.wrapper.active .info-text.login .animation {
  transform: translateX(120%); opacity: 0; filter: blur(10px);
  transition-delay: calc(0.1s * var(--i));
}

.wrapper .info-text.register {
  left: 0;
  text-align: left;
  padding: 0 140px 60px 40px;
  pointer-events: none;
}
.wrapper.active .info-text.register { pointer-events: auto; }
.wrapper .info-text.register .animation {
  transform: translateX(-120%); opacity: 0; filter: blur(10px);
  transition: 0.7s ease;
  transition-delay: calc(0.1s * var(--j));
}
.wrapper.active .info-text.register .animation {
  transform: translateX(0); opacity: 1; filter: blur(0);
  transition-delay: calc(0.1s * var(--i));
}

.info-text h2 {
  font-size: 32px;
  font-weight: 800;
  color: #ffffff;
  line-height: 1.3;
  text-transform: uppercase;
}
.info-text p {
  font-size: 13.5px;
  color: rgba(255,255,255,0.85);
  margin-top: 8px;
  line-height: 1.6;
}

/* ─────────────── BG ANIMATIONS ─────────────── */
/* bg-animate = the diagonal slab that fills the info-text side */
.wrapper .bg-animate {
  position: absolute;
  top: 0; right: 0;
  width: 850px; height: 600px;
  background: linear-gradient(45deg, #1a1a1a, #555555);  /* dark grey slab */
  border-bottom: 3px solid #333333;
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
  top: 100%; left: 230px;
  width: 850px; height: 700px;
  background: #ffffff;           /* white fill behind form */
  border-top: 3px solid #333333;
  transform: rotate(0) skewY(0);
  transform-origin: bottom left;
  transition: 1.5s ease;
  transition-delay: 0.5s;
}
.wrapper.active .bg-animate2 {
  transform: rotate(-11deg) skewY(-41deg);
  transition-delay: 1.2s;
}

/* ─────────────── RESPONSIVE ─────────────── */
@media (max-width: 800px) {
  .wrapper { width: 95vw; height: auto; min-height: 460px; }
  .wrapper .info-text { display: none; }
  .wrapper .form-box  { width: 100%; padding: 40px 28px !important; }
  .wrapper .form-box.register { right: unset; left: 0; display: none; }
  .wrapper.active .form-box.register { display: flex; }
  .wrapper .form-box.login  { display: flex; }
  .wrapper.active .form-box.login  { display: none; }
}
</style>
