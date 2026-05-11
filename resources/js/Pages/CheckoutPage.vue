<template>
  <nav-component />
  <breadcrumb-component />

  <div class="checkout-page">

    <!-- EMPTY CART -->
    <div v-if="cartStore.items.length === 0 && !orderSuccess" class="empty-cart">
      <div class="empty-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      </div>
      <h3>Your cart is empty</h3>
      <p>Add some items to get started</p>
      <router-link to="/" class="btn-shop">Continue Shopping</router-link>
    </div>

    <!-- ✅ SUCCESS SCREEN -->
    <div v-else-if="orderSuccess" class="success-screen">
      <div class="success-card">
        <div class="success-icon">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <h2>Order Placed!</h2>
        <p>Your order has been submitted successfully.</p>

        <div class="order-num-box">
          <span class="order-num-label">Your Order Number</span>
          <span class="order-num-value">{{ orderNumber }}</span>
          <button class="copy-btn" @click="copyOrderNumber">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            {{ copied ? 'Copied!' : 'Copy' }}
          </button>
        </div>

        <div class="track-hint">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Use this number to track your order on our Track Order page
        </div>

        <div class="success-actions">
          <button class="btn-track" @click="router.push('/track')">Track My Order</button>
          <button class="btn-continue" @click="router.push('/')">Continue Shopping</button>
        </div>
      </div>
    </div>

    <div v-else class="checkout-wrapper">

      <!-- TOP BAR -->
      <div class="top-bar">
        <button class="btn-back" @click="router.push('/')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Back
        </button>
        <div class="logo-area">
          <span class="logo-text">Checkout</span>
        </div>
        <div class="secure-badge">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Secure
        </div>
      </div>

      <!-- STEP PROGRESS -->
      <div class="progress-bar">
        <template v-for="(label, idx) in steps" :key="idx">
          <div :class="['prog-step', { active: currentStep === idx+1, done: currentStep > idx+1 }]"
               @click="currentStep > idx+1 ? currentStep = idx+1 : null">
            <div class="prog-circle">
              <svg v-if="currentStep > idx+1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span v-else>{{ idx+1 }}</span>
            </div>
            <span class="prog-label">{{ label }}</span>
          </div>
          <div v-if="idx < steps.length-1" :class="['prog-line', { filled: currentStep > idx+1 }]"></div>
        </template>
      </div>

      <!-- MAIN GRID -->
      <div class="main-grid">

        <!-- LEFT -->
        <div class="left-col">

          <!-- STEP 1: Contact -->
          <transition name="fade-up" mode="out-in">
            <div v-if="currentStep === 1" key="s1" class="panel">
              <div class="panel-header">
                <div class="panel-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                  <h2>Contact Information</h2>
                  <p>We'll use this to send your order updates</p>
                </div>
              </div>
              <div class="panel-body">
                <div class="field-group">
                  <div class="field">
                    <label>Full Name <span class="req">*</span></label>
                    <input v-model="form.name" type="text" placeholder="John Doe" :class="{ error: errors.name }" @input="errors.name=''" />
                    <span v-if="errors.name" class="err-msg">{{ errors.name }}</span>
                  </div>
                </div>
                <div class="field-row">
                  <div class="field">
                    <label>Email Address</label>
                    <input v-model="form.email" type="email" placeholder="you@example.com" />
                  </div>
                  <div class="field">
                    <label>Phone Number <span class="req">*</span></label>
                    <input v-model="form.phone" type="tel" placeholder="+1 (555) 000-0000" :class="{ error: errors.phone }" @input="errors.phone=''" />
                    <span v-if="errors.phone" class="err-msg">{{ errors.phone }}</span>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <button class="btn-ghost" @click="router.push('/')">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                  Back to Shop
                </button>
                <button class="btn-primary" @click="validateStep1">
                  Continue to Shipping
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
              </div>
            </div>
          </transition>

          <!-- STEP 2: Shipping -->
          <transition name="fade-up" mode="out-in">
            <div v-if="currentStep === 2" key="s2" class="panel">
              <div class="panel-header">
                <div class="panel-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div>
                  <h2>Shipping Address</h2>
                  <p>Where should we deliver your order?</p>
                </div>
              </div>
              <div class="panel-body">
                <div class="field">
                  <label>Street Address <span class="req">*</span></label>
                  <textarea v-model="form.address" rows="2" placeholder="123 Main Street, Apt 4B" :class="{ error: errors.address }" @input="errors.address=''"></textarea>
                  <span v-if="errors.address" class="err-msg">{{ errors.address }}</span>
                </div>
                <div class="field-row">
                  <div class="field">
                    <label>City <span class="req">*</span></label>
                    <input v-model="form.city" type="text" placeholder="New York" :class="{ error: errors.city }" @input="errors.city=''" />
                    <span v-if="errors.city" class="err-msg">{{ errors.city }}</span>
                  </div>
                  <div class="field">
                    <label>Postal Code</label>
                    <input v-model="form.postalCode" type="text" placeholder="10001" />
                  </div>
                </div>
                <div class="field-row">
                  <div class="field">
                    <label>Province / State</label>
                    <input v-model="form.province" type="text" placeholder="e.g. Punjab" />
                  </div>
                  <div class="field">
                    <label>Country</label>
                    <select v-model="form.country">
                      <option value="">Select country</option>
                      <option>United States</option>
                      <option>Pakistan</option>
                      <option>United Kingdom</option>
                      <option>Canada</option>
                      <option>Australia</option>
                      <option>Germany</option>
                      <option>France</option>
                      <option>UAE</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="panel-footer">
                <button class="btn-ghost" @click="currentStep = 1">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                  Back
                </button>
                <button class="btn-primary" @click="validateStep2">
                  Continue to Payment
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
              </div>
            </div>
          </transition>

          <!-- STEP 3: Payment -->
          <transition name="fade-up" mode="out-in">
            <div v-if="currentStep === 3" key="s3" class="panel">
              <div class="panel-header">
                <div class="panel-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div>
                  <h2>Payment Method</h2>
                  <p>Choose how you'd like to pay</p>
                </div>
              </div>

              <!-- Payment Tabs -->
              <div class="pay-tabs-row">
                <button v-for="g in paymentGroups" :key="g.id"
                        :class="['pay-chip', { active: activeGroup === g.id }]"
                        @click="switchPaymentGroup(g)">
                  <span v-html="g.svg"></span>
                  {{ g.label }}
                </button>
              </div>

              <div class="panel-body pay-body">
                <!-- CARD -->
                <transition name="fade-up" mode="out-in">
                  <div v-if="activeGroup === 'card'" key="card">
                    <div class="brand-row">
                      <svg viewBox="0 0 50 30" class="brand-svg"><rect width="50" height="30" rx="4" fill="#fff" stroke="#e8e8e8"/><circle cx="19" cy="15" r="8" fill="#EB001B" opacity=".9"/><circle cx="31" cy="15" r="8" fill="#F79E1B" opacity=".9"/><path d="M25 8.6A8 8 0 0 1 28 15a8 8 0 0 1-3 6.4A8 8 0 0 1 22 15a8 8 0 0 1 3-6.4z" fill="#FF5F00"/></svg>
                      <svg viewBox="0 0 60 30" class="brand-svg"><rect width="60" height="30" rx="4" fill="#fff" stroke="#e8e8e8"/><text x="7" y="21" font-family="Arial" font-weight="900" font-size="14" fill="#1A1F71">VISA</text></svg>
                      <div class="stripe-pill">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#635bff" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Powered by Stripe
                      </div>
                    </div>
                    <div class="field">
                      <label>Card Number <span class="req">*</span></label>
                      <div id="stripe-card-number" class="stripe-box"></div>
                    </div>
                    <div class="field-row" style="margin-top:12px">
                      <div class="field">
                        <label>Expiry Date <span class="req">*</span></label>
                        <div id="stripe-card-expiry" class="stripe-box"></div>
                      </div>
                      <div class="field">
                        <label>CVC <span class="req">*</span></label>
                        <div id="stripe-card-cvc" class="stripe-box"></div>
                      </div>
                    </div>
                    <div class="card-type-row" v-if="detectedCardBrand">
                      <span class="card-brand-tag">{{ detectedCardBrand }}</span>
                      <span class="card-brand-label">detected</span>
                    </div>
                    <div v-if="stripeError" class="stripe-err">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                      {{ stripeError }}
                    </div>
                  </div>
                </transition>

                <!-- COMING SOON -->
                <transition name="fade-up" mode="out-in">
                  <div v-if="['wallets','p2p','paypal','wire','cod'].includes(activeGroup)" :key="activeGroup" class="coming-soon-block">
                    <div class="cs-icon">
                      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h4>Coming Soon</h4>
                    <p>This payment method is not available yet.<br/>Please use <strong>Card</strong> to complete your order.</p>
                  </div>
                </transition>
              </div>

              <div class="panel-footer">
                <button class="btn-ghost" @click="currentStep = 2">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                  Back
                </button>
                <button class="btn-place" :disabled="activeGroup !== 'card' || loading" @click="placeOrder">
                  <svg v-if="!loading" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  <span v-if="loading" class="spin"></span>
                  {{ loading ? 'Processing...' : `Place Order — $${totalAmount}` }}
                </button>
              </div>
            </div>
          </transition>
        </div>

        <!-- RIGHT: Order Summary -->
        <aside class="right-col">
          <div class="summary-panel">
            <h3>Order Summary</h3>
            <div class="sum-items">
              <div v-for="item in cartStore.items" :key="item.id + item.size" class="sum-item">
                <div class="thumb-wrap">
                  <img :src="item.image" :alt="item.name" />
                  <span class="qty-badge">{{ item.quantity }}</span>
                </div>
                <div class="item-info">
                  <p class="item-name">{{ item.name }}</p>
                  <p class="item-size">Size: {{ item.size }}</p>
                  <p v-if="item.stock_quantity" class="item-stock">{{ item.stock_quantity }} left</p>
                  <div class="qty-row">
                    <button class="qty-btn" @click="decreaseQty(item)" :disabled="item.quantity <= 1">
                      <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <span>{{ item.quantity }}</span>
                    <button class="qty-btn" @click="increaseQty(item)" :disabled="item.stock_quantity && item.quantity >= Number(item.stock_quantity)">
                      <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                  </div>
                </div>
                <div class="item-right">
                  <span class="item-price">${{ (Number(item.price) * item.quantity).toFixed(2) }}</span>
                  <button class="del-btn" @click="removeItem(item)">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                </div>
              </div>
            </div>
            <div class="divider"></div>
            <div class="sum-rows">
              <div class="sum-row"><span>Subtotal</span><span>${{ subtotal.toFixed(2) }}</span></div>
              <div class="sum-row">
                <span>Shipping</span>
                <span :class="shipping === 0 ? 'tag-free' : ''">{{ shippingLabel }}</span>
              </div>
            </div>
            <div class="divider"></div>
            <div class="sum-total"><span>Total</span><strong>${{ totalAmount }}</strong></div>
            <div class="trust-list">
              <div class="trust-item">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                SSL Secure Checkout
              </div>
              <div class="trust-item">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
                60-Day Free Returns
              </div>
              <div class="trust-item">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                Fast Worldwide Delivery
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useCartStore } from '@/store/cart'
import { useRouter } from 'vue-router'

