<template>
  <div class="checkout-page">

    <div v-if="cartStore.items.length === 0" class="empty-cart">
      <div class="empty-icon">🛒</div>
      <h3>Your cart is empty</h3>
      <router-link to="/" class="btn-back">Continue Shopping</router-link>
    </div>

    <div v-else class="checkout-grid">

      <!-- LEFT: FORM -->
      <div class="form-side">

        <!-- Step Indicator -->
        <div class="steps-bar">
          <div v-for="s in 3" :key="s"
               :class="['step-pill', { active: currentStep === s, done: currentStep > s }]"
               @click="currentStep > s ? currentStep = s : null">
            <span class="pill-num">{{ currentStep > s ? '✓' : s }}</span>
            <span class="pill-label">{{ ['Contact', 'Shipping', 'Payment'][s-1] }}</span>
          </div>
          <div class="step-track">
            <div class="step-progress" :style="{ width: ((currentStep-1)/2*100) + '%' }"></div>
          </div>
        </div>

        <!-- STEP 1: Contact -->
        <transition name="slide-fade" mode="out-in">
          <div v-if="currentStep === 1" key="step1" class="form-panel">
            <h2 class="panel-title">Contact Information</h2>
            <div class="field-group">
              <label>Full Name</label>
              <input v-model="form.name" type="text" placeholder="John Doe" class="field-input" />
            </div>
            <div class="field-row">
              <div class="field-group">
                <label>Email</label>
                <input v-model="form.email" type="email" placeholder="you@email.com" class="field-input" />
              </div>
              <div class="field-group">
                <label>Phone</label>
                <input v-model="form.phone" type="tel" placeholder="+1 000 000 0000" class="field-input" />
              </div>
            </div>
            <button class="btn-next" :disabled="!form.name || !form.phone" @click="currentStep = 2">
              Continue to Shipping <span>→</span>
            </button>
          </div>
        </transition>

        <!-- STEP 2: Shipping -->
        <transition name="slide-fade" mode="out-in">
          <div v-if="currentStep === 2" key="step2" class="form-panel">
            <h2 class="panel-title">Shipping Address</h2>
            <div class="field-group">
              <label>Street Address</label>
              <textarea v-model="form.address" placeholder="123 Main Street, Apt 4B" class="field-input field-textarea"></textarea>
            </div>
            <div class="field-row">
              <div class="field-group">
                <label>City</label>
                <input v-model="form.city" type="text" placeholder="New York" class="field-input" />
              </div>
              <div class="field-group">
                <label>Postal Code</label>
                <input v-model="form.postalCode" type="text" placeholder="10001" class="field-input" />
              </div>
            </div>
            <div class="field-group">
              <label>Country</label>
              <select v-model="form.country" class="field-input field-select">
                <option value="">Select country</option>
                <option>United States</option>
                <option>Pakistan</option>
                <option>United Kingdom</option>
                <option>Canada</option>
                <option>Australia</option>
              </select>
            </div>
            <div class="btn-row">
              <button class="btn-back-step" @click="currentStep = 1">← Back</button>
              <button class="btn-next" :disabled="!form.address || !form.city" @click="currentStep = 3">
                Continue to Payment <span>→</span>
              </button>
            </div>
          </div>
        </transition>

        <!-- STEP 3: Payment -->
        <transition name="slide-fade" mode="out-in">
          <div v-if="currentStep === 3" key="step3" class="form-panel">
            <h2 class="panel-title">Payment Method</h2>

            <!-- Payment: Sidebar tabs + Content -->
            <div class="payment-layout">

              <!-- Sidebar -->
              <div class="payment-sidebar">
                <div v-for="group in paymentGroups" :key="group.id"
                     :class="['payment-tab', { active: activePaymentGroup === group.id }]"
                     @click="activePaymentGroup = group.id; form.paymentMethod = group.defaultMethod">
                  <span class="tab-icon">{{ group.icon }}</span>
                  <span class="tab-label">{{ group.label }}</span>
                  <span class="tab-arrow">›</span>
                </div>
              </div>

              <!-- Content -->
              <div class="payment-content">

                <!-- Credit / Debit Card -->
                <div v-if="activePaymentGroup === 'card'" class="payment-form-area">
                  <div class="card-logos">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/200px-Mastercard-logo.svg.png" alt="MC" class="card-logo" />
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/200px-Visa_Inc._logo.svg.png" alt="Visa" class="card-logo" />
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/American_Express_logo_%282018%29.svg/200px-American_Express_logo_%282018%29.svg.png" alt="Amex" class="card-logo" />
                  </div>
                  <div class="pay-field-group">
                    <label>Card Number</label>
                    <input v-model="form.cardNumber" type="text" placeholder="1234 5678 9012 3456"
                           maxlength="19" class="pay-input" @input="formatCard" />
                  </div>
                  <div class="pay-field-row">
                    <div class="pay-field-group">
                      <label>Expiry Date</label>
                      <input v-model="form.cardExpiry" type="text" placeholder="MM / YY"
                             maxlength="7" class="pay-input" @input="formatExpiry" />
                    </div>
                    <div class="pay-field-group">
                      <label>CVV2</label>
                      <input v-model="form.cardCvv" type="password" placeholder="•••"
                             maxlength="4" class="pay-input" />
                    </div>
                  </div>
                  <div class="pay-field-group">
                    <label>Email</label>
                    <input v-model="form.email" type="email" placeholder="example@email.com" class="pay-input" />
                  </div>
                </div>

                <!-- Apple / Google Pay -->
                <div v-else-if="activePaymentGroup === 'wallets'" class="payment-form-area">
                  <div class="wallet-options">
                    <div :class="['wallet-btn', { selected: form.paymentMethod === 'applepay' }]"
                         @click="form.paymentMethod = 'applepay'">
                      <span class="w-icon">🍎</span> Apple Pay
                    </div>
                    <div :class="['wallet-btn', { selected: form.paymentMethod === 'googlepay' }]"
                         @click="form.paymentMethod = 'googlepay'">
                      <span class="w-icon">🔵</span> Google Pay
                    </div>
                  </div>
                  <div class="redirect-note">
                    You'll be redirected to <strong>{{ form.paymentMethod === 'applepay' ? 'Apple Pay' : 'Google Pay' }}</strong> to complete payment securely.
                  </div>
                </div>

                <!-- Cash App / Venmo / Zelle -->
                <div v-else-if="activePaymentGroup === 'p2p'" class="payment-form-area">
                  <div class="wallet-options">
                    <div :class="['wallet-btn', { selected: form.paymentMethod === 'cashapp' }]"
                         @click="form.paymentMethod = 'cashapp'">
                      <span class="w-icon">💵</span> Cash App
                    </div>
                    <div :class="['wallet-btn', { selected: form.paymentMethod === 'venmo' }]"
                         @click="form.paymentMethod = 'venmo'">
                      <span class="w-icon">✌️</span> Venmo
                    </div>
                    <div :class="['wallet-btn', { selected: form.paymentMethod === 'zelle' }]"
                         @click="form.paymentMethod = 'zelle'">
                      <span class="w-icon">⚡</span> Zelle
                    </div>
                  </div>
                  <div class="redirect-note">
                    You'll complete payment via <strong>{{ getMethodName(form.paymentMethod) }}</strong> after order is confirmed.
                  </div>
                </div>

                <!-- PayPal -->
                <div v-else-if="activePaymentGroup === 'paypal'" class="payment-form-area">
                  <div class="paypal-block">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/PayPal.svg/200px-PayPal.svg.png" alt="PayPal" class="paypal-logo" />
                    <p>You will be redirected to PayPal to complete your purchase securely. Once payment is confirmed, you'll return to our site.</p>
                  </div>
                </div>

                <!-- Wire Transfer -->
                <div v-else-if="activePaymentGroup === 'wire'" class="payment-form-area">
                  <div class="wire-info">
                    <div class="wire-row"><span>Bank Name</span><strong>Chase Bank</strong></div>
                    <div class="wire-row"><span>Account Name</span><strong>Your Store LLC</strong></div>
                    <div class="wire-row"><span>Account No.</span><strong>****4521</strong></div>
                    <div class="wire-row"><span>Routing No.</span><strong>021000021</strong></div>
                  </div>
                  <div class="redirect-note" style="margin-top:12px">
                    Include your order number as reference. Order ships after payment clears (1-2 business days).
                  </div>
                </div>

                <!-- Cash on Delivery -->
                <div v-else-if="activePaymentGroup === 'cod'" class="payment-form-area">
                  <div class="cod-block">
                    <div class="cod-icon">📦</div>
                    <h4>Cash on Delivery</h4>
                    <p>Pay when your order arrives at your door. No card required.</p>
                  </div>
                </div>

              </div>
            </div>
            <!-- /payment-layout -->

            <div class="btn-row" style="margin-top:24px">
              <button class="btn-back-step" @click="currentStep = 2">← Back</button>
              <button class="btn-place-order" :disabled="!form.paymentMethod || loading" @click="placeOrder">
                <span v-if="loading" class="spinner-inline"></span>
                <span v-else>🔒 Place Order · ${{ totalAmount }}</span>
              </button>
            </div>
          </div>
        </transition>

      </div>

      <!-- RIGHT: ORDER SUMMARY -->
      <div class="summary-side">
        <div class="summary-card">
          <h3 class="summary-title">Order Summary</h3>
          <p class="item-count">{{ cartStore.items.length }} item{{ cartStore.items.length > 1 ? 's' : '' }}</p>

          <div class="summary-items">
            <div v-for="item in cartStore.items" :key="item.id + item.size" class="summary-item">
              <div class="item-img-wrap">
                <img :src="item.image" :alt="item.name" class="item-img" />
                <span class="item-qty-badge">{{ item.quantity }}</span>
              </div>
              <div class="item-details">
                <p class="item-name">{{ item.name }}</p>
                <p class="item-meta">Size: {{ item.size }}</p>
                <div class="qty-controls">
                  <button class="qty-ctrl-btn" @click="decreaseQty(item)" :disabled="item.quantity <= 1">−</button>
                  <span class="qty-num">{{ item.quantity }}</span>
                  <button class="qty-ctrl-btn" @click="increaseQty(item)">+</button>
                </div>
              </div>
              <div class="item-price-col">
                <p class="item-total">${{ (Number(item.price) * item.quantity).toFixed(2) }}</p>
                <button class="remove-btn" @click="removeItem(item)">✕</button>
              </div>
            </div>
          </div>

          <div class="summary-divider"></div>

          <div class="price-breakdown">
            <div class="price-row">
              <span>Subtotal</span><span>${{ subtotal }}</span>
            </div>
            <div class="price-row">
              <span>Shipping</span>
              <span :class="shipping === 0 ? 'free-tag' : ''">{{ shipping === 0 ? 'FREE' : '$' + shipping }}</span>
            </div>
            <div class="price-row" v-if="shipping === 0">
            </div>
          </div>

          <div class="summary-divider"></div>

          <div class="total-row">
            <span>Total</span>
            <span class="total-amount">${{ totalAmount }}</span>
          </div>

        </div>
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
const activePaymentGroup = ref('card')

