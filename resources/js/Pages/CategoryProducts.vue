<template>
  <div>
    <nav-component />
    <breadcrumb-component />

    <div class="container-fluid px-2 px-sm-3 px-md-4">
      <!-- Heading -->
      <div class="text-center mb-3 mb-md-4">
        <h1 class="fw-bold page-title">{{ category?.name }}</h1>
      </div>

      <!-- Loader -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-dark"></div>
      </div>

      <div v-else>

        <!-- ===== PRODUCTS SECTION (full width, no sidebar) ===== -->
        <div class="row g-2 g-sm-3 g-md-4 mb-4 mb-md-5" v-if="products.length > 0">
          <div
            v-for="product in products"
            :key="product.id"
            class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-products"
          >
            <div class="card product-card h-100 shadow-sm">
              <div class="product-inner">
                <div class="color-strip" v-if="product.color_variants && product.color_variants.length > 1">
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
                <div class="product-img-wrapper flex-grow-1">
                  <transition name="color-swap" mode="out-in">
                    <img :key="getVariantImg(product)" :src="getVariantImg(product)" class="product-img" />
                  </transition>
                </div>
              </div>
              <div class="card-body text-center px-2 py-2">
                <h6 class="fw-bold mb-1 product-name">{{ product.name }}</h6>
                <p class="text-muted mb-2 product-price">$ {{ product.price }}</p>
                <router-link :to="`/product/${product.id}`" class="btn btn-dark btn-sm w-100">View &amp; Buy</router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== MODELS SECTION + SIDEBAR (side by side) ===== -->
<div
class="main-layout"
:class="{ 'sidebar-open': sidebarOpen }"
>

        <!-- Mobile Filter Toggle -->
        <button class="mobile-filter-btn d-xl-none" @click="mobileFilterOpen = !mobileFilterOpen">
          <i class="bi bi-sliders"></i>
          <span>Filters</span>
          <i class="bi" :class="mobileFilterOpen ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
        </button>

        <!-- Mobile sidebar drawer -->
        <div class="filter-sidebar d-xl-none" :class="{ 'mobile-open': mobileFilterOpen }">
          <div class="sidebar-inner">
            <div class="sidebar-title">CUSTOMIZE YOUR LOOK</div>
            <div class="filter-section">
              <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input v-model="customName" type="text" class="search-input" placeholder="Keyword Search" />
              </div>
            </div>
            <div class="filter-section">
              <div class="filter-label">COLORS</div>
              <div class="color-swatches">
                <div v-for="c in selectedColors" :key="'sel-' + c.id" class="swatch selected" :style="{ background: c.code }" :title="c.name" @click="removeColor(c)"></div>
                <button class="swatch-add" @click="showColorPopup = true" title="Add Color"><i class="bi bi-plus"></i></button>
                <button class="swatch-filter" title="Filter"><i class="bi bi-sliders"></i></button>
              </div>
            </div>
            <div class="filter-section">
              <div class="d-flex gap-2">
                <button class="btn-apply" @click="applyFilters">APPLY</button>
                <button class="btn-clear" @click="clearFilters">CLEAR ALL</button>
              </div>
            </div>
            <div class="sidebar-divider"></div>
            <div class="filter-label mb-2">SEARCH &amp; FILTER</div>
            <div class="filter-section">
              <div class="filter-accordion-label" @click="modelNameOpen = !modelNameOpen">
                <span>GENERAL</span>
                <span class="acc-arrow" :class="{ open: modelNameOpen }"><i class="bi bi-chevron-down"></i></span>
              </div>
              <transition name="acc-slide">
                <div v-show="modelNameOpen" class="acc-body">
                  <div class="filter-option" :class="{ active: selectedModelName === 'all' }" @click="selectedModelName = 'all'">All</div>
                  <div v-for="name in uniqueModelNames" :key="name" class="filter-option" :class="{ active: selectedModelName === name }" @click="selectedModelName = name">{{ name }}</div>
                </div>
              </transition>
            </div>
          </div>
        </div>

        <!-- LEFT: MODELS CONTENT -->
        <div class="content-area">

          <!-- SELECT DESIGN heading -->
           <button class="desktop-filter-btn" @click="toggleSidebar">
  <i class="bi bi-sliders"></i>
  Filters
