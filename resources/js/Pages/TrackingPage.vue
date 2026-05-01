<template>
      <nav-component />
  <breadcrumb-component />
  <div class="track-wrap">

    <!-- Search Bar -->
    <div class="track-hero">
      <h1 class="track-title">Track Your Order</h1>
      <p class="track-sub">Enter your tracking number to see real-time order updates</p>
      <div class="track-input-row">
        <input
          v-model="trackingInput"
          type="text"
          placeholder="e.g. ORD-2024-0065"
          class="track-input"
          @keydown.enter="fetchTracking"
        />
        <button class="track-btn" @click="fetchTracking" :disabled="loading">
          <span v-if="loading" class="btn-spinner"></span>
          <span v-else>Track</span>
        </button>
      </div>
      <p v-if="error" class="track-error">{{ error }}</p>
    </div>

    <!-- Result -->
    <div v-if="order" class="track-result">

      <!-- Status Banner -->
      <div class="status-banner" :class="statusClass(order.status)">
        <div class="status-icon-wrap">
          <component :is="statusIcon(order.status)" class="status-icon" />
        </div>
        <div>
          <div class="status-label">{{ statusLabel(order.status) }}</div>
          <div class="status-meta">Order #{{ order.order_number }} &nbsp;·&nbsp; {{ formatDate(order.created_at) }}</div>
        </div>
        <div class="status-pill" :class="statusClass(order.status)">{{ ucfirst(order.status) }}</div>
      </div>

      <!-- Timeline -->
      <div class="timeline-card">
        <div class="card-head">Shipment Timeline</div>
        <div class="timeline">
          <div
            v-for="(step, i) in timeline"
            :key="i"
            class="tl-step"
            :class="{ done: step.done, active: step.active }"
          >
            <div class="tl-dot-col">
              <div class="tl-dot"></div>
              <div v-if="i < timeline.length - 1" class="tl-line"></div>
            </div>
            <div class="tl-body">
              <div class="tl-name">{{ step.label }}</div>
              <div class="tl-time">{{ step.time }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Two columns: Shipping + Payment -->
      <div class="info-grid">

        <!-- Shipping Info -->
        <div class="info-card">
          <div class="card-head">Shipping Details</div>
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
          <div class="card-head">Order Summary</div>
          <div class="info-rows">
            <div class="info-row" v-for="item in order.items" :key="item.id">
              <span class="info-lbl">{{ item.name }} <span class="qty-badge">x{{ item.quantity }}</span></span>
              <span class="info-val">${{ (item.price * item.quantity).toFixed(2) }}</span>
            </div>
            <div class="info-row total-row">
              <span class="info-lbl">Total</span>
              <span class="info-val total-val">${{ Number(order.total).toFixed(2) }}</span>
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

      <!-- Admin notes if any -->
      <div v-if="order.admin_notes" class="note-card">
        <div class="card-head">Note from Prosix</div>
        <p class="note-text">{{ order.admin_notes }}</p>
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
    // ✅ Pehle checkout orders mein dhundo
    const res = await axios.get('/api/track-order', {
      params: { tracking: trackingInput.value.trim() }
    })
    order.value = res.data
  } catch (e1) {
    try {
      // ✅ Nahi mila to PlaceOrder mein dhundo
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
  { key: 'new',        label: 'Order Placed',    icon: '📦' },
  { key: 'confirmed',  label: 'Confirmed',        icon: '✅' },
  { key: 'production', label: 'In Production',   icon: '🏭' },
  { key: 'shipped',    label: 'Shipped',          icon: '🚚' },
  { key: 'delivered',  label: 'Delivered',        icon: '🎉' },
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
  'new': 's-new',
  'confirmed': 's-confirmed',
  'production': 's-production',
  'shipped': 's-shipped',
  'delivered': 's-delivered',
  'cancelled': 's-cancelled',
}[s] || 's-new')

const statusLabel = (s) => ({
  'new': 'Order Received',
  'confirmed': 'Order Confirmed',
  'production': 'In Production',
  'shipped': 'On the Way',
  'delivered': 'Delivered!',
  'cancelled': 'Cancelled',
}[s] || s)

const statusIcon = () => 'div'

const ucfirst = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : ''

