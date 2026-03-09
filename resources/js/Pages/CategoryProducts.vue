<template>
  <div>
    <nav-component />
        <breadcrumb-component />

    <div class="container ">
      <!-- Heading -->
      <div class="text-center mb-5">
        <h1 class="fw-bold">{{ category?.name }}</h1>
      </div>

      <!-- Loader -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-dark"></div>
      </div>

      <div v-else>
        <!-- ===== NORMAL PRODUCTS with color variants ===== -->
        <div class="row g-4 mb-5" v-if="products.length > 0">
          <div
            v-for="product in products"
            :key="product.id"
            class="col-lg-2 col-md-4 col-sm-6 col-6"
          >
            <div class="card product-card h-100 shadow-sm">

              <!-- Product inner: color strip LEFT + image RIGHT -->
              <div class="product-inner">

                <!-- LEFT: Color variant small boxes (only if color_variants exist) -->
                <div
                  class="color-strip"
                  v-if="product.color_variants && product.color_variants.length > 1"
                >
                  <div
                    v-for="(v, vi) in product.color_variants.slice(0, 6)"
                    :key="vi"
                    class="color-box"
                    :class="{ active: (activeVariant[product.id] ?? 0) === vi }"
                    @click.stop="setVariant(product.id, vi)"
                    :title="v.color"
                  >
                    <img :src="v.image" :alt="v.color" />
                    <span class="color-dot" :style="{ background: v.hex }"></span>
                  </div>
                </div>

                <!-- RIGHT: Main product image (changes on color click) -->
                <div class="product-img-wrapper flex-grow-1">
                  <transition name="color-swap" mode="out-in">
                    <img
                      :key="getVariantImg(product)"
                      :src="getVariantImg(product)"
                      class="product-img"
                    />
                  </transition>
                </div>
              </div>

              <div class="card-body text-center">
                <h6 class="fw-bold mb-1">{{ product.name }}</h6>
                <p class="text-muted mb-2">$ {{ product.price }}</p>
                <router-link
                  :to="`/product/${product.id}`"
                  class="btn btn-dark btn-sm w-100"
                >
                  View &amp; Buy
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== CUSTOMIZER MODELS - GROUPED BY model_name ===== -->
        <div v-if="Object.keys(groupedModels).length > 0">
          <div
            v-for="(groupModels, modelName) in groupedModels"
            :key="modelName"
            class="mb-5"
          >
            <div class="model-group-heading">
              <span>{{ modelName }}</span>
            </div>

            <div class="row g-4">
              <div
                v-for="model in groupModels"
                :key="model.id"
                class="col-lg-2 col-md-4 col-sm-6 col-6"
              >
                <div class="card model-card">
                  <div class="card-image-wrapper">
                    <!-- Cart Icon → opens purchase modal -->
                    <button
                      class="model-cart-btn"
                      @click.stop="router.push(`/product/${model.id}?type=model`)
