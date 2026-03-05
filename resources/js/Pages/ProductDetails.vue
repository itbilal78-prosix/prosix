<template>
  <div class="page-wrapper">
    <nav-component />
    <breadcrumb-component />

    <div class="product-container">
      <div class="product-layout">

        <!-- ══ LEFT: Gallery ══ -->
        <div class="product-visual">
          <div class="gallery-wrap">

            <!-- Left thumbnails -->
            <div class="thumbnails" v-if="!isModel && thumbList.length > 1">
              <div
                v-for="(thumb, ti) in thumbList"
                :key="ti"
                class="thumb"
                :class="{ active: activeThumbIdx === ti }"
                @click="selectThumb(ti)"
              >
                <img :src="thumb.src" :alt="thumb.label" />
                <span v-if="thumb.colorHex" class="thumb-dot" :style="{ background: thumb.colorHex }"></span>
              </div>
            </div>

            <div :class="['main-image-wrapper', { 'is-model': isModel }]" ref="imageContainer">
              <span class="badge-new" v-if="product.is_new">NEW</span>

              <!-- Out of stock overlay -->
              <div class="oos-overlay" v-if="!isModel && !stockAvailable">
                <span>Out of Stock</span>
              </div>

              <template v-if="isModel">
                <img :src="product.image" class="main-product-image" alt="Product" />
              </template>
              <template v-else>
                <transition name="img-fade" mode="out-in">
                  <img :key="displayImage" :src="displayImage" class="main-product-image" alt="Product" />
                </transition>
              </template>

              <button class="img-action-btn wishlist-btn" @click="toggleLike"
                :class="{ active: cartStore.isLiked(product.id) }">
                <i :class="cartStore.isLiked(product.id) ? 'bi bi-heart-fill' : 'bi bi-heart'"></i>
              </button>
              <button class="img-action-btn share-btn" @click="handleShare">
                <i class="bi bi-share"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- ══ RIGHT: Product Info ══ -->
        <div class="product-info">
          <h1 class="product-title">{{ product.name }}</h1>
          <p class="product-subtitle">{{ product.type }}</p>

          <div class="price-row">
            <span class="product-price">${{ formatPrice(product.price) }}</span>
            <span class="stock-badge stock-in" v-if="stockAvailable">
              <i class="bi bi-check-circle-fill"></i> In Stock
              <span v-if="product.stock_quantity" style="opacity:.7;font-weight:500"> ({{ product.stock_quantity }} left)</span>
            </span>
            <span class="stock-badge stock-out" v-else>
              <i class="bi bi-x-circle-fill"></i> Out of Stock
            </span>
          </div>

          <!-- Shipping info -->
          <p class="delivery-info" v-if="product.shipping_enabled">
            <i class="bi bi-truck me-1"></i>
            <span v-if="!product.shipping_cost || product.shipping_cost == 0"><strong>Free Shipping</strong></span>
            <span v-else>Shipping: <strong>${{ formatPrice(product.shipping_cost) }}</strong></span>
            <span v-if="product.free_shipping_above">&nbsp;· Free above <strong>${{ formatPrice(product.free_shipping_above) }}</strong></span>
          </p>
          <hr class="divider" />

          <!-- Color Swatches -->
          <div class="option-group" v-if="!isModel && productColors.length">
            <div class="option-header">
              <label class="option-label">Color</label>
              <span class="selected-value">{{ selectedColorIdx === -1 ? 'None' : productColors[selectedColorIdx]?.name }}</span>
            </div>
            <div class="color-swatches">
              <button
                v-for="(c, ci) in productColors"
                :key="ci"
                class="color-swatch"
                :class="{ selected: selectedColorIdx === ci, 'white-swatch': c.hex === '#ffffff' || c.hex === '#FFFFFF' }"
                :style="{ background: c.hex }"
                :title="c.name"
                @click="selectColor(ci)"
              ></button>
            </div>
          </div>

          <!-- Sizes -->
          <div class="option-group">
            <div class="option-header">
              <label class="option-label">Select Size</label>
              <span class="size-chart-link" @click="openTab('sizechart')">Size Chart</span>
            </div>
            <div class="size-selector">
              <template v-if="productSizes.length">
                <button
                  v-for="sz in allSizes" :key="sz"
                  :class="['size-btn',
                    { selected: selectedSize === sz },
                    { unavailable: !productSizes.includes(sz) }
                  ]"
                  :disabled="!productSizes.includes(sz)"
                  @click="productSizes.includes(sz) && pickSize(sz)"
                >
                  {{ sz }}
                  <span class="sz-cross" v-if="!productSizes.includes(sz)">×</span>
                </button>
              </template>
              <template v-else>
                <button v-for="sz in allSizes" :key="sz"
                  :class="['size-btn', { selected: selectedSize === sz }]"
                  @click="pickSize(sz)">{{ sz }}</button>
              </template>
              <button class="size-btn other-btn" :class="{ selected: selectedSize === '__other__' }" @click="pickSize('__other__')">Other +</button>
            </div>
            <div v-if="selectedSize === '__other__'" class="custom-size-row">
              <input v-model="customSizeVal" type="text" class="custom-size-input" placeholder="e.g. 3XL, 42 chest..." @keyup.enter="confirmCustomSize" />
              <button class="custom-size-confirm" @click="confirmCustomSize">✓</button>
            </div>
            <p v-if="selectedSize === '__other__' && customSizeOk" class="custom-confirmed">✅ Custom: <strong>{{ customSizeVal }}</strong></p>
          </div>

          <!-- Quantity -->
          <div class="option-group">
            <label class="option-label">Quantity</label>
            <div class="quantity-selector">
              <button class="qty-btn" @click="decrementQty" :disabled="quantity <= 1">−</button>
              <input type="number" class="qty-input" v-model.number="quantity" min="1" :max="qtyMax || undefined" />
              <button class="qty-btn" @click="incrementQty" :disabled="qtyMax !== null && quantity >= qtyMax">+</button>
            </div>
            <p v-if="qtyMax" class="pf-hint" style="margin-top:4px;font-size:11px;color:#999">Max {{ qtyMax }} available</p>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons">
            <button class="btn-add-cart" @click="addToCart" :disabled="!effectiveSize || !stockAvailable">
              <i class="bi bi-cart-plus"></i>
              {{ !stockAvailable ? 'Out of Stock' : 'Add to Cart' }}
            </button>
            <button class="btn-checkout" @click="router.push('/checkout')" :disabled="cartStore.items.length === 0">Buy Now</button>
          </div>
          <p v-if="!effectiveSize && stockAvailable" style="font-size:12px;color:#e53e3e;margin-top:-10px;margin-bottom:14px">⚠ Select a size to continue</p>

          <!-- Features -->
          <div class="product-features">
            <div class="feature-item"><i class="bi bi-truck"></i>
              <div><strong>{{ (!product.shipping_cost || product.shipping_cost == 0) ? 'Free Delivery' : 'Shipping Available' }}</strong>
                <p>{{ product.free_shipping_above ? `Free above $${formatPrice(product.free_shipping_above)}` : 'Standard & Express options' }}</p>
              </div>
            </div>
            <div class="feature-item"><i class="bi bi-arrow-clockwise"></i><div><strong>60 Day Return</strong><p>Free returns via dropoff</p></div></div>
            <div class="feature-item"><i class="bi bi-shield-check"></i><div><strong>Secure Payment</strong><p>Multiple options</p></div></div>
          </div>
        </div>
      </div>

      <!-- ══ TABS ══ -->
      <div class="tabs-section" ref="tabsRef">
        <div class="tabs-nav">
          <button v-for="tab in tabs" :key="tab.id"
            class="tab-btn" :class="{ active: activeTab === tab.id }"
            @click="openTab(tab.id)">
            {{ tab.id === 'reviews' ? `Reviews (${allReviews.length})` : tab.label }}
          </button>
        </div>

        <!-- Description -->
        <div class="tab-panel" :class="{ active: activeTab === 'description' }">
          <div class="desc-grid">
            <div class="desc-text">
              <h3>Product Details</h3>
              <p>{{ product.description || 'Premium quality product designed for performance and style.' }}</p>
            </div>
            <div>
              <h3 style="font-size:15px;font-weight:700;margin-bottom:14px">Specifications</h3>
              <table class="spec-table">
                <tr><td>Type</td><td>{{ product.type || 'Sportswear' }}</td></tr>
                <tr><td>Fit</td><td>Athletic Regular Fit</td></tr>
                <tr><td>Care</td><td>Machine Wash Cold</td></tr>
                <tr v-if="productSizes.length"><td>Available Sizes</td><td>{{ productSizes.join(', ') }}</td></tr>
                <tr v-if="productColors.length"><td>Colors</td><td>{{ productColors.map(c=>c.name).join(', ') }}</td></tr>
                <tr v-if="product.stock_quantity"><td>In Stock</td><td>{{ product.stock_quantity }} units</td></tr>
              </table>
            </div>
          </div>
        </div>

        <!-- Size Chart -->
        <div class="tab-panel" :class="{ active: activeTab === 'sizechart' }">
          <h3 class="tab-heading">Size Chart</h3>
          <template v-if="product.size_chart_image">
            <div class="sc-img-wrap">
              <img :src="product.size_chart_image" alt="Size Chart" class="sc-img" />
            </div>
          </template>
          <template v-else>
            <div style="overflow-x:auto">
              <table class="size-chart-table">
                <thead><tr><th>Size</th><th>Chest (in)</th><th>Waist (in)</th><th>Hip (in)</th><th>Height</th></tr></thead>
                <tbody>
                  <tr v-for="row in sizeChartData" :key="row.size"
                    :class="{ highlighted: effectiveSize === row.size, 'row-unavail': productSizes.length && !productSizes.includes(row.size) }">
                    <td><strong>{{ row.size }}{{ effectiveSize === row.size ? ' ✓' : '' }}</strong>
                      <span v-if="productSizes.length && !productSizes.includes(row.size)" style="font-size:10px;color:#e53e3e;margin-left:4px">N/A</span>
                    </td>
                    <td>{{ row.chest }}</td><td>{{ row.waist }}</td><td>{{ row.hip }}</td><td>{{ row.height }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>
        </div>

        <!-- Shipping -->
        <div class="tab-panel" :class="{ active: activeTab === 'shipping' }">
          <div class="shipping-grid">
            <div class="ship-card"><i class="bi bi-lightning-charge"></i><h4>Express Delivery</h4><p>1–2 business days.</p></div>
            <div class="ship-card"><i class="bi bi-truck"></i><h4>Standard Delivery</h4>
              <p>
                <template v-if="!product.shipping_cost || product.shipping_cost == 0">Free shipping</template>
                <template v-else>${{ formatPrice(product.shipping_cost) }} flat rate</template>
                <template v-if="product.free_shipping_above"> · Free above ${{ formatPrice(product.free_shipping_above) }}</template>
              </p>
            </div>
            <div class="ship-card"><i class="bi bi-arrow-left-right"></i><h4>60-Day Returns</h4><p>Full refund. Free drop-off.</p></div>
          </div>
        </div>

        <!-- Reviews -->
        <div class="tab-panel" :class="{ active: activeTab === 'reviews' }">
          <div class="reviews-summary" v-if="allReviews.length">
            <div class="rating-big">
              <div class="rating-num">{{ avgRating }}</div>
              <div class="stars-row"><span v-for="s in 5" :key="s" :style="{ color: s <= Math.round(avgRatingRaw) ? '#f59e0b' : '#ddd' }">★</span></div>
              <div class="rating-count">{{ allReviews.length }} reviews</div>
            </div>
            <div class="rating-bars">
              <div class="rating-bar-row" v-for="bar in computedBars" :key="bar.star">
                <span class="bar-label">{{ bar.star }}★</span>
                <div class="bar-track"><div class="bar-fill" :style="{ width: bar.pct + '%' }"></div></div>
                <span class="bar-count">{{ bar.count }}</span>
              </div>
            </div>
          </div>
          <div class="review-form-wrap">
            <h3 class="review-form-title"><i :class="editingReviewId ? 'bi bi-pencil' : 'bi bi-pencil-square'"></i> {{ editingReviewId ? 'Edit Your Review' : 'Write a Review' }}</h3>
            <div class="review-form">
              <div class="form-group">
                <label>Your Rating *</label>
                <div class="star-picker">
                  <span v-for="s in 5" :key="s" class="star-pick" :class="{ lit: s <= (hoverStar || newReview.stars) }"
                    @mouseenter="hoverStar=s" @mouseleave="hoverStar=0" @click="newReview.stars=s">★</span>
                  <span class="star-label-txt" v-if="hoverStar || newReview.stars">{{ starLabels[hoverStar||newReview.stars] }}</span>
                </div>
              </div>
              <div class="form-row-2">
                <div class="form-group"><label>Your Name *</label><input v-model="newReview.name" type="text" placeholder="e.g. Ahmed Khan" class="form-input" /></div>
                <div class="form-group"><label>Review Title</label><input v-model="newReview.title" type="text" placeholder="e.g. Great quality!" class="form-input" /></div>
              </div>
              <div class="form-group"><label>Your Review *</label><textarea v-model="newReview.text" rows="4" placeholder="Share your experience..." class="form-input form-textarea"></textarea></div>
              <div style="display:flex;gap:10px">
                <button class="submit-btn" @click="submitReview" :disabled="submitting">
                  <span v-if="submitting"><i class="bi bi-arrow-repeat spin-icon"></i> Saving...</span>
                  <span v-else-if="editingReviewId"><i class="bi bi-check-lg"></i> Update Review</span>
                  <span v-else><i class="bi bi-send"></i> Submit Review</span>
                </button>
                <button v-if="editingReviewId" class="cancel-edit-btn" @click="cancelEdit">Cancel</button>
              </div>
            </div>
          </div>
          <div v-if="allReviews.length">
            <h4 class="reviews-list-title">Customer Reviews ({{ allReviews.length }})</h4>
            <div class="review-cards">
              <div class="review-card" v-for="r in allReviews" :key="r.id">
                <div class="review-header">
                  <div class="reviewer-left">
                    <div class="reviewer-avatar">{{ r.name.charAt(0).toUpperCase() }}</div>
                    <div><div class="reviewer-name">{{ r.name }}<span class="review-title-inline" v-if="r.title"> — {{ r.title }}</span></div><div class="review-date">{{ r.date }}</div></div>
                  </div>
                  <div style="display:flex;gap:6px">
                    <button class="review-action-btn edit-btn" @click="startEdit(r)"><i class="bi bi-pencil"></i></button>
                    <button class="review-action-btn delete-btn" @click="deleteReview(r.id)"><i class="bi bi-trash"></i></button>
                  </div>
                </div>
                <div class="review-stars-row"><span v-for="s in 5" :key="s" :style="{ color: s <= r.stars ? '#f59e0b' : '#ddd' }">★</span></div>
                <p class="review-text">{{ r.text }}</p>
              </div>
            </div>
          </div>
          <div class="empty-reviews" v-else><i class="bi bi-chat-square-text"></i><p>No reviews yet — be the first!</p></div>
        </div>
      </div>

      <!-- Related -->
      <div class="related-section">
        <div class="section-header"><h2>You May Also Like</h2></div>
        <div v-if="relatedLoading" class="loading-state"><div class="spinner"></div></div>
        <div v-else-if="!relatedItems.length" class="empty-state">Nothing found.</div>
        <div v-else>
          <div class="related-grid">
            <template v-if="isModel">
              <div v-for="m in paginatedRelated" :key="m.id" class="model-rel-card" @click="router.push(`/product/${m.id}`)">
                <div class="model-rel-img"><img v-if="m.thumbnail" :src="m.thumbnail" class="model-rel-thumb" /></div>
                <div class="model-rel-body"><h3 class="model-rel-title">{{ m.title }}</h3><p class="model-rel-price">${{ m.price||'0.00' }}</p></div>
              </div>
            </template>
            <template v-else>
              <div v-for="rel in paginatedRelated" :key="rel.id" class="product-card-link" @click="previewRelated(rel)">
                <div class="product-card" :class="{ 'card-active': previewProductId === rel.id }">
                  <div class="card-image"><img :src="rel.image" alt="Product" /><div class="card-overlay"><span class="quick-view-btn">Preview</span></div></div>
                  <div class="card-content"><h3 class="card-title">{{ rel.name }}</h3><p class="card-price">${{ formatPrice(rel.price) }}</p></div>
                </div>
              </div>
            </template>
          </div>
          <div class="slider-dots" v-if="totalPages > 1">
            <button v-for="p in totalPages" :key="p" class="dot" :class="{ active: relatedPage===p-1 }" @click="relatedPage=p-1"></button>
          </div>
          <div class="slider-nav" v-if="totalPages > 1">
            <button class="slider-btn" @click="relatedPage=Math.max(0,relatedPage-1)" :disabled="relatedPage===0"><i class="bi bi-chevron-left"></i></button>
            <button class="slider-btn" @click="relatedPage=Math.min(totalPages-1,relatedPage+1)" :disabled="relatedPage===totalPages-1"><i class="bi bi-chevron-right"></i></button>
          </div>
          <div v-if="previewImage && !isModel" class="preview-banner">
            <div class="preview-banner-inner">
              <i class="bi bi-eye"></i>
              <span>Previewing: <strong>{{ previewProductName }}</strong></span>
              <button class="preview-go-btn" @click="router.push(`/product/${previewProductId}`)">View Full →</button>
              <button class="preview-close-btn" @click="clearPreview"><i class="bi bi-x"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <transition name="toast-slide"><div class="toast-msg" v-if="toastVisible">{{ toastText }}</div></transition>
    <footer-component />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useCartStore } from '@/store/cart'

