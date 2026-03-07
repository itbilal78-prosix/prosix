<template>
  <nav-component />
  <breadcrumb-component />

  <div class="checkout-page">
    <!-- EMPTY CART -->
    <div v-if="cartStore.items.length === 0" class="empty-cart">
      <div class="empty-icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      </div>
      <h3>Your cart is empty</h3>
      <p>Add some items to get started</p>
      <router-link to="/" class="btn-shop">Continue Shopping</router-link>
    </div>

    <div v-else class="checkout-wrapper">
      <!-- HEADER -->
      <div class="page-header">
        <button class="btn-back" @click="router.push('/')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </button>
        <h1>Checkout</h1>
        <div class="secure-badge">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Secure Checkout
        </div>
      </div>

      <!-- BODY -->
      <div class="checkout-body">
        <!-- ─── LEFT COLUMN ─── -->
        <div class="left-col">

          <!-- STEP BAR -->
          <div class="step-bar-wrap">
            <template v-for="(label, idx) in steps" :key="idx">
              <div :class="['step', { active: currentStep === idx+1, done: currentStep > idx+1 }]"
                   @click="currentStep > idx+1 ? currentStep = idx+1 : null">
                <div class="step-circle">
                  <svg v-if="currentStep > idx+1" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                  <span v-else>{{ idx+1 }}</span>
                </div>
                <span class="step-label">{{ label }}</span>
              </div>
              <div v-if="idx < steps.length-1" :class="['step-line', { filled: currentStep > idx+1 }]"></div>
            </template>
          </div>

          <!-- ── STEP 1: Contact ── -->
          <transition name="slide" mode="out-in">
            <div v-if="currentStep === 1" key="s1" class="card">
              <div class="card-head">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <h2>Contact Information</h2>
              </div>
              <div class="fields">
                <div class="field">
                  <label>Full Name <span class="req">*</span></label>
                  <input v-model="form.name" type="text" placeholder="John Doe" :class="{ error: errors.name }" @input="errors.name=''" />
                  <span v-if="errors.name" class="err-msg">{{ errors.name }}</span>
                </div>
                <div class="field-2col">
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
              <div class="card-footer">
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

          <!-- ── STEP 2: Shipping ── -->
          <transition name="slide" mode="out-in">
            <div v-if="currentStep === 2" key="s2" class="card">
              <div class="card-head">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <h2>Shipping Address</h2>
              </div>
              <div class="fields">
                <div class="field">
                  <label>Street Address <span class="req">*</span></label>
                  <textarea v-model="form.address" rows="2" placeholder="123 Main Street, Apt 4B" :class="{ error: errors.address }" @input="errors.address=''"></textarea>
                  <span v-if="errors.address" class="err-msg">{{ errors.address }}</span>
                </div>
                <div class="field-2col">
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
                <div class="field-2col">
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
              <div class="card-footer">
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

          <!-- ── STEP 3: Payment ── -->
          <transition name="slide" mode="out-in">
            <div v-if="currentStep === 3" key="s3" class="card">
              <div class="card-head">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <h2>Payment Method</h2>
              </div>

              <div class="pay-panel">
                <!-- SIDEBAR TABS -->
                <nav class="pay-nav">
                  <button v-for="g in paymentGroups" :key="g.id"
                          :class="['pay-tab', { active: activeGroup === g.id }]"
                          @click="switchPaymentGroup(g)">
                    <span class="tab-icon" v-html="g.svg"></span>
                    <span class="tab-text">{{ g.label }}</span>
                    <svg class="tab-arr" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                  </button>
                </nav>

                <!-- PAYMENT CONTENT -->
                <div class="pay-content">

                  <!-- STRIPE CARD -->
                  <transition name="slide" mode="out-in">
                    <div v-if="activeGroup === 'card'" key="card" class="pay-section">
                      <div class="card-brands">
                        <!-- Mastercard -->
                        <svg viewBox="0 0 50 30" class="brand-logo"><rect width="50" height="30" rx="4" fill="#fff" stroke="#e5e5e5"/><circle cx="19" cy="15" r="8" fill="#EB001B" opacity=".9"/><circle cx="31" cy="15" r="8" fill="#F79E1B" opacity=".9"/><path d="M25 8.6A8 8 0 0 1 28 15a8 8 0 0 1-3 6.4A8 8 0 0 1 22 15a8 8 0 0 1 3-6.4z" fill="#FF5F00"/></svg>
                        <!-- Visa -->
                        <svg viewBox="0 0 60 30" class="brand-logo"><rect width="60" height="30" rx="4" fill="#fff" stroke="#e5e5e5"/><text x="7" y="21" font-family="Arial" font-weight="900" font-size="14" fill="#1A1F71">VISA</text></svg>
                        <!-- Amex -->
                        <svg viewBox="0 0 60 30" class="brand-logo"><rect width="60" height="30" rx="4" fill="#fff" stroke="#e5e5e5"/><text x="5" y="13" font-family="Arial" font-weight="700" font-size="7" fill="#006FCF">AMERICAN</text><text x="5" y="22" font-family="Arial" font-weight="700" font-size="7" fill="#006FCF">EXPRESS</text></svg>
                        <!-- Stripe badge -->
                        <div class="stripe-badge">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#635bff" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                          Powered by Stripe
                        </div>
                      </div>

                      <!-- Stripe Single Card Element -->
                      <div class="field">
                        <label>Card Information <span class="req">*</span></label>
                        <div id="stripe-card-element" class="stripe-element-single"></div>
                      </div>
                      <div v-if="stripeError" class="stripe-err">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ stripeError }}
                      </div>
                    </div>
                  </transition>

                  <!-- WALLETS -->
                  <transition name="slide" mode="out-in">
                    <div v-if="activeGroup === 'wallets'" key="wallets" class="pay-section">
                      <p class="pay-desc">Select your preferred digital wallet to complete payment.</p>
                      <div class="wallet-list">
                        <button v-for="w in walletOptions" :key="w.id"
                                :class="['wallet-item', { selected: form.paymentMethod === w.id }]"
                                @click="form.paymentMethod = w.id">
                          <span v-html="w.svg"></span>
                          <span class="w-name">{{ w.name }}</span>
                          <svg v-if="form.paymentMethod === w.id" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                      </div>
                      <div class="info-note">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        You'll be redirected to complete payment securely.
                      </div>
                    </div>
                  </transition>

                  <!-- P2P -->
                  <transition name="slide" mode="out-in">
                    <div v-if="activeGroup === 'p2p'" key="p2p" class="pay-section">
                      <p class="pay-desc">Choose a peer-to-peer payment service.</p>
                      <div class="wallet-list">
                        <button v-for="w in p2pOptions" :key="w.id"
                                :class="['wallet-item', { selected: form.paymentMethod === w.id }]"
                                @click="form.paymentMethod = w.id">
                          <span v-html="w.svg"></span>
                          <span class="w-name">{{ w.name }}</span>
                          <svg v-if="form.paymentMethod === w.id" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                      </div>
                      <div class="info-note">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Payment details sent after order confirmation.
                      </div>
                    </div>
                  </transition>

                  <!-- PAYPAL -->
                  <transition name="slide" mode="out-in">
                    <div v-if="activeGroup === 'paypal'" key="paypal" class="pay-section">
                      <div class="redir-block">
                        <svg width="88" height="26" viewBox="0 0 88 26"><text x="0" y="22" font-family="Arial" font-weight="900" font-size="22" fill="#003087">Pay</text><text x="42" y="22" font-family="Arial" font-weight="900" font-size="22" fill="#009cde">Pal</text></svg>
                        <p>You'll be securely redirected to PayPal. After confirmation, you'll return automatically.</p>
                      </div>
                    </div>
                  </transition>

                  <!-- WIRE -->
                  <transition name="slide" mode="out-in">
                    <div v-if="activeGroup === 'wire'" key="wire" class="pay-section">
                      <p class="pay-desc">Transfer directly to our bank account.</p>
                      <div class="wire-table">
                        <div class="wire-row"><span>Bank</span><strong>Chase Bank</strong></div>
                        <div class="wire-row"><span>Account Name</span><strong>Your Store LLC</strong></div>
                        <div class="wire-row"><span>Account No.</span><strong>•••• 4521</strong></div>
                        <div class="wire-row"><span>Routing No.</span><strong>021000021</strong></div>
                      </div>
                      <div class="info-note" style="margin-top:12px">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Include order number as reference. Ships 1–2 days after clearance.
                      </div>
                    </div>
                  </transition>

                  <!-- COD -->
                  <transition name="slide" mode="out-in">
                    <div v-if="activeGroup === 'cod'" key="cod" class="pay-section">
                      <div class="cod-block">
                        <div class="cod-icon">
                          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        </div>
                        <h4>Cash on Delivery</h4>
                        <p>Pay in cash when your order arrives. No card or pre-payment required.</p>
                      </div>
                    </div>
                  </transition>

                </div>
              </div>

              <div class="card-footer">
                <button class="btn-ghost" @click="currentStep = 2">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                  Back
                </button>
                <button class="btn-primary btn-place" :disabled="!form.paymentMethod || loading" @click="placeOrder">
                  <svg v-if="!loading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  <span v-if="loading" class="spin"></span>
                  {{ loading ? 'Processing...' : `Place Order — $${totalAmount}` }}
                </button>
              </div>
            </div>
          </transition>

        </div>

        <!-- ─── RIGHT COLUMN: Order Summary ─── -->
        <aside class="right-col">
          <div class="summary-card">
            <h3 class="sum-title">Order Summary</h3>

            <div class="sum-items">
              <div v-for="item in cartStore.items" :key="item.id + item.size" class="sum-item">
                <div class="sum-thumb">
                  <img :src="item.image" :alt="item.name" />
                  <span class="sum-qty-badge">{{ item.quantity }}</span>
                </div>
                <div class="sum-info">
                  <p class="sum-name">{{ item.name }}</p>
                  <p class="sum-meta">Size: {{ item.size }}</p>
                  <div class="qty-ctrl">
                    <button class="qty-btn" @click="decreaseQty(item)" :disabled="item.quantity <= 1">
                      <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <span>{{ item.quantity }}</span>
                    <button class="qty-btn" @click="increaseQty(item)">
                      <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                  </div>
                </div>
                <div class="sum-item-right">
                  <span class="sum-price">${{ (Number(item.price) * item.quantity).toFixed(2) }}</span>
                  <button class="del-btn" @click="removeItem(item)" title="Remove">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                </div>
              </div>
            </div>

            <div class="sum-divider"></div>

            <div class="sum-rows">
              <div class="sum-row"><span>Subtotal</span><span>${{ subtotal }}</span></div>
              <div class="sum-row">
                <span>Shipping</span>
                <span :class="shipping === 0 ? 'tag-free' : totalQty === 3 ? 'tag-discount' : ''">{{ shippingLabel }}</span>
              </div>
              <div v-if="totalQty === 3" class="ship-note">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                10% shipping for qty 3
              </div>
              <div v-if="totalQty > 3" class="ship-note free-note">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                Free shipping on qty 4+! 🎉
              </div>
            </div>

            <div class="sum-divider"></div>
            <div class="sum-total"><span>Total</span><span>${{ totalAmount }}</span></div>

            <div class="trust-badges">
              <div class="trust-item">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span>SSL Secure Checkout</span>
              </div>
              <div class="trust-item">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
                <span>60-Day Free Returns</span>
              </div>
              <div class="trust-item">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                <span>Fast Worldwide Delivery</span>
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

