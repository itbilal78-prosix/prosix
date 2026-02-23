<template>
  <div class="page-wrapper">
    <nav-component />

    <div class="product-container">
      <div class="product-layout">
        <!-- Left: Product Image -->
        <div class="product-visual">
          <div class="image-gallery">
            <div
              class="main-image-wrapper"
            
              ref="imageContainer"
            >
              <img
                v-if="product.image"
                :src="product.image"
                class="main-product-image"
                alt="Product"
              />
              
              <!-- Wishlist Heart -->
              <button
                class="wishlist-btn"
                @click="toggleLike"
                :class="{ active: cartStore.isLiked(product.id) }"
              >
                <i :class="cartStore.isLiked(product.id) ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
              </button>

              <!-- Share Button -->
              <button class="share-btn">
                <i class="bi bi-share"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Right: Product Info -->
        <div class="product-info">
          <div class="product-header">
            <h1 class="product-title">{{ product.name }}</h1>
            <div class="product-meta">
              <span class="product-type">{{ product.type }}</span>
              <span class="stock-badge">In Stock</span>
            </div>
          </div>

          <div class="price-section">
            <span class="product-price">${{ formatPrice(product.price) }}</span>
          </div>

          <p class="product-description">
            {{ product.description }}
          </p>

          <!-- Size Selection -->
          <div class="option-group">
            <label class="option-label">Select Size</label>
            <div class="size-selector">
              <button
                v-for="size in sizes"
                :key="size"
                :class="['size-btn', { selected: selectedSize === size }]"
                @click="selectedSize = size"
              >
                {{ size }}
              </button>
            </div>
          </div>

          <!-- Quantity -->
          <div class="option-group">
            <label class="option-label">Quantity</label>
            <div class="quantity-selector">
              <button
                class="qty-btn"
                @click="decrementQty"
                :disabled="quantity <= 1"
              >
                −
              </button>
              <input
                type="number"
                class="qty-input"
                v-model.number="quantity"
                min="1"
              />
              <button class="qty-btn" @click="incrementQty">+</button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button
              class="btn-add-cart"
              @click="addToCart"
              :disabled="!selectedSize"
            >
              Add to Cart
            </button>
            <button
              class="btn-checkout"
              @click="showCheckout = true"
              :disabled="cartStore.items.length === 0"
            >
              Buy Now
            </button>
          </div>

          <!-- Additional Info -->
          <div class="product-features">
            <div class="feature-item">
              <i class="bi bi-truck"></i>
              <div>
                <strong>Free Delivery</strong>
                <p>On orders above $200</p>
              </div>
            </div>
            <div class="feature-item">
              <i class="bi bi-arrow-clockwise"></i>
              <div>
                <strong>60 Day Return</strong>
                <p>Free returns via dropoff service</p>
              </div>
            </div>
            <div class="feature-item">
              <i class="bi bi-shield-check"></i>
              <div>
                <strong>Secure Payment</strong>
                <p>Multiple payment options</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products Section -->
      <div class="related-section">
        <div class="section-header">
          <h2>Recommended Products</h2>
          <button class="view-all-btn">
            View All
            <i class="bi bi-arrow-right"></i>
          </button>
        </div>

        <div v-if="relatedLoading" class="loading-state">
          <div class="spinner"></div>
        </div>

        <div v-else-if="relatedProducts.length === 0" class="empty-state">
          No related products found.
        </div>

      <div v-else class="products-grid">
  <router-link
    v-for="rel in relatedProducts"
    :key="rel.id"
    :to="`/product/${rel.id}`"  
    class="product-card-link d-block text-decoration-none"
  >
    <div class="product-card">
      <div class="card-image">
        <img :src="rel.image" alt="Product" />
        <div class="card-overlay">
          <span class="quick-view-btn">Quick View / Buy</span>
        </div>
      </div>
      <div class="card-content">
        <h3 class="card-title">{{ rel.name }}</h3>
        <p class="card-price">${{ formatPrice(rel.price) }}</p>
      </div>
    </div>
  </router-link>