const router    = useRouter()
const route     = useRoute()
const cartStore = useCartStore()

// ── Core state ──────────────────────────────────────────────────
const product = ref({
  id: null, name: '', type: '', price: '', description: '', image: null,
  is_new: false, in_stock: true, stock_quantity: null,
  shipping_enabled: false, shipping_cost: 0, free_shipping_above: null,
  sizes: [], colors: [], gallery_images: [], size_chart_image: null,
})
const isModel        = ref(false)
const imageContainer = ref(null)
const tabsRef        = ref(null)

// ── Stock ───────────────────────────────────────────────────────
const stockAvailable = computed(() => product.value.in_stock === true || product.value.in_stock === 1)
const qtyMax = computed(() => {
  const q = product.value.stock_quantity
  return (q !== null && q !== undefined && q > 0) ? q : null
})

// ── Colors ──────────────────────────────────────────────────────
const productColors = computed(() => product.value.colors || [])

// -1 = no color selected (show main image)
const selectedColorIdx = ref(-1)

// ── ✅ selectColor: toggle behavior
// - Click unselected color → select it (show color images if available)
// - Click same color again → deselect (go back to main image)
const selectColor = (ci) => {
  if (selectedColorIdx.value === ci) {
    // Deselect — go back to main image
    selectedColorIdx.value = -1
  } else {
    selectedColorIdx.value = ci
  }
  activeThumbIdx.value = 0
  previewImage.value   = null
}

