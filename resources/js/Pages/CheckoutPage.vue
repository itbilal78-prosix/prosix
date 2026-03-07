<template>
  <nav-component />
  <breadcrumb-component />

  <div class="checkout-page">

    <!-- EMPTY CART -->
    <div v-if="cartStore.items.length === 0" class="empty-cart">
      <div class="empty-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      </div>
      <h3>Your cart is empty</h3>
      <p>Add some items to get started</p>
      <router-link to="/" class="btn-shop">Continue Shopping</router-link>
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
                      <svg viewBox="0 0 60 30" class="brand-svg"><rect width="60" height="30" rx="4" fill="#fff" stroke="#e8e8e8"/><text x="5" y="13" font-family="Arial" font-weight="700" font-size="7" fill="#006FCF">AMERICAN</text><text x="5" y="22" font-family="Arial" font-weight="700" font-size="7" fill="#006FCF">EXPRESS</text></svg>
                      <div class="stripe-pill">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#635bff" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Powered by Stripe
                      </div>
                    </div>
                    <div class="field">
                      <label>Card Information <span class="req">*</span></label>
                      <div id="stripe-card-element" class="stripe-box"></div>
                    </div>
                    <div v-if="stripeError" class="stripe-err">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                      {{ stripeError }}
                    </div>
                  </div>
                </transition>

                <!-- WALLETS -->
                <transition name="fade-up" mode="out-in">
                  <div v-if="activeGroup === 'wallets'" key="wallets">
                    <p class="pay-hint">Select your preferred digital wallet.</p>
                    <div class="option-list">
                      <button v-for="w in walletOptions" :key="w.id"
                              :class="['option-item', { selected: form.paymentMethod === w.id }]"
                              @click="form.paymentMethod = w.id">
                        <span v-html="w.svg"></span>
                        <span class="opt-name">{{ w.name }}</span>
                        <span class="opt-check" v-if="form.paymentMethod === w.id">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                      </button>
                    </div>
                    <div class="info-note">You'll be redirected to complete payment securely.</div>
                  </div>
                </transition>

                <!-- P2P -->
                <transition name="fade-up" mode="out-in">
                  <div v-if="activeGroup === 'p2p'" key="p2p">
                    <p class="pay-hint">Choose a peer-to-peer payment service.</p>
                    <div class="option-list">
                      <button v-for="w in p2pOptions" :key="w.id"
                              :class="['option-item', { selected: form.paymentMethod === w.id }]"
                              @click="form.paymentMethod = w.id">
                        <span v-html="w.svg"></span>
                        <span class="opt-name">{{ w.name }}</span>
                        <span class="opt-check" v-if="form.paymentMethod === w.id">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                      </button>
                    </div>
                    <div class="info-note">Payment details sent after order confirmation.</div>
                  </div>
                </transition>

                <!-- PAYPAL -->
                <transition name="fade-up" mode="out-in">
                  <div v-if="activeGroup === 'paypal'" key="paypal" class="paypal-block">
                    <svg width="100" height="28" viewBox="0 0 88 26"><text x="0" y="22" font-family="Arial" font-weight="900" font-size="22" fill="#003087">Pay</text><text x="42" y="22" font-family="Arial" font-weight="900" font-size="22" fill="#009cde">Pal</text></svg>
                    <p>You'll be securely redirected to PayPal. After completing payment, you'll return here automatically.</p>
                  </div>
                </transition>

                <!-- WIRE -->
                <transition name="fade-up" mode="out-in">
                  <div v-if="activeGroup === 'wire'" key="wire">
                    <p class="pay-hint">Transfer directly to our bank account.</p>
                    <div class="wire-grid">
                      <div class="wire-row"><span>Bank</span><strong>Chase Bank</strong></div>
                      <div class="wire-row"><span>Account Name</span><strong>Your Store LLC</strong></div>
                      <div class="wire-row"><span>Account No.</span><strong>•••• 4521</strong></div>
                      <div class="wire-row"><span>Routing No.</span><strong>021000021</strong></div>
                    </div>
                    <div class="info-note" style="margin-top:14px">Include order number as reference. Ships 1–2 days after clearance.</div>
                  </div>
                </transition>

                <!-- COD -->
                <transition name="fade-up" mode="out-in">
                  <div v-if="activeGroup === 'cod'" key="cod" class="cod-block">
                    <div class="cod-icon">
                      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <h4>Cash on Delivery</h4>
                    <p>Pay in cash when your order arrives. No card or pre-payment needed.</p>
                  </div>
                </transition>

              </div>

              <div class="panel-footer">
                <button class="btn-ghost" @click="currentStep = 2">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                  Back
                </button>
                <button class="btn-place" :disabled="!form.paymentMethod || loading" @click="placeOrder">
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
                <span :class="shipping === 0 ? 'tag-free' : totalQty === 3 ? 'tag-disc' : ''">{{ shippingLabel }}</span>
              </div>
              <!-- Free Shipping Progress Bar -->
              <div v-if="freeShippingThreshold" class="free-ship-bar-wrap">
                <div class="free-ship-bar-track">
                  <div class="free-ship-bar-fill" :style="{ width: freeShippingProgress + '%' }"></div>
                </div>
                <div v-if="amountToFreeShipping" class="free-ship-msg">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                  Add <strong>${{ amountToFreeShipping }}</strong> more for FREE shipping!
                </div>
                <div v-else class="free-ship-msg unlocked">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                   Free shipping unlocked!
                </div>
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

