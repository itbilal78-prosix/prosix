<template>
    <nav-component />

        <breadcrumb-component />



  <div class="checkout-page">
    <div v-if="cartStore.items.length === 0" class="empty-cart">
      <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      <h3>Your cart is empty</h3>
      <router-link to="/" class="btn-shop">Continue Shopping</router-link>
    </div>

    <div v-else class="checkout-wrapper">
      <!-- HEADER -->
      <div class="page-header">
        <div class="header-left">
          <button class="btn-back-home" @click="router.push('/')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          </button>
          <h1 class="page-title">Checkout</h1>
        </div>
      </div>

      <!-- BODY -->
      <div class="checkout-body">
        <!-- LEFT -->
        <div class="left-col">

          <!-- STEP BAR above form -->
          <div class="step-bar-card">
            <div class="step-bar">
              <template v-for="(label, idx) in ['Contact', 'Shipping', 'Payment']" :key="idx">
                <div :class="['step-node', { active: currentStep === idx+1, done: currentStep > idx+1 }]"
                     @click="currentStep > idx+1 ? currentStep = idx+1 : null">
                  <div class="node-circle">
                    <svg v-if="currentStep > idx+1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span v-else>{{ idx+1 }}</span>
                  </div>
                  <span class="node-label">{{ label }}</span>
                </div>
                <div v-if="idx < 2" :class="['step-line', { filled: currentStep > idx+1 }]"></div>
              </template>
            </div>
          </div>

          <!-- STEP 1 -->
          <transition name="fu" mode="out-in">
            <div v-if="currentStep === 1" key="s1" class="card">
              <div class="card-head">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <h2>Contact Information</h2>
              </div>
              <div class="fields">
                <div class="field">
                  <label>Full Name</label>
                  <input v-model="form.name" type="text" placeholder="John Doe" />
                </div>
                <div class="field-2col">
                  <div class="field">
                    <label>Email Address</label>
                    <input v-model="form.email" type="email" placeholder="you@example.com" />
                  </div>
                  <div class="field">
                    <label>Phone Number</label>
                    <input v-model="form.phone" type="tel" placeholder="+1 (555) 000-0000" />
                  </div>
                </div>
              </div>
              <div class="card-footer two">
                <button class="btn-ghost" @click="router.push('/')">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                  Back to Shop
                </button>
                <button class="btn-primary" :disabled="!form.name || !form.phone" @click="currentStep = 2">
                  Continue
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
              </div>
            </div>
          </transition>

          <!-- STEP 2 -->
          <transition name="fu" mode="out-in">
            <div v-if="currentStep === 2" key="s2" class="card">
              <div class="card-head">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <h2>Shipping Address</h2>
              </div>
              <div class="fields">
                <div class="field">
                  <label>Street Address</label>
                  <textarea v-model="form.address" rows="2" placeholder="123 Main Street, Apt 4B"></textarea>
                </div>
                <div class="field-2col">
                  <div class="field"><label>City</label><input v-model="form.city" type="text" placeholder="New York" /></div>
                  <div class="field"><label>Postal Code</label><input v-model="form.postalCode" type="text" placeholder="10001" /></div>
                </div>
                <div class="field">
                  <label>Country</label>
                  <select v-model="form.country">
                    <option value="">Select country</option>
                    <option>United States</option><option>Pakistan</option>
                    <option>United Kingdom</option><option>Canada</option><option>Australia</option>
                  </select>
                </div>
              </div>
              <div class="card-footer two">
                <button class="btn-ghost" @click="currentStep = 1">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back
                </button>
                <button class="btn-primary" :disabled="!form.address || !form.city" @click="currentStep = 3">
                  Continue <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
              </div>
            </div>
          </transition>

          <!-- STEP 3 -->
          <transition name="fu" mode="out-in">
            <div v-if="currentStep === 3" key="s3" class="card">
              <div class="card-head">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <h2>Payment Method</h2>
              </div>
              <div class="pay-panel">
                <!-- Sidebar -->
                <nav class="pay-nav">
                  <button v-for="g in paymentGroups" :key="g.id"
                          :class="['pay-tab', { active: activeGroup === g.id }]"
                          @click="activeGroup = g.id; form.paymentMethod = g.defaultMethod">
                    <span class="tab-ico" v-html="g.svg"></span>
                    <span class="tab-txt">{{ g.label }}</span>
                    <svg class="tab-arr" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                  </button>
                </nav>
                <!-- Content -->
                <div class="pay-content-wrap">
                  <transition name="fu" mode="out-in">
                    <div v-if="activeGroup === 'card'" key="card" class="pay-section">
                      <div class="scheme-logos">
                        <svg viewBox="0 0 50 30" class="s-logo"><rect width="50" height="30" rx="3" fill="#fff" stroke="#e5e5e5"/><circle cx="19" cy="15" r="8" fill="#EB001B" opacity=".9"/><circle cx="31" cy="15" r="8" fill="#F79E1B" opacity=".9"/><path d="M25 8.6A8 8 0 0 1 28 15a8 8 0 0 1-3 6.4A8 8 0 0 1 22 15a8 8 0 0 1 3-6.4z" fill="#FF5F00"/></svg>
                        <svg viewBox="0 0 70 30" class="s-logo" style="width:52px"><rect width="70" height="30" rx="3" fill="#fff" stroke="#e5e5e5"/><text x="8" y="21" font-family="Arial" font-weight="900" font-size="14" fill="#1A1F71">VISA</text></svg>
                        <svg viewBox="0 0 60 30" class="s-logo" style="width:46px"><rect width="60" height="30" rx="3" fill="#fff" stroke="#e5e5e5"/><text x="5" y="13" font-family="Arial" font-weight="700" font-size="7" fill="#006FCF">AMERICAN</text><text x="5" y="22" font-family="Arial" font-weight="700" font-size="7" fill="#006FCF">EXPRESS</text></svg>
                      </div>
                      <div class="field">
                        <label>Card Number</label>
                        <div class="inp-wrap"><input v-model="form.cardNumber" type="text" placeholder="1234  5678  9012  3456" maxlength="19" @input="formatCard" /><svg class="inp-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
                      </div>
                      <div class="field-2col">
                        <div class="field"><label>Expiry</label><input v-model="form.cardExpiry" type="text" placeholder="MM / YY" maxlength="7" @input="formatExpiry" /></div>
                        <div class="field"><label>CVV</label><div class="inp-wrap"><input v-model="form.cardCvv" type="password" placeholder="•••" maxlength="4" /><svg class="inp-ico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div></div>
                      </div>
                    </div>
                  </transition>
                  <transition name="fu" mode="out-in">
                    <div v-if="activeGroup === 'wallets'" key="wallets" class="pay-section">
                      <p class="pay-desc">Select your preferred digital wallet.</p>
                      <div class="wallet-list">
                        <button v-for="w in walletOptions" :key="w.id" :class="['wallet-item', { selected: form.paymentMethod === w.id }]" @click="form.paymentMethod = w.id">
                          <span class="w-ico" v-html="w.svg"></span><span class="w-name">{{ w.name }}</span>
                          <svg v-if="form.paymentMethod === w.id" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                      </div>
                      <div class="info-note"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>You will be redirected to complete payment securely.</div>
                    </div>
                  </transition>
                  <transition name="fu" mode="out-in">
                    <div v-if="activeGroup === 'p2p'" key="p2p" class="pay-section">
                      <p class="pay-desc">Choose a peer-to-peer payment service.</p>
                      <div class="wallet-list">
                        <button v-for="w in p2pOptions" :key="w.id" :class="['wallet-item', { selected: form.paymentMethod === w.id }]" @click="form.paymentMethod = w.id">
                          <span class="w-ico" v-html="w.svg"></span><span class="w-name">{{ w.name }}</span>
                          <svg v-if="form.paymentMethod === w.id" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                      </div>
                      <div class="info-note"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Payment details sent after order confirmation.</div>
                    </div>
                  </transition>
                  <transition name="fu" mode="out-in">
                    <div v-if="activeGroup === 'paypal'" key="paypal" class="pay-section">
                      <div class="redir-block">
                        <svg width="88" height="26" viewBox="0 0 88 26"><text x="0" y="22" font-family="Arial" font-weight="900" font-size="22" fill="#003087">Pay</text><text x="42" y="22" font-family="Arial" font-weight="900" font-size="22" fill="#009cde">Pal</text></svg>
                        <p>You will be securely redirected to PayPal to complete your payment. Once confirmed, you will return automatically.</p>
                      </div>
                    </div>
                  </transition>
                  <transition name="fu" mode="out-in">
                    <div v-if="activeGroup === 'wire'" key="wire" class="pay-section">
                      <p class="pay-desc">Transfer directly to our bank account.</p>
                      <div class="wire-table">
                        <div class="wire-row"><span>Bank</span><strong>Chase Bank</strong></div>
                        <div class="wire-row"><span>Account Name</span><strong>Your Store LLC</strong></div>
                        <div class="wire-row"><span>Account No.</span><strong>•••• 4521</strong></div>
                        <div class="wire-row"><span>Routing No.</span><strong>021000021</strong></div>
                      </div>
                      <div class="info-note" style="margin-top:12px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Include order number as reference. Ships 1-2 days after clearance.</div>
                    </div>
                  </transition>
                  <transition name="fu" mode="out-in">
                    <div v-if="activeGroup === 'cod'" key="cod" class="pay-section">
                      <div class="cod-block">
                        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#111" stroke-width="1.4"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                        <h4>Cash on Delivery</h4>
                        <p>Pay in cash when your order arrives. No card needed.</p>
                      </div>
                    </div>
                  </transition>
                </div>
              </div>
              <div class="card-footer two">
                <button class="btn-ghost" @click="currentStep = 2">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back
                </button>
                <button class="btn-primary btn-order" :disabled="!form.paymentMethod || loading" @click="placeOrder">
                  <svg v-if="!loading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  <span v-if="loading" class="spin"></span>
                  <span>{{ loading ? 'Processing...' : 'Place Order — $' + totalAmount }}</span>
                </button>
              </div>
            </div>
          </transition>
        </div>

        <!-- RIGHT SUMMARY -->
        <aside class="right-col">
          <div class="summary-card">
            <h3 class="sum-title">Order Summary</h3>
            <div class="sum-items">
              <div v-for="item in cartStore.items" :key="item.id + item.size" class="sum-item">
                <div class="sum-img-wrap">
                  <img :src="item.image" :alt="item.name" class="sum-img" />
                  <span class="sum-badge">{{ item.quantity }}</span>
                </div>
                <div class="sum-info">
                  <p class="sum-name">{{ item.name }}</p>
                  <p class="sum-size">Size: {{ item.size }}</p>
                  <div class="qty-row">
                    <button class="q-btn" @click="decreaseQty(item)" :disabled="item.quantity <= 1">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <span class="q-val">{{ item.quantity }}</span>
                    <button class="q-btn" @click="increaseQty(item)">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                  </div>
                </div>
                <div class="sum-right">
                  <span class="sum-price">${{ (Number(item.price) * item.quantity).toFixed(2) }}</span>
                  <button class="del-btn" @click="removeItem(item)">
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
                <span :class="shipping === 0 ? 'free' : itemCount === 2 ? 'ten-pct' : ''">{{ shippingLabel }}</span>
              </div>
              <div v-if="totalQty === 3" class="ship-note">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                10% shipping for qty 3
              </div>
              <div v-if="totalQty > 3" class="ship-note free-ship">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                Free shipping on qty 4+! 🎉
              </div>
            </div>
            <div class="sum-divider"></div>
            <div class="sum-total"><span>Total</span><span>${{ totalAmount }}</span></div>

            <div class="trust-list">
              <div class="trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg><span>Secure checkout</span>
              </div>
              <div class="trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg><span>60-day returns</span>
              </div>
              <div class="trust-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg><span>Fast delivery</span>
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
import { ref, computed } from 'vue'
import { useCartStore } from '@/store/cart'
import { useRouter } from 'vue-router'