// ── Global gallery ───────────────────────────────────────────────
const globalGallery = computed(() => product.value.gallery_images || [])

// ── ✅ thumbList: Build left-side thumbnails
// Logic:
// 1. Always start with main product image
// 2. Add global gallery images
// 3. If a color is selected AND has images → prepend color thumb + color gallery
//    (replace slot 0 with color thumb if color has one)
const thumbList = computed(() => {
  if (isModel.value) return []

  const list = []

  const color = selectedColorIdx.value >= 0
    ? productColors.value[selectedColorIdx.value]
    : null

  const colorHasImages = color && (color.image || (color.gallery && color.gallery.length > 0))

  if (colorHasImages) {
    // Color thumbnail first
    if (color.image) {
      list.push({ src: color.image, label: color.name, colorHex: color.hex })
    }
    // Color gallery images
    ;(color.gallery || []).forEach((src, i) => {
      list.push({ src, label: `${color.name} #${i + 1}`, colorHex: null })
    })
    // Then main image
    if (product.value.image) {
      list.push({ src: product.value.image, label: 'Main', colorHex: null })
    }
    // Then global gallery
    globalGallery.value.forEach((src, i) => {
      list.push({ src, label: `Gallery #${i + 1}`, colorHex: null })
    })
  } else {
    // No color selected OR selected color has no images
    // Show: main image + global gallery
    if (product.value.image) {
      list.push({ src: product.value.image, label: 'Main', colorHex: null })
    }
    globalGallery.value.forEach((src, i) => {
      list.push({ src, label: `Gallery #${i + 1}`, colorHex: null })
    })
  }

  return list
})