"
title="View Product"

                    >
                      <i class="bi bi-cart" style="transform: scaleX(-1); display:inline-block;"></i>
                    </button>

                    <img v-if="model.thumbnail" :src="model.thumbnail" class="model-thumb" />
                    <template v-else>
                      <img v-if="model.front_black" :src="model.front_black" class="img-layer black" />
                      <img v-if="model.front_white" :src="model.front_white" class="img-layer white" />
                      <img v-if="model.front_svg"   :src="model.front_svg"   class="img-layer svg"   />
                      <img
                        v-if="!model.front_black && !model.front_white && !model.front_svg"
                        src="https://via.placeholder.com/300x180?text=No+Image"
                        class="img-layer svg"
                      />
                    </template>
                  </div>

                  <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <h5 class="card-title mb-0">{{ model.title }}</h5>
                      <span class="card-price">${{ model.price || '0.00' }}</span>
                    </div>
                  </div>

                  <div class="card-footer model-footer">
                    <button
                      class="btn btn-custom btn-sm w-100"
                      @click="handleCustomizerClick(model.id)"
                    >
                      Customize
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="!loading" class="text-center py-5 text-muted">
          <p>No customizer models in this subcategory yet.</p>
        </div>
      </div>
    </div>

    <!-- ===== PURCHASE MODAL (model cart click) ===== -->
    <transition name="modal-pop">
      <div
        v-if="showPurchaseModal"
        class="pm-overlay"
        @click.self="showPurchaseModal = false"
      >
        <div class="pm-box">
          <!-- Close -->
          <button class="pm-close" @click="showPurchaseModal = false">
            <i class="bi bi-x-lg"></i>
          </button>

          <div class="pm-layout">
            <!-- Model Image -->
            <div class="pm-img-side">
              <img v-if="pmModel.thumbnail" :src="pmModel.thumbnail" class="pm-main-img" />
              <template v-else>
                <img v-if="pmModel.front_black" :src="pmModel.front_black" class="pm-layer black" />
                <img v-if="pmModel.front_white" :src="pmModel.front_white" class="pm-layer white" />
                <img v-if="pmModel.front_svg"   :src="pmModel.front_svg"   class="pm-layer svg"   />
              </template>
            </div>

            <!-- Info & Size -->
            <div class="pm-info-side">
              <h4 class="pm-title">{{ pmModel.title }}</h4>
              <p class="pm-price">${{ pmModel.price || '0.00' }}</p>
              <hr class="pm-hr" />

              <!-- Size Selection -->
              <div class="pm-group">
                <label class="pm-label">Select Size *</label>
                <div class="pm-sizes">
                  <button
                    v-for="sz in standardSizes"
                    :key="sz"
                    class="pm-sz"
                    :class="{ selected: pmSize === sz }"
                    @click="pmSize = sz; pmCustomConfirmed = false; pmCustom = ''"
                  >{{ sz }}</button>
                  <button
                    class="pm-sz pm-other"
                    :class="{ selected: pmSize === '__other__' }"
                    @click="pmSize = '__other__'"
                  >Other +</button>
                </div>

                <!-- Custom size input -->
                <div v-if="pmSize === '__other__'" class="pm-custom-row">
                  <input
                    v-model="pmCustom"
                    type="text"
                    class="pm-custom-input"
                    placeholder="e.g. 3XL, 42 chest..."
                    @keyup.enter="confirmPmCustom"
                  />
                  <button class="pm-custom-btn" @click="confirmPmCustom">✓</button>
                </div>
                <p v-if="pmSize === '__other__' && pmCustomConfirmed" class="pm-confirmed">
                  ✅ Size: <strong>{{ pmCustom }}</strong>
                </p>
              </div>

              <!-- Quantity -->
              <div class="pm-group">
                <label class="pm-label">Quantity</label>
                <div class="pm-qty">
                  <button class="pm-qty-btn" @click="pmQty > 1 && pmQty--">−</button>
                  <span class="pm-qty-val">{{ pmQty }}</span>
                  <button class="pm-qty-btn" @click="pmQty++">+</button>
                </div>
              </div>

              <p v-if="!pmEffectiveSize" class="pm-warn">⚠ Please select or confirm a size first</p>

              <!-- Action Buttons -->
              <div class="pm-actions">
                <button class="pm-cart-btn" @click="addModelToCart" :disabled="!pmEffectiveSize">
                  <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
                <button class="pm-buy-btn" @click="buyModelNow" :disabled="!pmEffectiveSize">
                  Buy Now
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- ===== LOGIN REQUIRED MODAL ===== -->
    <div
      v-if="showLoginModal"
      class="modal fade show"
      style="display: block"
      @click.self="showLoginModal = false"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
          <div class="text-center pt-4 pb-2">
            <div class="login-icon-wrap mx-auto">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </div>
          </div>
          <div class="modal-body text-center px-5 pb-2">
            <h5 class="fw-bold mb-2">Login Required</h5>
            <p class="text-muted mb-0">