const form = ref({
  name: '', email: '', phone: '',
  address: '', city: '', postalCode: '', country: '',
  paymentMethod: 'card',
  cardNumber: '', cardExpiry: '', cardCvv: ''
})

const paymentGroups = [
  { id: 'card',    icon: '💳', label: 'Payment by card',         defaultMethod: 'card'     },
  { id: 'wallets', icon: '📱', label: 'Apple / Google Pay',      defaultMethod: 'applepay' },
  { id: 'p2p',     icon: '💸', label: 'Cash App / Venmo / Zelle',defaultMethod: 'cashapp'  },
  { id: 'paypal',  icon: '🅿️', label: 'PayPal',                  defaultMethod: 'paypal'   },
  { id: 'wire',    icon: '🏦', label: 'Wire Transfer',            defaultMethod: 'wire'     },
  { id: 'cod',     icon: '📦', label: 'Cash on Delivery',         defaultMethod: 'cod'      },
]

const methodNames = {
  card:'Credit/Debit Card', applepay:'Apple Pay', googlepay:'Google Pay',
  cashapp:'Cash App', venmo:'Venmo', zelle:'Zelle',
  paypal:'PayPal', wire:'Wire Transfer', cod:'Cash on Delivery'
}
const getMethodName = (id) => methodNames[id] || id