const cartStore = useCartStore()
const router = useRouter()
const loading = ref(false)
const currentStep = ref(1)
const activeGroup = ref('card')

const form = ref({
  name: '', email: '', phone: '',
  address: '', city: '', postalCode: '', country: '',
  paymentMethod: 'card',
  cardNumber: '', cardExpiry: '', cardCvv: ''
})

const svgCard   = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>'
const svgWallet = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>'
const svgDollar = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
const svgGlobe  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>'
const svgWire   = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><polyline points="8 8 3 12 8 16"/><polyline points="16 8 21 12 16 16"/></svg>'
const svgBox    = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>'

const paymentGroups = [
  { id: 'card',    svg: svgCard,   label: 'Credit / Debit Card',      defaultMethod: 'card'     },
  { id: 'wallets', svg: svgWallet, label: 'Apple / Google Pay',       defaultMethod: 'applepay' },
  { id: 'p2p',     svg: svgDollar, label: 'Cash App · Venmo · Zelle', defaultMethod: 'cashapp'  },
  { id: 'paypal',  svg: svgGlobe,  label: 'PayPal',                   defaultMethod: 'paypal'   },
  { id: 'wire',    svg: svgWire,   label: 'Wire Transfer',            defaultMethod: 'wire'     },
  { id: 'cod',     svg: svgBox,    label: 'Cash on Delivery',         defaultMethod: 'cod'      },
]