const STRIPE_PK = 'pk_test_51SvXN3HItCFNDxYMwZ1SdJWuuASPyJKQPVlvHAhAU4iICKJnrdBR4gZpdZWDdRYKWs9NVN2e400v8ng7M0Ubi15w00w0tMVAyz'

const loading     = ref(false)
const currentStep = ref(1)
const activeGroup = ref('card')
const stripeError = ref('')

let stripe = null
let cardElement = null

const steps = ['Contact', 'Shipping', 'Payment']

const form = ref({
  name: '', email: '', phone: '',
  address: '', city: '', postalCode: '', province: '', country: '',
  deliveryDays: 'standard',
  paymentMethod: 'stripe',
})
const errors = ref({ name: '', phone: '', address: '', city: '' })

const svgCard   = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>'
const svgWallet = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>'
const svgDollar = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
const svgGlobe  = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>'
const svgWire   = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><polyline points="8 8 3 12 8 16"/><polyline points="16 8 21 12 16 16"/></svg>'
const svgBox    = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>'

const paymentGroups = [
  { id: 'card',    svg: svgCard,   label: 'Card',      defaultMethod: 'stripe'   },
  { id: 'wallets', svg: svgWallet, label: 'Wallets',   defaultMethod: 'applepay' },
  { id: 'p2p',     svg: svgDollar, label: 'P2P',       defaultMethod: 'cashapp'  },
  { id: 'paypal',  svg: svgGlobe,  label: 'PayPal',    defaultMethod: 'paypal'   },
  { id: 'wire',    svg: svgWire,   label: 'Wire',      defaultMethod: 'wire'     },
  { id: 'cod',     svg: svgBox,    label: 'COD',       defaultMethod: 'cod'      },
]