// Stripe instances
let stripe      = null
let cardElement = null   // single combined card element

const steps = ['Contact', 'Shipping', 'Payment']

const form = ref({
  name: '', email: '', phone: '',
  address: '', city: '', postalCode: '', province: '', country: '',
  deliveryDays: 'standard',
  paymentMethod: 'stripe',
})

const errors = ref({ name: '', phone: '', address: '', city: '' })



// ── SVGs ──
const svgCard   = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>'
const svgWallet = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>'
const svgDollar = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
const svgGlobe  = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>'
const svgWire   = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><polyline points="8 8 3 12 8 16"/><polyline points="16 8 21 12 16 16"/></svg>'
const svgBox    = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>'

const paymentGroups = [
  { id: 'card',    svg: svgCard,   label: 'Credit / Debit Card',  defaultMethod: 'stripe'   },
  { id: 'wallets', svg: svgWallet, label: 'Apple / Google Pay',   defaultMethod: 'applepay' },
  { id: 'p2p',     svg: svgDollar, label: 'Cash App · Venmo · Zelle', defaultMethod: 'cashapp' },
  { id: 'paypal',  svg: svgGlobe,  label: 'PayPal',               defaultMethod: 'paypal'   },
  { id: 'wire',    svg: svgWire,   label: 'Wire Transfer',        defaultMethod: 'wire'     },
  { id: 'cod',     svg: svgBox,    label: 'Cash on Delivery',     defaultMethod: 'cod'      },
]