const appleS  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2a5.5 5.5 0 0 1 4 1.5A5.5 5.5 0 0 1 12 5a5.5 5.5 0 0 1-4-1.5A5.5 5.5 0 0 1 12 2z"/><path d="M20 17c0 3-3.5 5-8 5s-8-2-8-5c0-3.5 2-7 4-9.5 1 1.5 2.5 2.5 4 2.5s3-1 4-2.5c2 2.5 4 6 4 9.5z"/></svg>'
const googleS = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>'
const walletOptions = [
  { id: 'applepay',  svg: appleS,  name: 'Apple Pay'  },
  { id: 'googlepay', svg: googleS, name: 'Google Pay' },
]

const cashS  = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></svg>'
const venmoS = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 4v2c0 6-4 12-9 14L4 22"/><path d="M4 4l6 8 4-8"/></svg>'
const zelleS = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>'
const p2pOptions = [
  { id: 'cashapp', svg: cashS,  name: 'Cash App' },
  { id: 'venmo',   svg: venmoS, name: 'Venmo'    },
  { id: 'zelle',   svg: zelleS, name: 'Zelle'    },
]

const subtotal = computed(() =>
  cartStore.items.reduce((s, i) => s + Number(i.price) * i.quantity, 0).toFixed(2)
)

// Shipping logic:
// 1 product  → flat $19
// 2 products → subtotal + 10%
// 2+ unique products → FREE
const itemCount = computed(() => cartStore.items.length)