const subtotal  = computed(() => cartStore.items.reduce((s,i) => s + Number(i.price)*i.quantity, 0).toFixed(2))
const shipping  = computed(() => parseFloat(subtotal.value) >= 200 ? 0 : 19)
const totalAmount = computed(() => (parseFloat(subtotal.value) + shipping.value).toFixed(2))

const increaseQty = (item) => cartStore.updateQuantity(item.id, item.size, item.quantity + 1)
const decreaseQty = (item) => { if (item.quantity > 1) cartStore.updateQuantity(item.id, item.size, item.quantity - 1) }
const removeItem  = (item) => cartStore.removeFromCart(item.id, item.size)

const formatCard = () => {
  let v = form.value.cardNumber.replace(/\D/g,'').substring(0,16)
  form.value.cardNumber = v.replace(/(.{4})/g,'$1 ').trim()
}
const formatExpiry = () => {
  let v = form.value.cardExpiry.replace(/\D/g,'').substring(0,4)
  if (v.length >= 2) v = v.substring(0,2) + ' / ' + v.substring(2)
  form.value.cardExpiry = v
}

const placeOrder = async () => {
  loading.value = true
  try {
    const res = await axios.post('/api/orders', { cart: cartStore.items, checkout: form.value }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token') || ''}` }
    })
    alert(`✅ Order placed! ID: ${res.data.order_id || 'N/A'}`)
    cartStore.clearCart()
    router.push('/')
  } catch (e) {
    alert('❌ ' + (e.response?.data?.message || 'Failed to place order'))
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.checkout-page {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  background: #f5f3ef;
  padding: 40px 24px 80px;
}

.empty-cart { text-align: center; padding: 120px 20px; }
.empty-icon { font-size: 64px; margin-bottom: 20px; }
.empty-cart h3 { font-family:'Playfair Display',serif; font-size:28px; margin-bottom:20px; }
.btn-back { display:inline-block; padding:14px 32px; background:#1a1a1a; color:#fff; border-radius:6px; text-decoration:none; font-weight:600; }

/* GRID */
.checkout-grid {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 32px;
  max-width: 1200px;
  margin: 0 auto;
}
@media (max-width:900px) {
  .checkout-grid { grid-template-columns:1fr; }
  .summary-side { order:-1; }
}

/* STEPS BAR */
.steps-bar {
  display:flex; align-items:center; position:relative;
  margin-bottom:36px; background:#fff; border-radius:16px;
  padding:16px 28px; box-shadow:0 2px 12px rgba(0,0,0,0.06);
}
.step-track {
  position:absolute; bottom:0; left:0; right:0; height:3px;
  background:#e8e5e0; border-radius:0 0 16px 16px; overflow:hidden;
}
.step-progress { height:100%; background:#1a1a1a; transition:width 0.4s ease; }

.step-pill { display:flex; align-items:center; gap:10px; flex:1; cursor:default; opacity:0.4; transition:opacity 0.3s; }
.step-pill.active { opacity:1; }
.step-pill.done { opacity:0.75; cursor:pointer; }

.pill-num {
  width:32px; height:32px; border-radius:50%; background:#e8e5e0;
  display:flex; align-items:center; justify-content:center;
  font-size:13px; font-weight:700; transition:all 0.3s;
}
.step-pill.active .pill-num,
.step-pill.done .pill-num { background:#1a1a1a; color:#fff; }
.pill-label { font-size:13px; font-weight:600; color:#333; }

/* FORM PANEL */
.form-panel {
  background:#fff; border-radius:20px; padding:40px;
  box-shadow:0 2px 20px rgba(0,0,0,0.06);
}
.panel-title { font-family:'Playfair Display',serif; font-size:26px; color:#1a1a1a; margin-bottom:28px; }

/* FIELDS */
.field-group { display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
.field-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.field-group label { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:#888; }
.field-input {
  height:50px; padding:0 16px; border:1.5px solid #e8e5e0; border-radius:10px;
  font-family:'DM Sans',sans-serif; font-size:15px; color:#1a1a1a; background:#faf9f7; transition:all 0.2s;
}
.field-input:focus { outline:none; border-color:#1a1a1a; background:#fff; box-shadow:0 0 0 3px rgba(26,26,26,0.08); }
.field-textarea { height:auto !important; padding:14px 16px; resize:vertical; min-height:80px; }
.field-select { cursor:pointer; }

/* ════════════════════════════════
   PAYMENT SIDEBAR + CONTENT
════════════════════════════════ */
.payment-layout {
  display:grid;
  grid-template-columns:190px 1fr;
  border:1.5px solid #e8e5e0;
  border-radius:14px;
  overflow:hidden;
  min-height:280px;
}

.payment-sidebar {
  background:#f8f7f5;
  border-right:1.5px solid #e8e5e0;
  display:flex; flex-direction:column;
}

.payment-tab {
  display:flex; align-items:center; gap:10px;
  padding:13px 14px;
  cursor:pointer;
  border-left:3px solid transparent;
  border-bottom:1px solid #eeebe6;
  font-size:12px; font-weight:500; color:#555;
  transition:all 0.2s;
}
.payment-tab:last-child { border-bottom:none; }
.payment-tab:hover { background:#f0eee9; color:#1a1a1a; }
.payment-tab.active { background:#fff; border-left-color:#1a1a1a; color:#1a1a1a; font-weight:700; }

.tab-icon { font-size:17px; flex-shrink:0; }
.tab-label { flex:1; line-height:1.3; }
.tab-arrow { font-size:18px; color:#ccc; }
.payment-tab.active .tab-arrow { color:#1a1a1a; }

.payment-content { background:#fff; padding:22px; }

.payment-form-area { animation:fadeInUp 0.25s ease; }
@keyframes fadeInUp { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }

/* Card logos */
.card-logos { display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:14px; border-bottom:1px solid #f0ede8; }
.card-logo { height:20px; object-fit:contain; }

/* Pay fields */
.pay-field-group { display:flex; flex-direction:column; gap:5px; margin-bottom:12px; }
.pay-field-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.pay-field-group label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:#999; }
.pay-input {
  height:42px; padding:0 13px; border:1.5px solid #e8e5e0; border-radius:8px;
  font-family:'DM Sans',sans-serif; font-size:14px; background:#faf9f7; transition:all 0.2s;
}
.pay-input:focus { outline:none; border-color:#1a1a1a; background:#fff; box-shadow:0 0 0 3px rgba(26,26,26,0.07); }

/* Wallet buttons */
.wallet-options { display:flex; flex-direction:column; gap:9px; margin-bottom:14px; }
.wallet-btn {
  display:flex; align-items:center; gap:11px;
  padding:11px 15px; border:1.5px solid #e8e5e0; border-radius:10px;
  cursor:pointer; font-size:13px; font-weight:500;
  transition:all 0.2s; background:#faf9f7; color:#333;
}
.wallet-btn:hover { border-color:#1a1a1a; background:#fff; }
.wallet-btn.selected { border-color:#1a1a1a; background:#fff; font-weight:700; color:#1a1a1a; }
.w-icon { font-size:20px; }

/* Redirect note */
.redirect-note {
  background:#f8f7f5; border:1px solid #e8e5e0; border-radius:8px;
  padding:11px 13px; font-size:12px; color:#666; line-height:1.5;
}

/* PayPal */
.paypal-block { padding:10px 0; }
.paypal-logo { height:36px; margin-bottom:14px; }
.paypal-block p { font-size:13px; color:#666; line-height:1.6; }

/* Wire */
.wire-info { display:flex; flex-direction:column; gap:9px; }
.wire-row {
  display:flex; justify-content:space-between; font-size:13px;
  padding:9px 13px; background:#faf9f7; border-radius:8px; border:1px solid #e8e5e0;
}
.wire-row span { color:#888; }

/* COD */
.cod-block { text-align:center; padding:20px 10px; }
.cod-icon { font-size:44px; margin-bottom:10px; }
.cod-block h4 { font-size:16px; font-weight:700; margin-bottom:7px; }
.cod-block p { font-size:13px; color:#666; line-height:1.6; }

/* BUTTONS */
.btn-next {
  width:100%; height:52px; background:#1a1a1a; color:#fff;
  border:none; border-radius:12px;
  font-family:'DM Sans',sans-serif; font-size:15px; font-weight:600;
  cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;
  transition:all 0.2s; margin-top:8px;
}
.btn-next:hover:not(:disabled) { background:#333; transform:translateY(-1px); }
.btn-next:disabled { opacity:0.4; cursor:not-allowed; }

.btn-place-order {
  flex:1; height:52px; background:#1a1a1a; color:#fff;
  border:none; border-radius:12px;
  font-family:'DM Sans',sans-serif; font-size:15px; font-weight:700;
  cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;
  transition:all 0.2s;
}
.btn-place-order:hover:not(:disabled) { background:#333; transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.18); }
.btn-place-order:disabled { opacity:0.5; cursor:not-allowed; }

.btn-back-step {
  height:52px; padding:0 24px; background:#fff; color:#1a1a1a;
  border:1.5px solid #e8e5e0; border-radius:12px;
  font-family:'DM Sans',sans-serif; font-size:14px; font-weight:600;
  cursor:pointer; transition:all 0.2s;
}
.btn-back-step:hover { background:#f5f3ef; }
.btn-row { display:flex; gap:12px; align-items:center; }

.spinner-inline {
  width:18px; height:18px;
  border:2px solid rgba(255,255,255,0.3); border-top-color:#fff;
  border-radius:50%; animation:spin 0.7s linear infinite; display:inline-block;
}
@keyframes spin { to{transform:rotate(360deg)} }

/* TRANSITIONS */
.slide-fade-enter-active,.slide-fade-leave-active { transition:all 0.3s ease; }
.slide-fade-enter-from { opacity:0; transform:translateX(20px); }
.slide-fade-leave-to   { opacity:0; transform:translateX(-20px); }

/* ORDER SUMMARY */
.summary-side { position:sticky; top:30px; height:fit-content; }
.summary-card { background:#fff; border-radius:20px; padding:32px; box-shadow:0 2px 20px rgba(0,0,0,0.06); }
.summary-title { font-family:'Playfair Display',serif; font-size:22px; margin-bottom:4px; color:#1a1a1a; }
.item-count { font-size:13px; color:#999; margin-bottom:24px; }
.summary-items { display:flex; flex-direction:column; gap:20px; }
.summary-item { display:flex; align-items:flex-start; gap:16px; }
.item-img-wrap { position:relative; flex-shrink:0; }
.item-img { width:70px; height:70px; object-fit:contain; border-radius:10px; border:1px solid #e8e5e0; background:#faf9f7; padding:4px; }
.item-qty-badge { position:absolute; top:-6px; right:-6px; width:20px; height:20px; background:#1a1a1a; color:#fff; border-radius:50%; font-size:10px; font-weight:700; display:flex; align-items:center; justify-content:center; }
.item-details { flex:1; }
.item-name { font-size:13px; font-weight:600; color:#1a1a1a; margin-bottom:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px; }
.item-meta { font-size:11px; color:#aaa; margin-bottom:8px; }
.qty-controls { display:flex; align-items:center; border:1px solid #e8e5e0; border-radius:8px; overflow:hidden; width:fit-content; }
.qty-ctrl-btn { width:28px; height:28px; background:#fff; border:none; font-size:16px; cursor:pointer; transition:background 0.2s; color:#333; display:flex; align-items:center; justify-content:center; }
.qty-ctrl-btn:hover:not(:disabled) { background:#f5f3ef; }
.qty-ctrl-btn:disabled { opacity:0.3; cursor:not-allowed; }
.qty-num { width:30px; text-align:center; font-size:13px; font-weight:600; border-left:1px solid #e8e5e0; border-right:1px solid #e8e5e0; line-height:28px; }
.item-price-col { display:flex; flex-direction:column; align-items:flex-end; gap:8px; flex-shrink:0; }
.item-total { font-size:14px; font-weight:700; color:#1a1a1a; }
.remove-btn { background:none; border:none; font-size:11px; color:#ccc; cursor:pointer; transition:color 0.2s; }
.remove-btn:hover { color:#e74c3c; }
.summary-divider { height:1px; background:#f0ede8; margin:20px 0; }
.price-breakdown { display:flex; flex-direction:column; gap:10px; }
.price-row { display:flex; justify-content:space-between; font-size:14px; color:#555; }
.free-tag { color:#27ae60; font-weight:700; }
.free-note { font-size:12px; color:#27ae60; }
.total-row { display:flex; justify-content:space-between; font-size:18px; font-weight:700; color:#1a1a1a; }
.total-amount { font-family:'Playfair Display',serif; font-size:22px; color:#1a1a1a; }
.trust-badges { display:flex; gap:8px; margin-top:20px; flex-wrap:wrap; }
.badge { flex:1; text-align:center; font-size:10px; font-weight:600; color:#888; background:#f5f3ef; padding:8px 6px; border-radius:8px; white-space:nowrap; }
</style>