const cartStore = useCartStore()
const router    = useRouter()

// ✅ Apni live key yahan hai — real payments chalenge
// const STRIPE_PK = 'pk_test_51T8ITpHdpb4oZa9ZRkYey6WFkWkp9YsKftIp9WfBYMeYI76cCUCuVm0PoPu93IFH072Zk1DHLD3wFjfBp5kFjdAc00NzHMVUfk'
const STRIPE_PK = 'pk_live_51Me8FHImWDOcEyespCiF4IVvemDbUaKUlQk5U3UY5QBgR0Z3bcDrVCJbjfG2rYwNaMS5Dou34Oe7GAMwREHGGs6P000VDG7j3M'

const loading           = ref(false)
const orderNumber       = ref('')
const orderSuccess      = ref(false)
const copied            = ref(false)
const currentStep       = ref(1)
const activeGroup       = ref('card')
const stripeError       = ref('')
const detectedCardBrand = ref('')

let stripe     = null
let cardNumber = null
let cardExpiry = null
let cardCvc    = null

const steps = ['Contact', 'Shipping', 'Payment']

const form = ref({
  name: '', email: '', phone: '',
  address: '', city: '', postalCode: '', province: '', country: '',
  deliveryDays: 'standard',
  paymentMethod: 'stripe',
})
const errors = ref({ name: '', phone: '', address: '', city: '' })