</button>

          <div class="select-design-heading">SELECT DESIGN</div>

          <!-- ===== ALL view: grouped by model_name ===== -->
          <template v-if="selectedModelName === 'all'">
            <div v-for="(groupModels, modelName) in groupedFilteredModels" :key="modelName" class="mb-4 mb-md-5">
              <div class="model-group-heading">
                <span>{{ modelName }}</span>
              </div>
              <div class="row g-3 g-sm-4">
                <div v-for="model in groupModels" :key="model.id" class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-models">
                  <!-- MODEL CARD -->
                  <div class="model-card">
                    <div class="card-image-wrapper" @click.stop="router.push(`/product/${model.id}?type=model`)">
                      <img v-if="model.thumbnail" :src="model.thumbnail" class="model-thumb" draggable="false" />
                      <template v-else>
                        <img v-if="model.front_black" :src="model.front_black" class="img-layer black" />
                        <img v-if="model.front_white" :src="model.front_white" class="img-layer white" />
                        <img v-if="model.front_svg"   :src="model.front_svg"   class="img-layer svg" draggable="false" />
                        <div v-if="appliedName" class="svg-name-overlay" :style="{ color: appliedColor?.code || '#000' }">{{ appliedName }}</div>
                        <img v-if="!model.front_black && !model.front_white && !model.front_svg" src="https://via.placeholder.com/300x180?text=No+Image" class="img-layer svg" />
                      </template>
                    </div>
                    <div class="model-card-info">
                      <span class="model-card-title">{{ model.title }}</span>
                      <span class="model-card-price">${{ model.price || '0.00' }}</span>
                    </div>
                    <button class="btn-customize-always" @click="handleCustomizerClick(model.id)">Customize</button>
                  </div>
                  <!-- END MODEL CARD -->
                </div>
              </div>
            </div>
            <div v-if="Object.keys(groupedFilteredModels).length === 0" class="text-center py-5 text-muted">
              <p>No models found.</p>
            </div>
          </template>

          <!-- ===== Specific model_name selected ===== -->
          <template v-else>
            <div class="row g-2 g-sm-3">
              <div v-for="model in filteredModels" :key="model.id" class="col-6 col-sm-4 col-md-4 col-lg-3 col-xl-models">
                <!-- MODEL CARD -->
                <div class="model-card">
                  <div class="card-image-wrapper" @click.stop="router.push(`/product/${model.id}?type=model`)">
                    <img v-if="model.thumbnail" :src="model.thumbnail" class="model-thumb" draggable="false" />
                    <template v-else>
                      <img v-if="model.front_black" :src="model.front_black" class="img-layer black" />
                      <img v-if="model.front_white" :src="model.front_white" class="img-layer white" />
                      <img v-if="model.front_svg"   :src="model.front_svg"   class="img-layer svg" draggable="false" />
                      <div v-if="appliedName" class="svg-name-overlay" :style="{ color: appliedColor?.code || '#000' }">{{ appliedName }}</div>
                      <img v-if="!model.front_black && !model.front_white && !model.front_svg" src="https://via.placeholder.com/300x180?text=No+Image" class="img-layer svg" />
                    </template>
                  </div>
                  <div class="model-card-info">
                    <span class="model-card-title">{{ model.title }}</span>
                    <span class="model-card-price">${{ model.price || '0.00' }}</span>
                  </div>
                  <button class="btn-customize-always" @click="handleCustomizerClick(model.id)">Customize</button>
                </div>
                <!-- END MODEL CARD -->
              </div>
            </div>
            <div v-if="filteredModels.length === 0" class="text-center py-5 text-muted">
              <p>No models found.</p>
            </div>
          </template>

        </div>
        <!-- END content-area -->

        <!-- ===== RIGHT: FILTER SIDEBAR (desktop only, sticky) ===== -->
<div
class="filter-sidebar desktop-sidebar"
:class="{
  open: sidebarOpen,
  peek: sidebarPeek
}">
          <div class="sidebar-inner">
            <div class="sidebar-title">CUSTOMIZE YOUR LOOK</div>
            <div class="filter-section">
              <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input v-model="customName" type="text" class="search-input" placeholder="Keyword Search" />
              </div>
            </div>
            <div class="filter-section">
              <div class="filter-label">COLORS</div>
              <div class="color-swatches">
                <div v-for="c in selectedColors" :key="'sel-' + c.id" class="swatch selected" :style="{ background: c.code }" :title="c.name" @click="removeColor(c)"></div>
                <button class="swatch-add" @click="showColorPopup = true" title="Add Color"><i class="bi bi-plus"></i></button>
                <button class="swatch-filter" title="Filter"><i class="bi bi-sliders"></i></button>
              </div>
            </div>
            <div class="filter-section">
              <div class="d-flex gap-2">
                <button class="btn-apply" @click="applyFilters">APPLY</button>
                <button class="btn-clear" @click="clearFilters">CLEAR ALL</button>
              </div>
            </div>