const activeThumbIdx = ref(0)
const selectThumb    = (idx) => {
  activeThumbIdx.value = idx
  previewImage.value   = null
}

// ── ✅ displayImage: main logic
// Default (no color / color has no images) → main product image
// Color selected AND has images → show color image / thumb
// Manual thumb click → show that thumb
// Related product preview → show preview image
const displayImage = computed(() => {
  // Related product preview overrides everything
  if (previewImage.value) return previewImage.value

  // If thumb is manually selected, show it
  if (thumbList.value.length > 0 && activeThumbIdx.value < thumbList.value.length) {
    return thumbList.value[activeThumbIdx.value].src
  }

  // Fallback: main product image
  return product.value.image || ''
})

// ── Related product preview ─────────────────────────────────────
const previewImage       = ref(null)
const previewProductId   = ref(null)
const previewProductName = ref('')

const previewRelated = (rel) => {
  if (previewProductId.value === rel.id) { clearPreview(); return }
  previewImage.value       = rel.image
  previewProductId.value   = rel.id
  previewProductName.value = rel.name
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
const clearPreview = () => {
  previewImage.value       = null
  previewProductId.value   = null
  previewProductName.value = ''
}

// ── Sizes ────────────────────────────────────────────────────────
const allSizes      = ['YXS','YS','YM','YL','YXL','S','M','L','XL','2XL']
const productSizes  = computed(() => product.value.sizes || [])
const selectedSize  = ref('')
const customSizeVal = ref('')
const customSizeOk  = ref(false)
const effectiveSize = computed(() => {
  if (selectedSize.value === '__other__') return customSizeOk.value ? customSizeVal.value.trim() : ''
  return selectedSize.value
})
const pickSize = (sz) => {
  selectedSize.value = sz
  customSizeOk.value = false
  if (sz !== '__other__') customSizeVal.value = ''
}
const confirmCustomSize = () => {
  if (!customSizeVal.value.trim()) { showToast('Please enter a size!'); return }
  customSizeOk.value = true
  showToast(`✅ Size "${customSizeVal.value.trim()}" confirmed!`)
}

// ── Quantity ─────────────────────────────────────────────────────
const quantity     = ref(1)
const incrementQty = () => {
  if (qtyMax.value !== null && quantity.value >= qtyMax.value) {
    showToast(`⚠️ Only ${qtyMax.value} in stock!`); return
  }
  quantity.value++
}
const decrementQty = () => { if (quantity.value > 1) quantity.value-- }

// ── Tabs ─────────────────────────────────────────────────────────
const activeTab = ref('description')
const tabs = [
  { id: 'description', label: 'Description' },
  { id: 'sizechart',   label: 'Size Chart'  },
  { id: 'shipping',    label: 'Shipping & Returns' },
  { id: 'reviews',     label: 'Reviews' },
]
const openTab = (id) => {
  activeTab.value = id
  nextTick(() => tabsRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' }))
}

const sizeChartData = [
  { size:'YXS', chest:'26-27', waist:'22-23', hip:'27-28', height:"4'6-4'8"  },
  { size:'YS',  chest:'28-29', waist:'24-25', hip:'29-30', height:"4'8-4'10" },
  { size:'YM',  chest:'30-31', waist:'26-27', hip:'31-32', height:"4'10-5'0" },
  { size:'YL',  chest:'32-33', waist:'28-29', hip:'33-34', height:"5'0-5'2"  },
  { size:'YXL', chest:'34-35', waist:'29-30', hip:'35-36', height:"5'2-5'4"  },
  { size:'S',   chest:'35-37', waist:'29-31', hip:'37-39', height:"5'5-5'7"  },
  { size:'M',   chest:'38-40', waist:'32-34', hip:'40-42', height:"5'7-5'9"  },
  { size:'L',   chest:'41-43', waist:'35-37', hip:'43-45', height:"5'9-5'11" },
  { size:'XL',  chest:'44-46', waist:'38-40', hip:'46-48', height:"5'11-6'1" },
  { size:'2XL', chest:'47-49', waist:'41-43', hip:'49-51', height:"6'1-6'3"  },
]

// ── Reviews ──────────────────────────────────────────────────────
const allReviews      = ref([])
const hoverStar       = ref(0)
const submitting      = ref(false)
const editingReviewId = ref(null)
const starLabels      = { 1:'Poor', 2:'Fair', 3:'Good', 4:'Very Good', 5:'Excellent' }
const newReview       = ref({ stars: 0, name: '', title: '', text: '' })
const reviewKey       = computed(() => `prosix_reviews_${product.value.id}`)
const loadReviews     = () => { try { allReviews.value = JSON.parse(localStorage.getItem(reviewKey.value) || '[]') } catch { allReviews.value = [] } }
const saveReviews     = () => { try { localStorage.setItem(reviewKey.value, JSON.stringify(allReviews.value)) } catch {} }
const avgRatingRaw    = computed(() => !allReviews.value.length ? 0 : allReviews.value.reduce((s,r)=>s+r.stars,0)/allReviews.value.length)
const avgRating       = computed(() => avgRatingRaw.value ? avgRatingRaw.value.toFixed(1) : '0.0')
const computedBars    = computed(() => [5,4,3,2,1].map(star => { const count=allReviews.value.filter(r=>r.stars===star).length; return { star, count, pct: allReviews.value.length ? Math.round(count/allReviews.value.length*100) : 0 } }))
const submitReview    = () => {
  if (!newReview.value.stars)       { showToast('⭐ Please select a rating!'); return }
  if (!newReview.value.name.trim()) { showToast('📝 Please enter your name!'); return }
  if (!newReview.value.text.trim()) { showToast('💬 Please write your review!'); return }
  submitting.value = true
  setTimeout(() => {
    if (editingReviewId.value) {
      const idx = allReviews.value.findIndex(r=>r.id===editingReviewId.value)
      if (idx!==-1) allReviews.value[idx] = { ...allReviews.value[idx], ...newReview.value }
      editingReviewId.value = null; showToast('✅ Review updated!')
    } else {
      allReviews.value.unshift({ id: Date.now(), name: newReview.value.name.trim(), title: newReview.value.title.trim(), date: new Date().toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}), stars: newReview.value.stars, text: newReview.value.text.trim() })
      showToast('✅ Review submitted!')
    }
    saveReviews(); newReview.value={stars:0,name:'',title:'',text:''}; hoverStar.value=0; submitting.value=false
  }, 400)
}
const startEdit  = (r) => { editingReviewId.value=r.id; newReview.value={stars:r.stars,name:r.name,title:r.title||'',text:r.text}; nextTick(()=>tabsRef.value?.scrollIntoView({behavior:'smooth',block:'start'})) }
const cancelEdit = () => { editingReviewId.value=null; newReview.value={stars:0,name:'',title:'',text:''}; hoverStar.value=0 }
const deleteReview = (id) => { if(!confirm('Delete?')) return; allReviews.value=allReviews.value.filter(r=>r.id!==id); saveReviews(); showToast('🗑️ Review deleted.') }