// ✅ Copy order number
const copyOrderNumber = async () => {
  try {
    await navigator.clipboard.writeText(orderNumber.value)
    copied.value = true
    setTimeout(() => copied.value = false, 2000)
  } catch (e) {}
}

const svgCard   = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>'
const svgWallet = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>'
const svgDollar = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
const svgGlobe  = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>'
const svgWire   = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><polyline points="8 8 3 12 8 16"/><polyline points="16 8 21 12 16 16"/></svg>'
const svgBox    = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>'

// const paymentGroups = [
//   { id: 'card',    svg: svgCard,   label: 'Card',   defaultMethod: 'stripe'   },
//   { id: 'wallets', svg: svgWallet, label: 'Wallets',defaultMethod: 'applepay' },
//   { id: 'p2p',     svg: svgDollar, label: 'P2P',    defaultMethod: 'cashapp'  },
//   { id: 'paypal',  svg: svgGlobe,  label: 'PayPal', defaultMethod: 'paypal'   },
//   { id: 'wire',    svg: svgWire,   label: 'Wire',   defaultMethod: 'wire'     },
//   { id: 'cod',     svg: svgBox,    label: 'COD',    defaultMethod: 'cod'      },
// ]
const paymentGroups = [
  { id: 'card',    svg: svgCard,   label: 'Card',   defaultMethod: 'stripe'   },
  { id: 'wallets', svg: svgWallet, label: 'Wallets',defaultMethod: 'applepay' },
  { id: 'p2p',     svg: svgDollar, label: 'P2P',    defaultMethod: 'cashapp'  },
  { id: 'paypal',  svg: svgGlobe,  label: 'PayPal', defaultMethod: 'paypal'   },
  { id: 'wire',    svg: svgWire,   label: 'Wire',   defaultMethod: 'wire'     },
]
const formatPrice = (p) => {
  if (typeof p === 'string') return parseFloat(p.replace(/[^0-9.]/g, '')) || 0
  return Number(p) || 0
}