const walletOptions = [
  { id: 'applepay',  svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a5.5 5.5 0 0 1 4 1.5A5.5 5.5 0 0 1 12 5a5.5 5.5 0 0 1-4-1.5A5.5 5.5 0 0 1 12 2z"/><path d="M20 17c0 3-3.5 5-8 5s-8-2-8-5c0-3.5 2-7 4-9.5 1 1.5 2.5 2.5 4 2.5s3-1 4-2.5c2 2.5 4 6 4 9.5z"/></svg>', name: 'Apple Pay'  },
  { id: 'googlepay', svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>',  name: 'Google Pay' },
]

const p2pOptions = [
  { id: 'cashapp', svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></svg>',  name: 'Cash App' },
  { id: 'venmo',   svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 4v2c0 6-4 12-9 14L4 22"/><path d="M4 4l6 8 4-8"/></svg>', name: 'Venmo'    },
  { id: 'zelle',   svg: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>', name: 'Zelle'    },
]

// ── Computed ──
const subtotal = computed(() =>
  cartStore.items.reduce((s, i) => s + Number(i.price) * i.quantity, 0).toFixed(2)
)
const totalQty = computed(() => cartStore.items.reduce((s, i) => s + i.quantity, 0))
const shipping = computed(() => {
  const qty = totalQty.value
  const sub = parseFloat(subtotal.value)
  if (qty > 3)   return 0
  if (qty === 3) return parseFloat((sub * 0.10).toFixed(2))
  return 19
})
const shippingLabel = computed(() => {
  if (totalQty.value > 3)   return 'FREE'
  if (totalQty.value === 3) return `+10% ($${shipping.value.toFixed(2)})`
  return `$${shipping.value.toFixed(2)}`
})
const totalAmount = computed(() => (parseFloat(subtotal.value) + shipping.value).toFixed(2))

// ── Stripe Setup ──
const loadStripe = () => {
  return new Promise((resolve) => {
    if (window.Stripe) { resolve(window.Stripe); return }
    const s = document.createElement('script')
    s.src = 'https://js.stripe.com/v3/'
    s.onload = () => resolve(window.Stripe)
    document.head.appendChild(s)
  })
}

const mountStripeElements = async () => {
  await nextTick()
  const mountPoint = document.getElementById('stripe-card-element')
  if (!mountPoint) return

  // Destroy old element if exists (prevents duplicate mount on tab switch)
  if (cardElement) {
    cardElement.destroy()
    cardElement = null
  }

  // Load Stripe once
  if (!stripe) {
    const StripeJS = await loadStripe()
    stripe = StripeJS(STRIPE_PK)
  }

  const elements = stripe.elements()

  cardElement = elements.create('card', {
    hidePostalCode: true,
    style: {
      base: {
        fontFamily: "'DM Sans', sans-serif",
        fontSize: '14px',
        color: '#111111',
        letterSpacing: '0.02em',
        '::placeholder': { color: '#aaaaaa' },
      },
      invalid: { color: '#e53e3e', iconColor: '#e53e3e' },
    }
  })

  cardElement.mount('#stripe-card-element')
  cardElement.on('change', e => {
    stripeError.value = e.error ? e.error.message : ''
  })
}

// ── Lifecycle ──
onMounted(() => {
  if (currentStep.value === 3 && activeGroup.value === 'card') mountStripeElements()
})

watch(currentStep, (v) => {
  if (v === 3 && activeGroup.value === 'card') setTimeout(mountStripeElements, 100)
})

// ── Methods ──
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

    // ── Stripe card flow ──
    if (activeGroup.value === 'card') {
      const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: { name: form.value.name, phone: form.value.phone }
      })
      if (error) {
        stripeError.value = error.message
        loading.value = false
        return
      }
      paymentToken = paymentMethod.id
    }

    const res = await axios.post('/api/orders', {
      cart: cartStore.items,
      checkout: {
        name:          form.value.name,
        email:         form.value.email,
        phone:         form.value.phone,
        address:       form.value.address,
        city:          form.value.city,
        postalCode:    form.value.postalCode,
        province:      form.value.province,
        country:       form.value.country,
        deliveryDays:  form.value.deliveryDays,
        paymentMethod: activeGroup.value === 'card' ? 'stripe' : form.value.paymentMethod,
        stripeToken:   paymentToken,
      }
    }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token') || ''}` }
    })

    alert(`✅ Order placed! ID: ${res.data.order_id || 'N/A'}`)
    cartStore.clearCart()
    router.push('/')

  } catch (e) {
    const msg = e.response?.data?.message || 'Something went wrong. Please try again.'
    alert('❌ ' + msg)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.checkout-page {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  background: #f5f5f7;
  padding: 24px 20px 60px;
  color: #111;
}

/* ── Empty Cart ── */
.empty-cart {
  text-align: center; padding: 100px 20px;
  display: flex; flex-direction: column; align-items: center; gap: 14px;
}
.empty-icon { width: 80px; height: 80px; background: #fff; border-radius: 50%; border: 1px solid #e4e4e7; display: flex; align-items: center; justify-content: center; color: #ccc; }
.empty-cart h3 { font-size: 20px; font-weight: 700; }
.empty-cart p  { font-size: 13px; color: #888; }
.btn-shop { display: inline-block; padding: 11px 26px; background: #111; color: #fff; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; margin-top: 6px; }
.btn-shop:hover { background: #333; }

/* ── Wrapper ── */
.checkout-wrapper { max-width: 1160px; margin: 0 auto; }

/* ── Header ── */
.page-header {
  display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
}
.page-header h1 { font-size: 20px; font-weight: 700; flex: 1; }
.btn-back {
  width: 36px; height: 36px; background: #fff; border: 1px solid #e4e4e7;
  border-radius: 8px; display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #555; transition: all .2s;
}
.btn-back:hover { background: #f4f4f5; color: #111; }
.secure-badge {
  display: flex; align-items: center; gap: 5px;
  font-size: 11px; font-weight: 600; color: #666;
  background: #fff; border: 1px solid #e4e4e7; border-radius: 20px;
  padding: 5px 10px;
}

/* ── Body grid ── */
.checkout-body {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 20px;
  align-items: start;
}
@media (max-width: 900px) {
  .checkout-body { grid-template-columns: 1fr; }
  .right-col { order: -1; }
}

/* ── Step bar ── */
.step-bar-wrap {
  display: flex; align-items: center;
  background: #fff; border: 1px solid #e4e4e7; border-radius: 12px;
  padding: 14px 22px; margin-bottom: 14px;
}
.step { display: flex; align-items: center; gap: 8px; }
.step-circle {
  width: 32px; height: 32px; border-radius: 50%;
  background: #e4e4e7; color: #999;
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 700;
  transition: all .3s; flex-shrink: 0;
}
.step.active .step-circle, .step.done .step-circle { background: #111; color: #fff; }
.step.done { cursor: pointer; }
.step-label { font-size: 12px; font-weight: 600; color: #aaa; white-space: nowrap; transition: color .3s; }
.step.active .step-label, .step.done .step-label { color: #111; }
.step-line { flex: 1; height: 2px; background: #e4e4e7; margin: 0 10px; border-radius: 2px; transition: background .4s; }
.step-line.filled { background: #111; }

/* ── Card ── */
.card { background: #fff; border: 1px solid #e4e4e7; border-radius: 12px; overflow: hidden; }
.card-head { display: flex; align-items: center; gap: 8px; padding: 14px 20px; border-bottom: 1px solid #f0f0f0; }
.card-head h2 { font-size: 13px; font-weight: 700; }
.card-head svg { color: #888; flex-shrink: 0; }

/* ── Fields ── */
.fields { padding: 18px 20px; display: flex; flex-direction: column; gap: 12px; }
.field { display: flex; flex-direction: column; gap: 4px; }
.field label { font-size: 10.5px; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: .7px; }
.req { color: #e53e3e; }
.field input, .field select, .field textarea {
  height: 40px; padding: 0 12px;
  border: 1px solid #e4e4e7; border-radius: 7px;
  font-family: 'DM Sans', sans-serif; font-size: 13px; color: #111;
  background: #fafafa; transition: all .2s; width: 100%;
}
.field textarea { height: auto; padding: 10px 12px; resize: none; }
.field input:focus, .field select:focus, .field textarea:focus {
  outline: none; border-color: #111; background: #fff;
  box-shadow: 0 0 0 3px rgba(17,17,17,.06);
}
.field input.error, .field textarea.error { border-color: #e53e3e; }
.err-msg { font-size: 11px; color: #e53e3e; margin-top: 2px; }
.field-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }



/* ── Footer ── */
.card-footer {
  padding: 14px 20px; border-top: 1px solid #f0f0f0;
  display: flex; justify-content: space-between; align-items: center;
}

/* ── Buttons ── */
.btn-primary {
  display: inline-flex; align-items: center; gap: 7px;
  height: 40px; padding: 0 18px;
  background: #111; color: #fff; border: none; border-radius: 7px;
  font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all .2s;
}
.btn-primary:hover:not(:disabled) { background: #2d2d2d; }
.btn-primary:disabled { opacity: .35; cursor: not-allowed; }
.btn-place { height: 44px; padding: 0 22px; }
.btn-ghost {
  display: inline-flex; align-items: center; gap: 7px;
  height: 40px; padding: 0 15px;
  background: #fff; color: #555; border: 1px solid #e4e4e7; border-radius: 7px;
  font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500;
  cursor: pointer; transition: all .2s;
}
.btn-ghost:hover { background: #f9f9f9; color: #111; }
.spin {
  width: 13px; height: 13px; border: 2px solid rgba(255,255,255,.3);
  border-top-color: #fff; border-radius: 50%;
  animation: spin .7s linear infinite; display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Payment panel ── */
.pay-panel {
  display: grid; grid-template-columns: 170px 1fr;
  border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0;
}
.pay-nav {
  background: #fafafa; border-right: 1px solid #f0f0f0;
  display: flex; flex-direction: column;
}
.pay-tab {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 11px; background: none; border: none;
  border-bottom: 1px solid #f0f0f0; border-left: 2.5px solid transparent;
  cursor: pointer; color: #777; transition: all .18s;
}
.pay-tab:last-child { border-bottom: none; }
.pay-tab:hover { background: #f1f1f1; color: #111; }
.pay-tab.active { background: #fff; border-left-color: #111; color: #111; }
.tab-icon { display: flex; flex-shrink: 0; }
.tab-text { flex: 1; font-size: 11px; font-weight: 500; line-height: 1.3; text-align: left; }
.tab-arr { color: #ddd; flex-shrink: 0; transition: color .2s; }
.pay-tab.active .tab-arr { color: #111; }

.pay-content { padding: 18px; background: #fff; min-height: 240px; }
.pay-section {}

/* Stripe */
.card-brands { display: flex; align-items: center; gap: 7px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0; flex-wrap: wrap; }
.brand-logo { height: 24px; width: 40px; }
.stripe-badge {
  display: flex; align-items: center; gap: 4px;
  margin-left: auto; font-size: 10px; font-weight: 600; color: #635bff;
  background: #f4f3ff; border-radius: 4px; padding: 3px 7px;
}
/* Stripe single card element */
.stripe-element-single {
  padding: 11px 12px;
  border: 1px solid #e4e4e7;
  border-radius: 7px;
  background: #fafafa;
  transition: all .2s;
  min-height: 42px;
}
.stripe-element-single.StripeElement--focus {
  border-color: #111;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(17,17,17,.06);
}
.stripe-err {
  display: flex; align-items: center; gap: 6px;
  color: #e53e3e; font-size: 12px; margin-top: 10px;
  background: #fff5f5; border: 1px solid #fed7d7; border-radius: 6px; padding: 8px 10px;
}

/* Wallets / P2P */
.pay-desc { font-size: 12px; color: #666; margin-bottom: 10px; line-height: 1.5; }
.wallet-list { display: flex; flex-direction: column; gap: 6px; }
.wallet-item {
  display: flex; align-items: center; gap: 9px;
  padding: 9px 11px; border: 1px solid #e4e4e7; border-radius: 7px;
  background: #fafafa; cursor: pointer; font-family: 'DM Sans', sans-serif;
  font-size: 12px; font-weight: 500; color: #444; transition: all .18s;
}
.wallet-item:hover { border-color: #111; background: #fff; color: #111; }
.wallet-item.selected { border-color: #111; background: #fff; font-weight: 700; color: #111; }
.w-name { flex: 1; text-align: left; }
.info-note {
  display: flex; align-items: flex-start; gap: 6px;
  background: #f9f9f9; border: 1px solid #e4e4e7; border-radius: 7px;
  padding: 9px 10px; font-size: 11px; color: #666; line-height: 1.5; margin-top: 10px;
}

/* PayPal */
.redir-block { padding: 6px 0; }
.redir-block p { font-size: 12px; color: #555; line-height: 1.6; margin-top: 10px; }

/* Wire */
.wire-table { display: flex; flex-direction: column; gap: 5px; }
.wire-row {
  display: flex; justify-content: space-between;
  font-size: 12px; padding: 8px 10px;
  background: #fafafa; border: 1px solid #e4e4e7; border-radius: 6px;
}
.wire-row span { color: #999; }
.wire-row strong { font-weight: 700; color: #111; font-family: 'DM Mono', monospace; font-size: 11px; }

/* COD */
.cod-block { text-align: center; padding: 20px 10px; }
.cod-icon { width: 64px; height: 64px; background: #f5f5f7; border-radius: 50%; border: 1px solid #e4e4e7; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
.cod-block h4 { font-size: 14px; font-weight: 700; margin-bottom: 6px; }
.cod-block p { font-size: 12px; color: #666; line-height: 1.6; }

/* ── Transitions ── */
.slide-enter-active, .slide-leave-active { transition: all .2s ease; }
.slide-enter-from { opacity: 0; transform: translateY(8px); }
.slide-leave-to   { opacity: 0; transform: translateY(-5px); }

/* ── RIGHT COLUMN: Summary ── */
.right-col { position: sticky; top: 22px; }

.summary-card {
  background: #fff; border: 1px solid #e4e4e7; border-radius: 12px; padding: 18px;
}
.sum-title { font-size: 13px; font-weight: 700; margin-bottom: 14px; }

.sum-items { display: flex; flex-direction: column; gap: 12px; }
.sum-item  { display: flex; align-items: flex-start; gap: 10px; }

.sum-thumb { position: relative; flex-shrink: 0; }
.sum-thumb img {
  width: 52px; height: 52px; border-radius: 8px; object-fit: contain;
  border: 1px solid #e4e4e7; background: #fafafa; padding: 3px;
}
.sum-qty-badge {
  position: absolute; top: -5px; right: -5px;
  width: 16px; height: 16px; background: #111; color: #fff;
  border-radius: 50%; font-size: 9px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
}

.sum-info { flex: 1; min-width: 0; }
.sum-name { font-size: 11.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
.sum-meta { font-size: 10px; color: #aaa; margin-bottom: 6px; }

.qty-ctrl {
  display: flex; align-items: center;
  border: 1px solid #e4e4e7; border-radius: 5px; width: fit-content; overflow: hidden;
}
.qty-btn {
  width: 22px; height: 22px; background: #fff; border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center; color: #555; transition: background .15s;
}
.qty-btn:hover:not(:disabled) { background: #f4f4f5; }
.qty-btn:disabled { opacity: .3; cursor: not-allowed; }
.qty-ctrl span {
  width: 24px; text-align: center; font-size: 11px; font-weight: 600;
  border-left: 1px solid #e4e4e7; border-right: 1px solid #e4e4e7; line-height: 22px;
}

.sum-item-right { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0; }
.sum-price { font-size: 12px; font-weight: 700; }
.del-btn { background: none; border: none; cursor: pointer; color: #ccc; display: flex; transition: color .15s; padding: 0; }
.del-btn:hover { color: #e53e3e; }

.sum-divider { height: 1px; background: #f0f0f0; margin: 12px 0; }
.sum-rows { display: flex; flex-direction: column; gap: 7px; }
.sum-row { display: flex; justify-content: space-between; font-size: 12px; color: #555; }
.tag-free     { color: #16a34a; font-weight: 700; }
.tag-discount { color: #d97706; font-weight: 700; }
.ship-note { display: flex; align-items: center; gap: 5px; font-size: 10.5px; color: #aaa; margin-top: 3px; }
.ship-note.free-note { color: #16a34a; font-weight: 600; }
.sum-total { display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; }

.trust-badges { display: flex; flex-direction: column; gap: 6px; margin-top: 14px; padding-top: 12px; border-top: 1px solid #f0f0f0; }
.trust-item { display: flex; align-items: center; gap: 7px; font-size: 11px; color: #999; }
.trust-item svg { color: #bbb; flex-shrink: 0; }
</style>
