<template>
  <div class="d-flex flex-column min-vh-100" :class="{ 'dark-mode': isDarkMode }">
    <button class="scroll-top-btn" :class="{ visible: showScrollTop }" @click="scrollToTop" title="Back to Top">
      <i class="bi bi-arrow-up"></i>
    </button>

    <nav-component />

    <main class="flex-grow-1">
      <div v-if="safeSlides.length > 0">
        <section class="hero-carousel" id="hero">
          <div class="carousel-container">
            <Transition name="slide" mode="out-in">
              <div
                :key="currentSlide"
                class="carousel-slide active"
                :style="{ backgroundImage: `url(${safeSlides[currentSlide].backgroundImage})` }">
                <div class="carousel-overlay">
                  <div class="carousel-content d-flex align-items-center justify-content-start h-100">
                    <div class="text-and-image-wrapper d-flex flex-column align-items-start text-start">
                      <div class="title-button-row d-flex align-items-start gap-4 mb-3">
                        <h1 class="display-3 fw-bold main_title italic-title animate-from-top">
                          {{ safeSlides[currentSlide].title }}
                        </h1>
                        <div class="button-wrapper flex-shrink-0 animate-from-top delayed">
                          <button
                            v-if="safeSlides[currentSlide].buttonText"
                            class="btn btn-md px-4 single-line-btn"
                            @click="handleBuy(safeSlides[currentSlide].buttonLink)"
                          >
                            {{ safeSlides[currentSlide].buttonText }}
                          </button>
                        </div>
                      </div>
                      <p v-if="safeSlides[currentSlide].subtitle" class="lead text-white mt-2 mb-4 animate-from-top delayed-more">
                        {{ safeSlides[currentSlide].subtitle }}
                      </p>
                      <div class="image-content mt-0" v-if="safeSlides[currentSlide].pngImage">
                        <img :src="safeSlides[currentSlide].pngImage" :alt="safeSlides[currentSlide].title" class="hero-png img-fluid animate-from-left" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </Transition>
            <template v-if="safeSlides.length > 0">
              <button class="carousel-control-left prev" @click="prevSlide"><i class="bi bi-chevron-left"></i></button>
              <button class="carousel-control-right next" @click="nextSlide"><i class="bi bi-chevron-right"></i></button>
              <div class="carousel-dots">
                <button v-for="(slide, index) in safeSlides" :key="index" class="dot" :class="{ active: currentSlide === index }" @click="goToSlide(index)"></button>
              </div>
            </template>
          </div>
        </section>
      </div>
      <div v-else class="text-center py-5 mt-5"><h3>No Banner Available</h3></div>

      <!-- Sports Icons -->
      <section class="sports-icons-section">
        <div class="icons-track" :class="{ 'scrolling': isMobile || sportsIcons.length > 12 }">
          <div class="icon-item" v-for="(icon, index) in scrollingIcons" :key="index" @click="selectTeam(icon.teamId)" :class="{ active: activeTeam === icon.teamId }">
            <div class="icon-circle">
              <img :src="icon.highlight_image || icon.image" :alt="icon.name" class="icon-image" />
            </div>
            <div class="icon-name">{{ icon.name }}</div>
          </div>
        </div>
      </section>

      <!-- Deals Section -->
      <section class="deals-section py-5">
        <div class="full-container" v-if="deal">
          <div class="text-center mb-5">
            <h2 class="fw-bold display-5 text-black">{{ deal.title }}</h2>
            <p class="lead text-black">{{ deal.subtitle }}</p>
          </div>
          <div class="row align-items-stretch">
            <div class="col-lg-5 mb-4 mb-lg-0">
              <div v-if="deal.banners?.length" class="deal-banner-box">
                <Transition name="fade" mode="out-in">
                  <img :key="currentBanner" :src="deal.banners[currentBanner].image_path" class="banner-img" alt="Banner" />
                </Transition>
              </div>
            </div>
            <div class="col-lg-7">
              <div class="row g-4">
                <div v-for="img in deal.images" :key="img.id" class="col-6 col-md-3">
                  <div class="deal-card">
                    <div v-if="img.label" class="deal-ribbon">{{ img.label }}</div>
                    <img :src="img.image_path" class="deal-card-img" />
                    <div class="deal-overlay">
                      <a :href="img.link || '#'" class="btn btn-light btn-sm">View More</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Featured Products -->
      <section class="featured-products py-5">
        <div class="full-container">
          <div class="featured-header d-flex align-items-center justify-content-between mb-5 flex-wrap gap-3">
            <div class="left-ribbon"><span>Trending Now</span></div>
            <div class="tabs d-flex gap-2">
              <button v-for="tab in tabs" :key="tab.value" class="tab-btn px-4 py-2" :class="{ active: activeTab === tab.value }" @click="setTab(tab.value)">{{ tab.label }}</button>
            </div>
          </div>
          <div class="carousel-wrapper position-relative overflow-hidden">
            <div class="products-track d-flex" :style="featuredTrackStyle">
              <div v-for="(product, idx) in infiniteFeatured" :key="idx" class="product-card flex-shrink-0 px-2">
                <div class="product-card-inner bg-white rounded shadow-sm text-center">
                  <img :src="product.image" :alt="product.name" class="product-img img-fluid" loading="lazy" />
                  <div class="product-meta-row">
                    <h5 class="product-title">{{ product.name }}</h5>
                    <p class="product-price">${{ product.price.toFixed(2) }}</p>
                  </div>
                  <button v-if="product.type === 'product'" class="add-cart-btn btn btn-dark w-100 py-2" @click="addToCart(product)">ADD TO CART</button>
                  <button v-else-if="product.type === 'model'" class="add-cart-btn btn btn-dark w-100 py-2" @click="goToCustomizer(product)">CUSTOMIZE</button>
                </div>
              </div>
              <div v-if="displayedProducts.length === 0" class="w-100 text-center py-5">
                <p class="text-muted fs-4">No products found in this category...</p>
              </div>
            </div>
            <button class="carousel-btn carousel-btn-prev" @click="prevProduct"><i class="bi bi-chevron-left"></i></button>
            <button class="carousel-btn carousel-btn-next" @click="nextProduct"><i class="bi bi-chevron-right"></i></button>
          </div>
        </div>
      </section>

      <!-- Benefits -->
      <section class="benefits-section py-5">
        <div class="full-container">
          <div class="benefits-desktop d-flex flex-wrap justify-content-between" style="gap: 20px;">
            <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;"><div class="icon mb-4"><i class="bi bi-truck fs-1"></i></div><h4 class="mb-3">Free Shipping Delivery & Returns</h4><p class="mb-0">Shop with confidence and have your favorite Furniture delivered right to your doorstep without any additional cost.</p></div>
            <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;"><div class="icon mb-4"><i class="bi bi-heart fs-1"></i></div><h4 class="mb-3">30 Days Money Back Guarantee</h4><p class="mb-0">We Guarantee to rectify any unsatisfactory experience you may have with your purchase. No Queries posed.</p></div>
            <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;"><div class="icon mb-4"><i class="bi bi-headset fs-1"></i></div><h4 class="mb-3">Online free custom support 24/7</h4><p class="mb-0">Need help with your electronics? Get in touch with us anytime, anywhere and let's get your tech sorted.</p></div>
          </div>
          <div class="benefits-mobile-carousel">
            <div class="benefits-track-wrapper">
              <div class="benefits-track" :style="{ transform: `translateX(-${benefitIndex * 100}%)` }">
                <div class="benefit-slide"><div class="benefit-card text-center p-4"><div class="icon mb-4"><i class="bi bi-truck fs-1"></i></div><h4 class="mb-3">Free Shipping Delivery & Returns</h4><p class="mb-0">Shop with confidence and have your favorite Furniture delivered right to your doorstep without any additional cost.</p></div></div>
                <div class="benefit-slide"><div class="benefit-card text-center p-4"><div class="icon mb-4"><i class="bi bi-heart fs-1"></i></div><h4 class="mb-3">30 Days Money Back Guarantee</h4><p class="mb-0">We Guarantee to rectify any unsatisfactory experience you may have with your purchase. No Queries posed.</p></div></div>
                <div class="benefit-slide"><div class="benefit-card text-center p-4"><div class="icon mb-4"><i class="bi bi-headset fs-1"></i></div><h4 class="mb-3">Online free custom support 24/7</h4><p class="mb-0">Need help with your electronics? Get in touch with us anytime, anywhere and let's get your tech sorted.</p></div></div>
              </div>
            </div>
            <button class="benefit-btn benefit-btn-prev" @click="benefitIndex = Math.max(0, benefitIndex - 1)" :disabled="benefitIndex === 0"><i class="bi bi-chevron-left"></i></button>
            <button class="benefit-btn benefit-btn-next" @click="benefitIndex = Math.min(2, benefitIndex + 1)" :disabled="benefitIndex === 2"><i class="bi bi-chevron-right"></i></button>
            <div class="benefit-dots">
              <span v-for="n in 3" :key="n" class="bdot" :class="{ active: benefitIndex === n-1 }" @click="benefitIndex = n-1"></span>
            </div>
          </div>
        </div>
      </section>

      <!-- Latest Videos -->
      <section class="latest-videos-section">
        <div class="full-container">
          <div class="left-ribbon"><span>Featured Reels</span></div>
          <div class="videos-carousel-wrapper">
            <div class="videos-carousel-container">
              <div class="videos-track" :style="videoTrackStyle">
                <div v-for="(video, idx) in infiniteVideos" :key="idx" class="video-card">
                  <div class="video-thumbnail" @click="playVideo(video)">
                    <img :src="video.thumbnail" :alt="video.title" />
                    <div class="play-overlay"><div class="play-button"><i class="bi bi-play-fill"></i></div></div>
                    <div class="video-title-overlay">{{ video.title }}</div>
                  </div>
                </div>
              </div>
            </div>
            <button class="video-arrow prev-arrow" @click="prevVideo"><i class="bi bi-chevron-left"></i></button>
            <button class="video-arrow next-arrow" @click="nextVideo"><i class="bi bi-chevron-right"></i></button>
          </div>
        </div>
      </section>

      <div v-if="showVideoModal" class="video-modal" @click="closeVideo">
        <div class="video-modal-content" @click.stop>
          <button class="close-video-btn" @click="closeVideo"><i class="bi bi-x-lg"></i></button>
          <video ref="videoPlayer" :src="currentVideoUrl" controls autoplay class="fullscreen-video"></video>
        </div>
      </div>

      <!-- Apparel -->
      <section class="apparel-products py-5" style="background: #000000;">
        <div class="full-container">
          <h2 class="left2-ribbon text-start mb-5 fw-bold">Premium Showcase</h2>
          <div class="carousel-wrapper position-relative overflow-hidden">
            <div class="products-track d-flex" :style="apparelTrackStyle">
              <div v-for="(product, idx) in infiniteApparel" :key="idx" class="product-card flex-shrink-0 px-2">
                <div class="product-card-inner apparel-card-inner text-center">
                  <div class="product-image-wrapper">
                    <img :src="product.image" :alt="product.name" class="product-img img-fluid" loading="lazy" />
                  </div>
                  <div class="product-meta-row apparel-meta-row">
                    <h5 class="product-name text-white fw-semibold">{{ product.name }}</h5>
                    <p class="product-price text-white fw-bold">${{ product.price.toFixed(2) }}</p>
                  </div>
                  <button v-if="product.type === 'product'" class="btn btn-light w-100 py-2 fw-bold add-to-cart-btn" @click="addToCart(product)">ADD TO CART</button>
                  <button v-else-if="product.type === 'model'" class="btn btn-light w-100 py-2 fw-bold add-to-cart-btn" @click="goToCustomizer(product)">CUSTOMIZE</button>
                </div>
              </div>
              <div v-if="apparelProducts.length === 0" class="w-100 text-center py-5 text-white"><h4>No Apparel Products Found</h4></div>
            </div>
            <button class="carousel-btn carousel-btn-prev" @click="prevApparel"><i class="bi bi-chevron-left"></i></button>
            <button class="carousel-btn carousel-btn-next" @click="nextApparel"><i class="bi bi-chevron-right"></i></button>
          </div>
        </div>
      </section>

      <!-- TESTIMONIALS -->
      <section class="testimonials-section py-5">
        <div class="full-container">
          <div class="left-ribbon"><span>Trusted Reviews</span></div>
          <div v-if="loadingTestimonials" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3">Loading customer reviews...</p></div>
          <div v-else-if="testimonialsError" class="text-center py-5 text-danger"><p>{{ testimonialsError }}</p><button class="btn btn-outline-dark mt-3" @click="fetchTestimonials">Try Again</button></div>
          <div v-else-if="testimonials.length === 0" class="text-center py-5 text-muted"><p>No testimonials available yet.</p></div>
          <div v-else class="testimonials-outer-wrapper">
            <button class="t-arrow t-arrow-left" @click="prevTestimonial"><i class="bi bi-chevron-left"></i></button>
            <div class="testimonials-carousel-inner">
              <div class="testimonials-carousel" :style="testimonialTrackStyle">
                <div v-for="(testimonial, index) in infiniteTestimonials" :key="index" class="testimonial-item">
                  <div class="t-card-wrapper">
                    <div class="quote-float"><i class="bi bi-quote"></i></div>
                    <div class="testimonial-card">
                      <div class="stars-rating mb-3">
                        <i v-for="star in 5" :key="star" class="bi" :class="{ 'bi-star-fill': star <= testimonial.rating, 'bi-star': star > testimonial.rating }"></i>
                      </div>
                      <p class="testimonial-text mb-4">{{ testimonial.text }}</p>
                      <div class="testimonial-author d-flex align-items-center">
                        <div class="author-image me-3"><img :src="testimonial.image" :alt="testimonial.name" class="rounded-circle" /></div>
                        <div class="author-info">
                          <h5 class="author-name mb-0">{{ testimonial.name }}</h5>
                          <p class="author-position text-muted mb-0">{{ testimonial.position }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <button class="t-arrow t-arrow-right" @click="nextTestimonial"><i class="bi bi-chevron-right"></i></button>
          </div>
        </div>
      </section>

      <!-- RECENT BLOG -->
      <section class="recent-blog-section py-5">
        <div class="full-container">
          <div class="left-ribbon"><span>Our Stories</span></div>
          <div class="row g-4">
            <div class="col-6 col-lg-6" v-for="blog in visibleBlogs" :key="blog.id">
              <div class="blog-card d-flex flex-column flex-md-row">
                <div class="blog-image d-none d-md-block" v-if="blog.image">
                  <img :src="`/storage/${blog.image}`" :alt="blog.title" class="img-fluid" />
                </div>
                <div class="blog-content">
                  <div class="blog-meta d-none d-md-flex">
                    <span class="date">{{ new Date(blog.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</span>
                    <span class="comments">0 COMMENTS</span>
                  </div>
                  <h3 class="blog-title">{{ blog.title }}</h3>
                  <p class="blog-excerpt d-none d-md-block">{{ blog.description }}</p>
                  <a :href="`/blog/${blog.slug}`" class="read-more">Read More →</a>
                </div>
              </div>
            </div>
          </div>
          <div class="text-center mt-5">
            <button class="btn-view-all" @click="toggleAllBlogs">{{ showAllBlogs ? 'SHOW LESS' : 'VIEW ALL BLOGS' }}</button>
          </div>
        </div>
      </section>
    </main>

    <component :is="'script'" v-once></component>
    <footer-component />

    <!-- CHAT WIDGET -->
    <div class="chat-widget-wrapper">
      <div class="launcher-btn" :class="{ open: chatOpen }" @click="chatOpen = !chatOpen">
        <div class="launcher-icon close-icon"><i class="bi bi-x-lg"></i></div>
        <div class="launcher-icon chat-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.477 2 2 6.253 2 11.5c0 2.304.87 4.41 2.306 6.033L3 21l3.647-1.268A10.07 10.07 0 0012 21c5.523 0 10-4.253 10-9.5S17.523 2 12 2z" fill="currentColor"/></svg>
        </div>
      </div>
      <div class="launcher-pulse" v-if="!chatOpen"></div>
      <Transition name="widget-pop">
        <div class="widget-panel" v-if="chatOpen">
          <div class="widget-header">
            <div class="header-avatar"><svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.477 2 2 6.253 2 11.5c0 2.304.87 4.41 2.306 6.033L3 21l3.647-1.268A10.07 10.07 0 0012 21c5.523 0 10-4.253 10-9.5S17.523 2 12 2z" fill="white"/></svg></div>
            <div class="header-info">
              <span class="header-title">Support Assistant</span>
              <span class="header-status"><span class="status-dot"></span> Online</span>
            </div>
            <button class="header-close" @click="chatOpen = false"><i class="bi bi-x-lg"></i></button>
          </div>
          <div class="tab-row">
            <button class="tab-item" :class="{ active: chatTab === 'tawkto' }" @click="switchTab('tawkto')"><i class="bi bi-headset"></i> Live Chat</button>
            <button class="tab-item" :class="{ active: chatTab === 'whatsapp' }" @click="chatTab = 'whatsapp'"><i class="bi bi-whatsapp"></i> WhatsApp</button>
          </div>
          <div class="chat-tab-content tawkto-tab" v-if="chatTab === 'tawkto'">
            <div class="wa-body">
              <div class="wa-icon-wrap tawk-icon"><i class="bi bi-headset"></i></div>
              <h3 class="wa-title">Live Chat Support</h3>
              <p class="wa-desc">Chat with our support team in real-time. We're here to help you!</p>
              <div class="wa-hours"><i class="bi bi-clock"></i> Available 9am – 9pm PKT</div>
              <button class="wa-cta-btn tawk-btn" @click="openTawkTo"><i class="bi bi-headset"></i> Start Live Chat</button>
            </div>
          </div>
          <div class="chat-tab-content whatsapp-tab" v-if="chatTab === 'whatsapp'">
            <div class="wa-body">
              <div class="wa-icon-wrap"><i class="bi bi-whatsapp"></i></div>
              <h3 class="wa-title">Chat on WhatsApp</h3>
              <p class="wa-desc">Get instant help from our team. We usually reply within minutes.</p>
              <div class="wa-hours"><i class="bi bi-clock"></i> Available 9am – 9pm PKT</div>
              <a href="https://wa.me/03316566200" target="_blank" class="wa-cta-btn"><i class="bi bi-whatsapp"></i> Open WhatsApp</a>
              <p class="wa-number">+92 331 6566200</p>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCartStore } from '@/store/cart'

const cartStore = useCartStore()

const activeTeam = ref(null)
const activeTab = ref('all')
const router = useRouter()
const currentSlide = ref(0)
let carouselInterval
const showVideoModal = ref(false)
const currentVideoUrl = ref('')
const videoPlayer = ref(null)
const isDarkMode = ref(false)
const isMobile = ref(false)
const updateMobile = () => { isMobile.value = window.innerWidth < 992 }
const showAllBlogs = ref(false)
const visibleBlogs = computed(() => showAllBlogs.value ? blogs.value : blogs.value.slice(0, 4))
const toggleAllBlogs = () => { showAllBlogs.value = !showAllBlogs.value }
const benefitIndex = ref(0)
const slides = ref([])
const safeSlides = computed(() => slides.value.filter(s => s && typeof s.backgroundImage === 'string' && s.backgroundImage.trim() !== ''))
const sportsIcons = ref([])
const currentBanner = ref(0)
let bannerInterval = null

// ============================================
// SMART CAROUSEL
// ============================================
function useInfiniteCarousel(items, itemsPerView, autoSpeed) {
  const index = ref(0)
  const isTransitioning = ref(false)
  const transitionVal = ref('transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)')

  const shouldLoop = computed(() => items.value.length > itemsPerView.value)

  const infinite = computed(() => {
    if (items.value.length === 0) return []
    if (!shouldLoop.value) return [...items.value]
    return [...items.value, ...items.value, ...items.value]
  })

  const trackStyle = computed(() => {
    const pct = 100 / itemsPerView.value
    const translateX = shouldLoop.value
      ? (index.value + items.value.length) * pct
      : index.value * pct
    return {
      transform: `translateX(-${translateX}%)`,
      transition: transitionVal.value
    }
  })

  const next = () => {
    if (!shouldLoop.value) {
      const maxIdx = Math.max(0, items.value.length - itemsPerView.value)
      if (index.value < maxIdx) index.value++
      return
    }
    if (isTransitioning.value) return
    isTransitioning.value = true
    transitionVal.value = 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)'
    index.value++
    setTimeout(() => {
      if (index.value >= items.value.length) {
        transitionVal.value = 'none'
        index.value = 0
        nextTick(() => { isTransitioning.value = false })
      } else {
        isTransitioning.value = false
      }
    }, 460)
  }

  const prev = () => {
    if (!shouldLoop.value) {
      if (index.value > 0) index.value--
      return
    }
    if (isTransitioning.value) return
    isTransitioning.value = true
    transitionVal.value = 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)'
    index.value--
    setTimeout(() => {
      if (index.value < 0) {
        transitionVal.value = 'none'
        index.value = items.value.length - 1
        nextTick(() => { isTransitioning.value = false })
      } else {
        isTransitioning.value = false
      }
    }, 460)
  }

  let autoTimer = null
  const startAuto = () => {
    stopAuto()
    autoTimer = setInterval(() => {
      if (shouldLoop.value) next()
    }, autoSpeed)
  }
  const stopAuto = () => { if (autoTimer) { clearInterval(autoTimer); autoTimer = null } }

  return { trackStyle, infinite, next, prev, startAuto, stopAuto }
}

// Featured Products
const featuredProducts = ref([])
const tabs = ref([{ label: 'All', value: 'all' }, { label: 'New Arrivals', value: 'new' }, { label: 'Best Sellers', value: 'bestsellers' }])
const fetchFeaturedProducts = async () => {
  try { const res = await axios.get('/api/featured-products'); featuredProducts.value = res.data || [] } catch (e) { console.error(e) }
}
const setTab = (value) => { activeTab.value = value }
const displayedProducts = computed(() => {
  let products = [...featuredProducts.value]
  if (activeTab.value === 'new') { const d = new Date(); d.setDate(d.getDate() - 14); return products.filter(p => new Date(p.created_at || 0) >= d) }
  if (activeTab.value === 'bestsellers') return products.slice(0, 12)
  return products
})

// ✅ FIXED: 5 cards on large screens
const featuredItemsPerView = computed(() => {
  if (typeof window === 'undefined') return 5
  if (window.innerWidth < 576) return 1
  if (window.innerWidth < 768) return 2
  if (window.innerWidth < 992) return 3
  if (window.innerWidth < 1400) return 4
  return 5
})

const { trackStyle: featuredTrackStyle, infinite: infiniteFeatured, next: nextProduct, prev: prevProduct, startAuto: startFeaturedAuto, stopAuto: stopFeaturedAuto } = useInfiniteCarousel(displayedProducts, featuredItemsPerView, 2500)

// Apparel
const apparelProducts = ref([])
const fetchApparelProducts = async () => {
  try { const res = await axios.get('/api/apparel-products'); apparelProducts.value = res.data || [] } catch (e) { console.error(e) }
}

// ✅ FIXED: 5 cards on large screens for apparel too
const apparelItemsPerView = computed(() => {
  if (typeof window === 'undefined') return 5
  if (window.innerWidth < 576) return 1
  if (window.innerWidth < 768) return 2
  if (window.innerWidth < 992) return 3
  if (window.innerWidth < 1400) return 4
  return 5
})

const { trackStyle: apparelTrackStyle, infinite: infiniteApparel, next: nextApparel, prev: prevApparel, startAuto: startApparelAuto, stopAuto: stopApparelAuto } = useInfiniteCarousel(apparelProducts, apparelItemsPerView, 2500)

// Videos
const sportsVideos = ref([])
const videoItemsPerView = computed(() => { if (window.innerWidth < 576) return 1; if (window.innerWidth < 992) return 2; return 3 })
const fetchVideos = async () => { try { const res = await axios.get('/api/videos'); sportsVideos.value = res.data || [] } catch (e) { console.error(e) } }
const { trackStyle: videoTrackStyle, infinite: infiniteVideos, next: nextVideo, prev: prevVideo, startAuto: startVideoAuto, stopAuto: stopVideoAuto } = useInfiniteCarousel(sportsVideos, videoItemsPerView, 3000)
const playVideo = (video) => { currentVideoUrl.value = video.video_url; showVideoModal.value = true; stopVideoAuto() }
const closeVideo = () => { showVideoModal.value = false; currentVideoUrl.value = ''; startVideoAuto() }

// Testimonials
const testimonials = ref([])
const loadingTestimonials = ref(true)
const testimonialsError = ref(null)
const testimonialItemsPerView = computed(() => { if (window.innerWidth < 576) return 1; if (window.innerWidth < 992) return 2; return 3 })
const fetchTestimonials = async () => {
  loadingTestimonials.value = true; testimonialsError.value = null
  try {
    const res = await axios.get('/api/testimonials')
    testimonials.value = res.data.map(item => ({ text: item.text || 'No review text', name: item.name || 'Customer', position: item.position || 'Happy Buyer', image: item.image || 'https://via.placeholder.com/80?text=No+Photo', rating: item.rating || 5 }))
  } catch { testimonialsError.value = 'Could not load testimonials' }
  finally { loadingTestimonials.value = false }
}
const { trackStyle: testimonialTrackStyle, infinite: infiniteTestimonials, next: nextTestimonial, prev: prevTestimonial, startAuto: startTestimonialAuto } = useInfiniteCarousel(testimonials, testimonialItemsPerView, 3500)

// Categories / Icons
const fetchCategories = async () => {
  try { const res = await axios.get('/api/highlighted'); if (!Array.isArray(res.data)) { sportsIcons.value = []; return }; sportsIcons.value = res.data.map(cat => ({ teamId: cat.id, name: cat.name, image: cat.icon_image, highlight_image: cat.highlight_image })) } catch { sportsIcons.value = [] }
}
const scrollingIcons = computed(() => { if (isMobile.value || sportsIcons.value.length > 12) return [...sportsIcons.value, ...sportsIcons.value]; return sportsIcons.value })

// Deal
const deal = ref(null)
const fetchDeal = async () => { try { const res = await axios.get('/api/latest-deal'); deal.value = res.data } catch (e) { console.error(e) } }

// Blogs
const blogs = ref([])
const fetchBlogs = async () => { try { const res = await axios.get('/api/blogs'); blogs.value = res.data } catch (e) { console.error(e) } }

// ✅ FIXED: Slides - ensure full URL for background images
const fetchSlides = async () => {
  try {
    const res = await axios.get('/api/banners')
    slides.value = (res.data || []).map(s => ({
      ...s,
      backgroundImage: s.backgroundImage
        ? (s.backgroundImage.startsWith('http') ? s.backgroundImage : `${window.location.origin}${s.backgroundImage}`)
        : ''
    }))
  } catch { slides.value = [] }
}

const nextSlide = () => { if (slides.value.length) currentSlide.value = (currentSlide.value + 1) % slides.value.length }
const prevSlide = () => { if (slides.value.length) currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length }
const goToSlide = (i) => { currentSlide.value = i }
const handleBuy = (url) => { if (url) window.location.href = url }
const selectTeam = (id) => { activeTeam.value = id; router.push({ name: 'CategoryProducts', params: { id } }) }
const showScrollTop = ref(false)
const handleScroll = () => { showScrollTop.value = window.scrollY > 400 }
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }) }
const chatOpen = ref(false)
const chatTab = ref('tawkto')
const switchTab = (tab) => { chatTab.value = tab }
const openTawkTo = () => { if (window.Tawk_API && window.Tawk_API.maximize) window.Tawk_API.maximize() }
const handleResize = () => { updateMobile() }