const subtotal = computed(() =>
  cartStore.items.reduce((s, i) => s + formatPrice(i.price) * i.quantity, 0)
)
const shipping = computed(() => {
  let total = 0
  for (const item of cartStore.items) {
    if (!item.shipping_enabled) continue
    const cost      = formatPrice(item.shipping_cost || 0)
    const freeAbove = item.free_shipping_above ? formatPrice(item.free_shipping_above) : null
    if (freeAbove !== null && subtotal.value >= freeAbove) continue
    total += cost * item.quantity
  }
  return parseFloat(total.toFixed(2))
})
const shippingLabel  = computed(() => shipping.value === 0 ? 'FREE' : `$${shipping.value.toFixed(2)}`)
const totalAmount    = computed(() => (subtotal.value + shipping.value).toFixed(2))

const loadStripe = () => new Promise((resolve) => {
  if (window.Stripe) { resolve(window.Stripe); return }
  const s = document.createElement('script')
  s.src = 'https://js.stripe.com/v3/'
  s.onload = () => resolve(window.Stripe)
  document.head.appendChild(s)
})

const mountStripeElements = async () => {
  await nextTick()
  if (!document.getElementById('stripe-card-number')) return
  if (cardNumber) { try { cardNumber.destroy() } catch(e) {} cardNumber = null }
  if (cardExpiry) { try { cardExpiry.destroy() } catch(e) {} cardExpiry = null }
  if (cardCvc)    { try { cardCvc.destroy()    } catch(e) {} cardCvc    = null }
  if (!stripe) {
    const S = await loadStripe()
    stripe = S(STRIPE_PK)
  }
  const elements = stripe.elements({
    fonts: [{ cssSrc: 'https://fonts.googleapis.com/css2?family=Sora:wght@400;500&display=swap' }]
  })
  const style = {
    base: { fontFamily: "'Sora', sans-serif", fontSize: '14px', color: '#111', letterSpacing: '0.02em', '::placeholder': { color: '#bbb' } },
    invalid: { color: '#e53e3e', iconColor: '#e53e3e' }
  }
  cardNumber = elements.create('cardNumber', { style, showIcon: true })
  cardExpiry = elements.create('cardExpiry', { style })
  cardCvc    = elements.create('cardCvc',    { style })
  cardNumber.mount('#stripe-card-number')
  cardExpiry.mount('#stripe-card-expiry')
  cardCvc.mount('#stripe-card-cvc')
  cardNumber.on('change', (e) => {
    stripeError.value = e.error ? e.error.message : ''
    const brands = { visa: 'Visa', mastercard: 'Mastercard', amex: 'Amex', discover: 'Discover', jcb: 'JCB', diners: 'Diners', unionpay: 'UnionPay' }
    detectedCardBrand.value = (e.brand && e.brand !== 'unknown') ? (brands[e.brand] || e.brand) : ''
  })
  cardExpiry.on('change', (e) => { if (e.error) stripeError.value = e.error.message })
  cardCvc.on('change',    (e) => { if (e.error) stripeError.value = e.error.message })
}