// ── Related ──────────────────────────────────────────────────────
const relatedItems     = ref([])
const relatedLoading   = ref(false)
const relatedPage      = ref(0)
const perPage          = 5
const paginatedRelated = computed(() => relatedItems.value.slice(relatedPage.value*perPage, relatedPage.value*perPage+perPage))
const totalPages       = computed(() => Math.ceil(relatedItems.value.length/perPage))

// ── Toast ────────────────────────────────────────────────────────
const toastVisible = ref(false)
const toastText    = ref('')
let toastTimer
const showToast = (msg) => {
  toastText.value    = msg
  toastVisible.value = true
  clearTimeout(toastTimer)
  toastTimer = setTimeout(() => { toastVisible.value = false }, 2800)
}

const formatPrice = (p) => {
  if (typeof p === 'string') return parseFloat(p.replace(/[^0-9.]/g,'')) || 0
  return Number(p) || 0
}

// ── Load product ─────────────────────────────────────────────────
const loadProduct = async () => {
  let loadedAsModel = false
  try {
    const res = await axios.get(`/api/models/${route.params.id}`)
    const m = res.data
    if (m && m.id) {
      loadedAsModel = true; isModel.value = true
      product.value = {
        id: m.id, name: m.title, type: 'Custom Model', price: m.price || 0,
        description: m.description || '', image: m.thumbnail || m.views?.front?.svg || '',
        is_new: false, in_stock: true, stock_quantity: null, shipping_enabled: false,
        sizes: [], colors: [], gallery_images: [], size_chart_image: null,
        category_id: m.category_id || null, subcategory_id: m.subcategory_id || null
      }
      nextTick(() => loadReviews())
    }
  } catch {}

  if (!loadedAsModel) {
    try {
      const res = await axios.get(`/api/products/${route.params.id}`)
      isModel.value = false
      product.value = res.data
      // ✅ Always start with no color selected → main image shows first
      selectedColorIdx.value = -1
      activeThumbIdx.value   = 0
      nextTick(() => loadReviews())
    } catch (err) { console.error('Product load failed:', err) }
  }
}

const fetchRelated = async () => {
  relatedLoading.value = true; relatedItems.value = []
  try {
    if (isModel.value) {
      const subId = product.value.subcategory_id; const catId = product.value.category_id; let models = []
      if (subId) { try { const r = await axios.get(`/api/subcategories/${subId}/models`); models = r.data.models || [] } catch {} }
      if (!models.length && catId) { const r = await axios.get(`/api/categories/${catId}/models`); models = r.data.models || [] }
      relatedItems.value = models.filter(m => m.id !== product.value.id)
    } else {
      const res = await axios.get('/api/products')
      let all = Array.isArray(res.data) ? res.data : Array.isArray(res.data?.products) ? res.data.products : []
      relatedItems.value = all.filter(p => p?.id && p.id !== product.value?.id)
    }
  } catch { relatedItems.value = [] }
  relatedLoading.value = false
}

const toggleLike  = () => { cartStore.toggleLike(product.value.id); showToast(cartStore.isLiked(product.value.id) ? '❤️ Added to wishlist!' : '🤍 Removed from wishlist') }
const handleShare = () => { if (navigator.share) navigator.share({ title: product.value.name, url: window.location.href }); else { navigator.clipboard.writeText(window.location.href); showToast('🔗 Link copied!') } }

const addToCart = () => {
  if (!effectiveSize.value)  { showToast('⚠️ Please select a size!'); return }
  if (!stockAvailable.value) { showToast('❌ This product is out of stock!'); return }
  if (qtyMax.value && quantity.value > qtyMax.value) { showToast(`⚠️ Only ${qtyMax.value} in stock!`); return }
  const cartItem = { ...product.value, image: displayImage.value || product.value.image }
  cartStore.addToCart(cartItem, effectiveSize.value, quantity.value)
  flyToCart(); showToast(`🛒 ${quantity.value}× (${effectiveSize.value}) added!`); quantity.value = 1
}