</div>
      </div>
    </div>

    <!-- Checkout Modal -->
    <div
      v-if="showCheckout"
      class="checkout-modal"
      @click.self="showCheckout = false; checkoutStore.resetForm()"
    >
      <div class="modal-content">
        <div class="modal-header">
          <h3>Checkout</h3>
          <button
            class="close-btn"
            @click="showCheckout = false; checkoutStore.resetForm()"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="modal-body">
          <!-- Progress Indicator -->
          <div class="progress-steps">
            <div
              v-for="step in 3"
              :key="step"
              :class="['step', { active: checkoutStore.currentStep >= step }]"
            >
              <div class="step-number">{{ step }}</div>
              <span class="step-label">
                {{ step === 1 ? 'Info' : step === 2 ? 'Address' : 'Payment' }}
              </span>
            </div>
          </div>

          <!-- Step 1: Personal Info -->
          <div v-if="checkoutStore.currentStep === 1" class="form-step">
            <h4 class="step-title">Personal Information</h4>
            <div class="form-grid">
              <div class="form-group">
                <label>Full Name *</label>
                <input v-model="checkoutStore.form.name" type="text" placeholder="Muhammad Ahmed" required />
              </div>
              <div class="form-group">
                <label>Phone Number *</label>
                <input v-model="checkoutStore.form.phone" type="tel" placeholder="+92 XXX XXX XXXX" required />
              </div>
              <div class="form-group">
                <label>Age (optional)</label>
                <input v-model.number="checkoutStore.form.age" type="number" min="18" placeholder="25" />
              </div>
              <div class="form-group">
                <label>Delivery Time</label>
                <select v-model="checkoutStore.form.deliveryDays">
                  <option value="1-3">1–3 days (Express)</option>
                  <option value="3-5" selected>3–5 days (Standard)</option>
                  <option value="7+">7+ days (Economy)</option>
                </select>
              </div>
            </div>

            <div class="form-actions">
              <button class="btn-secondary" @click="showCheckout = false">Cancel</button>
              <button
                class="btn-primary"
                @click="checkoutStore.goToNextStep"
                :disabled="!checkoutStore.form.name || !checkoutStore.form.phone"
              >
                Next <i class="bi bi-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- Step 2: Address -->
          <div v-if="checkoutStore.currentStep === 2" class="form-step">
            <h4 class="step-title">Shipping Address</h4>
            <div class="form-grid">
              <div class="form-group full-width">
                <label>Full Address *</label>
                <textarea
                  v-model="checkoutStore.form.address"
                  rows="2"
                  placeholder="House #12, Street 5, DHA Phase 1, Lahore"
                  required
                ></textarea>
              </div>
              <div class="form-group">
                <label>City *</label>
                <input v-model="checkoutStore.form.city" type="text" placeholder="Lahore" required />
              </div>
              <div class="form-group">
                <label>Province</label>
                <select v-model="checkoutStore.form.province">
                  <option>Punjab</option>
                  <option>Sindh</option>
                  <option>Khyber Pakhtunkhwa</option>
                  <option>Balochistan</option>
                  <option>Islamabad Capital Territory</option>
                </select>
              </div>
              <div class="form-group">
                <label>Postal Code</label>
                <input v-model="checkoutStore.form.postalCode" type="text" placeholder="54000" />
              </div>
            </div>

            <div class="form-actions">
              <button class="btn-secondary" @click="checkoutStore.goToPrevStep">
                <i class="bi bi-arrow-left"></i> Back
              </button>
              <button
                class="btn-primary"
                @click="checkoutStore.goToNextStep"
                :disabled="!checkoutStore.form.address || !checkoutStore.form.city"
              >
                Next <i class="bi bi-arrow-right"></i>
              </button>
            </div>
          </div>

          <!-- Step 3: Payment -->
          <div v-if="checkoutStore.currentStep === 3" class="form-step">
            <h4 class="step-title">Payment Method</h4>

            <div class="payment-options">
              <label class="payment-option">
                <input type="radio" v-model="checkoutStore.form.paymentMethod" value="cod" checked />
                <div class="option-content">
                  <i class="bi bi-cash-coin"></i>
                  <div>
                    <strong>Cash on Delivery</strong>
                    <p>Pay when you receive</p>
                  </div>
                </div>
              </label>

              <label class="payment-option">
                <input type="radio" v-model="checkoutStore.form.paymentMethod" value="stripe" />
                <div class="option-content">
                  <i class="bi bi-credit-card"></i>
                  <div>
                    <strong>Credit / Debit Card</strong>
                    <p>Visa, Mastercard via Stripe</p>
                  </div>
                </div>
              </label>
            </div>

            <!-- Stripe Card Element -->
            <div v-if="checkoutStore.form.paymentMethod === 'stripe'" class="stripe-section mt-6 p-4 bg-gray-50 rounded-lg border border-gray-300 min-h-[140px]">
              <label class="block text-sm font-medium text-gray-700 mb-3">Enter Card Details</label>
              <div ref="cardElementRef" class="p-4 border border-gray-200 rounded bg-white min-h-[80px]"></div>
              <div v-if="cardError" class="mt-3 text-sm text-red-600">{{ cardError }}</div>
             
            </div>

            <div class="order-summary mt-6">
              <div class="summary-row">
                <span>Total Amount:</span>
                <strong>${{ cartStore.totalPrice.toFixed(2) }}</strong>
              </div>
              <div class="summary-row">
                <span>Delivery:</span>
                <span>{{ checkoutStore.form.deliveryDays }}</span>
              </div>
            </div>

            <div class="form-actions mt-6">
              <button class="btn-secondary" @click="checkoutStore.goToPrevStep">
                <i class="bi bi-arrow-left"></i> Back
              </button>
              <button class="btn-primary" @click="confirmOrder" :disabled="loading">
                <i class="bi bi-check-circle"></i>
                {{ loading ? 'Processing...' : 'Place Order' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <footer-component />
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { loadStripe } from '@stripe/stripe-js'
import { useCartStore } from '@/store/cart'
import { useCheckoutStore } from '@/store/checkout'
const route = useRoute()
const cartStore = useCartStore()
const checkoutStore = useCheckoutStore()

const product = ref({
  id: null,
  name: '',
  type: '',
  price: '',
  description: '',
  image: ''
})

const showCheckout = ref(false)
const sizes = ref(['S', 'M', 'L', 'XL', 'XXL'])
const selectedSize = ref('')
const quantity = ref(1)
const imageContainer = ref(null)
const relatedProducts = ref([])
const relatedLoading = ref(false)

// Stripe
const stripe = ref(null)
const elements = ref(null)
const card = ref(null)
const cardElementRef = ref(null)
const cardError = ref('')
const loading = ref(false)

const STRIPE_PUBLISHABLE_KEY = 'pk_test_51SvXN3HItCFNDxYMwZ1SdJWuuASPyJKQPVlvHAhAU4iICKJnrdBR4gZpdZWDdRYKWs9NVN2e400v8ng7M0Ubi15w00w0tMVAyz'

// Format price
const formatPrice = (price) => {
  if (typeof price === 'string') {
    return parseFloat(price.replace(/[^0-9.]/g, '')) || 0
  }
  return Number(price) || 0
}

onMounted(async () => {
  try {
const res = await axios.get(`/api/products/${route.params.id}`)
    product.value = res.data
    
    await fetchRelatedProducts()

    stripe.value = await loadStripe(STRIPE_PUBLISHABLE_KEY)
  } catch (err) {
    console.error('Failed to initialize:', err)
  }
})

const fetchRelatedProducts = async () => {
  relatedLoading.value = true

  try {
    const res = await axios.get('/api/products')
    
    // 🔥 Yeh line sabse important – response ko sahi tarah handle karo
    let allProducts = []

    if (Array.isArray(res.data)) {
      allProducts = res.data
    } else if (res.data && Array.isArray(res.data.products)) {
      allProducts = res.data.products
    } else if (res.data && Array.isArray(res.data.data)) {   // Laravel pagination case
      allProducts = res.data.data
    } else {
      console.warn('Unexpected API response format:', res.data)
    }

    console.log('Parsed allProducts length:', allProducts.length)
    console.log('allProducts type:', Array.isArray(allProducts) ? 'array' : typeof allProducts)

    relatedProducts.value = allProducts
      .filter(p => p?.id && p.id !== product.value?.id)   // extra safety
      .slice(0, 5)

    console.log('Final related count:', relatedProducts.value.length)
  } catch (err) {
    console.error('Related products fetch failed:', err.message)
    if (err.response) {
      console.log('Response status:', err.response.status)
      console.log('Response data:', err.response.data)
    }
    relatedProducts.value = []
  } finally {
    relatedLoading.value = false
  }
}

const incrementQty = () => quantity.value++
const decrementQty = () => {
  if (quantity.value > 1) quantity.value--
}

const toggleLike = () => cartStore.toggleLike(product.value.id)

const addToCart = () => {
  if (!selectedSize.value) {
    alert('Please select a size first!')
    return
  }
  cartStore.addToCart(product.value, selectedSize.value, quantity.value)
  flyImageToCart()
  quantity.value = 1
}

const flyImageToCart = () => {
  if (!imageContainer.value) return

  const productImg = imageContainer.value.querySelector('.main-product-image')
  if (!productImg) return

  //  Navbar cart icon dhundo (class name adjust karo apne navbar ke hisaab se)
  const cartIcon = document.querySelector('.bi-cart') || 
                   document.querySelector('.cart-icon') ||
                   document.querySelector('[href*="cart"]') ||
                   document.querySelector('a[href="/cart"]')

  // Clone image banao
  const clone = productImg.cloneNode(true)
  const rect = productImg.getBoundingClientRect()

  clone.style.position = 'fixed'
  clone.style.top = `${rect.top}px`
  clone.style.left = `${rect.left}px`
  clone.style.width = `${rect.width}px`
  clone.style.height = `${rect.height}px`
  clone.style.zIndex = '9999'
  clone.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)'
  clone.style.pointerEvents = 'none'
  clone.style.objectFit = 'contain'
  clone.style.borderRadius = '8px'

  document.body.appendChild(clone)

  setTimeout(() => {
    if (cartIcon) {
      // Cart icon tak fly karo
      const cartRect = cartIcon.getBoundingClientRect()
      clone.style.top = `${cartRect.top}px`
      clone.style.left = `${cartRect.left}px`
      clone.style.width = '30px'
      clone.style.height = '30px'
      clone.style.opacity = '0'
      clone.style.transform = 'scale(0.2) rotate(360deg)'
      
      // Cart icon bounce
      cartIcon.style.animation = 'cartBounce 0.6s ease'
      setTimeout(() => cartIcon.style.animation = '', 600)
    } else {
      // Fallback: top-right corner
      clone.style.top = '20px'
      clone.style.left = 'calc(100vw - 70px)'
      clone.style.width = '40px'
      clone.style.height = '40px'
      clone.style.opacity = '0'
      clone.style.transform = 'scale(0.2) rotate(360deg)'
    }
  }, 50)

  setTimeout(() => clone.remove(), 850)
}





onMounted(async () => {
  await loadProduct()
  await fetchRelatedProducts()
  stripe.value = await loadStripe(STRIPE_PUBLISHABLE_KEY)
})

// 🔥 Naya function banao product load karne ke liye
const loadProduct = async () => {
  try {
    const res = await axios.get(`/api/products/${route.params.id}`)
    product.value = res.data
  } catch (err) {
    console.error('Failed to load product:', err)
  }
}

// 🔥 Route change hone par product reload karo
watch(() => route.params.id, async (newId) => {
  if (newId) {
    selectedSize.value = ''  // Reset size
    quantity.value = 1        // Reset quantity
    
    await loadProduct()
    await fetchRelatedProducts()
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})
// Stripe mounting with better timing
watch([() => checkoutStore.currentStep, () => checkoutStore.form.paymentMethod], async ([step, method]) => {
  if (step !== 3 || method !== 'stripe' || !stripe.value || card.value) return

  if (!elements.value) {
    elements.value = stripe.value.elements()
  }

  card.value = elements.value.create('card', {
    style: {
      base: {
        fontSize: '16px',
        color: '#32325d',
        '::placeholder': { color: '#aab7c4' },
      },
      invalid: { color: '#fa755a' },
    }
  })

  await nextTick()

  if (cardElementRef.value) {
    card.value.mount(cardElementRef.value)
    card.value.on('change', event => {
      cardError.value = event.error?.message || ''
    })
  }
}, { immediate: true })

const confirmOrder = async () => {
  loading.value = true
  cardError.value = ''

  try {
    const cartItems = cartStore.items.map(item => ({
      id: item.id,
      name: item.name,
      price: formatPrice(item.price),
      quantity: item.quantity || 1,
      size: item.size || 'M'
    }))

    const payload = {
      cart: cartItems,
      checkout: {
        name: checkoutStore.form.name,
        phone: checkoutStore.form.phone,
        address: checkoutStore.form.address,
        city: checkoutStore.form.city,
        province: checkoutStore.form.province || 'Punjab',
        postalCode: checkoutStore.form.postalCode || '',
        deliveryDays: checkoutStore.form.deliveryDays || '3-5',
        paymentMethod: checkoutStore.form.paymentMethod || 'cod',
        age: checkoutStore.form.age || null
      }
    }

    if (checkoutStore.form.paymentMethod === 'stripe') {
      // Stripe payment
      const intentRes = await axios.post('/api/create-payment-intent', {
        amount: cartStore.totalPrice
      })

      const clientSecret = intentRes.data.clientSecret

      const { error, paymentIntent } = await stripe.value.confirmCardPayment(clientSecret, {
        payment_method: {
          card: card.value,
          billing_details: { name: checkoutStore.form.name }
        }
      })

      if (error) throw new Error(error.message)
      if (paymentIntent.status !== 'succeeded') throw new Error('Payment not completed')

      payload.checkout.paymentConfirmed = true
    }

    // Order place karo – token bhej rahe hain agar login hai
    const response = await axios.post('/api/orders', payload, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token') || ''}`,
        'Accept': 'application/json'
      }
    })

    alert(`✅ Order placed successfully!\nOrder ID: ${response.data.order_id || 'N/A'}`)

    cartStore.clearCart()
    checkoutStore.resetForm()
    showCheckout.value = false
  } catch (e) {
    console.error('Order error:', e)
    const msg = e.message || e.response?.data?.message || 'Failed to place order. Please try again.'
    alert('❌ ' + msg)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.page-wrapper {
  font-family: 'Montserrat', sans-serif;
  background: #ffffff;
  color: #000000;
  min-height: 100vh;
}

.product-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 24px;
}

/* Product Layout */
.product-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
  margin-bottom: 100px;
  margin-top: 100px;
}