onMounted(() => {
  if (currentStep.value === 3 && activeGroup.value === 'card') mountStripeElements()
})
watch(currentStep, (v) => {
  if (v === 3 && activeGroup.value === 'card') setTimeout(mountStripeElements, 150)
})

const switchPaymentGroup = (g) => {
  activeGroup.value = g.id
  form.value.paymentMethod = g.defaultMethod
  if (g.id === 'card') setTimeout(mountStripeElements, 150)
}

const validateStep1 = () => {
  errors.value = { name: '', phone: '', address: '', city: '' }
  let ok = true
  if (!form.value.name.trim())  { errors.value.name  = 'Full name is required'; ok = false }
  if (!form.value.phone.trim()) { errors.value.phone = 'Phone number is required'; ok = false }
  if (ok) currentStep.value = 2
}
const validateStep2 = () => {
  errors.value = { name: '', phone: '', address: '', city: '' }
  let ok = true
  if (!form.value.address.trim()) { errors.value.address = 'Address is required'; ok = false }
  if (!form.value.city.trim())    { errors.value.city    = 'City is required'; ok = false }
  if (ok) currentStep.value = 3
}

const increaseQty = (item) => {
  const maxQty = item.stock_quantity ? Number(item.stock_quantity) : null
  if (maxQty !== null && item.quantity >= maxQty) return
  const idx = cartStore.items.findIndex(i => i.id === item.id && i.size === item.size)
  if (idx !== -1) {
    typeof cartStore.updateQuantity === 'function'
      ? cartStore.updateQuantity(item.id, item.size, item.quantity + 1)
      : cartStore.items[idx].quantity++
  }
}
const decreaseQty = (item) => {
  if (item.quantity <= 1) return
  const idx = cartStore.items.findIndex(i => i.id === item.id && i.size === item.size)
  if (idx !== -1) {
    typeof cartStore.updateQuantity === 'function'
      ? cartStore.updateQuantity(item.id, item.size, item.quantity - 1)
      : cartStore.items[idx].quantity--
  }
}
const removeItem = (item) => {
  const idx = cartStore.items.findIndex(i => i.id === item.id && i.size === item.size)
  if (idx !== -1) {
    typeof cartStore.removeFromCart === 'function'
      ? cartStore.removeFromCart(item.id, item.size)
      : cartStore.items.splice(idx, 1)
  }
}

