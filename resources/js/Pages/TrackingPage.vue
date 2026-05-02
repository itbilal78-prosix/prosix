<template>
  <nav-component />
  <breadcrumb-component />

  <div class="track-wrap">

    <!-- Hero / Search -->
    <div class="track-hero">
      <div class="hero-eyebrow">
        <span class="hero-dot"></span>
        Real-time tracking
      </div>
      <h1 class="track-title">Track Your <span class="accent">Order</span></h1>
      <p class="track-sub">Enter your order number to get live updates on your shipment status.</p>

      <div class="track-input-row">
        <div class="search-icon">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <circle cx="6.5" cy="6.5" r="4.5" stroke="currentColor" stroke-width="1.5"/>
            <path d="M10.5 10.5L14 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <input
          v-model="trackingInput"
          type="text"
          placeholder="e.g. ORD-2024-0065"
          class="track-input"
          @keydown.enter="fetchTracking"
        />
        <button class="track-btn" @click="fetchTracking" :disabled="loading">
          <span v-if="loading" class="btn-spinner"></span>
          <span v-else>Track &rarr;</span>
        </button>
      </div>
      <p class="search-hint">Find your order number in your confirmation email</p>
      <p v-if="error" class="track-error">{{ error }}</p>
    </div>

    <!-- Result -->
    <div v-if="order" class="track-result">

      <!-- Status Card -->
      <div class="status-card">
        <div class="status-top">
          <div class="status-icon-box" :class="statusClass(order.status)">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="1" y="3" width="15" height="13" rx="1" stroke="currentColor"/>
              <path d="M16 8h4l3 5v3h-7V8z" stroke="currentColor"/>
              <circle cx="5.5" cy="18.5" r="2.5" stroke="currentColor"/>
              <circle cx="18.5" cy="18.5" r="2.5" stroke="currentColor"/>
            </svg>
          </div>
          <div class="status-info">
            <div class="status-heading">{{ statusLabel(order.status) }}</div>
            <div class="status-meta">Order #{{ order.order_number }} &nbsp;·&nbsp; {{ formatDate(order.created_at) }}</div>
          </div>
          <div class="status-tag" :class="statusClass(order.status)">{{ ucfirst(order.status) }}</div>
        </div>

        <!-- Progress Stepper -->
        <div class="progress-wrap">
          <div class="progress-steps">
            <div
              v-for="(step, i) in timeline"
              :key="i"
              class="ps-item"
              :class="{ done: step.done, active: step.active }"
            >
              <div class="ps-connector" v-if="i < timeline.length - 1" :class="{ filled: step.done }"></div>
              <div class="ps-dot">
                <svg v-if="step.done && !step.active" viewBox="0 0 12 12" fill="none">
                  <path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div v-else-if="step.active" class="ps-inner-dot"></div>
              </div>
              <div class="ps-label">{{ step.label }}</div>
              <div class="ps-time">{{ step.time }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Two columns: Shipping + Order Summary -->
      <div class="info-grid">

        <!-- Shipping Info -->
        <div class="info-card">
          <div class="card-head">
            <span class="card-dot"></span>
            Shipping Details
          </div>
          <div class="info-rows">
            <div class="info-row">
              <span class="info-lbl">Recipient</span>
              <span class="info-val">{{ order.shipping_name }}</span>
            </div>
            <div class="info-row">
              <span class="info-lbl">Phone</span>
              <span class="info-val">{{ order.shipping_phone }}</span>
            </div>
            <div class="info-row">
              <span class="info-lbl">City</span>
              <span class="info-val">{{ order.shipping_city }}</span>
            </div>
            <div class="info-row">
              <span class="info-lbl">Address</span>
              <span class="info-val">{{ order.shipping_address }}</span>
            </div>
            <div class="info-row" v-if="order.courier_name">
              <span class="info-lbl">Courier</span>
              <span class="info-val">{{ order.courier_name }}</span>
            </div>
            <div class="info-row" v-if="order.tracking_number">
              <span class="info-lbl">Courier Tracking</span>
              <span class="info-val courier-track">{{ order.tracking_number }}</span>
            </div>
            <div class="info-row" v-if="order.dispatch_date">
              <span class="info-lbl">Dispatched</span>
              <span class="info-val">{{ formatDate(order.dispatch_date) }}</span>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="info-card">
          <div class="card-head">
            <span class="card-dot"></span>
            Order Summary
          </div>
          <div class="info-rows">
            <div class="info-row" v-for="item in order.items" :key="item.id">
              <span class="info-lbl">{{ item.name }} <span class="qty-badge">x{{ item.quantity }}</span></span>
              <span class="info-val">${{ (item.price * item.quantity).toFixed(2) }}</span>
            </div>
            <div class="info-row total-row">
              <span class="total-lbl">Total</span>
              <span class="total-val">${{ Number(order.total).toFixed(2) }}</span>
            </div>
            <div class="info-row">
              <span class="info-lbl">Payment</span>
              <span class="info-val">{{ ucfirst(order.payment_method) }}</span>
            </div>
            <div class="info-row">
              <span class="info-lbl">Payment Status</span>
              <span class="pay-badge" :class="order.payment_status === 'paid' ? 'pay-paid' : 'pay-pending'">
                {{ ucfirst(order.payment_status) }}
              </span>
            </div>
          </div>
        </div>

      </div>

      <!-- Admin Note -->
      <div v-if="order.admin_notes" class="note-card">
        <div class="note-icon">
          <svg viewBox="0 0 18 18" fill="none" stroke="#c9ff3b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="9" r="7"/>
            <path d="M9 8v4M9 6h.01"/>
          </svg>
        </div>
        <div>
          <div class="note-head">Note from Prosix</div>
          <p class="note-text">{{ order.admin_notes }}</p>
        </div>
      </div>

    </div>
  </div>

  <footer-component />
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const trackingInput = ref('')
const order = ref(null)
const loading = ref(false)
const error = ref('')

const fetchTracking = async () => {
  if (!trackingInput.value.trim()) {
    error.value = 'Please enter a tracking number.'
    return
  }
  loading.value = true
  error.value = ''
  order.value = null

  try {
    const res = await axios.get('/api/track-order', {
      params: { tracking: trackingInput.value.trim() }
    })
    order.value = res.data
  } catch (e1) {
    try {
      const res2 = await axios.get('/api/track-place-order', {
        params: { tracking: trackingInput.value.trim() }
      })
      order.value = res2.data
    } catch (e2) {
      if (e2.response?.status === 404) {
        error.value = 'No order found with this tracking number.'
      } else {
        error.value = 'Something went wrong. Please try again.'
      }
    }
  } finally {
    loading.value = false
  }
}

const statusSteps = [
  { key: 'new',        label: 'Placed'     },
  { key: 'confirmed',  label: 'Confirmed'  },
  { key: 'production', label: 'Production' },
  { key: 'shipped',    label: 'Shipped'    },
  { key: 'delivered',  label: 'Delivered'  },
]

const timeline = computed(() => {
  if (!order.value) return []
  const currentIndex = statusSteps.findIndex(s => s.key === order.value.status)
  return statusSteps.map((step, i) => ({
    label: step.label,
    done: i <= currentIndex,
    active: i === currentIndex,
    time: i <= currentIndex ? getStepTime(step.key) : 'Pending'
  }))
})

const getStepTime = (key) => {
  if (!order.value) return ''
  if (key === 'new') return formatDate(order.value.created_at)
  if (key === 'shipped' && order.value.dispatch_date) return formatDate(order.value.dispatch_date)
  if (key === 'delivered' && order.value.delivered_date) return formatDate(order.value.delivered_date)
  return 'Completed'
}

const statusClass = (s) => ({
  'new':        'st-new',
  'confirmed':  'st-confirmed',
  'production': 'st-production',
  'shipped':    'st-shipped',
  'delivered':  'st-delivered',
  'cancelled':  'st-cancelled',
}[s] || 'st-new')

const statusLabel = (s) => ({
  'new':        'Order Received',
  'confirmed':  'Order Confirmed',
  'production': 'In Production',
  'shipped':    'On the Way',
  'delivered':  'Delivered!',
  'cancelled':  'Cancelled',
}[s] || s)

const ucfirst = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : ''

const formatDate = (d) => {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap');

/* ── Base ── */
.track-wrap {
  min-height: 100vh;
  background: #f5f4f0;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
  padding-bottom: 60px;
}

/* ── Hero ── */
.track-hero {
  background: #0a0a0a;
  padding: 64px 24px 80px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.track-hero::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse 60% 60% at 50% 0%, rgba(201,255,59,0.07) 0%, transparent 70%);
  pointer-events: none;
}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #ffffff;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 5px 14px;
  border-radius: 99px;
  margin-bottom: 24px;
}
.hero-dot {
  width: 6px;
  height: 6px;
  background: #ffffff;
  border-radius: 50%;
  animation: blink 1.4s ease-in-out infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

.track-title {
  font-family: 'Syne', sans-serif;
  font-size: 48px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -1.5px;
  line-height: 1.1;
  margin: 0 0 14px;
}
.track-title .accent { color: #ffffff; }

.track-sub {
  color: rgba(255,255,255,0.45);
  font-size: 15px;
  margin: 0 0 32px;
  max-width: 420px;
  margin-left: auto;
  margin-right: auto;
}

/* ── Search Box ── */
.track-input-row {
  display: flex;
  max-width: 560px;
  margin: 0 auto;
  background: #161616;
  border: 1px solid #2a2a2a;
  border-radius: 12px;
  overflow: hidden;
  transition: border-color 0.2s;
}
.track-input-row:focus-within { border-color: #ffffff; }

.search-icon {
  display: flex;
  align-items: center;
  padding: 0 16px;
  color: rgba(255,255,255,0.3);
  flex-shrink: 0;
}
.track-input {
  flex: 1;
  background: none;
  border: none;
  outline: none;
  color: #fff;
  font-size: 15px;
  font-family: 'DM Mono', monospace;
  padding: 16px 0;
  letter-spacing: 0.04em;
}
.track-input::placeholder {
  color: rgba(255,255,255,0.2);
  font-family: 'DM Sans', sans-serif;
  letter-spacing: 0;
}
.track-btn {
  background: #ffffff;
  color: #0a0a0a;
  font-weight: 700;
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  border: none;
  padding: 0 24px;
  cursor: pointer;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  flex-shrink: 0;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}
.track-btn:hover { background: #e0e0e0; }
.track-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(0,0,0,0.2);
  border-top-color: #0a0a0a;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.search-hint {
  color: rgba(255,255,255,0.2);
  font-size: 12px;
  margin-top: 10px;
}
.track-error {
  color: #ff6b6b;
  font-size: 13px;
  margin-top: 12px;
}

/* ── Result wrapper ── */
.track-result {
  max-width: 900px;
  margin: -32px auto 0;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  position: relative;
  z-index: 2;
}

/* ── Status Card ── */
.status-card {
  background: #fff;
  border: 1px solid #e8e8e2;
  border-radius: 16px;
  overflow: hidden;
}
.status-top {
  padding: 22px 28px;
  display: flex;
  align-items: center;
  gap: 16px;
  border-bottom: 1px solid #f0f0ea;
}
.status-icon-box {
  width: 48px; height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.status-icon-box svg { width: 22px; height: 22px; }

.st-new        { background: #ede9fe; color: #000000; }
.st-confirmed  { background: #dbeafe; color: #000000; }
.st-production { background: #fef3c7; color: #000000; }
.st-shipped    { background: #e0f2fe; color: #000000; }
.st-delivered  { background: #d1fae5; color: #000000; }
.st-cancelled  { background: #fee2e2; color: #000000; }

.status-info { flex: 1; }
.status-heading {
  font-family: 'Syne', sans-serif;
  font-size: 20px;
  font-weight: 700;
  color: #0a0a0a;
}
.status-meta { font-size: 13px; color: #888; margin-top: 3px; }

.status-tag {
  flex-shrink: 0;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  padding: 6px 14px;
  border-radius: 99px;
}
.st-new.status-tag        { background: #000000; color: #fff; }
.st-confirmed.status-tag  { background: #000000; color: #fff; }
.st-production.status-tag { background: #000000; color: #fef3c7; }
.st-shipped.status-tag    { background: #000000; color: #e0f2fe; }
.st-delivered.status-tag  { background: #000000; color: #ffffff; }
.st-cancelled.status-tag  { background: #000000; color: #fff; }

/* ── Progress Stepper ── */
.progress-wrap { padding: 24px 28px 20px; }
.progress-steps {
  display: flex;
  align-items: flex-start;
  position: relative;
}
.ps-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}
.ps-connector {
  position: absolute;
  top: 13px;
  left: 50%;
  width: 100%;
  height: 2px;
  background: #e8e8e2;
  z-index: 0;
}
.ps-connector.filled { background: #0a0a0a; }

.ps-dot {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: #e8e8e2;
  border: 2px solid #d0d0ca;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.ps-item.done .ps-dot {
  background: #0a0a0a;
  border-color: #0a0a0a;
  color: #fff;
}
.ps-dot svg { width: 12px; height: 12px; }

.ps-item.active .ps-dot {
  background: #e6e6e6;
  border-color: #ffffff;
  box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.2);
}
.ps-inner-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #0a0a0a;
}

.ps-label {
  font-size: 11px;
  font-weight: 600;
  color: #bbb;
  margin-top: 8px;
  text-align: center;
  letter-spacing: 0.04em;
}
.ps-item.done .ps-label,
.ps-item.active .ps-label { color: #0a0a0a; }

.ps-time {
  font-size: 10px;
  color: #bbb;
  margin-top: 2px;
  text-align: center;
}
.ps-item.done .ps-time,
.ps-item.active .ps-time { color: #888; }

/* ── Info Grid ── */
.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
@media(max-width: 640px) { .info-grid { grid-template-columns: 1fr; } }

.info-card {
  background: #fff;
  border: 1px solid #e8e8e2;
  border-radius: 16px;
  overflow: hidden;
}

/* ── Card Head ── */
.card-head {
  padding: 14px 22px;
  border-bottom: 1px solid #f0f0ea;
  font-family: 'Syne', sans-serif;
  font-size: 12px;
  font-weight: 700;
  color: #0a0a0a;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  gap: 8px;
}
.card-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #ffffff;
  flex-shrink: 0;
}

/* ── Info Rows ── */
.info-rows { padding: 4px 0; }
.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 11px 22px;
  border-bottom: 1px solid #f5f5f1;
  font-size: 13.5px;
  gap: 12px;
}
.info-row:last-child { border-bottom: none; }
.info-lbl { color: #999; }
.info-val { color: #111; font-weight: 500; text-align: right; }

.courier-track {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  letter-spacing: 0.06em;
}
.qty-badge {
  display: inline-block;
  background: #f0f0ea;
  color: #666;
  font-size: 11px;
  padding: 2px 7px;
  border-radius: 4px;
  margin-left: 5px;
}
.total-row { border-top: 2px solid #0a0a0a !important; }
.total-lbl {
  font-family: 'Syne', sans-serif;
  font-weight: 700;
  font-size: 14px;
  color: #0a0a0a;
}
.total-val {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 18px;
  color: #0a0a0a;
}

.pay-badge {
  padding: 4px 12px;
  border-radius: 99px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.pay-paid    { background: #d1fae5; color: #065f46; }
.pay-pending { background: #fef3c7; color: #92400e; }

/* ── Note Card ── */
.note-card {
  background: #0a0a0a;
  border-radius: 16px;
  padding: 22px 28px;
  display: flex;
  gap: 16px;
  align-items: flex-start;
}
.note-icon {
  width: 36px; height: 36px;
  background: rgba(201,255,59,0.12);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.note-icon svg { width: 18px; height: 18px; }
.note-head {
  font-family: 'Syne', sans-serif;
  font-size: 12px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 6px;
}
.note-text {
  font-size: 14px;
  color: rgba(255,255,255,0.65);
  line-height: 1.7;
  margin: 0;
}

/* ── Mobile ── */
@media(max-width: 600px) {
  .track-title { font-size: 32px; }
  .track-input-row { flex-direction: column; border-radius: 12px; }
  .track-input { padding: 16px; }
  .track-btn { padding: 14px; justify-content: center; }
  .search-icon { display: none; }
  .status-top { flex-wrap: wrap; }
  .progress-wrap { padding: 20px 12px 16px; overflow-x: auto; }
  .progress-steps { min-width: 400px; }
}
</style>