const walletOptions = [
  { id: 'applepay',  svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a5.5 5.5 0 0 1 4 1.5A5.5 5.5 0 0 1 12 5a5.5 5.5 0 0 1-4-1.5A5.5 5.5 0 0 1 12 2z"/><path d="M20 17c0 3-3.5 5-8 5s-8-2-8-5c0-3.5 2-7 4-9.5 1 1.5 2.5 2.5 4 2.5s3-1 4-2.5c2 2.5 4 6 4 9.5z"/></svg>', name: 'Apple Pay'  },
  { id: 'googlepay', svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>', name: 'Google Pay' },
]
const p2pOptions = [
  { id: 'cashapp', svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></svg>', name: 'Cash App' },
  { id: 'venmo',   svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 4v2c0 6-4 12-9 14L4 22"/><path d="M4 4l6 8 4-8"/></svg>', name: 'Venmo' },
  { id: 'zelle',   svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>', name: 'Zelle' },
]

const formatPrice = (p) => {
  if (typeof p === 'string') return parseFloat(p.replace(/[^0-9.]/g, '')) || 0
  return Number(p) || 0
}

// Subtotal: sum of all items
const subtotal = computed(() =>
  cartStore.items.reduce((s, i) => s + formatPrice(i.price) * i.quantity, 0)
)

const totalQty = computed(() =>
  cartStore.items.reduce((s, i) => s + i.quantity, 0)
)

// Shipping: cart subtotal >= free_shipping_above → FREE
// (Admin ne product page pe jo free_shipping_above set kiya, woh yahan check hoga)
const shipping = computed(() => {
  const sub = subtotal.value
  let total = 0
  for (const item of cartStore.items) {
    if (!item.shipping_enabled) continue
    const cost      = formatPrice(item.shipping_cost || 0)
    const freeAbove = item.free_shipping_above ? formatPrice(item.free_shipping_above) : null
    // ✅ Agar cart ka TOTAL subtotal free_shipping_above se zyada ya barabar ho → FREE
    if (freeAbove !== null && sub >= freeAbove) continue
    total += cost * item.quantity
  }
  return parseFloat(total.toFixed(2))
})

// Label shown in summary
const shippingLabel = computed(() => {
  if (shipping.value === 0) return 'FREE'
  return `$${shipping.value.toFixed(2)}`
})

// Sabse chhoti free_shipping_above threshold (progress bar ke liye)
const freeShippingThreshold = computed(() => {
  let lowest = null
  for (const item of cartStore.items) {
    if (item.shipping_enabled && item.free_shipping_above) {
      const t = formatPrice(item.free_shipping_above)
      if (lowest === null || t < lowest) lowest = t
    }
  }
  return lowest
})

// Kitna aur lagega free shipping ke liye
const amountToFreeShipping = computed(() => {
  if (!freeShippingThreshold.value) return null
  const diff = freeShippingThreshold.value - subtotal.value
  return diff > 0 ? parseFloat(diff.toFixed(2)) : null
})

// Progress percentage (0-100) for free shipping bar
const freeShippingProgress = computed(() => {
  if (!freeShippingThreshold.value) return 0
  const pct = (subtotal.value / freeShippingThreshold.value) * 100
  return Math.min(Math.round(pct), 100)
})

const totalAmount = computed(() =>
  (subtotal.value + shipping.value).toFixed(2)
)

const loadStripe = () => new Promise((resolve) => {
  if (window.Stripe) { resolve(window.Stripe); return }
  const s = document.createElement('script')
  s.src = 'https://js.stripe.com/v3/'
  s.onload = () => resolve(window.Stripe)
  document.head.appendChild(s)
})

const mountStripeElements = async () => {
  await nextTick()
  const mountPoint = document.getElementById('stripe-card-element')
  if (!mountPoint) return
  if (cardElement) { cardElement.destroy(); cardElement = null }
  if (!stripe) { const S = await loadStripe(); stripe = S(STRIPE_PK) }
  const elements = stripe.elements()
  cardElement = elements.create('card', {
    hidePostalCode: true,
    style: {
      base: { fontFamily: "'Sora', sans-serif", fontSize: '14px', color: '#111', letterSpacing: '0.02em', '::placeholder': { color: '#bbb' } },
      invalid: { color: '#e53e3e', iconColor: '#e53e3e' },
    }
  })
  cardElement.mount('#stripe-card-element')
  cardElement.on('change', e => { stripeError.value = e.error ? e.error.message : '' })
}

onMounted(() => {
  if (currentStep.value === 3 && activeGroup.value === 'card') mountStripeElements()
})
watch(currentStep, (v) => {
  if (v === 3 && activeGroup.value === 'card') setTimeout(mountStripeElements, 100)
})

const switchPaymentGroup = (g) => {
  activeGroup.value = g.id
  form.value.paymentMethod = g.defaultMethod
  if (g.id === 'card') setTimeout(mountStripeElements, 100)
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
  // ✅ Respect stock_quantity limit (same as product page)
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
  loading.value = true
  try {
    let paymentToken = null
    if (activeGroup.value === 'card') {
      const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card', card: cardElement,
        billing_details: { name: form.value.name, phone: form.value.phone }
      })
      if (error) { stripeError.value = error.message; loading.value = false; return }
      paymentToken = paymentMethod.id
    }
    const res = await axios.post('/api/orders', {
      cart: cartStore.items,
      checkout: {
        name: form.value.name, email: form.value.email, phone: form.value.phone,
        address: form.value.address, city: form.value.city, postalCode: form.value.postalCode,
        province: form.value.province, country: form.value.country,
        deliveryDays: form.value.deliveryDays,
        paymentMethod: activeGroup.value === 'card' ? 'stripe' : form.value.paymentMethod,
        stripeToken: paymentToken,
      }
    }, { headers: { Authorization: `Bearer ${localStorage.getItem('token') || ''}` } })
    alert(`✅ Order placed! ID: ${res.data.order_id || 'N/A'}`)
    cartStore.clearCart()
    router.push('/')
  } catch (e) {
    alert('❌ ' + (e.response?.data?.message || 'Something went wrong. Please try again.'))
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.checkout-page {
  font-family: 'Sora', sans-serif;
  min-height: 100vh;
  background: #F2F2F7;
  padding: 28px 24px 80px;
  color: #111;
}

/* Empty */
.empty-cart {
  display: flex; flex-direction: column; align-items: center;
  padding: 120px 20px; gap: 14px; text-align: center;
}
.empty-icon {
  width: 88px; height: 88px; background: #fff; border-radius: 50%;
  border: 1.5px solid #E5E5EA; display: flex; align-items: center;
  justify-content: center; color: #C7C7CC;
}
.empty-cart h3 { font-size: 22px; font-weight: 700; }
.empty-cart p  { font-size: 13px; color: #8E8E93; }
.btn-shop {
  display: inline-block; padding: 12px 28px; background: #111;
  color: #fff; border-radius: 10px; font-size: 13px; font-weight: 600;
  text-decoration: none; margin-top: 8px; transition: background .2s;
}
.btn-shop:hover { background: #333; }

/* Wrapper */
.checkout-wrapper { max-width: 1200px; margin: 0 auto; }

/* Top bar */
.top-bar {
  display: flex; align-items: center; gap: 14px; margin-bottom: 24px;
}
.btn-back {
  display: inline-flex; align-items: center; gap: 6px;
  height: 38px; padding: 0 14px; background: #fff;
  border: 1.5px solid #E5E5EA; border-radius: 10px;
  font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 600;
  color: #555; cursor: pointer; transition: all .2s;
}
.btn-back:hover { background: #f7f7f7; color: #111; border-color: #ccc; }
.logo-area { flex: 1; }
.logo-text { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
.secure-badge {
  display: flex; align-items: center; gap: 5px;
  font-size: 11px; font-weight: 600; color: #48BB78;
  background: #F0FFF4; border: 1.5px solid #C6F6D5;
  border-radius: 20px; padding: 5px 12px;
}

/* Progress */
.progress-bar {
  display: flex; flex-direction: row; align-items: center;
  background: #fff; border-radius: 16px;
  border: 1.5px solid #E5E5EA;
  padding: 18px 28px; margin-bottom: 20px;
  box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.prog-step { display: flex; flex-direction: row; align-items: center; gap: 10px; }
.prog-circle {
  width: 36px; height: 36px; border-radius: 50%;
  background: #E5E5EA; color: #8E8E93;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700; flex-shrink: 0;
  transition: all .3s;
}
.prog-step.active .prog-circle { background: #111; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,.18); }
.prog-step.done .prog-circle   { background: #111; color: #fff; cursor: pointer; }
.prog-label { font-size: 13px; font-weight: 600; color: #C7C7CC; transition: color .3s; white-space: nowrap; }
.prog-step.active .prog-label,
.prog-step.done .prog-label   { color: #111; }
.prog-line {
  flex: 1; height: 2px; background: #E5E5EA;
  margin: 0 14px; border-radius: 2px; transition: background .4s;
  min-width: 40px;
}
.prog-line.filled { background: #111; }

/* Main grid */
.main-grid {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 20px; align-items: start;
}
@media (max-width: 940px) {
  .main-grid { grid-template-columns: 1fr; }
  .right-col { order: -1; }
}

/* Panel */
.panel {
  background: #fff;
  border: 1.5px solid #E5E5EA;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,.05);
}

.panel-header {
  display: flex; align-items: center; gap: 14px;
  padding: 22px 28px; border-bottom: 1.5px solid #F2F2F7;
  background: #FAFAFA;
}
.panel-icon {
  width: 42px; height: 42px; background: #F2F2F7;
  border-radius: 12px; border: 1.5px solid #E5E5EA;
  display: flex; align-items: center; justify-content: center;
  color: #555; flex-shrink: 0;
}
.panel-header h2 { font-size: 15px; font-weight: 700; margin-bottom: 2px; }
.panel-header p  { font-size: 12px; color: #8E8E93; }

.panel-body { padding: 24px 28px; display: flex; flex-direction: column; gap: 16px; }
.pay-body   { padding: 20px 28px; }

.panel-footer {
  display: flex; justify-content: space-between; align-items: center;
  padding: 18px 28px; border-top: 1.5px solid #F2F2F7;
  background: #FAFAFA;
}

/* Fields */
.field { display: flex; flex-direction: column; gap: 6px; }
.field label {
  font-size: 11px; font-weight: 700; color: #8E8E93;
  text-transform: uppercase; letter-spacing: .8px;
}
.req { color: #FF3B30; }
.field input, .field select, .field textarea {
  height: 46px; padding: 0 14px;
  border: 1.5px solid #E5E5EA; border-radius: 12px;
  font-family: 'Sora', sans-serif; font-size: 14px;
  color: #111; background: #FAFAFA;
  transition: all .2s; width: 100%;
}
.field textarea { height: auto; padding: 12px 14px; resize: none; }
.field input:focus, .field select:focus, .field textarea:focus {
  outline: none; border-color: #111; background: #fff;
  box-shadow: 0 0 0 4px rgba(17,17,17,.06);
}
.field input.error, .field textarea.error { border-color: #FF3B30; }
.err-msg { font-size: 11px; color: #FF3B30; }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

/* Buttons */
.btn-primary {
  display: inline-flex; align-items: center; gap: 8px;
  height: 46px; padding: 0 22px;
  background: #111; color: #fff;
  border: none; border-radius: 12px;
  font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 700;
  cursor: pointer; transition: all .2s;
}
.btn-primary:hover { background: #2c2c2c; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,.15); }
.btn-ghost {
  display: inline-flex; align-items: center; gap: 8px;
  height: 46px; padding: 0 18px;
  background: #fff; color: #555;
  border: 1.5px solid #E5E5EA; border-radius: 12px;
  font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all .2s;
}
.btn-ghost:hover { background: #f7f7f7; color: #111; border-color: #ccc; }
.btn-place {
  display: inline-flex; align-items: center; gap: 8px;
  height: 50px; padding: 0 28px;
  background: #111; color: #fff;
  border: none; border-radius: 14px;
  font-family: 'Sora', sans-serif; font-size: 14px; font-weight: 700;
  cursor: pointer; transition: all .2s; letter-spacing: -.1px;
}
.btn-place:hover:not(:disabled) {
  background: #222;
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(0,0,0,.18);
}
.btn-place:disabled { opacity: .35; cursor: not-allowed; }
.spin {
  width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,.3);
  border-top-color: #fff; border-radius: 50%;
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Payment chips */
.pay-tabs-row {
  display: flex; flex-wrap: wrap; gap: 8px;
  padding: 16px 28px; border-bottom: 1.5px solid #F2F2F7;
  background: #FAFAFA;
}
.pay-chip {
  display: inline-flex; align-items: center; gap: 6px;
  height: 36px; padding: 0 14px;
  background: #fff; border: 1.5px solid #E5E5EA;
  border-radius: 10px; cursor: pointer; color: #555;
  font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 600;
  transition: all .18s;
}
.pay-chip:hover { border-color: #bbb; color: #111; }
.pay-chip.active { background: #111; color: #fff; border-color: #111; }

/* Stripe */
.brand-row {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 18px; padding-bottom: 16px;
  border-bottom: 1.5px solid #F2F2F7; flex-wrap: wrap;
}
.brand-svg { height: 28px; width: 46px; }
.stripe-pill {
  display: flex; align-items: center; gap: 5px;
  margin-left: auto; font-size: 10px; font-weight: 700;
  color: #635bff; background: #EEF2FF;
  border-radius: 6px; padding: 4px 9px;
}
.stripe-box {
  padding: 13px 14px; border: 1.5px solid #E5E5EA;
  border-radius: 12px; background: #FAFAFA;
  transition: all .2s; min-height: 46px;
}
.stripe-box.StripeElement--focus {
  border-color: #111; background: #fff;
  box-shadow: 0 0 0 4px rgba(17,17,17,.06);
}
.stripe-err {
  display: flex; align-items: center; gap: 7px;
  color: #FF3B30; font-size: 12px; margin-top: 12px;
  background: #FFF5F5; border: 1.5px solid #FED7D7;
  border-radius: 10px; padding: 10px 12px;
}

/* Options */
.pay-hint { font-size: 12.5px; color: #8E8E93; margin-bottom: 12px; line-height: 1.6; }
.option-list { display: flex; flex-direction: column; gap: 8px; }
.option-item {
  display: flex; align-items: center; gap: 10px;
  padding: 13px 16px; border: 1.5px solid #E5E5EA;
  border-radius: 12px; background: #FAFAFA;
  cursor: pointer; font-family: 'Sora', sans-serif;
  font-size: 13px; font-weight: 500; color: #444;
  transition: all .18s; text-align: left;
}
.option-item:hover { border-color: #aaa; background: #fff; color: #111; }
.option-item.selected { border-color: #111; background: #fff; font-weight: 700; color: #111; }
.opt-name { flex: 1; }
.opt-check { color: #111; }
.info-note {
  padding: 10px 14px; background: #F8F8F8;
  border: 1.5px solid #E5E5EA; border-radius: 10px;
  font-size: 11.5px; color: #666; line-height: 1.6; margin-top: 12px;
}

/* PayPal */
.paypal-block { padding: 10px 0; }
.paypal-block p { font-size: 12.5px; color: #555; line-height: 1.7; margin-top: 12px; }

/* Wire */
.wire-grid { display: flex; flex-direction: column; gap: 6px; }
.wire-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 11px 14px; background: #FAFAFA;
  border: 1.5px solid #E5E5EA; border-radius: 10px;
  font-size: 13px;
}
.wire-row span { color: #8E8E93; font-size: 12px; }
.wire-row strong { font-family: 'DM Mono', monospace; font-size: 12px; font-weight: 600; color: #111; }

/* COD */
.cod-block { text-align: center; padding: 28px 16px; }
.cod-icon {
  width: 72px; height: 72px; background: #F2F2F7;
  border-radius: 50%; border: 1.5px solid #E5E5EA;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px; color: #555;
}
.cod-block h4 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
.cod-block p  { font-size: 13px; color: #666; line-height: 1.7; }

/* Transitions */
.fade-up-enter-active, .fade-up-leave-active { transition: all .22s ease; }
.fade-up-enter-from { opacity: 0; transform: translateY(10px); }
.fade-up-leave-to   { opacity: 0; transform: translateY(-6px); }

/* Summary panel */
.right-col { position: sticky; top: 22px; }
.summary-panel {
  background: #fff; border: 1.5px solid #E5E5EA;
  border-radius: 20px; padding: 24px;
  box-shadow: 0 2px 12px rgba(0,0,0,.05);
}
.summary-panel h3 { font-size: 15px; font-weight: 700; margin-bottom: 18px; }

.sum-items { display: flex; flex-direction: column; gap: 16px; }
.sum-item  { display: flex; align-items: flex-start; gap: 12px; }

.thumb-wrap { position: relative; flex-shrink: 0; }
.thumb-wrap img {
  width: 60px; height: 60px; border-radius: 12px;
  object-fit: contain; border: 1.5px solid #E5E5EA;
  background: #FAFAFA; padding: 4px;
}
.qty-badge {
  position: absolute; top: -6px; right: -6px;
  width: 18px; height: 18px; background: #111; color: #fff;
  border-radius: 50%; font-size: 9px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
}

.item-info { flex: 1; min-width: 0; }
.item-name {
  font-size: 12.5px; font-weight: 600; margin-bottom: 3px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.item-size { font-size: 10.5px; color: #C7C7CC; margin-bottom: 4px; }
.item-stock { font-size: 10px; color: #FF9F0A; font-weight: 600; margin-bottom: 6px; }

.qty-row {
  display: inline-flex; align-items: center;
  border: 1.5px solid #E5E5EA; border-radius: 8px; overflow: hidden;
}
.qty-btn {
  width: 26px; height: 26px; background: #fff; border: none;
  cursor: pointer; display: flex; align-items: center;
  justify-content: center; color: #555; transition: background .15s;
}
.qty-btn:hover:not(:disabled) { background: #F2F2F7; }
.qty-btn:disabled { opacity: .3; cursor: not-allowed; }
.qty-row span {
  width: 28px; text-align: center; font-size: 12px; font-weight: 600;
  border-left: 1.5px solid #E5E5EA; border-right: 1.5px solid #E5E5EA;
  line-height: 26px;
}

.item-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }
.item-price { font-size: 13px; font-weight: 700; }
.del-btn { background: none; border: none; cursor: pointer; color: #C7C7CC; display: flex; transition: color .15s; padding: 0; }
.del-btn:hover { color: #FF3B30; }

.divider { height: 1.5px; background: #F2F2F7; margin: 16px 0; }

.sum-rows { display: flex; flex-direction: column; gap: 9px; }
.sum-row { display: flex; justify-content: space-between; font-size: 13px; color: #555; }
.tag-free { color: #000000; font-weight: 700; }
.tag-disc  { color: #FF9F0A; font-weight: 700; }
.ship-note { font-size: 11px; color: #8E8E93; margin-top: 2px; }
.ship-note.free { color: #000000; font-weight: 600; }

/* Free Shipping Progress Bar */
.free-ship-bar-wrap { margin-top: 10px; }
.free-ship-bar-track {
  width: 100%; height: 6px; background: #E5E5EA;
  border-radius: 99px; overflow: hidden; margin-bottom: 7px;
}
.free-ship-bar-fill {
  height: 100%; background: linear-gradient(90deg, #000000, #000000);
  border-radius: 99px; transition: width .5s ease;
}
.free-ship-msg {
  display: flex; align-items: center; gap: 5px;
  font-size: 11px; color: #8E8E93; font-weight: 500;
}
.free-ship-msg strong { color: #111; font-weight: 700; }
.free-ship-msg svg { color: #8E8E93; flex-shrink: 0; }
.free-ship-msg.unlocked { color: #000000; font-weight: 700; }
.free-ship-msg.unlocked svg { color: #000000; }

.sum-total { display: flex; justify-content: space-between; align-items: baseline; font-size: 17px; }
.sum-total span  { font-weight: 600; }
.sum-total strong { font-weight: 800; font-size: 20px; letter-spacing: -0.5px; }

.trust-list { display: flex; flex-direction: column; gap: 8px; margin-top: 18px; padding-top: 16px; border-top: 1.5px solid #F2F2F7; }
.trust-item { display: flex; align-items: center; gap: 8px; font-size: 11.5px; color: #8E8E93; }
.trust-item svg { color: #C7C7CC; flex-shrink: 0; }
</style>