const placeOrder = async () => {
  stripeError.value = ''
  if (activeGroup.value !== 'card') {
    stripeError.value = 'Please select Card payment to place order.'
    return
  }
  loading.value = true
  try {
    const { paymentMethod, error } = await stripe.createPaymentMethod({
      type: 'card',
      card: cardNumber,
      billing_details: {
        name:  form.value.name,
        phone: form.value.phone,
        email: form.value.email || undefined,
      }
    })
    if (error) { stripeError.value = error.message; loading.value = false; return }

    const orderPayload = {
      cart: cartStore.items.map(i => ({
  id: i.id, name: i.name, price: i.price,
  quantity: i.quantity, size: i.size, image: i.image,
  notes: i.notes || '',  // ✅ NOTES ADD
})),
      checkout: {
        name:          form.value.name,
        email:         form.value.email    || '',
        phone:         form.value.phone,
        address:       form.value.address,
        city:          form.value.city,
        postalCode:    form.value.postalCode  || '',
        province:      form.value.province    || '',
        country:       form.value.country     || '',
        deliveryDays:  form.value.deliveryDays || 'standard',
        paymentMethod: 'stripe',
        stripeToken:   paymentMethod.id,
        total:         totalAmount.value,
        shipping:      shipping.value,
      }
    }

    const res = await axios.post('/api/orders', orderPayload, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('auth_token') || localStorage.getItem('token') || ''}`
      }
    })

    // ✅ Backend se aaya hua order number save karo
    orderNumber.value  = res.data.order_number
    orderSuccess.value = true
    cartStore.clearCart()

  } catch (e) {
    const errData = e.response?.data
    if (errData?.errors) {
      alert('Validation:\n' + Object.values(errData.errors).flat().join('\n'))
    } else {
      alert(errData?.message || 'Something went wrong.')
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap');
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ✅ SUCCESS SCREEN */
.success-screen {
  min-height: calc(100vh - 110px);
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F2F2F7;
  padding: 40px 20px;
}
.success-card {
  background: #fff;
  border: 1.5px solid #E5E5EA;
  border-radius: 24px;
  padding: 48px 40px;
  text-align: center;
  max-width: 480px;
  width: 100%;
  box-shadow: 0 8px 40px rgba(0,0,0,.08);
}
.success-icon {
  width: 72px; height: 72px;
  border-radius: 50%;
  background: #D1FAE5;
  border: 2px solid #6EE7B7;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 20px;
  color: #059669;
}
.success-card h2 {
  font-size: 26px; font-weight: 800;
  letter-spacing: -0.5px; margin-bottom: 8px;
}
.success-card > p {
  color: #8E8E93; font-size: 14px; margin-bottom: 28px;
}
.order-num-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #F2F2F7;
  border: 1.5px solid #E5E5EA;
  border-radius: 14px;
  padding: 14px 18px;
  margin-bottom: 14px;
}
.order-num-label {
  font-size: 11px; font-weight: 700;
  color: #8E8E93; text-transform: uppercase;
  letter-spacing: .8px; white-space: nowrap;
}
.order-num-value {
  font-family: 'DM Mono', monospace;
  font-size: 18px; font-weight: 700;
  color: #111; flex: 1; text-align: center;
  letter-spacing: 1px;
}
.copy-btn {
  display: inline-flex; align-items: center; gap: 5px;
  height: 32px; padding: 0 12px;
  background: #111; color: #fff;
  border: none; border-radius: 8px;
  font-size: 12px; font-weight: 600;
  cursor: pointer; font-family: 'Sora', sans-serif;
  transition: background .2s; white-space: nowrap;
}
.copy-btn:hover { background: #333; }
.track-hint {
  display: inline-flex; align-items: center; gap: 6px;
  font-size: 12px; color: #8E8E93;
  background: #FFF9C4; border: 1.5px solid #FDE68A;
  border-radius: 10px; padding: 8px 14px;
  margin-bottom: 28px; text-align: left;
}
.success-actions {
  display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;
}
.btn-track {
  height: 46px; padding: 0 24px;
  background: #111; color: #fff;
  border: none; border-radius: 12px;
  font-family: 'Sora', sans-serif;
  font-size: 13px; font-weight: 700;
  cursor: pointer; transition: background .2s;
}
.btn-track:hover { background: #333; }
.btn-continue {
  height: 46px; padding: 0 24px;
  background: #fff; color: #555;
  border: 1.5px solid #E5E5EA; border-radius: 12px;
  font-family: 'Sora', sans-serif;
  font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all .2s;
}
.btn-continue:hover { border-color: #111; color: #111; }

/* EXISTING STYLES (same as before) */
.checkout-page { font-family: 'Sora', sans-serif; min-height: calc(100vh - 110px); background: #F2F2F7; padding: 20px 20px 60px; color: #111; }
.checkout-wrapper { width: 100%; max-width: 100%; margin: 0; padding: 0; }
.empty-cart { display: flex; flex-direction: column; align-items: center; padding: 120px 20px; gap: 14px; text-align: center; }
.empty-icon { width: 88px; height: 88px; background: #fff; border-radius: 50%; border: 1.5px solid #E5E5EA; display: flex; align-items: center; justify-content: center; color: #C7C7CC; }
.empty-cart h3 { font-size: 22px; font-weight: 700; }
.empty-cart p  { font-size: 13px; color: #8E8E93; }
.btn-shop { display: inline-block; padding: 12px 28px; background: #111; color: #fff; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; margin-top: 8px; transition: background .2s; }
.btn-shop:hover { background: #333; }
.top-bar { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
.btn-back { display: inline-flex; align-items: center; gap: 6px; height: 38px; padding: 0 14px; background: #fff; border: 1.5px solid #E5E5EA; border-radius: 10px; font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 600; color: #555; cursor: pointer; transition: all .2s; }
.btn-back:hover { background: #f7f7f7; color: #111; border-color: #ccc; }
.logo-area { flex: 1; }
.logo-text { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
.secure-badge { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: #48BB78; background: #F0FFF4; border: 1.5px solid #C6F6D5; border-radius: 20px; padding: 5px 12px; }
.progress-bar { display: flex; flex-direction: row; align-items: center; background: #fff; border-radius: 16px; border: 1.5px solid #E5E5EA; padding: 16px 32px; margin-bottom: 18px; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
.prog-step { display: flex; flex-direction: row; align-items: center; gap: 10px; }
.prog-circle { width: 36px; height: 36px; border-radius: 50%; background: #E5E5EA; color: #8E8E93; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; transition: all .3s; }
.prog-step.active .prog-circle { background: #111; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,.18); }
.prog-step.done .prog-circle   { background: #111; color: #fff; cursor: pointer; }
.prog-label { font-size: 13px; font-weight: 600; color: #C7C7CC; transition: color .3s; white-space: nowrap; }
.prog-step.active .prog-label, .prog-step.done .prog-label { color: #111; }
.prog-line { flex: 1; height: 2px; background: #E5E5EA; margin: 0 14px; border-radius: 2px; transition: background .4s; min-width: 40px; }
.prog-line.filled { background: #111; }
.main-grid { display: grid; grid-template-columns: 1fr 400px; gap: 20px; align-items: start; width: 100%; }
@media (max-width: 1100px) { .main-grid { grid-template-columns: 1fr 340px; } }
@media (max-width: 900px)  { .main-grid { grid-template-columns: 1fr; } .right-col { order: -1; } }
.panel { background: #fff; border: 1.5px solid #E5E5EA; border-radius: 20px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.panel-header { display: flex; align-items: center; gap: 14px; padding: 20px 28px; border-bottom: 1.5px solid #F2F2F7; background: #FAFAFA; }
.panel-icon { width: 42px; height: 42px; background: #F2F2F7; border-radius: 12px; border: 1.5px solid #E5E5EA; display: flex; align-items: center; justify-content: center; color: #555; flex-shrink: 0; }
.panel-header h2 { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
.panel-header p  { font-size: 12px; color: #8E8E93; }
.panel-body { padding: 22px 28px; display: flex; flex-direction: column; gap: 16px; }
.pay-body   { padding: 18px 28px; }
.panel-footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 28px; border-top: 1.5px solid #F2F2F7; background: #FAFAFA; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 11px; font-weight: 700; color: #8E8E93; text-transform: uppercase; letter-spacing: .8px; }
.req { color: #FF3B30; }
.field input, .field select, .field textarea { height: 46px; padding: 0 14px; border: 1.5px solid #E5E5EA; border-radius: 12px; font-family: 'Sora', sans-serif; font-size: 14px; color: #111; background: #FAFAFA; transition: all .2s; width: 100%; }
.field textarea { height: auto; padding: 12px 14px; resize: none; }
.field input:focus, .field select:focus, .field textarea:focus { outline: none; border-color: #111; background: #fff; box-shadow: 0 0 0 4px rgba(17,17,17,.06); }
.field input.error, .field textarea.error { border-color: #FF3B30; }
.err-msg { font-size: 11px; color: #FF3B30; }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.btn-primary { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 22px; background: #111; color: #fff; border: none; border-radius: 12px; font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .2s; }
.btn-primary:hover { background: #2c2c2c; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,.15); }
.btn-ghost { display: inline-flex; align-items: center; gap: 8px; height: 46px; padding: 0 18px; background: #fff; color: #555; border: 1.5px solid #E5E5EA; border-radius: 12px; font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s; }
.btn-ghost:hover { background: #f7f7f7; color: #111; border-color: #ccc; }
.btn-place { display: inline-flex; align-items: center; gap: 8px; height: 50px; padding: 0 28px; background: #111; color: #fff; border: none; border-radius: 14px; font-family: 'Sora', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .2s; letter-spacing: -.1px; }
.btn-place:hover:not(:disabled) { background: #222; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,0,0,.18); }
.btn-place:disabled { opacity: .35; cursor: not-allowed; }
.spin { width: 14px; height: 14px; border: 2px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.pay-tabs-row { display: flex; flex-wrap: wrap; gap: 8px; padding: 14px 28px; border-bottom: 1.5px solid #F2F2F7; background: #FAFAFA; }
.pay-chip { display: inline-flex; align-items: center; gap: 6px; height: 36px; padding: 0 14px; background: #fff; border: 1.5px solid #E5E5EA; border-radius: 10px; cursor: pointer; color: #555; font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 600; transition: all .18s; }
.pay-chip:hover { border-color: #bbb; color: #111; }
.pay-chip.active { background: #111; color: #fff; border-color: #111; }
.brand-row { display: flex; align-items: center; gap: 8px; margin-bottom: 18px; padding-bottom: 16px; border-bottom: 1.5px solid #F2F2F7; flex-wrap: wrap; }
.brand-svg { height: 28px; width: 46px; }
.stripe-pill { display: flex; align-items: center; gap: 5px; margin-left: auto; font-size: 10px; font-weight: 700; color: #635bff; background: #EEF2FF; border-radius: 6px; padding: 4px 9px; }
.stripe-box { padding: 13px 14px; border: 1.5px solid #E5E5EA; border-radius: 12px; background: #FAFAFA; transition: all .2s; min-height: 46px; }
.card-type-row { display: flex; align-items: center; gap: 6px; margin-top: 12px; }
.card-brand-tag { background: #111; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 6px; letter-spacing: .5px; text-transform: uppercase; }
.card-brand-label { font-size: 11px; color: #8E8E93; }
.stripe-err { display: flex; align-items: center; gap: 7px; color: #FF3B30; font-size: 12px; margin-top: 12px; background: #FFF5F5; border: 1.5px solid #FED7D7; border-radius: 10px; padding: 10px 12px; }
.coming-soon-block { text-align: center; padding: 36px 20px; display: flex; flex-direction: column; align-items: center; gap: 10px; }
.cs-icon { width: 64px; height: 64px; background: #F2F2F7; border: 1.5px solid #E5E5EA; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #8E8E93; margin-bottom: 4px; }
.coming-soon-block h4 { font-size: 17px; font-weight: 800; color: #111; letter-spacing: -.3px; }
.coming-soon-block p  { font-size: 13px; color: #8E8E93; line-height: 1.7; }
.coming-soon-block p strong { color: #111; }
.fade-up-enter-active, .fade-up-leave-active { transition: all .22s ease; }
.fade-up-enter-from { opacity: 0; transform: translateY(10px); }
.fade-up-leave-to   { opacity: 0; transform: translateY(-6px); }
.right-col { position: sticky; top: 22px; }
.summary-panel { background: #fff; border: 1.5px solid #E5E5EA; border-radius: 20px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
.summary-panel h3 { font-size: 15px; font-weight: 700; margin-bottom: 18px; }
.sum-items { display: flex; flex-direction: column; gap: 16px; }
.sum-item  { display: flex; align-items: flex-start; gap: 12px; }
.thumb-wrap { position: relative; flex-shrink: 0; }
.thumb-wrap img { width: 60px; height: 60px; border-radius: 12px; object-fit: contain; border: 1.5px solid #E5E5EA; background: #FAFAFA; padding: 4px; }
.qty-badge { position: absolute; top: -6px; right: -6px; width: 18px; height: 18px; background: #111; color: #fff; border-radius: 50%; font-size: 9px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
.item-info { flex: 1; min-width: 0; }
.item-name { font-size: 12.5px; font-weight: 600; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.item-size  { font-size: 10.5px; color: #C7C7CC; margin-bottom: 4px; }
.item-stock { font-size: 10px; color: #FF9F0A; font-weight: 600; margin-bottom: 6px; }
.qty-row { display: inline-flex; align-items: center; border: 1.5px solid #E5E5EA; border-radius: 8px; overflow: hidden; }
.qty-btn { width: 26px; height: 26px; background: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #555; transition: background .15s; }
.qty-btn:hover:not(:disabled) { background: #F2F2F7; }
.qty-btn:disabled { opacity: .3; cursor: not-allowed; }
.qty-row span { width: 28px; text-align: center; font-size: 12px; font-weight: 600; border-left: 1.5px solid #E5E5EA; border-right: 1.5px solid #E5E5EA; line-height: 26px; }
.item-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }
.item-price { font-size: 13px; font-weight: 700; }
.del-btn { background: none; border: none; cursor: pointer; color: #C7C7CC; display: flex; transition: color .15s; padding: 0; }
.del-btn:hover { color: #FF3B30; }
.divider { height: 1.5px; background: #F2F2F7; margin: 16px 0; }
.sum-rows { display: flex; flex-direction: column; gap: 9px; }
.sum-row { display: flex; justify-content: space-between; font-size: 13px; color: #555; }
.tag-free { color: #000; font-weight: 700; }
.sum-total { display: flex; justify-content: space-between; align-items: baseline; font-size: 17px; }
.sum-total span   { font-weight: 600; }
.sum-total strong { font-weight: 800; font-size: 20px; letter-spacing: -0.5px; }
.trust-list { display: flex; flex-direction: column; gap: 8px; margin-top: 18px; padding-top: 16px; border-top: 1.5px solid #F2F2F7; }
.trust-item { display: flex; align-items: center; gap: 8px; font-size: 11.5px; color: #8E8E93; }
.trust-item svg { color: #C7C7CC; flex-shrink: 0; }
</style>

