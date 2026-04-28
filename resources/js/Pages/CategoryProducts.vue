<template>
  <div>
    <nav-component />
    <breadcrumb-component />

    <div class="container-fluid px-2 px-sm-3 px-md-4">
      <div class="text-center mb-3 mb-md-4">
        <h1 class="fw-bold page-title">{{ category?.name }}</h1>
      </div>

      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-dark"></div>
      </div>

      <div v-else>
        <!-- ===== PRODUCTS SECTION ===== -->
        <div class="row g-2 g-sm-3 g-md-4 mb-4 mb-md-5" v-if="products.length > 0">
          <div
            v-for="product in products"
            :key="product.id"
            class="col-6 col-sm-4 col-md-3 col-lg-2"
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
                <router-link :to="`/product/${product.id}`" class="btn btn-dark btn-sm w-100">
                  View &amp; Buy
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- ===== MODELS SECTION ===== -->
        <div class="models-wrapper" :class="{ 'sidebar-open': sidebarOpen }">
          <button class="mobile-filter-btn" @click="mobileFilterOpen = !mobileFilterOpen">
            <i class="bi bi-sliders"></i>
            <span>Filters</span>
            <i class="bi" :class="mobileFilterOpen ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
          </button>

          <div class="mobile-sidebar" :class="{ 'mobile-open': mobileFilterOpen }">
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
                  <div
                    v-for="c in selectedColors"
                    :key="'msel-' + c.id"
                    class="swatch selected"
                    :style="{ background: c.code }"
                    :title="c.name"
                    @click="removeColor(c)"
                  ></div>
                  <button class="swatch-add" @click="showColorPopup = true"><i class="bi bi-plus"></i></button>
                  <button class="swatch-filter"><i class="bi bi-sliders"></i></button>
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
                  <span class="acc-arrow" :class="{ open: modelNameOpen }">
                    <i class="bi bi-chevron-down"></i>
                  </span>
                </div>
                <transition name="acc-slide">
                  <div v-show="modelNameOpen" class="acc-body">
                    <div
                      class="filter-option"
                      :class="{ active: selectedModelName === 'all' }"
                      @click="selectedModelName = 'all'"
                    >
                      All
                    </div>
                    <div
                      v-for="name in uniqueModelNames"
                      :key="name"
                      class="filter-option"
                      :class="{ active: selectedModelName === name }"
                      @click="selectedModelName = name"
                    >
                      {{ name }}
                    </div>
                  </div>
                </transition>
              </div>
            </div>
          </div>

          <div class="select-design-heading">SELECT DESIGN</div>

          <div class="models-grid-area">
            <template v-if="selectedModelName === 'all'">
              <div
                v-for="(groupModels, modelName) in groupedFilteredModels"
                :key="modelName"
                class="mb-4 mb-md-5"
              >
                <div class="model-group-heading">
                  <span>{{ modelName }}</span>
                </div>

                <div class="model-grid">
                  <div v-for="model in groupModels" :key="model.id" class="model-col">
                    <div class="model-card">
                      <div class="card-image-wrapper" @click.stop="router.push(`/product/${model.id}?type=model`)">
                        <template v-if="shouldUseCompositePreview(model)">
                          <img
                            v-if="getCompositeBase(model)"
                            :src="getCompositeBase(model)"
                            class="img-layer svg colored-base"
                            draggable="false"
                          />
                          <img
                            v-if="model.front_white"
                            :src="model.front_white"
                            class="img-layer white"
                            draggable="false"
                          />
                          <img
                            v-if="model.front_black"
                            :src="model.front_black"
                            class="img-layer black"
                            draggable="false"
                          />
                        </template>

                        <template v-else-if="model.thumbnail">
                          <img :src="model.thumbnail" class="model-thumb" draggable="false" />
                        </template>

                        <template v-else>
                          <img
                            v-if="model.front_black"
                            :src="model.front_black"
                            class="img-layer black"
                            draggable="false"
                          />
                          <img
                            v-if="model.front_white"
                            :src="model.front_white"
                            class="img-layer white"
                            draggable="false"
                          />
                          <img
                            v-if="model.front_svg"
                            :src="model.front_svg"
                            class="img-layer svg"
                            draggable="false"
                          />
                          <img
                            v-if="!model.front_black && !model.front_white && !model.front_svg"
                            src="https://via.placeholder.com/300x180?text=No+Image"
                            class="img-layer svg"
                          />
                        </template>
                      </div>

                      <div class="model-card-info">
                        <span class="model-card-title">{{ model.title }}</span>
                        <span class="model-card-price">${{ model.price || '0.00' }}</span>
                      </div>

                      <button class="btn-customize-always" @click="handleCustomizerClick(model.id)">
                        Customize
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="Object.keys(groupedFilteredModels).length === 0" class="text-center py-5 text-muted">
                <p>No models found.</p>
              </div>
            </template>

            <template v-else>
              <div class="model-grid">
                <div v-for="model in filteredModels" :key="model.id" class="model-col">
                  <div class="model-card">
                    <div class="card-image-wrapper" @click.stop="router.push(`/product/${model.id}?type=model`)">
                      <template v-if="shouldUseCompositePreview(model)">
                        <img
                          v-if="getCompositeBase(model)"
                          :src="getCompositeBase(model)"
                          class="img-layer svg colored-base"
                          draggable="false"
                        />
                        <img
                          v-if="model.front_white"
                          :src="model.front_white"
                          class="img-layer white"
                          draggable="false"
                        />
                        <img
                          v-if="model.front_black"
                          :src="model.front_black"
                          class="img-layer black"
                          draggable="false"
                        />
                      </template>

                      <template v-else-if="model.thumbnail">
                        <img :src="model.thumbnail" class="model-thumb" draggable="false" />
                      </template>

                      <template v-else>
                        <img
                          v-if="model.front_black"
                          :src="model.front_black"
                          class="img-layer black"
                          draggable="false"
                        />
                        <img
                          v-if="model.front_white"
                          :src="model.front_white"
                          class="img-layer white"
                          draggable="false"
                        />
                        <img
                          v-if="model.front_svg"
                          :src="model.front_svg"
                          class="img-layer svg"
                          draggable="false"
                        />
                        <img
                          v-if="!model.front_black && !model.front_white && !model.front_svg"
                          src="https://via.placeholder.com/300x180?text=No+Image"
                          class="img-layer svg"
                        />
                      </template>
                    </div>

                    <div class="model-card-info">
                      <span class="model-card-title">{{ model.title }}</span>
                      <span class="model-card-price">${{ model.price || '0.00' }}</span>
                    </div>

                    <button class="btn-customize-always" @click="handleCustomizerClick(model.id)">
                      Customize
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="filteredModels.length === 0" class="text-center py-5 text-muted">
                <p>No models found.</p>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <button class="sidebar-toggle-btn" @click="toggleSidebar" :class="{ open: sidebarOpen }">
      <i class="bi" :class="sidebarOpen ? 'bi-x-lg' : 'bi-sliders'"></i>
    </button>

    <div class="desktop-sidebar" :class="{ open: sidebarOpen }">
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
            <div
              v-for="c in selectedColors"
              :key="'dsel-' + c.id"
              class="swatch selected"
              :style="{ background: c.code }"
              :title="c.name"
              @click="removeColor(c)"
            ></div>
            <button class="swatch-add" @click="showColorPopup = true"><i class="bi bi-plus"></i></button>
            <button class="swatch-filter"><i class="bi bi-sliders"></i></button>
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
            <span class="acc-arrow" :class="{ open: modelNameOpen }">
              <i class="bi bi-chevron-down"></i>
            </span>
          </div>
          <transition name="acc-slide">
            <div v-show="modelNameOpen" class="acc-body">
              <div
                class="filter-option"
                :class="{ active: selectedModelName === 'all' }"
                @click="selectedModelName = 'all'"
              >
                All
              </div>
              <div
                v-for="name in uniqueModelNames"
                :key="name"
                class="filter-option"
                :class="{ active: selectedModelName === name }"
                @click="selectedModelName = name"
              >
                {{ name }}
              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>

    <!-- ===== COLOR POPUP ===== -->
    <transition name="modal-pop">
      <div v-if="showColorPopup" class="color-popup-overlay" @click.self="showColorPopup = false">
        <div class="color-popup-box">
          <button class="popup-close" @click="showColorPopup = false"><i class="bi bi-x-lg"></i></button>
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
              <!-- ✅ FIXED: show designed preview if available, else fallback -->
              <template v-if="getDesignedPreviewForModel(pmModel)">
                <img :src="getDesignedPreviewForModel(pmModel)" class="pm-main-img" />
                <img v-if="pmModel.front_white" :src="pmModel.front_white" class="pm-layer white" />
                <img v-if="pmModel.front_black" :src="pmModel.front_black" class="pm-layer black" />
              </template>
              <template v-else-if="pmModel.thumbnail">
                <img :src="pmModel.thumbnail" class="pm-main-img" />
              </template>
              <template v-else>
                <img v-if="pmModel.front_black" :src="pmModel.front_black" class="pm-layer black" />
                <img v-if="pmModel.front_white" :src="pmModel.front_white" class="pm-layer white" />
                <img v-if="pmModel.front_svg" :src="pmModel.front_svg" class="pm-layer svg" />
              </template>
            </div>

            <div class="pm-info-side">
              <h4 class="pm-title">{{ pmModel.title }}</h4>
              <p class="pm-price">${{ pmModel.price || '0.00' }}</p>
              <hr class="pm-hr" />

              <div class="pm-group">
                <label class="pm-label">Select Size *</label>
                <div class="pm-sizes">
                  <button
                    v-for="sz in standardSizes"
                    :key="sz"
                    class="pm-sz"
                    :class="{ selected: pmSize === sz }"
                    @click="pmSize = sz; pmCustomConfirmed = false; pmCustom = ''"
                  >
                    {{ sz }}
                  </button>
                  <button class="pm-sz pm-other" :class="{ selected: pmSize === '__other__' }" @click="pmSize = '__other__'">
                    Other +
                  </button>
                </div>

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

    <!-- LOGIN MODAL -->
    <div v-if="showLoginModal" class="modal fade show" style="display:block" @click.self="showLoginModal = false">
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
    <div v-if="showLoginModal" class="modal-backdrop fade show" @click="showLoginModal = false"></div>

    <transition name="toast-slide">
      <div class="cat-toast" v-if="toastVisible">{{ toastText }}</div>
    </transition>

    <footer-component />
  </div>