To use the Customizer, you must first log in to your account.
            </p>
          </div>
          <div class="modal-footer justify-content-center border-0 px-5 pb-4 gap-3">
            <button
              class="btn btn-outline-secondary rounded-pill px-4"
              @click="showLoginModal = false"
            >
              Cancel
            </button>
            <router-link
              :to="{ path: '/user-login', query: { redirect: `/models/${pendingModelId}` } }"
              class="btn btn-dark rounded-pill px-4"
              @click="showLoginModal = false"
            >
              Login
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showLoginModal"
      class="modal-backdrop fade show"
      @click="showLoginModal = false"
    ></div>

    <!-- Toast -->
    <transition name="toast-slide">
      <div class="cat-toast" v-if="toastVisible">{{ toastText }}</div>
    </transition>

    <footer-component />
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '@/store/cart'

const route     = useRoute()
const router    = useRouter()
const cartStore = useCartStore()

const categoryId     = route.params.id
const category       = ref(null)
const products       = ref([])
const models         = ref([])
const loading        = ref(true)
const showLoginModal = ref(false)
const pendingModelId = ref(null)

// ── Color variant active index per product
const activeVariant = ref({})

const setVariant = (productId, vi) => {
  activeVariant.value = { ...activeVariant.value, [productId]: vi }
}

const getVariantImg = (product) => {
  const vi = activeVariant.value[product.id] ?? 0
  return product.color_variants?.[vi]?.image || product.image || ''
}

// ── Toast
const toastVisible = ref(false)
const toastText    = ref('')
let toastTimer
const showToast = (msg) => {
  toastText.value    = msg
  toastVisible.value = true
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toastVisible.value = false }, 2500)
}

// ── Purchase Modal
const showPurchaseModal = ref(false)
const pmModel           = ref({})
const pmSize            = ref('')
const pmCustom          = ref('')
const pmCustomConfirmed = ref(false)
const pmQty             = ref(1)

const standardSizes = ['YXS', 'YS', 'YM', 'YL', 'YXL', 'S', 'M', 'L', 'XL', '2XL']

const pmEffectiveSize = computed(() => {
  if (pmSize.value === '__other__') return pmCustomConfirmed.value ? pmCustom.value.trim() : ''
  return pmSize.value
})

const confirmPmCustom = () => {
  if (!pmCustom.value.trim()) { showToast('Please type a size!'); return }
  pmCustomConfirmed.value = true
  showToast(`✅ Size "${pmCustom.value.trim()}" confirmed!`)
}

const openPurchaseModal = (model) => {
  pmModel.value           = model
  pmSize.value            = ''
  pmCustom.value          = ''
  pmCustomConfirmed.value = false
  pmQty.value             = 1
  showPurchaseModal.value = true
}

const addModelToCart = () => {
  if (!pmEffectiveSize.value) { showToast('⚠️ Please select a size!'); return }
  cartStore.addToCart(
    {
      id:    pmModel.value.id,
      name:  pmModel.value.title,
      price: pmModel.value.price || 0,
      image: pmModel.value.thumbnail || pmModel.value.front_svg || '',
    },
    pmEffectiveSize.value,
    pmQty.value
  )
  showToast(`🛒 ${pmQty.value}× (${pmEffectiveSize.value}) added to cart!`)
  showPurchaseModal.value = false
}

const buyModelNow = () => {
  if (!pmEffectiveSize.value) { showToast('⚠️ Please select a size!'); return }
  addModelToCart()
  router.push('/checkout')
}

// ── Group models by model_name
const groupedModels = computed(() => {
  const groups = {}
  models.value.forEach(m => {
    const key = m.model_name || 'Other'
    if (!groups[key]) groups[key] = []
    groups[key].push(m)
  })
  return groups
})

// ── Customizer click
const handleCustomizerClick = (modelId) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    router.push(`/models/${modelId}`)
  } else {
    pendingModelId.value = modelId
    showLoginModal.value = true
  }
}

// ── Load models
const loadModels = async () => {
  try {
    const subRes = await axios.get(`/api/subcategories/${categoryId}/models`)
    let loadedModels = subRes.data.models || []
    if (loadedModels.length > 0) { models.value = loadedModels; return }
    const catRes = await axios.get(`/api/categories/${categoryId}/models`)
    models.value = catRes.data.models || []
  } catch (err) {
    console.error('Models error:', err)
  }
}