// Total quantity across all items
const totalQty = computed(() =>
  cartStore.items.reduce((s, i) => s + i.quantity, 0)
)

const shipping = computed(() => {
  const qty = totalQty.value
  const sub = parseFloat(subtotal.value)
  if (qty > 3)   return 0                                      // FREE if total qty > 3
  if (qty === 3) return parseFloat((sub * 0.10).toFixed(2))   // 10% if qty == 3 (2 products or 3 of 1)
  return 19                                                     // flat $19
})

const shippingLabel = computed(() => {
  const qty = totalQty.value
  if (qty > 3)   return 'FREE'
  if (qty === 3) return `+10% ($${shipping.value.toFixed(2)})`
  return `$${shipping.value.toFixed(2)}`
})

const totalAmount = computed(() =>
  (parseFloat(subtotal.value) + shipping.value).toFixed(2)
)

const increaseQty = (item) => {
  const idx = cartStore.items.findIndex(i => i.id === item.id && i.size === item.size)
  if (idx !== -1) {
    if (typeof cartStore.updateQuantity === 'function') {
      cartStore.updateQuantity(item.id, item.size, item.quantity + 1)
    } else {
      cartStore.items[idx].quantity++
    }
  }
}

const decreaseQty = (item) => {
  if (item.quantity <= 1) return
  const idx = cartStore.items.findIndex(i => i.id === item.id && i.size === item.size)
  if (idx !== -1) {
    if (typeof cartStore.updateQuantity === 'function') {
      cartStore.updateQuantity(item.id, item.size, item.quantity - 1)
    } else {
      cartStore.items[idx].quantity--
    }
  }
}