const addToCart = (product) => { cartStore.addItem(product) }
const goToCustomizer = (product) => { router.push({ name: 'Customizer', params: { id: product.id } }) }

onMounted(async () => {
  updateMobile()
  window.addEventListener('resize', handleResize)
  window.addEventListener('scroll', handleScroll)
  await fetchCategories()
  fetchDeal(); fetchBlogs(); fetchVideos(); fetchFeaturedProducts(); fetchTestimonials()
  await fetchApparelProducts()
  await fetchSlides()
  startFeaturedAuto(); startApparelAuto(); startVideoAuto(); startTestimonialAuto()
  setTimeout(() => {
    carouselInterval = setInterval(() => {
      if (slides.value.length) currentSlide.value = (currentSlide.value + 1) % slides.value.length
    }, 4000)
  }, 3000)
  bannerInterval = setInterval(() => {
    if (deal.value?.banners?.length) {
      currentBanner.value = (currentBanner.value + 1) % deal.value.banners.length
    }
  }, 3000)
})

onUnmounted(() => {
  if (carouselInterval) clearInterval(carouselInterval)
  if (bannerInterval) clearInterval(bannerInterval)
  stopFeaturedAuto(); stopApparelAuto(); stopVideoAuto()
  window.removeEventListener('resize', handleResize)
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
:deep(iframe[title*="chat"]), :deep(.tawk-button), :deep(#tawk-bubble-container) { display: none !important; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body, html { font-family: 'Poppins', sans-serif; background: white; color: #000; overflow-x: hidden !important; max-width: 100vw; }
.full-container { width: 100%; padding: 0 40px; box-sizing: border-box; }
@media (min-width: 1400px) { .full-container { padding: 0 60px; } }
@media (min-width: 2000px) { .full-container { padding: 0 80px; } }
@media (max-width: 768px) { .full-container { padding: 0 20px; } }
@media (max-width: 575px) { .full-container { padding: 0 12px; } }
.dark-mode { background: #0a0a0a; color: #fff; }
.dark-mode .section-title { color: #fff; }
.dark-mode .product-card-inner { background: #1a1a1a; color: #fff; }
.dark-mode .testimonials-section { background: #111; }

/* ✅ HERO CAROUSEL - Background always shows */
.hero-carousel { position: relative; height: 100vh; min-height: 100vh; overflow: hidden; }
.carousel-container { position: relative; height: 100%; }
.carousel-slide {
  height: 100vh;
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center center;
  background-repeat: no-repeat;
  animation: slideBackgroundFromRight 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}
/* ✅ Overlay is transparent gradient - does NOT block background image */
.carousel-overlay {
  position: absolute;
  height: 100%;
  inset: 0;
  z-index: 2;
  display: flex;
  align-items: center;
  background: linear-gradient(to right, rgba(255,255,255,0.55) 0%, rgba(255,255,255,0.1) 60%, transparent 100%);
}
.carousel-content { max-width: 100%; margin: 0 5%; width: 90%; height: 100%; }
.text-and-image-wrapper { max-width: 60%; text-align: center; margin-top: 12%; }
.title-button-row { width: 100%; display: flex; align-items: flex-start; flex-wrap: nowrap; justify-content: space-between; }
.main_title { width: 60%; font-size: 4rem; line-height: 0.9; font-weight: 800; font-style: italic; text-align: left; margin: 0; padding: 0; word-break: break-word; flex-shrink: 0; color: black; }
.button-wrapper { margin-top: 50px; flex-shrink: 0; align-self: flex-start; }
.single-line-btn { white-space: nowrap; display: inline-block; text-align: center; line-height: 1.2; padding: 10px 30px; background: black; color: white; border-radius: 20px; font-size: 20px; letter-spacing: 1px; font-weight: 550; border: none; cursor: pointer; transition: all 0.3s ease; }
.single-line-btn:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.4); transform: translateY(-2px); }
.hero-png { max-width: 110%; max-height: 850px; height: auto; animation: float 6s ease-in-out infinite; }
.animate-from-left { opacity: 0; transform: translateX(-150%); animation: slideInFromLeft 1.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; animation-delay: 0.4s; }
.animate-from-top { opacity: 0; transform: translate(-100%, -100%); animation: slideInFromTopLeft 1.1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; animation-delay: 0.2s; }
.delayed { opacity: 0; transform: translate(-100%, 100%); animation: slideInFromBottomLeft 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; animation-delay: 0.6s; }
.delayed-more { animation-delay: 0.55s; }
@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
.slide-enter-active, .slide-leave-active { transition: opacity 0.9s ease-in-out; }
.slide-enter-from, .slide-leave-to { opacity: 0; }
@keyframes slideBackgroundFromRight { 0% { transform: translateX(100%); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
@keyframes slideInFromLeft { 0% { transform: translateX(-150%); opacity: 0; } 70% { transform: translateX(6%); opacity: 1; } 100% { transform: translateX(0); opacity: 1; } }
@keyframes slideInFromTopLeft { 0% { transform: translate(-100%, -100%); opacity: 0; } 100% { transform: translate(0, 0); opacity: 1; } }
@keyframes slideInFromBottomLeft { 0% { transform: translate(-100%, 100%); opacity: 0; } 100% { transform: translate(0, 0); opacity: 1; } }
.carousel-control-left, .carousel-control-right { position: absolute; top: 50%; transform: translateY(-50%); z-index: 4; width: 60px; height: 60px; cursor: pointer; background: transparent; border: none; outline: none; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
.carousel-control-left i, .carousel-control-right i { color: #000; font-weight: 700; font-size: 2.2rem; }
.carousel-control-left { left: 10px; }
.carousel-control-right { right: 10px; }
.carousel-dots { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 4; display: flex; gap: 6px; align-items: center; }
.dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: rgba(0,0,0,0.4); border: none; padding: 0; cursor: pointer; transition: all 0.3s; }
.dot.active { background: #000; width: 20px; border-radius: 10px; }

/* Sports Icons */
.sports-icons-section { background: black; padding: 25px 0; overflow: hidden; position: relative; }
.sports-icons-section::before, .sports-icons-section::after { content: ''; position: absolute; top: 0; bottom: 0; width: 70px; z-index: 2; }
.sports-icons-section::before { left: 0; background: linear-gradient(to right, black, transparent); }
.sports-icons-section::after { right: 0; background: linear-gradient(to left, black, transparent); }
.scrolling { animation: iconScroll 25s linear infinite; }
.scrolling:hover { animation-play-state: paused; }
@keyframes iconScroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
.icons-track { display: flex; flex-direction: row; flex-wrap: nowrap; gap: 50px; width: max-content; align-items: center; padding-left: 100px; }
.icon-item { display: flex; flex-direction: column; align-items: center; gap: 10px; cursor: pointer; transition: all 0.3s ease; }
.icon-item:hover { transform: translateY(-4px); }
.icon-item:hover .icon-circle::after, .icon-item.active .icon-circle::after { opacity: 1; transform: scale(1); animation: rotateDotted 8s linear infinite; }
.icon-item:hover .icon-circle, .icon-item.active .icon-circle { transform: scale(1.08); }
.icon-image { width: 90%; height: 90%; object-fit: cover; }
.icon-name { color: white; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; max-width: 100px; }
.icon-circle { width: 110px; height: 110px; display: flex; align-items: center; justify-content: center; background: black; border-radius: 50%; overflow: hidden; transition: all 0.4s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.3); position: relative; }
.icon-circle::after { content: ''; position: absolute; inset: 0; border: 3px dotted #fff; border-radius: 50%; opacity: 0; transition: opacity 0.35s, transform 0.35s; transform: scale(0.92); pointer-events: none; }
@keyframes rotateDotted { from { transform: scale(0.92) rotate(0deg); } to { transform: scale(0.92) rotate(360deg); } }
.lead { color: #000; }

/* Ribbons */
.left-ribbon { position: relative; display: inline-block; background: #000; color: #fff; padding: 10px 40px 10px 30px; font-weight: 800; font-size: 1.8rem; margin-left: -80px; letter-spacing: 1px; margin-bottom: 40px; border-radius: 0px 25px 25px 0px; }
.left-ribbon.dark { background: #fff; color: #000; }
.left-ribbon span { position: relative; z-index: 2; }
.left2-ribbon { position: relative; display: inline-block; background: #ffffff; color: #000000; padding: 14px 40px; font-weight: 800; font-size: 1.8rem; margin-left: -80px; letter-spacing: 1px; margin-bottom: 40px; border-radius: 0px 25px 25px 0px; }

/* Deals */
.deals-section { background-color: #e0e0e0; }
.deal-banner-box { width: 90%; height: 530px; border-radius: 18px; overflow: hidden; }
.banner-img { width: 100%; height: 100%; object-fit: cover; }
.deal-card { position: relative; height: 260px; border-radius: 14px; overflow: hidden; transition: 0.3s ease; }
.deal-card-img { width: 100%; height: 100%; object-fit: cover; transition: 0.4s ease; }
.deal-card:hover .deal-card-img { transform: scale(1.08); }
.deal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.65); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s ease; }
.deal-card:hover .deal-overlay { opacity: 1; }
.deal-ribbon { position: absolute; top: 15px; left: -35px; background: #000000; color: #fff; padding: 2px 40px; font-size: 12px; font-weight: 700; transform: rotate(-45deg); box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 5; letter-spacing: 1px; }
.fade-enter-active, .fade-leave-active { transition: all 0.7s ease; }
.fade-enter-from { opacity: 0; filter: blur(15px); transform: scale(1.1); }
.fade-leave-to { opacity: 0; filter: blur(10px); }

/* ✅ FEATURED PRODUCTS - 5 cards on desktop */
.featured-products { position: relative; padding: 80px 0; overflow: hidden; background: url('/assets/images/lines texture.svg') no-repeat center center; background-size: cover; }
.featured-products::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,0.933); z-index: 1; }
.featured-products > * { position: relative; z-index: 2; }
.featured-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 3rem; }
.section-title { font-size: 2.8rem; font-weight: 800; color: #000; margin: 0; }
.tabs { display: flex; gap: 10px; flex-wrap: wrap; }
.tab-btn { padding: 10px 20px; border: 2px solid #000; background: #fff; color: #000; font-weight: 600; cursor: pointer; transition: all 0.3s; border-radius: 4px; }
.tab-btn:hover, .tab-btn.active { background: #000; color: #fff; }
.carousel-wrapper { position: relative; overflow: hidden; padding: 0 55px; }
.products-track { display: flex; will-change: transform; }

/* ✅ DEFAULT: 5 cards = 20% each */
.product-card { min-width: 20%; padding: 0 8px; box-sizing: border-box; }

.product-card-inner { background: #fff; border-radius: 12px; padding: 16px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s; display: flex; flex-direction: column; height: 100%; }
.product-card:hover .product-card-inner { transform: translateY(-8px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
.product-img { width: 100%; height: 240px; object-fit: contain; background: #f8f9fa; margin-bottom: 12px; border-radius: 8px; }
.product-meta-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; padding: 0 4px; }
.product-title { font-size: 12px; font-weight: 600; color: #000; margin: 0; flex: 1; text-align: left; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-price { color: #000; font-weight: 800; font-size: 0.95rem; margin: 0; white-space: nowrap; flex-shrink: 0; }
.add-cart-btn { background: #000; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.3s; margin-top: auto; font-size: 12px; }
.add-cart-btn:hover { background: #333; transform: translateY(-2px); }
.carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 46px; height: 46px; border-radius: 50%; background: #000; border: none; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; box-shadow: 0 8px 20px rgba(0,0,0,0.2); transition: all 0.3s; z-index: 10; }
.carousel-btn i { font-size: 20px; }
.carousel-btn-prev { left: 5px; }
.carousel-btn-next { right: 5px; }
.carousel-btn:hover { background: #222; transform: translateY(-50%) scale(1.05); }

/* Apparel */
.apparel-card-inner { background: transparent !important; border: 1px solid #444; border-radius: 12px; padding: 16px; transition: all 0.35s ease; display: flex; flex-direction: column; height: 100%; }
.apparel-card-inner:hover { border-color: #fff; transform: translateY(-6px); }
.product-image-wrapper { margin-bottom: 12px; }
.apparel-meta-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; padding: 0 4px; }
.product-name { font-size: 12px; font-weight: 600; margin: 0; flex: 1; text-align: left; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.add-to-cart-btn { margin-top: auto; font-size: 12px; }

/* Benefits */
.benefits-section { background: #111; }
.benefit-card { background: white; border: 1px solid #333; transition: all 0.3s; color: black; border-radius: 8px; }
.benefit-card:hover { background: black; color: white; transform: translateY(-8px); }
.benefit-card:hover .icon, .benefit-card:hover h4, .benefit-card:hover p { color: white !important; }
.icon { color: black; line-height: 1; transition: all 0.3s; }
.benefits-desktop { display: flex; }
.benefits-mobile-carousel { display: none; }

/* Videos */
.latest-videos-section { position: relative; padding: 80px 0; overflow: hidden; background: url('/assets/images/lines texture.svg') no-repeat center center; background-size: cover; }
.latest-videos-section::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,0.933); z-index: 1; }
.latest-videos-section > * { position: relative; z-index: 2; }
.videos-carousel-wrapper { position: relative; padding: 0 70px; }
.videos-carousel-container { overflow: hidden; border-radius: 12px; }
.videos-track { display: flex; will-change: transform; }
.video-card { flex-shrink: 0; padding: 0 12px; box-sizing: border-box; width: 33.333%; }
.video-thumbnail { position: relative; width: 100%; height: 350px; border-radius: 12px; overflow: hidden; cursor: pointer; transition: all 0.4s; }
.video-thumbnail:hover { transform: translateY(-10px) scale(1.02); }
.video-thumbnail img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s; }
.video-thumbnail:hover img { transform: scale(1.1); }
.play-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.6)); display: flex; align-items: center; justify-content: center; }
.play-button { width: 70px; height: 70px; background: rgba(255,255,255,0.95); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: black; font-size: 2.2rem; transition: all 0.4s; box-shadow: 0 8px 25px rgba(0,0,0,0.3); }
.video-thumbnail:hover .play-button { transform: scale(1.15); background: black; color: white; }
.video-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; background: #000; color: #fff; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; z-index: 10; transition: all 0.3s; box-shadow: 0 5px 20px rgba(0,0,0,0.3); }
.video-arrow:hover { background: #333; transform: translateY(-50%) scale(1.1); }
.video-arrow.prev-arrow { left: 5px; }
.video-arrow.next-arrow { right: 5px; }
.video-title-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); color: white; padding: 20px 15px 15px; font-size: 16px; font-weight: 600; text-align: center; opacity: 0; transition: opacity 0.3s; }
.video-thumbnail:hover .video-title-overlay { opacity: 1; }
.video-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.video-modal-content { position: relative; width: 90%; max-width: 1400px; height: 80vh; background: #000; border-radius: 12px; overflow: hidden; }
.fullscreen-video { width: 100%; height: 100%; object-fit: contain; background: #000; }
.close-video-btn { position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; color: #000; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
.close-video-btn:hover { background: black; color: white; transform: scale(1.1) rotate(90deg); }

/* Testimonials */
.testimonials-section { background-color: #eeecec; padding: 100px 0; }
.testimonial-card, .testimonial-text, .author-name, .author-position, .stars-rating { user-select: none; -webkit-user-select: none; }
.testimonials-outer-wrapper { display: flex; align-items: center; width: 100%; gap: 0; }
.t-arrow { flex-shrink: 0; width: 48px; height: 48px; background: #000; color: #fff; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: all 0.3s; }
.t-arrow:hover { background: #333; transform: scale(1.1); }
.t-arrow-left { margin-right: 16px; }
.t-arrow-right { margin-left: 16px; }
.testimonials-carousel-inner { flex: 1; overflow: hidden; padding-top: 36px; }
.testimonials-carousel { display: flex; will-change: transform; }
.testimonial-item { flex: 0 0 33.333%; padding: 0 12px; box-sizing: border-box; }
.t-card-wrapper { position: relative; padding-top: 28px; }
.quote-float { position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 10; font-size: 3.6rem; line-height: 1; color: #000; pointer-events: none; }
.testimonial-card { background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 20px; min-height: auto; position: relative; transition: all 0.35s; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
.testimonial-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); border-color: #333; }
.stars-rating { font-size: 1rem; letter-spacing: 3px; margin-bottom: 12px; text-align: left; }
.stars-rating .bi-star-fill { color: #000 !important; }
.stars-rating .bi-star { color: #ccc !important; }
.testimonial-text { font-size: 1rem; line-height: 1.75; color: #444; margin-bottom: 20px; }
.testimonial-author { display: flex; align-items: center; gap: 15px; }
.author-image { width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 3px solid #f0f0f0; flex-shrink: 0; }
.author-image img { width: 100%; height: 100%; object-fit: cover; }
.author-name { font-size: 1.1rem; font-weight: 700; color: #111; margin: 0; }
.author-position { font-size: 0.85rem; color: #777; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 0.8px; }

/* Blog */
.recent-blog-section { position: relative; padding: 80px 0; overflow: hidden; background: url('/assets/images/lines texture.svg') no-repeat center center; background-size: cover; }
.recent-blog-section::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,0.933); z-index: 1; }
.recent-blog-section * { position: relative; z-index: 2; }
.blog-card { background: white; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.35s; height: 100%; }
.blog-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
.blog-image { flex: 0 0 45%; overflow: hidden; }
.blog-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
.blog-card:hover .blog-image img { transform: scale(1.08); }
.blog-content { flex: 1; padding: 25px 30px; display: flex; flex-direction: column; justify-content: center; }
.blog-meta { display: flex; gap: 20px; margin-bottom: 12px; font-size: 0.9rem; color: #777; text-transform: uppercase; }
.blog-title { font-size: 1.4rem; font-weight: 700; color: #000; margin-bottom: 12px; line-height: 1.3; }
.blog-excerpt { font-size: 1rem; color: #555; line-height: 1.6; margin-bottom: 18px; }
.read-more { color: #000; font-weight: 600; text-decoration: none; transition: color 0.3s; }
.read-more:hover { color: #dc3545; }
.btn-view-all { background: #000; color: white; padding: 14px 40px; border-radius: 50px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s; }
.btn-view-all:hover { background: #333; transform: translateY(-3px); }

/* Scroll top */
.scroll-top-btn { position: fixed; bottom: 30px; right: 30px; z-index: 9997; width: 52px; height: 52px; border-radius: 50%; background: #000; color: #fff; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; cursor: pointer; box-shadow: 0 4px 20px rgba(0,0,0,0.35); opacity: 0; transform: translateY(20px) scale(0.8); pointer-events: none; transition: opacity 0.35s ease, transform 0.35s ease, background 0.25s; }
.scroll-top-btn.visible { opacity: 1; transform: translateY(0) scale(1); pointer-events: auto; }
.scroll-top-btn:hover { background: #fff; color: #000; transform: translateY(-3px) scale(1.08); }

/* ============================================
   ✅ FULLY RESPONSIVE BREAKPOINTS
   ============================================ */

/* 4 cards: 992px - 1399px */
@media (min-width: 992px) and (max-width: 1399px) {
  .product-card { min-width: 25% !important; }
}

/* 3 cards: 768px - 991px */
@media (min-width: 768px) and (max-width: 991px) {
  .product-card { min-width: 33.333% !important; }
  .video-card { width: 50% !important; }
  .testimonial-item { flex: 0 0 50% !important; }
  .t-arrow { width: 38px !important; height: 38px !important; }
  .t-arrow-left { margin-right: 8px !important; }
  .t-arrow-right { margin-left: 8px !important; }
  .icons-track { gap: 35px !important; }
  .icon-circle { width: 75px !important; height: 75px !important; }
  .icon-name { font-size: 11px !important; }
  .main_title { font-size: 3.2rem !important; width: 60% !important; }
  .hero-png { max-height: 550px !important; }
  .carousel-wrapper { padding: 0 50px !important; }
  .video-thumbnail { height: 280px !important; }
  .blog-card { flex-direction: row !important; }
  .blog-image { flex: 0 0 40% !important; height: auto !important; }
}

/* Tablet & mobile: benefits carousel */
@media (max-width: 991px) {
  .benefits-desktop { display: none !important; }
  .benefits-mobile-carousel { display: block; position: relative; padding: 0 55px; }
  .benefits-track-wrapper { overflow: hidden; }
  .benefits-track { display: flex; transition: transform 0.5s ease; }
  .benefit-slide { flex-shrink: 0; width: 100%; padding: 0 10px; box-sizing: border-box; }
  .benefit-btn { position: absolute; top: 45%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 2px solid #000; color: #000; display: flex; align-items: center; justify-content: center; font-size: 1rem; cursor: pointer; z-index: 5; transition: all 0.3s; }
  .benefit-btn:hover:not(:disabled) { background: #000; color: #fff; }
  .benefit-btn:disabled { opacity: 0.3; cursor: not-allowed; }
  .benefit-btn-prev { left: 5px; }
  .benefit-btn-next { right: 5px; }
  .benefit-dots { display: flex; justify-content: center; gap: 8px; margin-top: 20px; }
  .bdot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.4); cursor: pointer; transition: all 0.3s; }
  .bdot.active { background: #fff; width: 20px; border-radius: 10px; }
}

/* 2 cards: 576px - 767px */
@media (max-width: 767px) {
  .product-card { min-width: 50% !important; }
  .video-card { width: 50% !important; }
  .testimonial-item { flex: 0 0 100% !important; }
  .product-img { height: 220px !important; }
  .video-thumbnail { height: 260px !important; }
}

/* 1 card: mobile small */
@media (max-width: 575px) {
  .hero-carousel { height: 100vh !important; min-height: 600px !important; }
  .carousel-content { margin: 0 !important; width: 100% !important; padding: 0 15px !important; }
  .text-and-image-wrapper { max-width: 100% !important; margin-top: 20% !important; }
  .title-button-row { flex-direction: column !important; align-items: center !important; gap: 20px !important; }
  .main_title { width: 100% !important; font-size: 2.2rem !important; text-align: center !important; padding: 0 10px !important; }
  .button-wrapper { margin-top: 0 !important; width: 100% !important; display: flex !important; justify-content: center !important; }
  .single-line-btn { font-size: 15px !important; padding: 12px 35px !important; }
  .lead { font-size: 14px !important; text-align: center !important; padding: 0 20px !important; }
  .hero-png { max-width: 90% !important; max-height: 300px !important; }
  .sports-icons-section { padding: 20px 0 !important; }
  .icons-track { gap: 28px !important; padding-left: 60px !important; }
  .icon-circle { width: 58px !important; height: 58px !important; }
  .icon-name { font-size: 9px !important; max-width: 65px !important; }
  .featured-products { padding: 40px 0 !important; }
  .featured-header { flex-direction: column !important; align-items: flex-start !important; }
  .tabs { width: 100%; flex-wrap: wrap !important; }
  .tab-btn { font-size: 12px !important; padding: 8px 15px !important; flex: 1; min-width: 80px; }
  .carousel-wrapper { padding: 0 44px !important; }
  .product-card { min-width: 100% !important; padding: 0 8px !important; }
  .product-img { height: 200px !important; }
  .carousel-btn { width: 35px !important; height: 35px !important; }
  .carousel-btn i { font-size: 16px !important; }
  .latest-videos-section { padding: 40px 0 !important; }
  .videos-carousel-wrapper { padding: 0 44px !important; }
  .video-card { width: 100% !important; padding: 0 6px !important; }
  .video-thumbnail { height: 220px !important; }
  .video-arrow { width: 38px !important; height: 38px !important; font-size: 1rem !important; }
  .video-modal-content { width: 95% !important; height: 50vh !important; }
  .testimonials-section { padding: 50px 0 !important; }
  .testimonial-item { flex: 0 0 100% !important; padding: 0 6px !important; }
  .testimonial-card { padding: 16px !important; }
  .t-arrow { width: 34px !important; height: 34px !important; font-size: 0.9rem !important; }
  .t-arrow-left { margin-right: 6px !important; }
  .t-arrow-right { margin-left: 6px !important; }
  .testimonials-carousel-inner { padding-top: 30px !important; }
  .quote-float { font-size: 2.8rem !important; }
  .recent-blog-section { padding: 40px 0 !important; }
  .col-6.col-lg-6 { padding-left: 6px !important; padding-right: 6px !important; }
  .blog-card { flex-direction: column !important; box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important; }
  .blog-image { display: block !important; flex: 0 0 auto !important; height: 100px !important; }
  .blog-image img { width: 100% !important; height: 100px !important; object-fit: cover !important; }
  .blog-content { padding: 10px 12px !important; }
  .blog-meta { display: none !important; }
  .blog-excerpt { display: none !important; }
  .blog-title { font-size: 0.82rem !important; margin-bottom: 8px !important; display: -webkit-box !important; -webkit-line-clamp: 2 !important; -webkit-box-orient: vertical !important; overflow: hidden !important; }
  .read-more { font-size: 0.78rem !important; color: #dc3545 !important; font-weight: 700 !important; }
  .left-ribbon { font-size: 1.3rem !important; padding: 8px 24px 8px 20px !important; margin-left: -20px !important; }
  .left2-ribbon { font-size: 1.3rem !important; padding: 8px 24px !important; margin-left: -20px !important; }
}

/* Landscape mobile */
@media (orientation: landscape) and (max-height: 600px) {
  .hero-carousel { height: 100vh !important; }
  .text-and-image-wrapper { margin-top: 3% !important; }
  .main_title { font-size: 2.5rem !important; }
  .hero-png { max-height: 500px !important; }
}

/* Very large screens */
@media (min-width: 2000px) {
  .product-card { min-width: 20% !important; }
  .video-card { width: 20% !important; }
  .testimonial-item { flex: 0 0 25% !important; }
  .product-img { height: 320px !important; }
}

/* General */
img { max-width: 100%; height: auto; }
*:focus-visible { outline: 2px solid #000; outline-offset: 2px; }

/* Chat */
.tawk-icon { background: #000 !important; }
.tawk-btn { background: #000 !important; }
.tawk-btn:hover { background: #222 !important; }
.tawkto-tab { justify-content: center; }
#tawk-bubble-container, .tawk-min-container, iframe[title="chat widget"] { display: none !important; }
.chat-widget-wrapper { position: fixed; bottom: 30px; left: 24px; z-index: 99990; font-family: 'Poppins', sans-serif; }
.launcher-btn { width: 58px; height: 58px; border-radius: 50%; background: #000; color: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 6px 24px rgba(0,0,0,0.35); transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), background 0.25s; position: relative; z-index: 2; border: 2.5px solid #fff; }
.launcher-btn:hover { transform: scale(1.1); background: #222; }
.launcher-btn.open { background: #111; }
.launcher-icon { position: absolute; transition: opacity 0.25s, transform 0.3s cubic-bezier(0.34,1.56,0.64,1); }
.close-icon { opacity: 0; transform: rotate(-90deg) scale(0.6); font-size: 1.2rem; }
.chat-icon { opacity: 1; transform: rotate(0deg) scale(1); }
.launcher-btn.open .close-icon { opacity: 1; transform: rotate(0deg) scale(1); }
.launcher-btn.open .chat-icon { opacity: 0; transform: rotate(90deg) scale(0.6); }
.launcher-pulse { position: absolute; bottom: 2px; width: 58px; height: 58px; border-radius: 50%; background: rgba(0,0,0,0.15); animation: pulseRing 2.4s ease-out infinite; pointer-events: none; z-index: 1; }
@keyframes pulseRing { 0% { transform: scale(1); opacity: 0.7; } 100% { transform: scale(2); opacity: 0; } }
.widget-panel { position: absolute; bottom: 72px; left: 0; width: 340px; background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.22), 0 4px 16px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column; max-height: 560px; border: 1px solid #e8e8e8; }
.widget-pop-enter-active { transition: all 0.35s cubic-bezier(0.34,1.56,0.64,1); }
.widget-pop-leave-active { transition: all 0.22s ease-in; }
.widget-pop-enter-from { opacity: 0; transform: translateY(20px) scale(0.92); transform-origin: bottom left; }
.widget-pop-leave-to { opacity: 0; transform: translateY(10px) scale(0.95); transform-origin: bottom left; }
.widget-header { background: #000; color: #fff; padding: 16px 18px; display: flex; align-items: center; gap: 12px; }
.header-avatar { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid rgba(255,255,255,0.3); }
.header-info { flex: 1; }
.header-title { display: block; font-weight: 700; font-size: 0.95rem; }
.header-status { display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: rgba(255,255,255,0.75); margin-top: 2px; }
.status-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; display: inline-block; animation: blink 2s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }
.header-close { background: rgba(255,255,255,0.12); border: none; color: #fff; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; transition: background 0.2s; }
.header-close:hover { background: rgba(255,255,255,0.25); }
.tab-row { display: flex; border-bottom: 1.5px solid #f0f0f0; background: #fafafa; }
.tab-item { flex: 1; padding: 11px 0; background: none; border: none; font-size: 0.82rem; font-weight: 600; color: #888; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 6px; border-bottom: 2.5px solid transparent; margin-bottom: -1.5px; font-family: 'Poppins', sans-serif; }
.tab-item:hover { color: #000; }
.tab-item.active { color: #000; border-bottom-color: #000; background: #fff; }
.chat-tab-content { display: flex; flex-direction: column; flex: 1; overflow: hidden; }
.whatsapp-tab { justify-content: center; }
.wa-body { display: flex; flex-direction: column; align-items: center; padding: 28px 24px; text-align: center; }
.wa-icon-wrap { width: 68px; height: 68px; border-radius: 50%; background: #000; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 14px; box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
.wa-title { font-size: 1.05rem; font-weight: 700; color: #111; margin: 0 0 8px; }
.wa-desc { font-size: 0.82rem; color: #666; line-height: 1.5; margin: 0 0 12px; }
.wa-hours { font-size: 0.76rem; color: #888; background: #f5f5f5; padding: 5px 14px; border-radius: 20px; display: flex; align-items: center; gap: 6px; margin-bottom: 18px; }
.wa-cta-btn { display: flex; align-items: center; gap: 8px; background: #000; color: #fff; padding: 12px 28px; border-radius: 30px; font-size: 0.88rem; font-weight: 700; text-decoration: none; transition: all 0.25s; font-family: 'Poppins', sans-serif; box-shadow: 0 4px 14px rgba(0,0,0,0.2); border: none; cursor: pointer; }
.wa-cta-btn:hover { background: #222; transform: translateY(-2px); }
.wa-number { font-size: 0.76rem; color: #aaa; margin-top: 10px; }
@media (max-width: 400px) { .widget-panel { width: 290px; } .chat-widget-wrapper { left: 10px; bottom: 16px; } }
</style>