</template>

<script setup>
import axios from 'axios'
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '@/store/cart'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()

const categoryId = route.params.id
const category = ref(null)
const products = ref([])
const models = ref([])
const loading = ref(true)
const showLoginModal = ref(false)
const pendingModelId = ref(null)
const mobileFilterOpen = ref(false)

const activeVariant = ref({})
const setVariant = (productId, vi) => {
  activeVariant.value = { ...activeVariant.value, [productId]: vi }
}
const getVariantImg = (product) => {
  const vi = activeVariant.value[product.id] ?? 0
  return product.color_variants?.[vi]?.image || product.image || ''
}

const toastVisible = ref(false)
const toastText = ref('')
let toastTimer
const showToast = (msg) => {
  toastText.value = msg
  toastVisible.value = true
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toastVisible.value = false }, 2500)
}

const showPurchaseModal = ref(false)
const pmModel = ref({})
const pmSize = ref('')
const pmCustom = ref('')
const pmCustomConfirmed = ref(false)
const pmQty = ref(1)
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

// ✅ GLOBAL DESIGN STATE KEY — category-independent, sirf ek global key
const GLOBAL_DESIGN_KEY = 'prosix_global_design_state'

// ✅ Save design globally (not per category)
const saveGlobalDesignState = () => {
  try {
    localStorage.setItem(GLOBAL_DESIGN_KEY, JSON.stringify({
      customName: customName.value,
      appliedName: appliedName.value,
      appliedColor: appliedColor.value,
      selectedModelName: selectedModelName.value,
      appliedTargetModelName: appliedTargetModelName.value,
      selectedColors: selectedColors.value,
      appliedSelectedColors: appliedSelectedColors.value,
      categoryId: categoryId, // track karo kahan se apply hua
    }))
  } catch (e) {
    console.error('Failed to save global design state:', e)
  }
}

// ✅ Restore global design state
const restoreGlobalDesignState = () => {
  try {
    const raw = localStorage.getItem(GLOBAL_DESIGN_KEY)
    if (!raw) return

    const parsed = JSON.parse(raw)
    customName.value = parsed?.customName || ''
    appliedName.value = parsed?.appliedName || ''
    appliedColor.value = parsed?.appliedColor || null
    selectedModelName.value = parsed?.selectedModelName || 'all'
    appliedTargetModelName.value = parsed?.appliedTargetModelName || 'all'
    selectedColors.value = Array.isArray(parsed?.selectedColors) ? parsed.selectedColors : []
    appliedSelectedColors.value = Array.isArray(parsed?.appliedSelectedColors) ? parsed.appliedSelectedColors : []
  } catch (e) {
    console.error('Failed to restore global design state:', e)
  }
}

// ✅ Clear global design state
const clearGlobalDesignState = () => {
  try {
    localStorage.removeItem(GLOBAL_DESIGN_KEY)
  } catch (e) {
    console.error('Failed to clear global design state:', e)
  }
}

// ✅ Also keep old per-category key for backward compat
const PREVIEW_STORAGE_KEY = computed(() => `category_preview_state_${route.params.id}`)
const savePreviewState = () => {
  saveGlobalDesignState()
  try {
    localStorage.setItem(PREVIEW_STORAGE_KEY.value, localStorage.getItem(GLOBAL_DESIGN_KEY))
  } catch {}
}
const restorePreviewState = () => {
  restoreGlobalDesignState()
}
const clearPreviewState = () => {
  clearGlobalDesignState()
  try { localStorage.removeItem(PREVIEW_STORAGE_KEY.value) } catch {}
}

// ✅ Get designed preview image for a model (for cart & purchase modal)
const getDesignedPreviewForModel = (model) => {
  if (!model?.id) return null
  return coloredSvgCache.value[model.id] || null
}