// ── Load products
const fetchProducts = async () => {
  try {
    const res = await axios.get(`/api/category/${route.params.id}/products`)
    products.value = res.data
    console.log("Loaded:", products.value)
  } catch (e) {
    console.error('Products error:', e)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProducts()
  loadModels()
})
</script>

<style scoped>
/* ===== PRODUCT CARDS ===== */
.product-card {
  border-radius: 14px;
  overflow: hidden;
  transition: .35s ease;
}
.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 22px rgba(0,0,0,.15);
}

/* Product inner: color strip + image side by side */
.product-inner {
  display: flex;
  height: 150px;
}

/* ── Left color strip ── */
.color-strip {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 6px 4px;
  background: #f5f5f5;
  border-right: 1px solid #ebebeb;
  width: 36px;
  flex-shrink: 0;
  overflow: hidden;
}
.color-box {
  width: 28px;
  height: 28px;
  border-radius: 5px;
  border: 2px solid transparent;
  overflow: hidden;
  cursor: pointer;
  position: relative;
  background: #fff;
  transition: border-color .15s;
  flex-shrink: 0;
}
.color-box.active { border-color: #000; }
.color-box:hover  { border-color: #888; }
.color-box img    { width: 100%; height: 100%; object-fit: contain; padding: 2px; }
.color-dot {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  border: 1px solid rgba(0,0,0,.2);
}

/* ── Image area ── */
.product-img-wrapper {
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex: 1;
}
.product-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

/* Color swap animation */
.color-swap-enter-active, .color-swap-leave-active { transition: opacity .18s ease; }
.color-swap-enter-from, .color-swap-leave-to { opacity: 0; }

/* ===== GROUP HEADING ===== */
.model-group-heading {
  text-align: center;
  position: relative;
  margin-bottom: 20px;
  margin-top: 10px;
}
.model-group-heading::before,
.model-group-heading::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 38%;
  height: 1px;
  background: #ccc;
}
.model-group-heading::before { left: 0; }
.model-group-heading::after  { right: 0; }
.model-group-heading span {
  font-size: 15px;
  font-weight: 700;
  background: #fff;
  padding: 0 14px;
  position: relative;
  z-index: 1;
  letter-spacing: .5px;
}

/* ===== MODEL CARDS ===== */
.model-cart-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 32px;
  height: 32px;
  background: rgba(255,255,255,.9);
  border: 1px solid #e0e0e0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  font-size: 14px;
  transition: .2s;
  color: #333;
}
.model-cart-btn:hover { background: #000; color: #fff; border-color: #000; }

.model-card {
  border-radius: 14px;
  background: #fff;
  overflow: hidden;
  height: 90%;
  transition: .35s ease;
}
.model-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 14px 28px rgba(0,0,0,.18);
}
.card-image-wrapper {
  height: 250px;
  background: #fff;
  position: relative;
  overflow: hidden;
}
.model-thumb {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 90%;
  object-fit: contain;
}
.img-layer {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 90%;
  object-fit: contain;
}
.black { z-index: 3; mix-blend-mode: screen; }
.white { z-index: 2; mix-blend-mode: multiply; }
.svg   { z-index: 1; }

.card-title  { font-size: 13px; font-weight: 600; }
.card-price  { font-size: 13px; font-weight: 700; }

.model-footer {
  padding: 10px;
  background: #fff;
  border-top: 1px solid #eee;
}