const flyToCart = () => {
  const wrapper = imageContainer.value; if (!wrapper) return
  const img = wrapper.querySelector('img'); if (!img) return
  const cartIcon = document.querySelector('nav .bi-cart3')?.closest('a,button') || document.querySelector('.bi-cart3')?.closest('a,button,div') || document.querySelector('a[href*="cart"]')
  const srcRect = img.getBoundingClientRect()
  const clone   = img.cloneNode(true)
  Object.assign(clone.style, { position:'fixed', top:srcRect.top+'px', left:srcRect.left+'px', width:srcRect.width+'px', height:srcRect.height+'px', zIndex:'99999', transition:'all 0.8s cubic-bezier(0.25,0.46,0.45,0.94)', pointerEvents:'none', objectFit:'contain', borderRadius:'8px', opacity:'1' })
  document.body.appendChild(clone)
  requestAnimationFrame(() => { requestAnimationFrame(() => { Object.assign(clone.style, { top:'20px', left:(window.innerWidth-80)+'px', width:'40px', height:'40px', opacity:'0', transform:'scale(0.1) rotate(360deg)' }) }) })
  setTimeout(() => { if (cartIcon) { cartIcon.style.transition='transform 0.2s ease'; cartIcon.style.transform='scale(1.4)'; setTimeout(()=>{cartIcon.style.transform='scale(1)'},200) } clone.remove() }, 820)
}

const resetState = () => {
  selectedSize.value    = ''; customSizeVal.value = ''; customSizeOk.value = false
  quantity.value        = 1;  relatedPage.value   = 0;  activeTab.value    = 'description'
  selectedColorIdx.value = -1; activeThumbIdx.value = 0
  editingReviewId.value = null; newReview.value = { stars:0, name:'', title:'', text:'' }
  clearPreview()
}

