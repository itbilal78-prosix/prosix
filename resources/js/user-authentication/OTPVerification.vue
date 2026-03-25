<template>
  <div class="auth-body">

    <!-- Background grain overlay -->
    <div class="bg-grain"></div>

    <div class="otp-wrapper">
      <div class="otp-card">

        <!-- ICON -->
        <div class="icon-ring">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
        </div>

        <!-- HEADING -->
        <h2>Verify Your Email</h2>
        <p class="subtitle">
          Enter the 6-digit code sent to<br />
          <span class="email-highlight">{{ route.query.email || 'your email' }}</span>
        </p>

        <!-- 6-DIGIT OTP BOXES -->
        <div class="otp-boxes">
          <input
            v-for="(digit, i) in otpDigits"
            :key="i"
            :ref="el => { if (el) inputs[i] = el }"
            v-model="otpDigits[i]"
            class="otp-box"
            :class="{ filled: otpDigits[i], shake: shakeError }"
            type="text"
            inputmode="numeric"
            maxlength="1"
            @input="handleInput(i, $event)"
            @keydown="handleKeydown(i, $event)"
            @paste="handlePaste($event)"
            @focus="handleFocus(i)"
          />
        </div>

        <!-- VERIFY BUTTON -->
        <button
          class="btn-verify"
          :disabled="loading || otp.length < 6"
          @click="submitOtp"
        >
          <span v-if="!loading">Verify OTP</span>
          <span v-else class="spinner"></span>
        </button>

        <!-- RESEND SECTION -->
        <div class="resend-section">
          <span class="resend-label">Didn't receive the code?</span>
          <button
            class="resend-btn"
            :disabled="resendDisabled"
            @click="resendOtp"
          >
            {{ resendDisabled ? `Resend in ${timer}s` : 'Resend OTP' }}
          </button>
        </div>

        <!-- ALERTS -->
        <transition name="fade">
          <div v-if="error" class="auth-alert error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ error }}
          </div>
        </transition>

        <transition name="fade">
          <div v-if="success" class="auth-alert success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {{ success }}
          </div>
        </transition>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const inputs = ref([])
const otpDigits = ref(['', '', '', '', '', ''])
const error = ref('')
const success = ref('')
const loading = ref(false)
const shakeError = ref(false)

const resendDisabled = ref(true)
const timer = ref(120)

const otp = computed(() => otpDigits.value.join(''))

// ---- INPUT HANDLERS ----

const handleInput = (i, e) => {
  const val = e.target.value.replace(/\D/g, '')
  otpDigits.value[i] = val.slice(-1)
  error.value = ''
  if (val && i < 5) inputs.value[i + 1]?.focus()
}

const handleKeydown = (i, e) => {
  if (e.key === 'Backspace' && !otpDigits.value[i] && i > 0) {
    inputs.value[i - 1]?.focus()
  }
}

const handlePaste = (e) => {
  const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6)
  if (!pasted) return
  e.preventDefault()
  pasted.split('').forEach((ch, i) => { otpDigits.value[i] = ch })
  inputs.value[Math.min(pasted.length, 5)]?.focus()
}

const handleFocus = (i) => {
  inputs.value[i]?.select()
}

// ---- VERIFY OTP ----
const submitOtp = async () => {
  if (otp.value.length < 6) return
  error.value = ''
  loading.value = true

  try {
    const res = await fetch('/api/user/verify-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: route.query.email, otp: otp.value })
    })
    const data = await res.json()

    if (res.ok && data.status) {
      success.value = data.message
      setTimeout(() => router.push('/user-login'), 1500)
    } else {
      error.value = data.message
      triggerShake()
    }
  } catch {
    error.value = 'Network error. Please try again.'
    triggerShake()
  } finally {
    loading.value = false
  }
}

// ---- RESEND OTP ----
const resendOtp = async () => {
  error.value = ''
  try {
    const res = await fetch('/api/user/resend-otp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: route.query.email })
    })
    const data = await res.json()
    if (res.ok && data.status) {
      success.value = 'OTP resent successfully'
      otpDigits.value = ['', '', '', '', '', '']
      inputs.value[0]?.focus()
      startTimer()
    } else {
      error.value = data.message
    }
  } catch {
    error.value = 'Could not resend OTP'
  }
}