.btn-custom {
  background: #000;
  color: #fff;
  height: 38px;
  border-radius: 8px;
  font-weight: 600;
  border: none;
  cursor: pointer;
}
.btn-custom:hover { background: #222; color: #fff; }

/* ===== PURCHASE MODAL ===== */
.pm-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9100;
  padding: 20px;
}
.pm-box {
  background: #fff;
  border-radius: 16px;
  max-width: 640px;
  width: 100%;
  overflow: hidden;
  position: relative;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
  max-height: 90vh;
}
.pm-close {
  position: absolute;
  top: 12px;
  right: 12px;
  width: 32px;
  height: 32px;
  background: #f0f0f0;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 13px;
  z-index: 10;
  transition: .2s;
}
.pm-close:hover { background: #000; color: #fff; }

.pm-layout {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
}
@media (max-width: 520px) { .pm-layout { grid-template-columns: 1fr; } }

.pm-img-side {
  background: #f5f5f5;
  min-height: 280px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}
.pm-main-img { max-width: 82%; max-height: 260px; object-fit: contain; }
.pm-layer {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  max-height: 78%;
  object-fit: contain;
}

.pm-info-side {
  padding: 28px 22px;
  overflow-y: auto;
  max-height: 520px;
}
.pm-title { font-size: 18px; font-weight: 700; margin-bottom: 4px; line-height: 1.2; }
.pm-price { font-size: 24px; font-weight: 800; margin-bottom: 0; }
.pm-hr    { border: none; border-top: 1px solid #ebebeb; margin: 14px 0; }

.pm-group { margin-bottom: 18px; }
.pm-label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #555; margin-bottom: 8px; }

.pm-sizes { display: flex; flex-wrap: wrap; gap: 5px; }
.pm-sz {
  min-width: 42px;
  height: 36px;
  padding: 0 8px;
  border: 1.5px solid #e0e0e0;
  background: #fff;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: .15s;
  font-family: inherit;
}
.pm-sz:hover { border-color: #000; }
.pm-sz.selected { background: #000; color: #fff; border-color: #000; }
.pm-other { background: #f5f5f5; border-style: dashed; }
.pm-other.selected { background: #000; color: #fff; border-style: solid; }

.pm-custom-row {
  display: flex;
  gap: 6px;
  margin-top: 10px;
}
.pm-custom-input {
  flex: 1;
  height: 38px;
  padding: 0 12px;
  border: 1.5px solid #e0e0e0;
  border-radius: 8px;
  font-size: 13px;
  font-family: inherit;
  transition: .2s;
}
.pm-custom-input:focus { outline: none; border-color: #000; }
.pm-custom-btn {
  height: 38px;
  padding: 0 14px;
  background: #000;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
}
.pm-confirmed { font-size: 12px; color: #166534; margin-top: 6px; margin-bottom: 0; }
.pm-warn { font-size: 12px; color: #e53e3e; margin-bottom: 8px; }

.pm-qty {
  display: flex;
  align-items: center;
  width: fit-content;
  border: 1.5px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
}
.pm-qty-btn {
  width: 38px;
  height: 38px;
  border: none;
  background: #fff;
  font-size: 18px;
  cursor: pointer;
  transition: .2s;
}
.pm-qty-btn:hover { background: #f5f5f5; }
.pm-qty-val {
  width: 46px;
  text-align: center;
  font-size: 15px;
  font-weight: 700;
  border-left: 1.5px solid #e0e0e0;
  border-right: 1.5px solid #e0e0e0;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.pm-cart-btn, .pm-buy-btn {
  height: 46px;
  border: none;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: .2s;
  font-family: inherit;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
.pm-cart-btn { background: #000; color: #fff; }
.pm-cart-btn:hover:not(:disabled) { background: #333; }
.pm-buy-btn  { background: #fff; color: #000; border: 1.5px solid #000; }
.pm-buy-btn:hover:not(:disabled)  { background: #000; color: #fff; }
.pm-cart-btn:disabled, .pm-buy-btn:disabled { opacity: .35; cursor: not-allowed; }

/* Modal transition */
.modal-pop-enter-active, .modal-pop-leave-active { transition: all .28s ease; }
.modal-pop-enter-from, .modal-pop-leave-to { opacity: 0; }
.modal-pop-enter-from .pm-box { transform: scale(.96) translateY(12px); }

/* ===== LOGIN MODAL ===== */
.login-icon-wrap {
  width: 72px;
  height: 72px;
  background: #f0f0f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Toast */
.cat-toast {
  position: fixed;
  bottom: 28px;
  right: 28px;
  background: #111;
  color: #fff;
  padding: 12px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  z-index: 9999;
  pointer-events: none;
}
.toast-slide-enter-active, .toast-slide-leave-active { transition: all .3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateY(14px); }
</style>