<div class="sidebar-handle" @click="toggleSidebar">
      <i class="bi bi-chevron-left"></i>
</div>

            <div class="sidebar-divider"></div>
            <div class="filter-label mb-2">SEARCH &amp; FILTER</div>
            <div class="filter-section">
              <div class="filter-accordion-label" @click="modelNameOpen = !modelNameOpen">
                <span>GENERAL</span>
                <span class="acc-arrow" :class="{ open: modelNameOpen }"><i class="bi bi-chevron-down"></i></span>
              </div>
              <transition name="acc-slide">
                <div v-show="modelNameOpen" class="acc-body">
                  <div class="filter-option" :class="{ active: selectedModelName === 'all' }" @click="selectedModelName = 'all'">All</div>
                  <div v-for="name in uniqueModelNames" :key="name" class="filter-option" :class="{ active: selectedModelName === name }" @click="selectedModelName = name">{{ name }}</div>
                </div>
              </transition>
            </div>
          </div>
        </div>
        <!-- END filter-sidebar -->

      </div>
      <!-- END main-layout -->

      </div>
      <!-- END models.length > 0 -->

    </div>
    <!-- ===== COLOR POPUP ===== -->
    <transition name="modal-pop">
      <div v-if="showColorPopup" class="color-popup-overlay" @click.self="showColorPopup = false">
        <div class="color-popup-box">
          <button class="popup-close" @click="showColorPopup = false">
            <i class="bi bi-x-lg"></i>
          </button>
          <div class="popup-title">Select your Team Colors.</div>
          <div class="popup-sub">*If a color isn't available, black or white will be used instead.</div>
          <div class="popup-colors-grid">
            <div
              v-for="color in allColors"
              :key="color.id"
              class="popup-color-cell"
              :class="{ selected: isColorSelected(color) }"
              :style="{ background: color.code }"
              :title="color.name"
              @click="toggleColor(color)"
            >
              <span class="popup-color-label">{{ color.name }}</span>
              <i v-if="isColorSelected(color)" class="bi bi-check popup-check"></i>
            </div>
          </div>
          <div class="popup-footer">
            <button class="btn-apply" @click="showColorPopup = false">Done</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ===== PURCHASE MODAL ===== -->
    <transition name="modal-pop">
      <div v-if="showPurchaseModal" class="pm-overlay" @click.self="showPurchaseModal = false">
        <div class="pm-box">
          <button class="pm-close" @click="showPurchaseModal = false"><i class="bi bi-x-lg"></i></button>
          <div class="pm-layout">
            <div class="pm-img-side">
              <img v-if="pmModel.thumbnail" :src="pmModel.thumbnail" class="pm-main-img" />
              <template v-else>
                <img v-if="pmModel.front_black" :src="pmModel.front_black" class="pm-layer black" />
                <img v-if="pmModel.front_white" :src="pmModel.front_white" class="pm-layer white" />
                <img v-if="pmModel.front_svg"   :src="pmModel.front_svg"   class="pm-layer svg"   />
              </template>
            </div>
            <div class="pm-info-side">
              <h4 class="pm-title">{{ pmModel.title }}</h4>
              <p class="pm-price">${{ pmModel.price || '0.00' }}</p>
              <hr class="pm-hr" />
              <div class="pm-group">
                <label class="pm-label">Select Size *</label>
                <div class="pm-sizes">
                  <button v-for="sz in standardSizes" :key="sz" class="pm-sz" :class="{ selected: pmSize === sz }" @click="pmSize = sz; pmCustomConfirmed = false; pmCustom = ''">{{ sz }}</button>
                  <button class="pm-sz pm-other" :class="{ selected: pmSize === '__other__' }" @click="pmSize = '__other__'">Other +</button>
                </div>
                <div v-if="pmSize === '__other__'" class="pm-custom-row">
                  <input v-model="pmCustom" type="text" class="pm-custom-input" placeholder="e.g. 3XL, 42 chest..." @keyup.enter="confirmPmCustom" />
                  <button class="pm-custom-btn" @click="confirmPmCustom">✓</button>
                </div>
                <p v-if="pmSize === '__other__' && pmCustomConfirmed" class="pm-confirmed">✅ Size: <strong>{{ pmCustom }}</strong></p>
              </div>
              <div class="pm-group">
                <label class="pm-label">Quantity</label>
                <div class="pm-qty">
                  <button class="pm-qty-btn" @click="pmQty > 1 && pmQty--">−</button>
                  <span class="pm-qty-val">{{ pmQty }}</span>
                  <button class="pm-qty-btn" @click="pmQty++">+</button>
                </div>
              </div>
              <p v-if="!pmEffectiveSize" class="pm-warn">⚠ Please select or confirm a size first</p>
              <div class="pm-actions">
                <button class="pm-cart-btn" @click="addModelToCart" :disabled="!pmEffectiveSize"><i class="bi bi-cart-plus"></i> Add to Cart</button>
                <button class="pm-buy-btn" @click="buyModelNow" :disabled="!pmEffectiveSize">Buy Now</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- LOGIN MODAL -->
    <div v-if="showLoginModal" class="modal fade show" style="display: block" @click.self="showLoginModal = false">
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
          <div class="modal-body text-center px-4 px-sm-5 pb-2">
            <h5 class="fw-bold mb-2">Login Required</h5>
            <p class="text-muted mb-0">To use the Customizer, you must first log in to your account.</p>
          </div>
          <div class="modal-footer justify-content-center border-0 px-4 px-sm-5 pb-4 gap-3">
            <button class="btn btn-outline-secondary rounded-pill px-4" @click="showLoginModal = false">Cancel</button>
            <router-link :to="{ path: '/user-login', query: { redirect: `/models/${pendingModelId}` } }" class="btn btn-dark rounded-pill px-4" @click="showLoginModal = false">Login</router-link>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showLoginModal" class="modal-backdrop fade show" @click="showLoginModal = false"></div>

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