// ---- TIMER ----
const startTimer = () => {
  resendDisabled.value = true
  timer.value = 120
  const interval = setInterval(() => {
    timer.value--
    if (timer.value <= 0) {
      resendDisabled.value = false
      clearInterval(interval)
    }
  }, 1000)
}

// ---- SHAKE ----
const triggerShake = () => {
  shakeError.value = true
  setTimeout(() => { shakeError.value = false }, 600)
}

onMounted(() => {
  startTimer()
  inputs.value[0]?.focus()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

/* ---- RESET / BASE ---- */
* { box-sizing: border-box; margin: 0; padding: 0; }

.auth-body {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: #f4f4f4;
  font-family: 'DM Sans', sans-serif;
  overflow: hidden;
}

/* ---- GRAIN OVERLAY ---- */
.bg-grain {
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 0;
  opacity: 0;
}

/* ---- CARD ---- */
.otp-wrapper {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 440px;
  padding: 16px;
}

.otp-card {
  background: #ffffff;
  border: 2px solid #111111;
  border-radius: 16px;
  padding: 48px 40px 40px;
  text-align: center;
  box-shadow:
    6px 6px 0px #111111,
    0 16px 48px rgba(0,0,0,0.08);
}

/* ---- ICON ---- */
.icon-ring {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid #111;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 24px;
  color: #111;
  background: #f9f9f9;
}

/* ---- HEADING ---- */
h2 {
  font-family: 'Syne', sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #111111;
  letter-spacing: -0.3px;
  margin-bottom: 10px;
}

.subtitle {
  font-size: 13.5px;
  color: #888;
  line-height: 1.6;
  margin-bottom: 32px;
}

.email-highlight {
  color: #333;
  font-weight: 500;
}

/* ---- OTP BOXES ---- */
.otp-boxes {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-bottom: 28px;
}

.otp-box {
  width: 52px;
  height: 58px;
  border-radius: 10px;
  border: 2px solid #d0d0d0;
  background: #fafafa;
  color: #111111;
  font-family: 'Syne', sans-serif;
  font-size: 22px;
  font-weight: 700;
  text-align: center;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  caret-color: transparent;
}

.otp-box:focus {
  border-color: #111;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(0,0,0,0.08);
}

.otp-box.filled {
  border-color: #111;
  background: #fff;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20%       { transform: translateX(-6px); }
  40%       { transform: translateX(6px); }
  60%       { transform: translateX(-4px); }
  80%       { transform: translateX(4px); }
}

.otp-box.shake {
  animation: shake 0.5s ease;
  border-color: #e53e3e !important;
}

/* ---- VERIFY BUTTON ---- */
.btn-verify {
  width: 100%;
  height: 48px;
  border-radius: 10px;
  border: 2px solid #111;
  background: #111111;
  color: #ffffff;
  font-family: 'Syne', sans-serif;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s, opacity 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-verify:hover:not(:disabled) {
  background: #2a2a2a;
  transform: translateY(-1px);
}

.btn-verify:active:not(:disabled) {
  transform: translateY(0);
}

.btn-verify:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

/* ---- SPINNER ---- */
.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255,255,255,0.2);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ---- RESEND ---- */
.resend-section {
  margin-top: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.resend-label {
  font-size: 13px;
  color: #888;
}

.resend-btn {
  font-size: 13px;
  font-weight: 600;
  color: #111;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  text-decoration: underline;
  text-underline-offset: 3px;
  transition: color 0.2s;
}

.resend-btn:disabled {
  color: #bbb;
  text-decoration: none;
  cursor: default;
}

/* ---- ALERTS ---- */
.auth-alert {
  margin-top: 18px;
  padding: 11px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
}

.auth-alert.error {
  background: #fff5f5;
  border: 1.5px solid #feb2b2;
  color: #c53030;
}

.auth-alert.success {
  background: #f0fff4;
  border: 1.5px solid #9ae6b4;
  color: #276749;
}

/* ---- TRANSITIONS ---- */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s, transform 0.3s; }
.fade-enter-from { opacity: 0; transform: translateY(-6px); }
.fade-leave-to   { opacity: 0; }

/* ---- RESPONSIVE ---- */
@media (max-width: 480px) {
  .otp-card { padding: 36px 24px 30px; }
  .otp-box  { width: 44px; height: 52px; font-size: 20px; }
  .otp-boxes { gap: 8px; }
}
</style>