onMounted(async () => { await loadProduct(); await fetchRelated() })
watch(() => route.params.id, async (newId) => {
  if (!newId) return
  resetState()
  await loadProduct()
  await fetchRelated()
  window.scrollTo({ top: 0, behavior: 'smooth' })
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap');
*{margin:0;padding:0;box-sizing:border-box}
.page-wrapper{font-family:'Montserrat',sans-serif;background:#fff;color:#000;min-height:100vh}
.product-container{max-width:1400px;margin:0 auto;padding:0 32px 80px}
.product-layout{display:grid;grid-template-columns:1fr 1fr;gap:60px;margin-bottom:80px;padding-top:24px}
@media(max-width:968px){.product-layout{grid-template-columns:1fr;gap:36px}}

/* Gallery */
.product-visual{position:sticky;top:90px;height:fit-content}
.gallery-wrap{display:flex;gap:10px}
.thumbnails{display:flex;flex-direction:column;gap:8px;width:72px}
.thumb{width:72px;height:72px;border:2px solid #e0e0e0;border-radius:8px;overflow:hidden;cursor:pointer;transition:.2s;background:#f8f9fa;position:relative;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.thumb.active{border-color:#000;box-shadow:0 0 0 2px rgba(0,0,0,.15)}
.thumb img{width:100%;height:100%;object-fit:contain;padding:4px}
.thumb-dot{position:absolute;bottom:4px;right:4px;width:10px;height:10px;border-radius:50%;border:1.5px solid rgba(255,255,255,.8)}
.main-image-wrapper{flex:1;position:relative;background:#f8f9fa;border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;aspect-ratio:1;display:flex;align-items:center;justify-content:center}
.main-image-wrapper.is-model{background:#fff}
.main-product-image{width:100%;height:100%;object-fit:contain;padding:16px}
.oos-overlay{position:absolute;inset:0;background:rgba(255,255,255,.78);display:flex;align-items:center;justify-content:center;z-index:5}
.oos-overlay span{background:#e53e3e;color:#fff;font-size:13px;font-weight:800;padding:8px 20px;border-radius:6px;letter-spacing:.5px;text-transform:uppercase}
.img-fade-enter-active,.img-fade-leave-active{transition:opacity .2s ease,transform .2s ease}
.img-fade-enter-from{opacity:0;transform:scale(.97)}
.img-fade-leave-to{opacity:0;transform:scale(1.02)}
.badge-new{position:absolute;top:14px;left:14px;background:#2563eb;color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:4px;z-index:10}
.img-action-btn{position:absolute;top:14px;width:40px;height:40px;background:#fff;border:1px solid #e0e0e0;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:16px;transition:.2s;color:#666;z-index:10}
.img-action-btn:hover,.img-action-btn.active{background:#000;color:#fff;border-color:#000}
.wishlist-btn{right:14px}.share-btn{right:62px}

/* Product Info */
.product-info{padding-top:8px}
.product-title{font-size:28px;font-weight:700;line-height:1.2;margin-bottom:6px}
.product-subtitle{font-size:13px;color:#666;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px}
.price-row{display:flex;align-items:center;gap:12px;margin-bottom:6px}
.product-price{font-size:27px;font-weight:800}
.stock-badge{font-size:12px;padding:4px 12px;border-radius:20px;font-weight:600;display:inline-flex;align-items:center;gap:5px}
.stock-in{background:#dcfce7;color:#166534}
.stock-out{background:#fee2e2;color:#991b1b}
.delivery-info{font-size:13px;color:#666;margin-bottom:14px}
.divider{border:none;border-top:1px solid #e8e8e8;margin:14px 0}
.option-group{margin-bottom:16px}
.option-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.option-label{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.selected-value{font-size:12px;color:#555;font-weight:600}
.size-chart-link{font-size:12px;color:#2563eb;cursor:pointer;text-decoration:underline}
.color-swatches{display:flex;gap:8px;flex-wrap:wrap}
.color-swatch{width:32px;height:32px;border-radius:50%;cursor:pointer;border:3px solid transparent;transition:.15s;outline:none}
.color-swatch:hover,.color-swatch.selected{border-color:#000;transform:scale(1.15)}
.white-swatch{box-shadow:inset 0 0 0 1px #ccc}
.size-selector{display:flex;flex-wrap:wrap;gap:6px}
.size-btn{min-width:44px;height:40px;padding:0 8px;border:1.5px solid #e0e0e0;background:#fff;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer;transition:.15s;font-family:inherit;position:relative}
.size-btn:hover:not(.unavailable):not(:disabled){border-color:#000}
.size-btn.selected{background:#000;color:#fff;border-color:#000}
.size-btn.unavailable{background:#fafafa;color:#ccc;border-color:#e8e8e8;cursor:not-allowed;overflow:hidden}
.size-btn.unavailable::after{content:'';position:absolute;top:50%;left:50%;width:130%;height:1.5px;background:#ddd;transform:translate(-50%,-50%) rotate(-45deg)}
.sz-cross{position:absolute;top:-6px;right:-6px;width:14px;height:14px;background:#e53e3e;color:#fff;border-radius:50%;font-size:10px;display:flex;align-items:center;justify-content:center;line-height:1}
.other-btn{background:#f5f5f5;border-style:dashed}
.other-btn.selected{background:#000;color:#fff;border-style:solid}
.custom-size-row{display:flex;gap:8px;margin-top:10px}
.custom-size-input{flex:1;height:40px;padding:0 12px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:13px;font-family:inherit;transition:.2s}
.custom-size-input:focus{outline:none;border-color:#000}
.custom-size-confirm{height:40px;padding:0 14px;background:#000;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit}
.custom-confirmed{font-size:12px;color:#166534;margin-top:6px}
.quantity-selector{display:flex;align-items:center;width:fit-content;border:1.5px solid #e0e0e0;border-radius:6px;overflow:hidden}
.qty-btn{width:42px;height:42px;border:none;background:#fff;font-size:18px;cursor:pointer;transition:.2s}
.qty-btn:hover:not(:disabled){background:#f5f5f5}
.qty-btn:disabled{opacity:.3;cursor:not-allowed}
.qty-input{width:52px;height:42px;border:none;border-left:1.5px solid #e0e0e0;border-right:1.5px solid #e0e0e0;text-align:center;font-size:15px;font-weight:600}
.qty-input:focus{outline:none}
.action-buttons{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px}
.btn-add-cart,.btn-checkout{height:50px;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-add-cart{background:#000;color:#fff}
.btn-add-cart:hover:not(:disabled){background:#333}
.btn-checkout{background:#fff;color:#000;border:1.5px solid #000}
.btn-checkout:hover:not(:disabled){background:#000;color:#fff}
.btn-add-cart:disabled,.btn-checkout:disabled{opacity:.35;cursor:not-allowed}
.product-features{display:grid;gap:12px;padding-top:18px;border-top:1px solid #e8e8e8}
.feature-item{display:flex;align-items:flex-start;gap:12px}
.feature-item i{font-size:20px;color:#2563eb;margin-top:2px}
.feature-item strong{display:block;font-size:13px;font-weight:700;margin-bottom:2px}
.feature-item p{font-size:12px;color:#777;margin:0}

/* Tabs */
.tabs-section{border-top:2px solid #e8e8e8;margin-bottom:70px}
.tabs-nav{display:flex;border-bottom:1px solid #e8e8e8;overflow-x:auto}
.tab-btn{padding:14px 22px;background:none;border:none;font-size:13px;font-weight:600;cursor:pointer;color:#888;border-bottom:3px solid transparent;margin-bottom:-1px;transition:.2s;font-family:inherit;white-space:nowrap}
.tab-btn:hover{color:#000}
.tab-btn.active{color:#000;border-bottom-color:#2563eb}
.tab-panel{display:none;padding:26px 0}
.tab-panel.active{display:block}
.tab-heading{font-size:17px;font-weight:700;margin-bottom:16px}
.desc-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px}
@media(max-width:768px){.desc-grid{grid-template-columns:1fr}}
.desc-text h3{font-size:15px;font-weight:700;margin-bottom:10px}
.desc-text p{font-size:14px;color:#555;line-height:1.8;margin-bottom:10px}
.spec-table{width:100%;border-collapse:collapse}
.spec-table tr{border-bottom:1px solid #f0f0f0}
.spec-table td{padding:9px 0;font-size:14px}
.spec-table td:first-child{color:#777;width:44%}
.spec-table td:last-child{font-weight:600}
.sc-img-wrap{text-align:center}
.sc-img{max-width:100%;border-radius:10px;border:1px solid #e8e8e8;box-shadow:0 2px 12px rgba(0,0,0,.07)}
.size-chart-table{width:100%;border-collapse:collapse;font-size:13px;min-width:460px}
.size-chart-table th{background:#000;color:#fff;padding:11px 14px;text-align:center;font-weight:700}
.size-chart-table td{padding:9px 14px;text-align:center;border-bottom:1px solid #f0f0f0}
.size-chart-table tr:nth-child(even){background:#fafafa}
.size-chart-table tr.highlighted{background:#dbeafe!important}
.size-chart-table tr.row-unavail td{color:#ccc}
.shipping-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
@media(max-width:768px){.shipping-grid{grid-template-columns:1fr}}
.ship-card{padding:18px;border:1px solid #e8e8e8;border-radius:8px}
.ship-card i{font-size:24px;color:#2563eb;margin-bottom:8px;display:block}
.ship-card h4{font-size:13px;font-weight:700;margin-bottom:4px}
.ship-card p{font-size:12px;color:#666}
.reviews-summary{display:grid;grid-template-columns:150px 1fr;gap:32px;margin-bottom:28px;align-items:center;padding-bottom:24px;border-bottom:1px solid #e8e8e8}
.rating-big{text-align:center}
.rating-num{font-size:50px;font-weight:800;line-height:1}
.stars-row{font-size:22px;display:flex;justify-content:center;gap:2px;margin:5px 0}
.rating-count{font-size:13px;color:#777}
.rating-bar-row{display:flex;align-items:center;gap:10px;margin-bottom:7px;font-size:12px}
.bar-label{width:34px;text-align:right;color:#777}
.bar-track{flex:1;height:7px;background:#e8e8e8;border-radius:4px;overflow:hidden}
.bar-fill{height:100%;background:#f59e0b;border-radius:4px}
.bar-count{width:22px;font-weight:600;color:#777}
.review-form-wrap{background:#f8f9fa;border-radius:12px;padding:24px;margin-bottom:26px;border:1px solid #ebebeb}
.review-form-title{font-size:16px;font-weight:700;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.review-form{display:flex;flex-direction:column;gap:14px}
.form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{display:flex;flex-direction:column;gap:5px}
.form-group label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#555}
.form-input{padding:10px 13px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-family:inherit;background:#fff;transition:.2s}
.form-input:focus{outline:none;border-color:#000}
.form-textarea{resize:vertical;min-height:96px}
.star-picker{display:flex;align-items:center;gap:3px}
.star-pick{font-size:28px;cursor:pointer;color:#e0e0e0;transition:color .1s;user-select:none;line-height:1}
.star-pick.lit{color:#f59e0b;transform:scale(1.1)}
.star-label-txt{font-size:12px;color:#555;margin-left:8px;font-weight:600}
.submit-btn{height:46px;background:#000;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px;padding:0 24px}
.submit-btn:hover:not(:disabled){background:#333}
.submit-btn:disabled{opacity:.45;cursor:not-allowed}
.cancel-edit-btn{height:46px;background:#fff;color:#000;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;padding:0 20px;font-family:inherit;transition:.2s}
.cancel-edit-btn:hover{border-color:#000}
.spin-icon{animation:sp2 .6s linear infinite;display:inline-block}
@keyframes sp2{to{transform:rotate(360deg)}}
.reviews-list-title{font-size:15px;font-weight:700;margin-bottom:14px}
.review-cards{display:flex;flex-direction:column;gap:12px}
.review-card{padding:18px;border:1px solid #e8e8e8;border-radius:10px;transition:box-shadow .2s}
.review-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.07)}
.review-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px}
.reviewer-left{display:flex;align-items:center;gap:10px}
.reviewer-avatar{width:34px;height:34px;border-radius:50%;background:#111;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0}
.reviewer-name{font-weight:700;font-size:13px}
.review-title-inline{font-size:12px;color:#555;font-style:italic}
.review-date{font-size:11px;color:#aaa}
.review-stars-row{font-size:16px;margin-bottom:6px;display:flex;gap:2px}
.review-text{font-size:13px;color:#555;line-height:1.7}
.empty-reviews{text-align:center;padding:36px;color:#bbb}
.empty-reviews i{font-size:30px;display:block;margin-bottom:10px}
.review-action-btn{width:32px;height:32px;border-radius:50%;border:1.5px solid #e0e0e0;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:.2s}
.edit-btn:hover{background:#2563eb;color:#fff;border-color:#2563eb}
.delete-btn:hover{background:#e53e3e;color:#fff;border-color:#e53e3e}
.related-section{padding-top:56px;border-top:1px solid #e8e8e8}
.section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:22px}
.section-header h2{font-size:20px;font-weight:700}
.related-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px}
@media(max-width:1100px){.related-grid{grid-template-columns:repeat(4,1fr)}}
@media(max-width:768px){.related-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:480px){.related-grid{grid-template-columns:repeat(2,1fr)}}
.model-rel-card{border:1px solid #ebebeb;border-radius:10px;overflow:hidden;cursor:pointer;transition:.3s;background:#fff}
.model-rel-card:hover{transform:translateY(-4px);box-shadow:0 8px 20px rgba(0,0,0,.1)}
.model-rel-img{height:180px;background:#f8f9fa;position:relative;overflow:hidden}
.model-rel-thumb{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);max-height:90%;max-width:90%;object-fit:contain}
.model-rel-body{padding:10px 12px}
.model-rel-title{font-size:13px;font-weight:600;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.model-rel-price{font-size:13px;font-weight:800}
.product-card-link{text-decoration:none;color:inherit;display:block;cursor:pointer}
.product-card{border:1px solid #ebebeb;border-radius:8px;overflow:hidden;transition:.3s;background:#fff}
.product-card:hover{transform:translateY(-4px);box-shadow:0 8px 20px rgba(0,0,0,.1)}
.product-card.card-active{border-color:#2563eb;box-shadow:0 0 0 2px #2563eb40;transform:translateY(-4px)}
.card-image{position:relative;aspect-ratio:3/4;background:#fafafa;overflow:hidden}
.card-image img{width:100%;height:100%;object-fit:contain;padding:8px;transition:.4s}
.product-card:hover .card-image img{transform:scale(1.06)}
.card-overlay{position:absolute;inset:0;background:rgba(0,0,0,.6);display:flex;align-items:center;justify-content:center;opacity:0;transition:.3s}
.product-card:hover .card-overlay{opacity:1}
.quick-view-btn{background:#fff;color:#000;padding:6px 14px;border-radius:3px;font-size:11px;font-weight:700}
.card-content{padding:8px 10px}
.card-title{font-size:12px;font-weight:600;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.card-price{font-size:13px;font-weight:800}
.preview-banner{margin-top:18px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 18px}
.preview-banner-inner{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.preview-banner-inner i{color:#2563eb;font-size:16px}
.preview-banner-inner span{font-size:13px;color:#1e40af;flex:1}
.preview-go-btn{background:#2563eb;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;transition:.2s}
.preview-close-btn{width:28px;height:28px;background:#fff;border:1px solid #bfdbfe;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;color:#6b7280;transition:.2s}
.preview-close-btn:hover{background:#e53e3e;color:#fff;border-color:#e53e3e}
.slider-dots{display:flex;justify-content:center;gap:6px;margin-top:16px}
.dot{width:8px;height:8px;border-radius:50%;background:#ddd;border:none;cursor:pointer;transition:.2s;padding:0}
.dot.active{background:#2563eb;transform:scale(1.2)}
.slider-nav{display:flex;justify-content:center;gap:10px;margin-top:8px}
.slider-btn{width:34px;height:34px;background:#fff;border:1px solid #e0e0e0;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;transition:.2s}
.slider-btn:hover:not(:disabled){background:#000;color:#fff}
.slider-btn:disabled{opacity:.3;cursor:not-allowed}
.loading-state,.empty-state{text-align:center;padding:50px;color:#aaa}
.spinner{width:36px;height:36px;border:3px solid #f0f0f0;border-top-color:#000;border-radius:50%;animation:sp3 .8s linear infinite;margin:0 auto}
@keyframes sp3{to{transform:rotate(360deg)}}
.toast-msg{position:fixed;bottom:28px;right:28px;background:#111;color:#fff;padding:12px 18px;border-radius:8px;font-size:14px;font-weight:600;z-index:9999;pointer-events:none}
.toast-slide-enter-active,.toast-slide-leave-active{transition:all .35s ease}
.toast-slide-enter-from,.toast-slide-leave-to{opacity:0;transform:translateY(20px)}
</style>