const formatDate = (d) => {
  if (!d) return ''
  return new Date(d).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.track-wrap {
  min-height: 100vh;
  background: #f8f8f8;
  font-family: 'DM Sans', 'Segoe UI', sans-serif;
  padding-bottom: 60px;
}

/* Hero */
.track-hero {
  background: #000;
  padding: 60px 24px 48px;
  text-align: center;
}
.track-title {
  font-size: 36px;
  font-weight: 700;
  color: #fff;
  margin: 0 0 10px;
  letter-spacing: -0.5px;
}
.track-sub {
  color: rgba(255,255,255,0.55);
  font-size: 15px;
  margin: 0 0 28px;
}
.track-input-row {
  display: flex;
  justify-content: center;
  gap: 0;
  max-width: 520px;
  margin: 0 auto;
}
.track-input {
  flex: 1;
  height: 50px;
  border: none;
  border-radius: 10px 0 0 10px;
  padding: 0 18px;
  font-size: 15px;
  font-family: inherit;
  background: #fff;
  color: #111;
  outline: none;
}
.track-btn {
  height: 50px;
  padding: 0 28px;
  background: #fff;
  color: #111;
  font-weight: 700;
  font-size: 15px;
  font-family: inherit;
  border: none;
  border-radius: 0 10px 10px 0;
  border-left: 1.5px solid #e5e5e5;
  cursor: pointer;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
}
.track-btn:hover { background: #f0f0f0; }
.track-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(0,0,0,0.2);
  border-top-color: #111;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.track-error {
  color: #ff6b6b;
  font-size: 13px;
  margin: 12px 0 0;
}

/* Result */
.track-result {
  max-width: 860px;
  margin: -24px auto 0;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Status Banner */
.status-banner {
  border-radius: 14px;
  padding: 20px 24px;
  display: flex;
  align-items: center;
  gap: 16px;
}
.s-new        { background: #ede9fe; }
.s-confirmed  { background: #dbeafe; }
.s-production { background: #fef3c7; }
.s-shipped    { background: #e0f2fe; }
.s-delivered  { background: #d1fae5; }
.s-cancelled  { background: #fee2e2; }

.status-label {
  font-size: 18px;
  font-weight: 700;
  color: #0a0a0a;
}
.status-meta {
  font-size: 13px;
  color: #666;
  margin-top: 2px;
}
.status-pill {
  margin-left: auto;
  padding: 5px 14px;
  border-radius: 99px;
  font-size: 13px;
  font-weight: 700;
}
.s-new .status-pill        { background: #5b21b6; color: #fff; }
.s-confirmed .status-pill  { background: #1e40af; color: #fff; }
.s-production .status-pill { background: #92400e; color: #fff; }
.s-shipped .status-pill    { background: #075985; color: #fff; }
.s-delivered .status-pill  { background: #065f46; color: #fff; }
.s-cancelled .status-pill  { background: #991b1b; color: #fff; }

/* Cards */
.timeline-card, .info-card, .note-card {
  background: #fff;
  border: 1.5px solid #e8e8e8;
  border-radius: 14px;
  overflow: hidden;
}
.card-head {
  padding: 14px 22px;
  border-bottom: 1.5px solid #f0f0f0;
  font-size: 14px;
  font-weight: 700;
  color: #0a0a0a;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* Timeline */
.timeline {
  padding: 20px 24px;
}
.tl-step {
  display: flex;
  gap: 16px;
  opacity: 0.35;
}
.tl-step.done, .tl-step.active { opacity: 1; }
.tl-dot-col {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex-shrink: 0;
}
.tl-dot {
  width: 14px; height: 14px;
  border-radius: 50%;
  background: #e0e0e0;
  border: 2px solid #ccc;
  flex-shrink: 0;
}
.tl-step.done .tl-dot  { background: #0a0a0a; border-color: #0a0a0a; }
.tl-step.active .tl-dot { background: #0a0a0a; border-color: #0a0a0a; box-shadow: 0 0 0 3px rgba(0,0,0,0.12); }
.tl-line {
  width: 2px;
  flex: 1;
  min-height: 28px;
  background: #e0e0e0;
  margin: 4px 0;
}
.tl-step.done .tl-line { background: #0a0a0a; }
.tl-body {
  padding-bottom: 20px;
}
.tl-name { font-size: 14px; font-weight: 600; color: #111; }
.tl-time { font-size: 12px; color: #888; margin-top: 2px; }

/* Info grid */
.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
@media(max-width: 640px) { .info-grid { grid-template-columns: 1fr; } }

.info-rows { padding: 8px 0; }
.info-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 10px 22px;
  border-bottom: 1px solid #f5f5f5;
  font-size: 14px;
  gap: 12px;
}
.info-row:last-child { border-bottom: none; }
.info-lbl { color: #888; }
.info-val { color: #111; font-weight: 500; text-align: right; }
.courier-track { font-family: 'DM Mono', monospace; font-size: 13px; letter-spacing: 0.05em; }
.qty-badge {
  display: inline-block;
  background: #f0f0f0;
  color: #555;
  font-size: 11px;
  padding: 1px 6px;
  border-radius: 4px;
  margin-left: 4px;
}
.total-row { border-top: 2px solid #0a0a0a; margin-top: 4px; }
.total-val { font-size: 16px; font-weight: 700; }
.pay-badge {
  padding: 3px 12px;
  border-radius: 99px;
  font-size: 12px;
  font-weight: 700;
}
.pay-paid    { background: #d1fae5; color: #065f46; }
.pay-pending { background: #fef3c7; color: #92400e; }

/* Note */
.note-text {
  padding: 16px 22px;
  font-size: 14px;
  color: #444;
  line-height: 1.6;
  margin: 0;
}

@media(max-width: 600px) {
  .track-title { font-size: 26px; }
  .track-input-row { flex-direction: column; }
  .track-input { border-radius: 10px 10px 0 0; }
  .track-btn  { border-radius: 0 0 10px 10px; border-left: none; border-top: 1.5px solid #e5e5e5; justify-content: center; }
}
</style>