@media (max-width: 968px) {
  .product-layout {
    grid-template-columns: 1fr;
    gap: 40px;
  }
}

/* Product Visual */
.product-visual {
  position: sticky;
  top: 100px;
  width: 90%;
  height: fit-content;
}

.main-image-wrapper {
  position: relative;
  background: #ffffff;
  border: 1px solid #000000;
  border-radius: 8px;
  overflow: hidden;
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-product-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.wishlist-btn,
.share-btn {
  position: absolute;
  top: 20px;
  width: 44px;
  height: 44px;
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 18px;
}

.wishlist-btn {
  right: 20px;
}

.share-btn {
  right: 74px;
}

.wishlist-btn:hover,
.share-btn:hover {
  background: #000;
  color: #fff;
  border-color: #000;
}

.wishlist-btn.active {
  background: #000;
  color: #fff;
  border-color: #000;
}

/* Product Info */
.product-info {
  padding-top: 20px;
}



.product-title {
  font-size: 36px;
  font-weight: 600;
  letter-spacing: -0.5px;
  margin-bottom: 12px;
  line-height: 1.2;
}

.product-meta {
  display: flex;
  align-items: center;
  gap: 12px;
}

.product-type {
  font-size: 14px;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stock-badge {
  font-size: 12px;
  padding: 4px 12px;
  background: #000;
  color: #fff;
  border-radius: 4px;
  font-weight: 500;
}

.price-section {
  margin-bottom: 20px;
  border-bottom: 1px solid #e0e0e0;
}

.product-price {
  font-size: 32px;
  font-weight: 700;
}

.product-description {
  font-size: 15px;
  line-height: 1.7;
  color: #555;
  margin-bottom: 15px;
}

/* Option Groups */
.option-group {
  margin-bottom: 15px;
}

.option-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 5px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.size-selector {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.size-btn {
  width: 45px;
  height: 45px;
  border: 1px solid #e0e0e0;
  background: #fff;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.size-btn:hover {
  border-color: #000;
}

.size-btn.selected {
  background: #000;
  color: #fff;
  border-color: #000;
}

.quantity-selector {
  display: flex;
  align-items: center;
  gap: 0;
  width: fit-content;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  overflow: hidden;
}

.qty-btn {
  width: 48px;
  height: 48px;
  border: none;
  background: #fff;
  font-size: 20px;
  cursor: pointer;
  transition: background 0.2s;
}

.qty-btn:hover:not(:disabled) {
  background: #f8f8f8;
}

.qty-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.qty-input {
  width: 60px;
  height: 48px;
  border: none;
  border-left: 1px solid #e0e0e0;
  border-right: 1px solid #e0e0e0;
  text-align: center;
  font-size: 15px;
  font-weight: 500;
}

.qty-input:focus {
  outline: none;
}

/* Action Buttons */
.action-buttons {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 32px;
}

.btn-add-cart,
.btn-checkout {
  height: 50px;
  border: none;
  border-radius: 4px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-add-cart {
  background: #000;
  color: #fff;
}

.btn-add-cart:hover:not(:disabled) {
  background: #333;
}

.btn-checkout {
  background: #fff;
  color: #000;
  border: 1px solid #000;
}

.btn-checkout:hover:not(:disabled) {
  background: #000;
  color: #fff;
}

.btn-add-cart:disabled,
.btn-checkout:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* Product Features */
.product-features {
  display: grid;
  gap: 20px;
  padding-top: 32px;
  border-top: 1px solid #e0e0e0;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.feature-item i {
  font-size: 24px;
  margin-top: 2px;
}

.feature-item strong {
  display: block;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 4px;
}

.feature-item p {
  font-size: 13px;
  color: #666;
  margin: 0;
}

/* Related Section */
.related-section {
  padding-top: 80px;
  border-top: 1px solid #e8e8e8;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}

.section-header h2 {
  font-size: 24px;
  font-weight: 600;
  letter-spacing: -0.3px;
}

.view-all-btn {
  background: none;
  border: none;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: gap 0.2s;
  color: #333;
}

.view-all-btn:hover {
  gap: 10px;
  color: #000;
}

.loading-state,
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f0f0f0;
  border-top-color: #000;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
}

@media (max-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
}

@media (max-width: 480px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

.product-card {
  position: relative;
  cursor: pointer;
  border-radius: 6px;
  overflow: hidden;
  transition: all 0.3s ease;
  background: #fff;
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.card-image {
  position: relative;
  aspect-ratio: 3/4;
  background: #fafafa;
  overflow: hidden;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 12px;
  transition: transform 0.4s ease;
}

.product-card:hover .card-image img {
  transform: scale(1.08);
}

.card-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.65);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .card-overlay {
  opacity: 1;
}

.quick-view-btn {
  background: #fff;
  color: #000;
  padding: 10px 20px;
  border-radius: 3px;
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  transition: all 0.2s;
}

.quick-view-btn:hover {
  background: #000;
  color: #fff;
}

.card-content {
  padding: 12px;
  background: #fff;
}

.card-title {
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #333;
}

.card-price {
  font-size: 14px;
  font-weight: 700;
  margin: 0;
  color: #000;
}

/* Checkout Modal */
.checkout-modal {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
}

.modal-content {
  background: #fff;
  border-radius: 8px;
  width: 100%;
  max-width: 700px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 32px;
  border-bottom: 1px solid #e0e0e0;
}

.modal-header h3 {
  font-size: 24px;
  font-weight: 600;
}

.close-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: none;
  font-size: 18px;
  cursor: pointer;
  border-radius: 50%;
  transition: background 0.2s;
}

.close-btn:hover {
  background: #f0f0f0;
}

.modal-body {
  padding: 32px;
  overflow-y: auto;
}

/* Progress Steps */
.progress-steps {
  display: flex;
  justify-content: space-between;
  margin-bottom: 40px;
  position: relative;
}

.progress-steps::before {
  content: '';
  position: absolute;
  top: 20px;
  left: 40px;
  right: 40px;
  height: 2px;
  background: #e0e0e0;
  z-index: 0;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  position: relative;
  z-index: 1;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  transition: all 0.3s;
}

.step.active .step-number {
  background: #000;
  color: #fff;
  border-color: #000;
}

.step-label {
  font-size: 12px;
  color: #999;
  font-weight: 500;
}

.step.active .step-label {
  color: #000;
  font-weight: 600;
}

/* Form Steps */
.form-step {
  animation: fadeIn 0.3s;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.step-title {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 24px;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 32px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.form-group input,
.form-group select,
.form-group textarea {
  height: 48px;
  padding: 0 16px;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  font-size: 14px;
  font-family: inherit;
  transition: border-color 0.2s;
}

.form-group textarea {
  height: auto;
  padding: 12px 16px;
  resize: vertical;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #000;
}

/* Payment Options */
.payment-options {
  display: grid;
  gap: 12px;
  margin-bottom: 24px;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.payment-option:hover:not(.disabled) {
  border-color: #000;
}

.payment-option.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.payment-option input[type="radio"] {
  width: 20px;
  height: 20px;
  cursor: pointer;
}

.option-content {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.option-content i {
  font-size: 24px;
}

.option-content strong {
  display: block;
  font-size: 14px;
  margin-bottom: 2px;
}

.option-content p {
  font-size: 12px;
  color: #666;
  margin: 0;
}

/* Order Summary */
.order-summary {
  padding: 20px;
  background: #f8f8f8;
  border-radius: 4px;
  margin-bottom: 24px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 14px;
}

.summary-row:last-child {
  margin-bottom: 0;
}

/* Form Actions */
.form-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.btn-primary,
.btn-secondary {
  height: 52px;
  border: none;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-primary {
  background: #000;
  color: #fff;
}

.btn-primary:hover:not(:disabled) {
  background: #333;
}

.btn-secondary {
  background: #fff;
  color: #000;
  border: 1px solid #e0e0e0;
}

.btn-secondary:hover {
  background: #f8f8f8;
}

.btn-primary:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.cart-bounce {
  animation: cartBounce 0.6s ease;
}

@keyframes cartBounce {
  0%, 100% { transform: scale(1); }
  35% { transform: scale(1.3); }
  65% { transform: scale(0.9); }
}
</style>