// ✅ FIXED: addModelToCart now includes designed image
const addModelToCart = () => {
  if (!pmEffectiveSize.value) { showToast('⚠️ Please select a size!'); return }

  // Use designed preview image if available, else fallback
  const designedImage = getDesignedPreviewForModel(pmModel.value)
    || pmModel.value.thumbnail
    || pmModel.value.front_svg
    || ''

  cartStore.addToCart(
    {
      id: pmModel.value.id,
      name: pmModel.value.title,
      price: pmModel.value.price || 0,
      image: designedImage,
      // ✅ Save design metadata so ProductDetail page can restore it
      _designedColors: appliedSelectedColors.value,
      _designedName: appliedName.value,
      _isDesigned: !!(appliedSelectedColors.value.length || appliedName.value),
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

const customName = ref('')
const appliedName = ref('')
const nameSearch = ref('')
const selectedModelName = ref('all')
const appliedTargetModelName = ref('all')

const selectedColors = ref([])
const appliedSelectedColors = ref([])
const appliedColor = ref(null)

const showColorPopup = ref(false)
const allColors = ref([])
const modelNameOpen = ref(true)
const sidebarOpen = ref(false)
const coloredSvgCache = ref({})

const uniqueModelNames = computed(() =>
  [...new Set(models.value.map(m => m.model_name).filter(Boolean))]
)

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

const normalizeHex = (hex) => {
  if (!hex) return '#000000'
  let value = String(hex).trim().toUpperCase()
  if (!value.startsWith('#')) value = `#${value}`
  if (/^#[0-9A-F]{3}$/.test(value)) value = '#' + value.slice(1).split('').map(ch => ch + ch).join('')
  if (/^#[0-9A-F]{4}$/.test(value)) value = '#' + value.slice(1, 4).split('').map(ch => ch + ch).join('')
  if (/^#[0-9A-F]{8}$/.test(value)) value = value.slice(0, 7)
  return /^#[0-9A-F]{6}$/.test(value) ? value : '#000000'
}

const svgToDataUri = (svgText) => `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svgText)}`

const isModelInAppliedScope = (model) => {
  if (!model) return false
  if (appliedTargetModelName.value === 'all') return true
  return model.model_name === appliedTargetModelName.value
}

const shouldUseCompositePreview = (model) => {
  return !!(
    model?.front_svg &&
    model?.front_black &&
    model?.front_white &&
    isModelInAppliedScope(model) &&
    (appliedSelectedColors.value.length || appliedName.value.trim())
  )
}

const getCompositeBase = (model) => {
  if (!model?.id) return model?.front_svg || model?.thumbnail || ''
  return coloredSvgCache.value[model.id] || model.front_svg || model.thumbnail || ''
}

const getPrimaryAppliedColor = () => {
  if (appliedSelectedColors.value.length) return appliedSelectedColors.value[0]?.code || '#000000'
  return appliedColor.value?.code || '#000000'
}

const getTextStyleSnapshot = (node) => {
  if (!node) return {}
  return {
    fontFamily: node.getAttribute('font-family') || '',
    fontSize: node.getAttribute('font-size') || '',
    fontWeight: node.getAttribute('font-weight') || '',
    fontStyle: node.getAttribute('font-style') || '',
    letterSpacing: node.getAttribute('letter-spacing') || '',
    textTransform: node.getAttribute('text-transform') || '',
    textAnchor: node.getAttribute('text-anchor') || '',
    dominantBaseline: node.getAttribute('dominant-baseline') || '',
    style: node.getAttribute('style') || '',
    x: node.getAttribute('x') || '',
    y: node.getAttribute('y') || '',
    dx: node.getAttribute('dx') || '',
    dy: node.getAttribute('dy') || '',
    transform: node.getAttribute('transform') || ''
  }
}

const restoreTextStyleSnapshot = (node, snapshot = {}) => {
  if (!node || !snapshot) return
  const attrMap = {
    'font-family': snapshot.fontFamily, 'font-size': snapshot.fontSize,
    'font-weight': snapshot.fontWeight, 'font-style': snapshot.fontStyle,
    'letter-spacing': snapshot.letterSpacing, 'text-transform': snapshot.textTransform,
    'text-anchor': snapshot.textAnchor, 'dominant-baseline': snapshot.dominantBaseline,
    'x': snapshot.x, 'y': snapshot.y, 'dx': snapshot.dx, 'dy': snapshot.dy, 'transform': snapshot.transform
  }
  Object.entries(attrMap).forEach(([key, value]) => { if (value !== '') node.setAttribute(key, value) })
  if (snapshot.style) node.setAttribute('style', snapshot.style)
}

const setOrReplaceStyleProp = (styleText, prop, value) => {
  const safeStyle = styleText || ''
  const regex = new RegExp(`${prop}\\s*:\\s*[^;]+;?`, 'i')
  if (regex.test(safeStyle)) return safeStyle.replace(regex, `${prop}:${value};`)
  return `${safeStyle}${safeStyle && !safeStyle.trim().endsWith(';') ? ';' : ''}${prop}:${value};`
}

const getStylePropValue = (styleText, prop) => {
  if (!styleText) return ''
  const match = styleText.match(new RegExp(`${prop}\\s*:\\s*([^;]+)`, 'i'))
  return match ? match[1].trim() : ''
}

const getNumericFontSize = (node) => {
  const attrSize = parseFloat(node.getAttribute('font-size'))
  if (!Number.isNaN(attrSize) && attrSize > 0) return attrSize
  const style = node.getAttribute('style') || ''
  const match = style.match(/font-size\s*:\s*([0-9.]+)/i)
  if (match) return parseFloat(match[1])
  return 48
}

const applyFontSizeToNode = (node, size) => {
  node.setAttribute('font-size', String(size))
  const style = node.getAttribute('style') || ''
  node.setAttribute('style', setOrReplaceStyleProp(style, 'font-size', `${size}px`))
}

const fitTextIntoSvg = (svg, target, textValue) => {
  if (!svg || !target || !textValue) return
  const viewBox = (svg.getAttribute('viewBox') || '').trim().split(/\s+/).map(Number)
  const svgWidth = viewBox.length === 4 ? viewBox[2] : (parseFloat(svg.getAttribute('width')) || 300)
  const textLength = textValue.length
  let fontSize = getNumericFontSize(target)
  if (textLength >= 8) fontSize *= 0.90
  if (textLength >= 12) fontSize *= 0.82
  if (textLength >= 16) fontSize *= 0.72
  if (textLength >= 22) fontSize *= 0.60
  const maxWidth = svgWidth * 0.52
  const approxWidth = textLength * fontSize * 0.62
  if (approxWidth > maxWidth) fontSize = Math.max(12, Math.floor(maxWidth / Math.max(textLength * 0.62, 1)))
  applyFontSizeToNode(target, fontSize)
  if (!target.getAttribute('text-anchor')) target.setAttribute('text-anchor', 'middle')
}

const updateNodeFillKeepFont = (node, color) => {
  if (!node) return
  const snapshot = getTextStyleSnapshot(node)
  node.setAttribute('fill', color)
  const currentStyle = node.getAttribute('style') || ''
  node.setAttribute('style', setOrReplaceStyleProp(currentStyle, 'fill', color))
  if (snapshot.fontFamily) node.setAttribute('font-family', snapshot.fontFamily)
  if (snapshot.fontWeight) node.setAttribute('font-weight', snapshot.fontWeight)
  if (snapshot.fontStyle) node.setAttribute('font-style', snapshot.fontStyle)
  if (snapshot.letterSpacing) node.setAttribute('letter-spacing', snapshot.letterSpacing)
  if (snapshot.textTransform) node.setAttribute('text-transform', snapshot.textTransform)
  if (snapshot.textAnchor) node.setAttribute('text-anchor', snapshot.textAnchor)
  if (snapshot.dominantBaseline) node.setAttribute('dominant-baseline', snapshot.dominantBaseline)
  const styleAfterFill = node.getAttribute('style') || ''
  let finalStyle = styleAfterFill
  if (snapshot.style) {
    const fontProps = ['font-family','font-size','font-weight','font-style','letter-spacing','text-transform']
    fontProps.forEach((prop) => {
      const match = snapshot.style.match(new RegExp(`${prop}\\s*:\\s*[^;]+`, 'i'))
      if (match) finalStyle = setOrReplaceStyleProp(finalStyle, prop, match[0].split(':').slice(1).join(':').trim())
    })
  }
  node.setAttribute('style', finalStyle)
}

const setSvgTextKeepStyle = (node, newText) => {
  const attrs = [...node.attributes].map(a => [a.name, a.value])
  const tspans = [...node.querySelectorAll('tspan')]
  if (tspans.length) {
    const first = tspans[0]
    const firstAttrs = [...first.attributes].map(a => [a.name, a.value])
    first.textContent = newText
    firstAttrs.forEach(([n, v]) => first.setAttribute(n, v))
    tspans.slice(1).forEach(t => { t.textContent = '' })
  } else {
    node.textContent = newText
  }
  attrs.forEach(([n, v]) => node.setAttribute(n, v))
}


const updateSvgNameIfPresent = (svg, newName, color) => {
  const textNodes = [...svg.querySelectorAll('text')]
    .filter(node => !node.closest('defs'))

  if (!textNodes.length || !newName.trim()) return false

  let targets = textNodes.filter(node => {
    const type = (node.getAttribute('data-type') || '').toLowerCase()
    const id = (node.getAttribute('id') || '').toLowerCase()
    return type === 'teamname' || id.includes('team')
  })

  if (!targets.length) {
    targets = textNodes.filter(node => {
      const txt = (node.textContent || '').trim()
      if (!txt) return false
      if (/^[0-9#\s]+$/.test(txt)) return false
      return /[a-zA-Z]/.test(txt)
    })
  }

  if (!targets.length) return false

  targets.forEach(target => {
    const snap = getTextStyleSnapshot(target)

    const tspans = [...target.querySelectorAll('tspan')]
    if (tspans.length) {
      tspans[0].textContent = newName
      tspans.slice(1).forEach(t => t.textContent = '')
    } else {
      target.textContent = newName
    }

    // ✅ font/style same rakho
    restoreTextStyleSnapshot(target, snap)

    // ✅ sirf color update, font ko touch nahi
    if (color) {
      target.setAttribute('fill', color)
      const oldStyle = target.getAttribute('style') || snap.style || ''
      target.setAttribute('style', setOrReplaceStyleProp(oldStyle, 'fill', color))
    }
  })

  return true
}

const SVG_SHAPE_SELECTOR = 'path, polygon, rect, circle, ellipse, line, polyline, text, tspan, stop'
const isSkippablePaint = (value) => {
  if (!value) return true
  const v = String(value).trim().toLowerCase()
  return (!v || v === 'none' || v === 'transparent' || v === 'currentcolor' || v === 'inherit' || v === 'initial' || v === 'unset' || v.startsWith('url('))
}

const colorToHex = (input) => {
  if (!input) return ''
  const raw = String(input).trim()
  if (!raw) return ''
  if (isSkippablePaint(raw)) return ''
  if (raw.startsWith('#')) return normalizeHex(raw)
  const rgbMatch = raw.match(/^rgba?\(([^)]+)\)$/i)
  if (rgbMatch) {
    const parts = rgbMatch[1].split(',').map(v => parseFloat(v.trim()))
    if (parts.length >= 3) {
      const [r, g, b] = parts
      return '#' + [r, g, b].map(n => Math.max(0, Math.min(255, Number.isFinite(n) ? n : 0))).map(n => Math.round(n).toString(16).padStart(2, '0')).join('').toUpperCase()
    }
  }
  const canvas = document.createElement('canvas')
  const ctx = canvas.getContext('2d')
  if (!ctx) return ''
  ctx.fillStyle = '#000000'
  ctx.fillStyle = raw
  const normalized = ctx.fillStyle || ''
  if (!normalized) return ''
  if (normalized.startsWith('#')) return normalizeHex(normalized)
  const normalizedRgb = normalized.match(/^rgba?\(([^)]+)\)$/i)
  if (normalizedRgb) {
    const parts = normalizedRgb[1].split(',').map(v => parseFloat(v.trim()))
    if (parts.length >= 3) {
      const [r, g, b] = parts
      return '#' + [r, g, b].map(n => Math.max(0, Math.min(255, Number.isFinite(n) ? n : 0))).map(n => Math.round(n).toString(16).padStart(2, '0')).join('').toUpperCase()
    }
  }
  return ''
}

const getNodePaintValue = (node, prop) => {
  if (!node) return ''
  const attrValue = node.getAttribute(prop)
  if (!isSkippablePaint(attrValue)) return colorToHex(attrValue)
  const styleValue = getStylePropValue(node.getAttribute('style') || '', prop)
  if (!isSkippablePaint(styleValue)) return colorToHex(styleValue)
  return ''
}

const setNodePaintValue = (node, prop, color) => {
  if (!node || !color) return
  node.setAttribute(prop, color)
  const style = node.getAttribute('style') || ''
  node.setAttribute('style', setOrReplaceStyleProp(style, prop, color))
}

const getRecolorableSvgNodes = (svg) => {
  return [...svg.querySelectorAll(SVG_SHAPE_SELECTOR)].filter(node => {
    if (node.closest('clipPath') || node.closest('mask')) return false
    const tag = node.tagName.toLowerCase()
    if (tag === 'stop') {
      const stopColor = getStylePropValue(node.getAttribute('style') || '', 'stop-color') || node.getAttribute('stop-color')
      return !!colorToHex(stopColor)
    }
    const fill = getNodePaintValue(node, 'fill')
    const stroke = getNodePaintValue(node, 'stroke')
    return !!(fill || stroke)
  })
}

const collectOriginalModelColors = (svg) => {
  const ordered = []
  const seen = new Set()
  getRecolorableSvgNodes(svg).forEach((node) => {
    const fill = getNodePaintValue(node, 'fill')
    const stroke = getNodePaintValue(node, 'stroke')
    ;[fill, stroke].forEach((paint) => {
      if (paint && !seen.has(paint)) { seen.add(paint); ordered.push(paint) }
    })
  })
  return ordered
}

const buildOriginalToSelectedColorMap = (originalColors = [], pickedColors = []) => {
  const cleanPicked = (pickedColors || []).map(c => normalizeHex(c.code || c)).filter(Boolean)
  if (!originalColors.length || !cleanPicked.length) return {}
  const limitedPicked = cleanPicked.slice(0, Math.max(1, Math.min(cleanPicked.length, originalColors.length)))
  const map = {}
  originalColors.forEach((originalColor, index) => { map[originalColor] = limitedPicked[index % limitedPicked.length] })
  return map
}
// ============================================================
// HELPER: Hex to RGB object
// ============================================================
const hexToRgbObj = (hex) => {
  if (!hex) return null
  hex = String(hex).replace('#', '').trim()
  if (hex.length === 3) hex = hex.split('').map(c => c + c).join('')
  if (hex.length !== 6) return null
  const num = parseInt(hex, 16)
  if (isNaN(num)) return null
  return {
    r: (num >> 16) & 255,
    g: (num >> 8) & 255,
    b: num & 255
  }
}

// ============================================================
// HELPER: Color distance between two RGB objects
// ============================================================
const rgbDistance = (c1, c2) => {
  if (!c1 || !c2) return 999
  return Math.sqrt(
    Math.pow(c1.r - c2.r, 2) +
    Math.pow(c1.g - c2.g, 2) +
    Math.pow(c1.b - c2.b, 2)
  )
}

// ============================================================
// HELPER: Recolor PNG image via Canvas - pixel by pixel
// ============================================================
const recolorPngViaCanvas = (dataUrl, colorMap) => {
  return new Promise((resolve) => {
    if (!dataUrl || typeof dataUrl !== 'string') {
      resolve(dataUrl)
      return
    }
    if (!dataUrl.startsWith('data:image')) {
      resolve(dataUrl)
      return
    }
    if (!colorMap || !Object.keys(colorMap).length) {
      resolve(dataUrl)
      return
    }

    const img = new Image()
    img.crossOrigin = 'anonymous'

    img.onload = () => {
      try {
        const canvas = document.createElement('canvas')
        canvas.width = img.width
        canvas.height = img.height
        const ctx = canvas.getContext('2d')
        ctx.drawImage(img, 0, 0)

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)
        const data = imageData.data

        // colorMap ki keys ko RGB mein convert karo
        const mappingList = []
        Object.entries(colorMap).forEach(([oldHex, newHex]) => {
          const oldRgb = hexToRgbObj(oldHex)
          const newRgb = hexToRgbObj(newHex)
          if (oldRgb && newRgb) {
            mappingList.push({ oldRgb, newRgb })
          }
        })

        if (!mappingList.length) {
          resolve(dataUrl)
          return
        }

        // Pixel cache - same color dobara process na ho
        const pixelCache = new Map()

        for (let i = 0; i < data.length; i += 4) {
          // Transparent pixels skip karo
          if (data[i + 3] < 30) continue

          const r = data[i]
          const g = data[i + 1]
          const b = data[i + 2]
          const cacheKey = `${r},${g},${b}`

          if (!pixelCache.has(cacheKey)) {
            // Nearest original color dhundo
            let nearestDist = Infinity
            let nearestNewRgb = null

            mappingList.forEach(mapping => {
              const dist = rgbDistance({ r, g, b }, mapping.oldRgb)
              if (dist < nearestDist) {
                nearestDist = dist
                nearestNewRgb = mapping.newRgb
              }
            })

            // Sirf replace karo agar distance 90 se kam ho
            if (nearestDist < 90 && nearestNewRgb) {
              pixelCache.set(cacheKey, nearestNewRgb)
            } else {
              pixelCache.set(cacheKey, null)
            }
          }

          const replacement = pixelCache.get(cacheKey)
          if (replacement) {
            data[i] = replacement.r
            data[i + 1] = replacement.g
            data[i + 2] = replacement.b
          }
        }

        ctx.putImageData(imageData, 0, 0)
        resolve(canvas.toDataURL('image/png', 1.0))
      } catch (e) {
        console.error('Canvas recolor error:', e)
        resolve(dataUrl)
      }
    }

    img.onerror = () => {
      resolve(dataUrl)
    }

    img.src = dataUrl
  })
}

// ============================================================
// HELPER: SVG ke andar style attribute update karo
// ============================================================
const recolorStyleAttribute = (styleStr, colorMap) => {
  if (!styleStr || !Object.keys(colorMap).length) return styleStr

  let updated = styleStr

  // fill in style
  const fillMatch = updated.match(/fill\s*:\s*([^;]+)/i)
  if (fillMatch) {
    const hex = colorToHex(fillMatch[1].trim())
    if (hex && colorMap[hex]) {
      updated = updated.replace(fillMatch[0], `fill:${colorMap[hex]}`)
    }
  }

  // stroke in style
  const strokeMatch = updated.match(/stroke\s*:\s*([^;]+)/i)
  if (strokeMatch) {
    const hex = colorToHex(strokeMatch[1].trim())
    if (hex && colorMap[hex]) {
      updated = updated.replace(strokeMatch[0], `stroke:${colorMap[hex]}`)
    }
  }

  // stop-color in style
  const stopMatch = updated.match(/stop-color\s*:\s*([^;]+)/i)
  if (stopMatch) {
    const hex = colorToHex(stopMatch[1].trim())
    if (hex && colorMap[hex]) {
      updated = updated.replace(stopMatch[0], `stop-color:${colorMap[hex]}`)
    }
  }

  return updated
}

// ============================================================
// HELPER: SVG DOM element ke saare colors recolor karo
// ============================================================
const recolorAllSvgElements = (svgRoot, colorMap) => {
  if (!svgRoot || !Object.keys(colorMap).length) return

  const allEls = svgRoot.querySelectorAll('*')

  allEls.forEach(el => {
    // fill attribute
    const fill = el.getAttribute('fill')
    if (fill && fill !== 'none' && !fill.startsWith('url(')) {
      const hex = colorToHex(fill)
      if (hex && colorMap[hex]) {
        el.setAttribute('fill', colorMap[hex])
      }
    }

    // stroke attribute
    const stroke = el.getAttribute('stroke')
    if (stroke && stroke !== 'none' && !stroke.startsWith('url(')) {
      const hex = colorToHex(stroke)
      if (hex && colorMap[hex]) {
        el.setAttribute('stroke', colorMap[hex])
      }
    }

    // stop-color attribute
    const stopColor = el.getAttribute('stop-color')
    if (stopColor) {
      const hex = colorToHex(stopColor)
      if (hex && colorMap[hex]) {
        el.setAttribute('stop-color', colorMap[hex])
      }
    }

    // style attribute
    const style = el.getAttribute('style')
    if (style) {
      const newStyle = recolorStyleAttribute(style, colorMap)
      if (newStyle !== style) {
        el.setAttribute('style', newStyle)
      }
    }
  })
}

// ============================================================
// MAIN: recolorSvgParts — ASYNC version with full mascot/pattern support
// ============================================================
const recolorSvgParts = async (svgText, colors = [], customAppliedName = '') => {
  if (!svgText) return svgText

  const parser = new DOMParser()
  const doc = parser.parseFromString(svgText, 'image/svg+xml')
  const svg = doc.querySelector('svg')
  if (!svg) return svgText

  // ── Step 1: Original colors collect karo aur colorMap banao ──
  const originalColors = collectOriginalModelColors(svg)
  const colorMap = buildOriginalToSelectedColorMap(originalColors, colors)

  if (!Object.keys(colorMap).length && !customAppliedName) {
    return svgText
  }

  // ── Step 2: Basic SVG shapes recolor karo (existing logic) ──
  if (Object.keys(colorMap).length) {
    getRecolorableSvgNodes(svg).forEach((node) => {
      const tag = node.tagName.toLowerCase()

      if (tag === 'stop') {
        const oldStop =
          colorToHex(node.getAttribute('stop-color')) ||
          colorToHex(getStylePropValue(node.getAttribute('style') || '', 'stop-color'))
        if (oldStop && colorMap[oldStop]) {
          node.setAttribute('stop-color', colorMap[oldStop])
          node.setAttribute(
            'style',
            setOrReplaceStyleProp(node.getAttribute('style') || '', 'stop-color', colorMap[oldStop])
          )
        }
        return
      }

      const originalFill = getNodePaintValue(node, 'fill')
      const originalStroke = getNodePaintValue(node, 'stroke')

      if (originalFill && colorMap[originalFill]) {
        if (tag === 'text' || tag === 'tspan')
          updateNodeFillKeepFont(node, colorMap[originalFill])
        else
          setNodePaintValue(node, 'fill', colorMap[originalFill])
      }

      if (originalStroke && colorMap[originalStroke]) {
        setNodePaintValue(node, 'stroke', colorMap[originalStroke])
      }
    })
  }

  // ── Step 3: Pattern defs ke andar SVG elements recolor karo ──
  if (Object.keys(colorMap).length) {
    const patternEls = svg.querySelectorAll('defs pattern')
    patternEls.forEach(pattern => {
      // Pattern ke directly andar SVG tag
      const innerSvg = pattern.querySelector('svg')
      if (innerSvg) {
        recolorAllSvgElements(innerSvg, colorMap)
      }
      // Pattern ke andar directly shapes bhi ho sakti hain
      recolorAllSvgElements(pattern, colorMap)
    })

    // Mascot overlay groups bhi recolor karo
    const appGroups = svg.querySelectorAll('[id^="app-group-"]')
    appGroups.forEach(group => {
      recolorAllSvgElements(group, colorMap)
    })

    // Pattern overlay group
    const patternOverlayGroup = svg.querySelector('#pattern-overlay-group')
    if (patternOverlayGroup) {
      recolorAllSvgElements(patternOverlayGroup, colorMap)
    }

    // Mascot overlay group
    const mascotOverlayGroup = svg.querySelector('#mascot-overlay-group')
    if (mascotOverlayGroup) {
      recolorAllSvgElements(mascotOverlayGroup, colorMap)
    }
  }

  // ── Step 4: PNG images (mascots) recolor karo via Canvas ──
  if (Object.keys(colorMap).length) {
    const imageEls = svg.querySelectorAll('image')
    const imagePromises = []

    imageEls.forEach(imgEl => {
      const href =
        imgEl.getAttribute('href') ||
        imgEl.getAttribute('xlink:href') ||
        ''

      if (
        !href.startsWith('data:image/png') &&
        !href.startsWith('data:image/jpeg') &&
        !href.startsWith('data:image/jpg')
      ) return

      const promise = recolorPngViaCanvas(href, colorMap).then(newDataUrl => {
        if (newDataUrl && newDataUrl !== href) {
          imgEl.setAttribute('href', newDataUrl)
          if (imgEl.getAttribute('xlink:href')) {
            imgEl.setAttribute('xlink:href', newDataUrl)
          }
        }
      })

      imagePromises.push(promise)
    })

    if (imagePromises.length > 0) {
      await Promise.all(imagePromises)
    }
  }

  // ── Step 5: Name apply karo ──
  const finalName = (customAppliedName || '').trim()
  if (finalName) {
    updateSvgNameIfPresent(svg, finalName, getPrimaryAppliedColor())
  }

  return new XMLSerializer().serializeToString(svg)
}

// ============================================================
// buildColoredSvgForModel — await added kyunki recolorSvgParts async hai
// ============================================================
// const buildColoredSvgForModel = async (model) => {
//   if (!model?.id || !model?.front_svg) return

//   if (!isModelInAppliedScope(model)) {
//     const nextCache = { ...coloredSvgCache.value }
//     delete nextCache[model.id]
//     coloredSvgCache.value = nextCache
//     return
//   }

//   try {
//     const response = await fetch(model.front_svg)
//     const svgText = await response.text()

//     // ✅ await lagaya - ab async hai
//     const recolored = await recolorSvgParts(
//       svgText,
//       appliedSelectedColors.value,
//       appliedName.value
//     )

//     const dataUri = svgToDataUri(recolored)
//     coloredSvgCache.value = { ...coloredSvgCache.value, [model.id]: dataUri }
//   } catch (error) {
//     console.error('SVG recolor failed for model:', model.id, error)
//   }
// }
const buildColoredSvgForModel = async (model) => {
  if (!model?.id || !model?.front_svg) return

  if (!isModelInAppliedScope(model)) {
    const nextCache = { ...coloredSvgCache.value }
    delete nextCache[model.id]
    coloredSvgCache.value = nextCache
    return
  }

  try {
    const response = await fetch(model.front_svg)
    const svgText = await response.text()

    const recolored = await recolorSvgParts(
      svgText,
      appliedSelectedColors.value,
      appliedName.value
    )

    // ✅ Font ko Base64 mein embed karo
    const svgWithFonts = await embedFontsInSvgText(recolored)

    const dataUri = svgToDataUri(svgWithFonts)
    coloredSvgCache.value = { ...coloredSvgCache.value, [model.id]: dataUri }

  } catch (error) {
    console.error('SVG recolor failed for model:', model.id, error)
  }
}

// ✅ Naya function — font files fetch karke base64 embed karo
const embedFontsInSvgText = async (svgText) => {
  if (!svgText) return svgText

  const parser = new DOMParser()
  const doc = parser.parseFromString(svgText, 'image/svg+xml')
  const svg = doc.querySelector('svg')
  if (!svg) return svgText

  // SVG mein existing font-face rules dhundo
  let defs = svg.querySelector('defs')
  if (!defs) {
    defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs')
    svg.insertBefore(defs, svg.firstChild)
  }

  // Text elements se used fonts collect karo
  const usedFonts = new Set()
  svg.querySelectorAll('text, tspan').forEach(el => {
    const ff = el.getAttribute('font-family') || el.style?.fontFamily || ''
    if (ff) usedFonts.add(ff.replace(/['"]/g, '').trim())
  })

  if (!usedFonts.size) return svgText

  // Backend fonts mein se matching fonts dhundo aur embed karo
  const backendFonts = window.__allFonts || []

  let fontCSS = ''
  for (const font of backendFonts) {
    const fontFamily = `font_${font.id}`
    if (!usedFonts.has(fontFamily)) continue

    try {
      const fontRes = await fetch(font.file_url)
      const fontBuffer = await fontRes.arrayBuffer()
      const fontBase64 = btoa(
        new Uint8Array(fontBuffer).reduce((data, byte) => data + String.fromCharCode(byte), '')
      )
      fontCSS += `@font-face { font-family: '${fontFamily}'; src: url('data:font/truetype;base64,${fontBase64}') format('truetype'); }\n`
    } catch (e) {
      console.warn('Font embed failed:', fontFamily, e)
    }
  }

  if (fontCSS) {
    // Purani font styles hata do
    defs.querySelectorAll('style[data-embedded-fonts]').forEach(s => s.remove())

    const styleEl = document.createElementNS('http://www.w3.org/2000/svg', 'style')
    styleEl.setAttribute('data-embedded-fonts', '1')
    styleEl.textContent = fontCSS
    defs.insertBefore(styleEl, defs.firstChild)
  }

  return new XMLSerializer().serializeToString(svg)
}

const refreshAllModelPreviews = async () => {
  const targetModels = models.value.filter(m => shouldUseCompositePreview(m))
  const untargetedModels = models.value.filter(m => !shouldUseCompositePreview(m))
  await Promise.all(targetModels.map(buildColoredSvgForModel))
  if (untargetedModels.length) {
    const nextCache = { ...coloredSvgCache.value }
    untargetedModels.forEach(model => { delete nextCache[model.id] })
    coloredSvgCache.value = nextCache
  }
}

const applyFilters = async () => {
  appliedSelectedColors.value = [...selectedColors.value]
  appliedColor.value = selectedColors.value.length > 0 ? selectedColors.value[0] : null
  appliedName.value = customName.value.trim()
  appliedTargetModelName.value = selectedModelName.value

  showColorPopup.value = false
  mobileFilterOpen.value = false

  if (!appliedSelectedColors.value.length && !appliedName.value) {
    coloredSvgCache.value = {}
    clearPreviewState()
    return
  }

  await refreshAllModelPreviews()
  // ✅ Save globally so all pages can access this design
  savePreviewState()
}

const clearFilters = async () => {
  nameSearch.value = ''
  selectedModelName.value = 'all'
  appliedTargetModelName.value = 'all'
  selectedColors.value = []
  appliedSelectedColors.value = []
  appliedColor.value = null
  customName.value = ''
  appliedName.value = ''
  coloredSvgCache.value = {}
  // ✅ Clear global design state
  clearPreviewState()
}

const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value }

const filteredModels = computed(() => {
  let list = models.value.filter(m => !m.is_hidden)  // ← yeh ek line add ki
  if (selectedModelName.value !== 'all') list = list.filter(m => m.model_name === selectedModelName.value)
  if (nameSearch.value.trim()) {
    const q = nameSearch.value.trim().toLowerCase()
    list = list.filter(m => (m.title || '').toLowerCase().includes(q) || (m.model_name || '').toLowerCase().includes(q))
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

const handleCustomizerClick = (modelId) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    window.location.href = `/customize/${modelId}`
  } else {
    pendingModelId.value = modelId
    showLoginModal.value = true
  }
}

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

watch(() => appliedTargetModelName.value, async () => {
  if (!models.value.length) return
  if (!appliedSelectedColors.value.length && !appliedName.value) { coloredSvgCache.value = {}; return }
  await refreshAllModelPreviews()
})

watch(() => appliedSelectedColors.value.map(c => c.id).join(','), async () => {
  if (!models.value.length) return
  if (!appliedSelectedColors.value.length && !appliedName.value) { coloredSvgCache.value = {}; return }
  await refreshAllModelPreviews()
})

watch(() => appliedName.value, async () => {
  if (!models.value.length) return
  if (!appliedSelectedColors.value.length && !appliedName.value) { coloredSvgCache.value = {}; return }
  await refreshAllModelPreviews()
})

watch(() => models.value.length, async (count) => {
  if (!count) return
  if (!appliedSelectedColors.value.length && !appliedName.value) return
  await refreshAllModelPreviews()
})

onMounted(async () => {
  // ✅ Backend fonts pehle load karo
  try {
    const fontsRes = await axios.get('/api/fonts')
    window.__allFonts = fontsRes.data
  } catch (e) {
    window.__allFonts = []
  }

  restorePreviewState()
  await Promise.all([fetchCategory(), fetchProducts(), loadModels(), fetchColors()])
  if (appliedSelectedColors.value.length || appliedName.value) {
    await refreshAllModelPreviews()
  }
})
</script>


<style scoped>
/* All styles same as original — no changes needed */
.container-fluid{padding-bottom: 60px;}
.page-title {font-size: clamp(18px, 4vw, 28px);letter-spacing: 1px;}
.models-wrapper {width: 100%;transition: padding-right 0.35s ease;box-sizing: border-box;}
.models-wrapper.sidebar-open {padding-right: 268px;}
.sidebar-toggle-btn {position: fixed;right: 0;top: 50%;transform: translateY(-50%);width: 40px;height: 60px;background: #111;color: #fff;border: none;border-radius: 8px 0 0 8px;display: flex;align-items: center;justify-content: center;font-size: 17px;cursor: pointer;z-index: 1100;box-shadow: -3px 0 10px rgba(0,0,0,0.18);transition: background 0.2s, right 0.35s ease;}
.sidebar-toggle-btn:hover {background: #333;}
.sidebar-toggle-btn.open {right: 260px;border-radius: 8px 0 0 8px;}
.desktop-sidebar {position: fixed;right: -270px;top: 50px;width: 260px;height: 100vh;overflow-y: auto;background: #fff;border-left: 1px solid #e2e2e2;box-shadow: -6px 0 24px rgba(0,0,0,0.10);transition: right 0.35s ease;z-index: 1050;padding: 70px 0 40px;box-sizing: border-box;}
.desktop-sidebar.open {right: 0;}
.mobile-filter-btn {display: none;align-items: center;gap: 8px;width: 100%;padding: 10px 14px;background: #000;color: #fff;border: none;border-radius: 8px;font-size: 14px;font-weight: 600;cursor: pointer;margin-bottom: 10px;}
.mobile-filter-btn i {font-size: 15px;}
.mobile-filter-btn span {flex: 1;text-align: left;}
.mobile-sidebar {display: none;margin-bottom: 12px;}
.mobile-sidebar.mobile-open {display: block;}
.sidebar-inner {padding: 16px;}
.sidebar-title {font-size: 13px;font-weight: 800;text-align: center;letter-spacing: 1px;color: #111;margin-bottom: 14px;}
.filter-section {margin-bottom: 14px;}
.filter-label {font-size: 11px;font-weight: 700;letter-spacing: .8px;color: #555;text-transform: uppercase;margin-bottom: 8px;display: block;}
.sidebar-divider {border-top: 1px solid #ebebeb;margin: 12px 0;}
.search-box {display: flex;align-items: center;border: 1px solid #ddd;border-radius: 6px;padding: 0 10px;background: #fafafa;}
.search-icon {color: #999;font-size: 13px;margin-right: 6px;}
.search-input {border: none;background: transparent;outline: none;font-size: 13px;width: 100%;height: 34px;color: #333;}
.search-input::placeholder {color: #bbb;}
.color-swatches {display: flex;flex-wrap: wrap;gap: 5px;align-items: center;}
.swatch {width: 26px;height: 26px;border-radius: 4px;cursor: pointer;border: 2px solid transparent;transition: .15s;}
.swatch.selected {border-color: #000;box-shadow: 0 0 0 1px #fff inset;}
.swatch-add,.swatch-filter {width: 26px;height: 26px;border-radius: 4px;border: 1.5px solid #ccc;background: #fff;display: flex;align-items: center;justify-content: center;cursor: pointer;font-size: 14px;color: #555;transition: .15s;}
.swatch-add:hover,.swatch-filter:hover {border-color: #000;color: #000;}
.btn-apply {background: #000;color: #fff;border: none;border-radius: 6px;padding: 8px 16px;font-size: 12px;font-weight: 700;cursor: pointer;transition: .15s;}
.btn-apply:hover {background: #333;}
.btn-clear {background: transparent;color: #555;border: 1px solid #ccc;border-radius: 6px;padding: 8px 10px;font-size: 12px;font-weight: 600;cursor: pointer;transition: .15s;}
.btn-clear:hover {border-color: #000;color: #000;}
.filter-accordion-label {display: flex;justify-content: space-between;align-items: center;font-size: 12px;font-weight: 700;letter-spacing: .6px;color: #333;cursor: pointer;padding: 6px 0;border-bottom: 1px solid #ebebeb;text-transform: uppercase;}
.acc-arrow {transition: transform .25s;display: flex;align-items: center;}
.acc-arrow.open {transform: rotate(180deg);}
.acc-body {padding-top: 6px;}
.filter-option {padding: 5px 8px;font-size: 13px;color: #444;border-radius: 5px;cursor: pointer;transition: .15s;}
.filter-option:hover {background: #f5f5f5;}
.filter-option.active {background: #000;color: #fff;font-weight: 600;}
.acc-slide-enter-active,.acc-slide-leave-active {transition: all .25s ease;overflow: hidden;}
.acc-slide-enter-from,.acc-slide-leave-to {max-height: 0;opacity: 0;}
.acc-slide-enter-to,.acc-slide-leave-from {max-height: 400px;opacity: 1;}
.select-design-heading {font-size: clamp(14px, 2.5vw, 23px);font-weight: 800;letter-spacing: 2px;color: #111;margin-bottom: 16px;}
.model-group-heading {text-align: center;position: relative;margin-bottom: 16px;margin-top: 10px;}
.model-group-heading::before,.model-group-heading::after {content: '';position: absolute;top: 50%;width: 38%;height: 1px;background: #ccc;}
.model-group-heading::before {left: 0;}
.model-group-heading::after {right: 0;}
.model-group-heading span {font-size: clamp(12px, 1.8vw, 20px);font-weight: 700;background: #fff;padding: 0 12px;position: relative;z-index: 1;letter-spacing: .5px;}
.model-grid {display: grid;grid-template-columns: repeat(6, 1fr);gap: 40px;transition: grid-template-columns 0.35s ease, gap 0.35s ease;}
.models-wrapper.sidebar-open .model-grid {grid-template-columns: repeat(5, 1fr);}
.model-col {min-width: 0;}
.model-card {background: #fff;display: flex;flex-direction: column;width: 100%;}
.card-image-wrapper {width: 100%;aspect-ratio: 4 / 4;background: #eee;position: relative;overflow: hidden;display: flex;align-items: center;justify-content: center;border: 1px solid #e5e5e5;cursor: pointer;transition: opacity 0.2s;}
.card-image-wrapper:hover {opacity: 0.88;}
.model-card-info {background: #eee;display: flex;justify-content: space-between;align-items: center;padding: 6px 8px;gap: 4px;flex-wrap: wrap;}
.model-card-title {font-size: clamp(9px, 1vw, 17px);color: #000000;font-weight: 400;line-height: 1.3;}
.model-card-price {font-size: clamp(9px, 0.9vw, 17px);color: #000;font-weight: 600;white-space: nowrap;}
.btn-customize-always {width: 100%;background: #000;color: #fff;border: none;border-radius: 0;height: clamp(26px, 2.8vw, 38px);font-size: clamp(8px, 0.9vw, 12px);font-weight: 700;letter-spacing: 0.5px;text-transform: uppercase;cursor: pointer;transition: background 0.15s;margin-top: auto;}
.btn-customize-always:hover {background: #222;}
.model-thumb {position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);width: 100%;height: 100%;object-fit: contain;z-index: 1;}
.img-layer {position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);height: 100%;width: 100%;object-fit: contain;}
.colored-base {z-index: 2;}
.black {z-index: 5;mix-blend-mode: screen;pointer-events: none;}
.white {z-index: 4;mix-blend-mode: multiply;pointer-events: none;}
.svg {z-index: 3;}
.product-card {border-radius: 12px;overflow: hidden;transition: .35s ease;}
.product-card:hover {transform: translateY(-4px);box-shadow: 0 10px 20px rgba(0,0,0,.13);}
.product-inner {display: flex;height: clamp(130px, 20vw, 230px);}
.product-name {font-size: clamp(10px, 1.3vw, 17px);}
.product-price {font-size: clamp(10px, 1.3vw, 17px);font-weight: 700;}
.color-strip {display: flex;flex-direction: column;gap: 3px;padding: 5px 3px;background: #f5f5f5;border-right: 1px solid #ebebeb;width: clamp(24px, 3.5vw, 34px);flex-shrink: 0;overflow: hidden;}
.color-box {width: clamp(18px, 2.5vw, 26px);height: clamp(18px, 2.5vw, 26px);border-radius: 4px;border: 2px solid transparent;overflow: hidden;cursor: pointer;position: relative;background: #fff;transition: border-color .15s;flex-shrink: 0;}
.color-box.active {border-color: #000;}
.color-box:hover {border-color: #888;}
.color-box img {width: 100%;height: 100%;object-fit: contain;padding: 2px;}
.color-dot {position: absolute;bottom: 2px;right: 2px;width: 5px;height: 5px;border-radius: 50%;border: 1px solid rgba(0,0,0,.2);}
.product-img-wrapper {background: #fff;display: flex;align-items: center;justify-content: center;overflow: hidden;flex: 1;}
.product-img {width: 90%;height: 95%;object-fit: contain;}
.color-swap-enter-active,.color-swap-leave-active {transition: opacity .18s ease;}
.color-swap-enter-from,.color-swap-leave-to {opacity: 0;}
.color-popup-overlay {position: fixed;inset: 0;background: rgba(0,0,0,.5);display: flex;align-items: center;justify-content: center;z-index: 9200;padding: 16px;}
.color-popup-box {background: #fff;border-radius: 14px;padding: 20px;max-width: 480px;width: 100%;position: relative;max-height: 88vh;overflow-y: auto;box-shadow: 0 20px 60px rgba(0,0,0,.25);}
.popup-close {position: absolute;top: 12px;right: 12px;width: 30px;height: 30px;background: #f0f0f0;border: none;border-radius: 50%;display: flex;align-items: center;justify-content: center;cursor: pointer;font-size: 12px;transition: .2s;}
.popup-close:hover {background: #000;color: #fff;}
.popup-title {font-size: 15px;font-weight: 700;margin-bottom: 4px;}
.popup-sub {font-size: 11px;color: #e53e3e;margin-bottom: 14px;}
.popup-colors-grid {display: flex;flex-wrap: wrap;gap: 6px;margin-bottom: 14px;}
.popup-color-cell {width: clamp(34px, 9vw, 44px);height: clamp(34px, 9vw, 44px);border-radius: 6px;cursor: pointer;border: 2px solid transparent;position: relative;display: flex;align-items: flex-end;justify-content: center;transition: .15s;overflow: hidden;}
.popup-color-cell:hover {transform: scale(1.08);border-color: rgba(0,0,0,.3);}
.popup-color-cell.selected {border-color: #000;box-shadow: 0 0 0 2px #fff inset;}
.popup-color-label {font-size: 7px;font-weight: 700;color: #fff;text-shadow: 0 1px 2px rgba(0,0,0,.8);background: rgba(0,0,0,.3);width: 100%;text-align: center;padding: 1px 0;}
.popup-check {position: absolute;top: 3px;right: 3px;font-size: 12px;color: #fff;text-shadow: 0 1px 3px rgba(0,0,0,.8);}
.popup-footer {text-align: right;}
.pm-overlay {position: fixed;inset: 0;background: rgba(0,0,0,.6);display: flex;align-items: center;justify-content: center;z-index: 9100;padding: 12px;}
.pm-box {background: #fff;border-radius: 16px;max-width: 640px;width: 100%;overflow: hidden;position: relative;box-shadow: 0 20px 60px rgba(0,0,0,.25);max-height: 92vh;overflow-y: auto;}
.pm-close {position: absolute;top: 10px;right: 10px;width: 32px;height: 32px;background: #f0f0f0;border: none;border-radius: 50%;display: flex;align-items: center;justify-content: center;cursor: pointer;font-size: 13px;z-index: 10;transition: .2s;}
.pm-close:hover {background: #000;color: #fff;}
.pm-layout {display: grid;grid-template-columns: 1fr 1.2fr;}
.pm-img-side {background: #f5f5f5;min-height: 220px;position: relative;display: flex;align-items: center;justify-content: center;}
.pm-main-img {max-width: 82%;max-height: 240px;object-fit: contain;position: relative;z-index: 2;}
.pm-layer {position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);max-height: 78%;object-fit: contain;}
.pm-layer.white {z-index: 3;mix-blend-mode: multiply;}
.pm-layer.black {z-index: 4;mix-blend-mode: screen;}
.pm-layer.svg {z-index: 1;}
.pm-info-side {padding: clamp(14px, 3vw, 28px) clamp(12px, 2.5vw, 22px);overflow-y: auto;max-height: 520px;}
.pm-title {font-size: clamp(14px, 2.5vw, 18px);font-weight: 700;margin-bottom: 4px;}
.pm-price {font-size: clamp(16px, 3vw, 24px);font-weight: 800;margin-bottom: 0;}
.pm-hr {border: none;border-top: 1px solid #ebebeb;margin: 12px 0;}
.pm-group {margin-bottom: 16px;}
.pm-label {display: block;font-size: 11px;font-weight: 700;text-transform: uppercase;letter-spacing: .8px;color: #555;margin-bottom: 8px;}
.pm-sizes {display: flex;flex-wrap: wrap;gap: 4px;}
.pm-sz {min-width: 36px;height: 34px;padding: 0 6px;border: 1.5px solid #e0e0e0;background: #fff;border-radius: 6px;font-size: clamp(10px, 1.5vw, 12px);font-weight: 700;cursor: pointer;transition: .15s;font-family: inherit;}
.pm-sz:hover {border-color: #000;}
.pm-sz.selected {background: #000;color: #fff;border-color: #000;}
.pm-other {background: #f5f5f5;border-style: dashed;}
.pm-other.selected {background: #000;color: #fff;border-style: solid;}
.pm-custom-row {display: flex;gap: 6px;margin-top: 8px;}
.pm-custom-input {flex: 1;height: 36px;padding: 0 10px;border: 1.5px solid #e0e0e0;border-radius: 8px;font-size: 13px;font-family: inherit;transition: .2s;}
.pm-custom-input:focus {outline: none;border-color: #000;}
.pm-custom-btn {height: 36px;padding: 0 12px;background: #000;color: #fff;border: none;border-radius: 8px;font-size: 13px;font-weight: 700;cursor: pointer;}
.pm-confirmed {font-size: 12px;color: #166534;margin-top: 6px;margin-bottom: 0;}
.pm-warn {font-size: 12px;color: #e53e3e;margin-bottom: 8px;}
.pm-qty {display: flex;align-items: center;width: fit-content;border: 1.5px solid #e0e0e0;border-radius: 8px;overflow: hidden;}
.pm-qty-btn {width: 36px;height: 36px;border: none;background: #fff;font-size: 17px;cursor: pointer;transition: .2s;}
.pm-qty-btn:hover {background: #f5f5f5;}
.pm-qty-val {width: 44px;text-align: center;font-size: 14px;font-weight: 700;border-left: 1.5px solid #e0e0e0;border-right: 1.5px solid #e0e0e0;height: 36px;display: flex;align-items: center;justify-content: center;}
.pm-actions {display: grid;grid-template-columns: 1fr 1fr;gap: 8px;}
.pm-cart-btn,.pm-buy-btn {height: 44px;border: none;border-radius: 8px;font-size: 13px;font-weight: 700;cursor: pointer;transition: .2s;font-family: inherit;display: flex;align-items: center;justify-content: center;gap: 6px;}
.pm-cart-btn {background: #000;color: #fff;}
.pm-cart-btn:hover:not(:disabled) {background: #333;}
.pm-buy-btn {background: #fff;color: #000;border: 1.5px solid #000;}
.pm-buy-btn:hover:not(:disabled) {background: #000;color: #fff;}
.pm-cart-btn:disabled,.pm-buy-btn:disabled {opacity: .35;cursor: not-allowed;}
.modal-pop-enter-active,.modal-pop-leave-active {transition: all .28s ease;}
.modal-pop-enter-from,.modal-pop-leave-to {opacity: 0;}
.modal-pop-enter-from .pm-box,.modal-pop-enter-from .color-popup-box {transform: scale(.96) translateY(12px);}
.login-icon-wrap {width: 70px;height: 70px;background: #f0f0f0;border-radius: 50%;display: flex;align-items: center;justify-content: center;}
.cat-toast {position: fixed;bottom: 20px;right: 16px;background: #111;color: #fff;padding: 10px 16px;border-radius: 8px;font-size: 13px;font-weight: 600;z-index: 9999;pointer-events: none;max-width: calc(100vw - 32px);word-break: break-word;}
.toast-slide-enter-active,.toast-slide-leave-active {transition: all .3s ease;}
.toast-slide-enter-from,.toast-slide-leave-to {opacity: 0;transform: translateY(14px);}
@media (max-width: 1199px) {
  .desktop-sidebar {display: none !important;}
  .sidebar-toggle-btn {display: none !important;}
  .mobile-filter-btn {display: flex;}
  .models-wrapper {padding-right: 0 !important;}
  .model-grid {grid-template-columns: repeat(4, 1fr) !important;gap: 10px;}
}
@media (max-width: 991px) {
  .model-grid {grid-template-columns: repeat(3, 1fr) !important;gap: 8px;}
  .pm-layout {grid-template-columns: 1fr;}
  .pm-img-side {min-height: 180px;max-height: 220px;}
  .pm-info-side {max-height: none;padding: 16px;}
  .pm-main-img {max-height: 160px;}
}
@media (max-width: 767px) {
  .model-grid {grid-template-columns: repeat(2, 1fr) !important;gap: 8px;}
  .model-card-title {font-size: 12px;}
  .model-card-price {font-size: 11px;}
  .btn-customize-always {font-size: 10px;height: 30px;}
  .pm-actions {grid-template-columns: 1fr;}
  .product-inner {height: clamp(120px, 40vw, 180px);}
}
@media (max-width: 480px) {
  .model-grid {grid-template-columns: repeat(2, 1fr) !important;gap: 6px;}
  .pm-sizes {gap: 3px;}
  .pm-sz {min-width: 30px;height: 30px;padding: 0 4px;font-size: 10px;}
  .pm-actions {grid-template-columns: 1fr;}
  .pm-cart-btn,.pm-buy-btn {height: 42px;}
  .cat-toast {bottom: 14px;right: 8px;left: 8px;font-size: 12px;max-width: none;}
  .select-design-heading {letter-spacing: 1px;}
  .product-inner {height: clamp(110px, 44vw, 150px);}
}
@media (max-width: 360px) {
  .model-grid {grid-template-columns: repeat(2, 1fr) !important;gap: 5px;}
  .btn-customize-always {height: 26px;font-size: 9px;}
  .page-title {font-size: 16px;}
  .product-inner {height: 110px;}
  .model-card-title {font-size: 10px;}
  .model-card-price {font-size: 10px;}
}
</style>