const removeItem = (item) => {
  const idx = cartStore.items.findIndex(i => i.id === item.id && i.size === item.size)
  if (idx !== -1) {
    if (typeof cartStore.removeFromCart === 'function') {
      cartStore.removeFromCart(item.id, item.size)
    } else {
      cartStore.items.splice(idx, 1)
    }
  }
}

const formatCard = () => {
  let v = form.value.cardNumber.replace(/\D/g, '').substring(0, 16)
  form.value.cardNumber = v.replace(/(.{4})/g, '$1 ').trim()
}
const formatExpiry = () => {
  let v = form.value.cardExpiry.replace(/\D/g, '').substring(0, 4)
  if (v.length >= 2) v = v.substring(0, 2) + ' / ' + v.substring(2)
  form.value.cardExpiry = v
}

const placeOrder = async () => {
  loading.value = true
  try {
    const res = await axios.post('/api/orders', { cart: cartStore.items, checkout: form.value }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token') || ''}` }
    })
    alert(`Order placed! ID: ${res.data.order_id || 'N/A'}`)
    cartStore.clearCart()
    router.push('/')
  } catch (e) {
    alert('Failed: ' + (e.response?.data?.message || 'Please try again'))
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
*, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }

.checkout-page { font-family:'Inter',sans-serif; min-height:100vh; background:#f4f4f5; padding:20px 20px 60px; color:#111; }

.empty-cart { text-align:center; padding:100px 20px; display:flex; flex-direction:column; align-items:center; gap:16px; }
.empty-cart h3 { font-size:20px; font-weight:600; color:#444; }
.btn-shop { display:inline-block; padding:11px 26px; background:#111; color:#fff; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; }
.btn-shop:hover { background:#333; }

.checkout-wrapper { max-width:100%; margin:0 auto; }

.page-header { margin-bottom:16px; }
.header-left { display:flex; align-items:center; gap:12px; }
.page-title { font-size:20px; font-weight:700; letter-spacing:-.4px; }
.btn-back-home { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; background:#fff; border:1px solid #e4e4e7; border-radius:8px; cursor:pointer; color:#555; transition:all .2s; flex-shrink:0; }
.btn-back-home:hover { background:#f4f4f5; border-color:#bbb; color:#111; }

.step-bar-card { background:#fff; border:1px solid #e4e4e7; border-radius:12px; padding:16px 24px; margin-bottom:16px; }
.step-bar { display:flex; align-items:center; width:100%; }
.step-node { display:flex; align-items:center; gap:9px; }
.node-circle { width:36px; height:36px; border-radius:50%; background:#e4e4e7; color:#999; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; transition:all .3s; flex-shrink:0; }
.step-node.active .node-circle, .step-node.done .node-circle { background:#111; color:#fff; }
.step-node.done { cursor:pointer; }
.node-label { font-size:13px; font-weight:600; color:#999; transition:color .3s; white-space:nowrap; }
.step-node.active .node-label, .step-node.done .node-label { color:#111; }
.step-line { flex:1; height:2px; background:#e4e4e7; margin:0 12px; border-radius:2px; transition:background .4s; }
.step-line.filled { background:#111; }

.checkout-body { display:grid; grid-template-columns:1fr 800px; gap:20px; align-items:start; }
@media(max-width:880px) { .checkout-body { grid-template-columns:1fr; } .right-col { order:-1; } }

.card { background:#fff; border:1px solid #e4e4e7; border-radius:12px; overflow:hidden; width:100%; }
.card-head { display:flex; align-items:center; gap:9px; padding:16px 20px; border-bottom:1px solid #f0f0f0; }
.card-head h2 { font-size:13px; font-weight:700; }
.card-head svg { color:#777; }

.fields { padding:20px; display:flex; flex-direction:column; gap:13px; }
.field { display:flex; flex-direction:column; gap:5px; }
.field label { font-size:11px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.7px; }
.field input, .field select, .field textarea { height:40px; padding:0 12px; border:1px solid #e4e4e7; border-radius:7px; font-family:'Inter',sans-serif; font-size:13px; color:#111; background:#fafafa; transition:all .2s; width:100%; }
.field textarea { height:auto; padding:10px 12px; resize:none; }
.field input:focus, .field select:focus, .field textarea:focus { outline:none; border-color:#111; background:#fff; box-shadow:0 0 0 3px rgba(17,17,17,.06); }
.field select { cursor:pointer; }
.field-2col { display:grid; grid-template-columns:1fr 1fr; gap:11px; }

.inp-wrap { position:relative; }
.inp-wrap input { padding-right:38px; }
.inp-ico { position:absolute; right:12px; top:50%; transform:translateY(-50%); pointer-events:none; }

.card-footer { padding:16px 20px; border-top:1px solid #f0f0f0; display:flex; justify-content:flex-end; }
.card-footer.two { justify-content:space-between; }

.btn-primary { display:inline-flex; align-items:center; gap:7px; height:40px; padding:0 18px; background:#111; color:#fff; border:none; border-radius:7px; font-family:'Inter',sans-serif; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; }
.btn-primary:hover:not(:disabled) { background:#2d2d2d; }
.btn-primary:disabled { opacity:.35; cursor:not-allowed; }
.btn-order { height:44px; padding:0 22px; font-size:13px; }
.btn-ghost { display:inline-flex; align-items:center; gap:7px; height:40px; padding:0 15px; background:#fff; color:#555; border:1px solid #e4e4e7; border-radius:7px; font-family:'Inter',sans-serif; font-size:13px; font-weight:500; cursor:pointer; transition:all .2s; }
.btn-ghost:hover { background:#f9f9f9; }

.spin { width:13px; height:13px; border:2px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:sp .7s linear infinite; display:inline-block; }
@keyframes sp { to { transform:rotate(360deg); } }

/* Payment panel */
.pay-panel { display:grid; grid-template-columns:180px 1fr; border-top:1px solid #f0f0f0; border-bottom:1px solid #f0f0f0; }
.pay-nav { background:#fafafa; border-right:1px solid #f0f0f0; display:flex; flex-direction:column; }
.pay-tab { display:flex; align-items:center; gap:8px; padding:11px 12px; background:none; border:none; border-bottom:1px solid #f0f0f0; border-left:2px solid transparent; cursor:pointer; color:#666; transition:all .18s; }
.pay-tab:last-child { border-bottom:none; }
.pay-tab:hover { background:#f1f1f1; color:#111; }
.pay-tab.active { background:#fff; border-left-color:#111; color:#111; }
.tab-ico { display:flex; flex-shrink:0; }
.tab-txt { flex:1; font-size:11.5px; font-weight:500; line-height:1.3; text-align:left; }
.tab-arr { color:#ddd; flex-shrink:0; transition:color .2s; }
.pay-tab.active .tab-arr { color:#111; }

.pay-content-wrap { background:#fff; padding:20px; min-height:240px; }
.pay-section { animation:fu-anim .22s ease; }
@keyframes fu-anim { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }

.scheme-logos { display:flex; align-items:center; gap:7px; margin-bottom:13px; padding-bottom:11px; border-bottom:1px solid #f0f0f0; }
.s-logo { height:24px; width:38px; }

.pay-content-wrap .field { margin-bottom:10px; }
.pay-content-wrap .field label { font-size:10px; font-weight:600; color:#999; text-transform:uppercase; letter-spacing:.7px; }
.pay-content-wrap .field input { height:42px; font-size:14px; }

.pay-desc { font-size:12px; color:#666; margin-bottom:11px; line-height:1.5; }
.wallet-list { display:flex; flex-direction:column; gap:6px; margin-bottom:11px; }
.wallet-item { display:flex; align-items:center; gap:9px; padding:9px 12px; border:1px solid #e4e4e7; border-radius:7px; background:#fafafa; cursor:pointer; font-family:'Inter',sans-serif; font-size:12px; font-weight:500; color:#444; transition:all .18s; }
.wallet-item:hover { border-color:#111; background:#fff; color:#111; }
.wallet-item.selected { border-color:#111; background:#fff; color:#111; font-weight:600; }
.w-ico { display:flex; flex-shrink:0; }
.w-name { flex:1; }

.info-note { display:flex; align-items:flex-start; gap:7px; background:#f9f9f9; border:1px solid #e4e4e7; border-radius:7px; padding:9px 11px; font-size:11px; color:#666; line-height:1.5; }
.redir-block { padding:6px 0; }
.redir-block p { font-size:12px; color:#555; line-height:1.6; margin-top:11px; }
.wire-table { display:flex; flex-direction:column; gap:6px; }
.wire-row { display:flex; justify-content:space-between; font-size:12px; padding:8px 11px; background:#fafafa; border-radius:6px; border:1px solid #e4e4e7; }
.wire-row span { color:#999; }
.wire-row strong { font-weight:600; color:#111; }
.cod-block { text-align:center; padding:16px 10px; }
.cod-block h4 { font-size:14px; font-weight:700; margin:11px 0 5px; }
.cod-block p { font-size:12px; color:#666; line-height:1.6; }

.fu-enter-active, .fu-leave-active { transition:all .22s ease; }
.fu-enter-from { opacity:0; transform:translateY(10px); }
.fu-leave-to   { opacity:0; transform:translateY(-6px); }

/* Summary */
.summary-card { background:#fff; border:1px solid #e4e4e7; border-radius:12px; padding:20px; position:sticky; top:22px;  }
.sum-title { font-size:13px; font-weight:700; margin-bottom:16px; }
.sum-items { display:flex; flex-direction:column; gap:13px; margin-bottom:16px; }
.sum-item { display:flex; align-items:center; gap:10px; }
.sum-img-wrap { position:relative; flex-shrink:0; }
.sum-img { width:54px; height:54px; border-radius:7px; object-fit:contain; border:1px solid #e4e4e7; background:#fafafa; padding:3px; }
.sum-badge { position:absolute; top:-5px; right:-5px; width:16px; height:16px; background:#111; color:#fff; border-radius:50%; font-size:9px; font-weight:700; display:flex; align-items:center; justify-content:center; }
.sum-info { flex:1; min-width:0; }
.sum-name { font-size:11px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:2px; }
.sum-size { font-size:10px; color:#aaa; margin-bottom:6px; }
.qty-row { display:flex; align-items:center; border:1px solid #e4e4e7; border-radius:5px; width:fit-content; overflow:hidden; }
.q-btn { width:22px; height:22px; background:#fff; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#555; transition:background .15s; }
.q-btn:hover:not(:disabled) { background:#f4f4f5; }
.q-btn:disabled { opacity:.3; cursor:not-allowed; }
.q-val { width:24px; text-align:center; font-size:11px; font-weight:600; border-left:1px solid #e4e4e7; border-right:1px solid #e4e4e7; line-height:22px; }
.sum-right { display:flex; flex-direction:column; align-items:flex-end; gap:6px; flex-shrink:0; }
.sum-price { font-size:12px; font-weight:700; }
.del-btn { background:none; border:none; cursor:pointer; color:#ccc; display:flex; transition:color .15s; }
.del-btn:hover { color:#ef4444; }
.sum-divider { height:1px; background:#f0f0f0; margin:13px 0; }
.sum-rows { display:flex; flex-direction:column; gap:8px; }
.sum-row { display:flex; justify-content:space-between; font-size:12px; color:#555; }
.free { color:#16a34a; font-weight:600; }
.sum-total { display:flex; justify-content:space-between; font-size:14px; font-weight:700; }
.free-note { display:flex; align-items:center; gap:5px; margin-top:10px; font-size:11px; color:#16a34a; font-weight:500; }
.trust-list { display:flex; flex-direction:column; gap:7px; margin-top:16px; padding-top:14px; border-top:1px solid #f0f0f0; }
.trust-item { display:flex; align-items:center; gap:7px; font-size:11px; color:#999; }
.trust-item svg { color:#bbb; flex-shrink:0; }
.ten-pct { color:#d97706; font-weight:600; }
.ship-note { display:flex; align-items:center; gap:5px; font-size:10px; color:#999; margin-top:4px; }
.ship-note.free-ship { color:#16a34a; font-weight:500; }
</style>