// Mobile filter toggle
const mobileFilterOpen = ref(false)

// ── Color variant (products)
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
const standardSizes     = ['YXS', 'YS', 'YM', 'YL', 'YXL', 'S', 'M', 'L', 'XL', '2XL']

const pmEffectiveSize = computed(() => {
  if (pmSize.value === '__other__') return pmCustomConfirmed.value ? pmCustom.value.trim() : ''
  return pmSize.value
})

const confirmPmCustom = () => {
  if (!pmCustom.value.trim()) { showToast('Please type a size!'); return }
  pmCustomConfirmed.value = true
  showToast(`✅ Size "${pmCustom.value.trim()}" confirmed!`)
}

const addModelToCart = () => {
  if (!pmEffectiveSize.value) { showToast('⚠️ Please select a size!'); return }
  cartStore.addToCart(
    { id: pmModel.value.id, name: pmModel.value.title, price: pmModel.value.price || 0, image: pmModel.value.thumbnail || pmModel.value.front_svg || '' },
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

// ─────────────────────────────────────────────
// FILTER STATE
// ─────────────────────────────────────────────
const customName = ref('')
const appliedName = ref('')
const nameSearch        = ref('')
const selectedModelName = ref('all')
const selectedColors    = ref([])
const appliedColor      = ref(null)
const showColorPopup    = ref(false)
const allColors         = ref([])
const modelNameOpen     = ref(true)
const sidebarOpen = ref(false)
const sidebarPeek = ref(false)
const uniqueModelNames = computed(() => {
  return [...new Set(models.value.map(m => m.model_name).filter(Boolean))]
})

const isColorSelected = (color) => selectedColors.value.some(c => c.id === color.id)

const toggleColor = (color) => {
  if (isColorSelected(color)) {
    selectedColors.value = selectedColors.value.filter(c => c.id !== color.id)
  } else {
    selectedColors.value = [...selectedColors.value, color]
  }
}

const removeColor = (color) => {
  selectedColors.value = selectedColors.value.filter(c => c.id !== color.id)
}

const applyFilters = () => {
  appliedColor.value = selectedColors.value.length > 0
    ? selectedColors.value[0]
    : null
  appliedName.value = customName.value
  showColorPopup.value = false
  mobileFilterOpen.value = false
}

const clearFilters = () => {
  nameSearch.value        = ''
  selectedModelName.value = 'all'
  selectedColors.value    = []
  appliedColor.value      = null
  customName.value        = ''
  appliedName.value       = ''
}
const toggleSidebar = () => {

  if (sidebarOpen.value) {
    sidebarOpen.value = false
    sidebarPeek.value = false
  }

  else {
    sidebarOpen.value = true
  }

}
// ─────────────────────────────────────────────
// FILTERED MODELS
// ─────────────────────────────────────────────
const filteredModels = computed(() => {
  let list = models.value
  if (selectedModelName.value !== 'all') {
    list = list.filter(m => m.model_name === selectedModelName.value)
  }
  if (nameSearch.value.trim()) {
    const q = nameSearch.value.trim().toLowerCase()
    list = list.filter(m =>
      (m.title || '').toLowerCase().includes(q) ||
      (m.model_name || '').toLowerCase().includes(q)
    )
  }
  return list
})

const groupedFilteredModels = computed(() => {
  const groups = {}
  filteredModels.value.forEach(m => {
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

// ─────────────────────────────────────────────
// DATA FETCHING
// ─────────────────────────────────────────────
const loadModels = async () => {
  try {
    const subRes = await axios.get(`/api/subcategories/${categoryId}/models`)
    let loadedModels = subRes.data.models || []
    if (loadedModels.length > 0) {
      models.value = loadedModels.sort((a, b) => a.position - b.position)
      return
    }
    const catRes = await axios.get(`/api/categories/${categoryId}/models`)
    models.value = (catRes.data.models || []).sort((a, b) => a.position - b.position)
  } catch (err) {
    console.error('Models error:', err)
  }
}

const fetchProducts = async () => {
  try {
    const res = await axios.get(`/api/category/${route.params.id}/products`)
    products.value = res.data
  } catch (e) {
    console.error('Products error:', e)
  } finally {
    loading.value = false
  }
}

const fetchCategory = async () => {
  try {
    const res = await axios.get(`/api/categories/${categoryId}`)
    category.value = res.data
  } catch (e) {
    console.error('Category error:', e)
  }
}

const fetchColors = async () => {
  try {
    const res = await axios.get('/api/colors')
    allColors.value = res.data
  } catch (e) {
    console.error('Colors error:', e)
  }
}

onMounted(() => {
  fetchCategory()
  fetchProducts()
  loadModels()
  fetchColors()
})
</script>

<style scoped>

/* ===== SVG NAME OVERLAY ===== */
.svg-name-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: clamp(14px, 3vw, 22px);
  font-weight: 700;
  z-index: 20;
  white-space: nowrap;
}

/* ===== LAYOUT ===== */
.main-layout {
  display: flex;
  gap: 16px;
  align-items: flex-start;
  flex-wrap: wrap;
}
.content-area {
  flex: 1;
  min-width: 0;
  width: 100%;
}

/* ===== PAGE TITLE ===== */
.page-title {
  font-size: clamp(20px, 4vw, 28px);
  letter-spacing: 1px;
}

/* ===== 5-COLUMN FOR PRODUCTS (xl screens) ===== */
@media (min-width: 1400px) {
  .col-xl-products {
    flex: 0 0 20%;
    max-width: 20%;
  }
}

/* ===== MODEL GRID COLUMNS ===== */
@media (min-width: 1400px) {
  .col-xl-models {
    flex: 0 0 20%;
    max-width: 20%;
  }
}

/* ===== FILTER SIDEBAR ===== */
.filter-sidebar {
  /* width: 280px; */
  flex-shrink: 0;
  /* position: sticky; */
  top: 140px;
  align-self: flex-start;
}

/* ===== MOBILE FILTER BUTTON ===== */
.mobile-filter-btn {
  display: none;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 10px 14px;
  background: #000;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  margin-bottom: 8px;
  order: -1;
}
.mobile-filter-btn i { font-size: 15px; }
.mobile-filter-btn span { flex: 1; text-align: left; }

.sidebar-inner {
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 10px;
  padding: 14px;
}
.sidebar-title {
  font-size: clamp(13px, 1.5vw, 17px);
  font-weight: 800;
  text-align: center;
  letter-spacing: 1px;
  color: #111;
  margin-bottom: 12px;
}
.filter-section { margin-bottom: 14px; }
.filter-label {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .8px;
  color: #555;
  text-transform: uppercase;
  margin-bottom: 8px;
}
.sidebar-divider {
  border-top: 1px solid #ebebeb;
  margin: 12px 0;
}

/* Search */
.search-box {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 0 10px;
  background: #fafafa;
}
.search-icon { color: #999; font-size: 13px; margin-right: 6px; }
.search-input {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  width: 100%;
  height: 34px;
  color: #333;
}
.search-input::placeholder { color: #bbb; }

/* Color swatches */
.color-swatches { display: flex; flex-wrap: wrap; gap: 5px; align-items: center; }
.swatch {
  width: 26px; height: 26px;
  border-radius: 4px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: .15s;
}
.swatch.selected { border-color: #000; box-shadow: 0 0 0 1px #fff inset; }
.swatch-add, .swatch-filter {
  width: 26px; height: 26px;
  border-radius: 4px;
  border: 1.5px solid #ccc;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 14px;
  color: #555;
  transition: .15s;
}
.swatch-add:hover, .swatch-filter:hover { border-color: #000; color: #000; }

/* Buttons */
.btn-apply {
  background: #000;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: .15s;
}
.btn-apply:hover { background: #333; }
.btn-clear {
  background: transparent;
  color: #555;
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 8px 10px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: .15s;
}
.btn-clear:hover { border-color: #000; color: #000; }

/* Accordion */
.filter-accordion-label {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: .6px;
  color: #333;
  cursor: pointer;
  padding: 6px 0;
  border-bottom: 1px solid #ebebeb;
  text-transform: uppercase;
}
.acc-arrow { transition: transform .25s; display: flex; align-items: center; }
.acc-arrow.open { transform: rotate(180deg); }
.acc-body { padding-top: 6px; }
.filter-option {
  padding: 5px 8px;
  font-size: 13px;
  color: #444;
  border-radius: 5px;
  cursor: pointer;
  transition: .15s;
}
.filter-option:hover { background: #f5f5f5; }
.filter-option.active { background: #000; color: #fff; font-weight: 600; }
.acc-slide-enter-active, .acc-slide-leave-active { transition: all .25s ease; overflow: hidden; }
.acc-slide-enter-from, .acc-slide-leave-to { max-height: 0; opacity: 0; }
.acc-slide-enter-to, .acc-slide-leave-from { max-height: 400px; opacity: 1; }

/* ===== COLOR POPUP ===== */
.color-popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9200;
  padding: 16px;
}
.color-popup-box {
  background: #fff;
  border-radius: 14px;
  padding: 20px;
  max-width: 480px;
  width: 100%;
  position: relative;
  max-height: 88vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
}
.popup-close {
  position: absolute;
  top: 12px; right: 12px;
  width: 30px; height: 30px;
  background: #f0f0f0;
  border: none;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 12px;
  transition: .2s;
}
.popup-close:hover { background: #000; color: #fff; }
.popup-title { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
.popup-sub { font-size: 11px; color: #e53e3e; margin-bottom: 14px; }
.popup-colors-grid { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
.popup-color-cell {
  width: clamp(38px, 10vw, 44px);
  height: clamp(38px, 10vw, 44px);
  border-radius: 6px;
  cursor: pointer;
  border: 2px solid transparent;
  position: relative;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  transition: .15s;
  overflow: hidden;
}
.popup-color-cell:hover { transform: scale(1.08); border-color: rgba(0,0,0,.3); }
.popup-color-cell.selected { border-color: #000; box-shadow: 0 0 0 2px #fff inset; }
.popup-color-label {
  font-size: 7px;
  font-weight: 700;
  color: #fff;
  text-shadow: 0 1px 2px rgba(0,0,0,.8);
  background: rgba(0,0,0,.3);
  width: 100%;
  text-align: center;
  padding: 1px 0;
}
.popup-check {
  position: absolute;
  top: 3px; right: 3px;
  font-size: 12px;
  color: #fff;
  text-shadow: 0 1px 3px rgba(0,0,0,.8);
}
.popup-footer { text-align: right; }

/* ===== SELECT DESIGN ===== */
.select-design-heading {
  font-size: clamp(16px, 3vw, 22px);
  font-weight: 800;
  letter-spacing: 2px;
  color: #111;
  margin-bottom: 16px;
  margin-top: 0;
}

/* ===== GROUP HEADING ===== */
.model-group-heading {
  text-align: center;
  position: relative;
  margin-bottom: 16px;
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
  font-size: clamp(13px, 2vw, 15px);
  font-weight: 700;
  background: #fff;
  padding: 0 12px;
  position: relative;
  z-index: 1;
  letter-spacing: .5px;
}

/* ===== PRODUCT CARDS ===== */
.product-card { border-radius: 12px; overflow: hidden; transition: .35s ease; }
.product-card:hover { transform: translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,.13); }

.product-inner {
  display: flex;
  height: clamp(160px, 25vw, 250px);
}

.product-name {
  font-size: clamp(12px, 1.5vw, 16px);
}
.product-price {
  font-size: clamp(12px, 1.5vw, 16px);
  font-weight: 700;
}

.color-strip {
  display: flex;
  flex-direction: column;
  gap: 3px;
  padding: 5px 3px;
  background: #f5f5f5;
  border-right: 1px solid #ebebeb;
  width: clamp(28px, 4vw, 36px);
  flex-shrink: 0;
  overflow: hidden;
}
.color-box {
  width: clamp(22px, 3vw, 28px);
  height: clamp(22px, 3vw, 28px);
  border-radius: 4px;
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
  bottom: 2px; right: 2px;
  width: 6px; height: 6px;
  border-radius: 50%;
  border: 1px solid rgba(0,0,0,.2);
}
.product-img-wrapper {
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex: 1;
}
.product-img { width: 90%; height: 95%; object-fit: contain; }
.color-swap-enter-active, .color-swap-leave-active { transition: opacity .18s ease; }
.color-swap-enter-from, .color-swap-leave-to { opacity: 0; }

/* ===== MODEL CARDS ===== */
.model-card {
  background: #fff;
  border: none;
  border-radius: 0;
  overflow: visible;
  cursor: default;
  height: 100%;
  width: 100%;
  padding: 0;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
}

.card-image-wrapper {
  height: clamp(180px, 18vw, 380px);
  background: #eeeeee;
  border-radius: 0;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  border: 1px solid #eeeeee;
  cursor: pointer;
}
.card-image-wrapper:hover { opacity: 0.93; }

/* Card info below image */
.model-card-info {
      background: #eeeeee;

  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 10px 10px 10px;
  gap: 4px;
}
.model-card-title {
  font-size: clamp(16px, 1.8vw, 18px);
  color: #000000;
  font-weight: 400;
}
.model-card-price {
  font-size: clamp(11px, 1.3vw, 13px);
  color: #000000;
  font-weight: 600;
  white-space: nowrap;
}

/* Always-visible Customize button */
.btn-customize-always {
  width: 100%;
  background: #000;
  color: #fff;
  border: none;
  border-radius: 0;
  height: clamp(34px, 4vw, 40px);
  font-size: clamp(11px, 1.3vw, 13px);
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  cursor: pointer;
  transition: background 0.15s;
  margin-top: auto;
}
.btn-customize-always:hover { background: #222; }

.model-thumb {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  height: 100%;
  object-fit: contain;
  z-index: 1;
}
.img-layer {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  height: 100%; width: 100%;
  object-fit: contain;
}
.black { z-index: 5; mix-blend-mode: screen; }
.white { z-index: 4; mix-blend-mode: multiply; }
.svg   { z-index: 3; }

/* ===== PURCHASE MODAL ===== */
.pm-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9100;
  padding: 12px;
}
.pm-box {
  background: #fff;
  border-radius: 16px;
  max-width: 640px;
  width: 100%;
  overflow: hidden;
  position: relative;
  box-shadow: 0 20px 60px rgba(0,0,0,.25);
  max-height: 92vh;
  overflow-y: auto;
}
.pm-close {
  position: absolute;
  top: 10px; right: 10px;
  width: 32px; height: 32px;
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
.pm-img-side {
  background: #f5f5f5;
  min-height: 220px;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}
.pm-main-img { max-width: 82%; max-height: 240px; object-fit: contain; }
.pm-layer {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  max-height: 78%;
  object-fit: contain;
}
.pm-info-side {
  padding: clamp(16px, 3vw, 28px) clamp(12px, 2.5vw, 22px);
  overflow-y: auto;
  max-height: 520px;
}
.pm-title { font-size: clamp(15px, 2.5vw, 18px); font-weight: 700; margin-bottom: 4px; }
.pm-price { font-size: clamp(18px, 3vw, 24px); font-weight: 800; margin-bottom: 0; }
.pm-hr    { border: none; border-top: 1px solid #ebebeb; margin: 12px 0; }
.pm-group { margin-bottom: 16px; }
.pm-label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .8px;
  color: #555;
  margin-bottom: 8px;
}
.pm-sizes { display: flex; flex-wrap: wrap; gap: 4px; }
.pm-sz {
  min-width: 38px;
  height: 34px;
  padding: 0 6px;
  border: 1.5px solid #e0e0e0;
  background: #fff;
  border-radius: 6px;
  font-size: clamp(10px, 1.5vw, 12px);
  font-weight: 700;
  cursor: pointer;
  transition: .15s;
  font-family: inherit;
}
.pm-sz:hover { border-color: #000; }
.pm-sz.selected { background: #000; color: #fff; border-color: #000; }
.pm-other { background: #f5f5f5; border-style: dashed; }
.pm-other.selected { background: #000; color: #fff; border-style: solid; }
.pm-custom-row { display: flex; gap: 6px; margin-top: 8px; }
.pm-custom-input {
  flex: 1;
  height: 36px;
  padding: 0 10px;
  border: 1.5px solid #e0e0e0;
  border-radius: 8px;
  font-size: 13px;
  font-family: inherit;
  transition: .2s;
}
.pm-custom-input:focus { outline: none; border-color: #000; }
.pm-custom-btn { height: 36px; padding: 0 12px; background: #000; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; }
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
.pm-qty-btn { width: 36px; height: 36px; border: none; background: #fff; font-size: 17px; cursor: pointer; transition: .2s; }
.pm-qty-btn:hover { background: #f5f5f5; }
.pm-qty-val {
  width: 44px;
  text-align: center;
  font-size: 14px;
  font-weight: 700;
  border-left: 1.5px solid #e0e0e0;
  border-right: 1.5px solid #e0e0e0;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.pm-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.pm-cart-btn, .pm-buy-btn {
  height: 44px;
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

/* Modals animation */
.modal-pop-enter-active, .modal-pop-leave-active { transition: all .28s ease; }
.modal-pop-enter-from, .modal-pop-leave-to { opacity: 0; }
.modal-pop-enter-from .pm-box,
.modal-pop-enter-from .color-popup-box { transform: scale(.96) translateY(12px); }

.login-icon-wrap {
  width: 70px; height: 70px;
  background: #f0f0f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Toast */
.cat-toast {
  position: fixed;
  bottom: 20px; right: 16px;
  background: #111;
  color: #fff;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  z-index: 9999;
  pointer-events: none;
  max-width: calc(100vw - 32px);
}
.toast-slide-enter-active, .toast-slide-leave-active { transition: all .3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateY(14px); }





.col-xl-models {
  flex: 0 0 16.666%;
  max-width: 16.666%;
}
.sidebar-open .col-xl-models {
  flex: 0 0 20%;
  max-width: 20%;
}
.desktop-sidebar {
  position: fixed;
  right: -260px;
  top: 140px;
  width: 260px;
  transition: 0.35s ease;
  z-index: 999;
}


.desktop-sidebar.peek {

  right: -230px;

}

.desktop-sidebar.open {

  right: 20px;

}

.sidebar-handle {

  position: absolute;
  left: -30px;
  top: 50%;

  transform: translateY(-50%);

  background: black;
  color: white;

  width: 30px;
  height: 70px;

  display: flex;
  align-items: center;
  justify-content: center;

  cursor: pointer;
  z-index: 1000;

}

/* ============================================================
   RESPONSIVE BREAKPOINTS
   ============================================================ */

/* Large tablets / small desktops: sidebar becomes narrower */
@media (max-width: 1200px) {
  .filter-sidebar { width: 230px; }
}

/* Tablets + mobile (under 1199px): collapsible filter */
@media (max-width: 1199px) {
  .main-layout {
    flex-direction: column;
  }

  .content-area {
    order: 2;
    width: 100%;
  }

  .filter-sidebar {
    order: 1;
    width: 100%;
    position: static;
    display: none;
  }

  .filter-sidebar.mobile-open {
    display: block;
  }

  .mobile-filter-btn {
    display: flex;
    order: 0;
  }
}

/* Tablets (portrait) */
@media (max-width: 768px) {
  .pm-layout {
    grid-template-columns: 1fr;
  }
  .pm-img-side {
    min-height: 180px;
    max-height: 200px;
  }
  .pm-info-side {
    max-height: none;
    padding: 16px;
  }
  .pm-main-img { max-height: 160px; }

  .card-image-wrapper {
    height: clamp(160px, 45vw, 260px);
  }
  .product-inner {
    height: clamp(140px, 40vw, 200px);
  }
}

/* Mobile */
@media (max-width: 480px) {
  .card-image-wrapper {
    height: clamp(150px, 48vw, 220px);
  }
  .product-inner {
    height: clamp(130px, 45vw, 180px);
  }
  .pm-sizes { gap: 3px; }
  .pm-sz { min-width: 34px; height: 32px; padding: 0 5px; }
  .pm-actions { grid-template-columns: 1fr; }
  .pm-cart-btn, .pm-buy-btn { height: 42px; }
  .cat-toast { bottom: 14px; right: 12px; font-size: 12px; }
}

/* Very small phones */
@media (max-width: 360px) {
  .card-image-wrapper,
  .product-inner {
    height: 130px;
  }
  .sidebar-inner { padding: 10px; }
}
</style>
