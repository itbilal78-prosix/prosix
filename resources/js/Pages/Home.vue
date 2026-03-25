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
              <div :key="currentSlide" class="carousel-slide active"
                :style="{ backgroundImage: `url(${isMobile && safeSlides[currentSlide].mobileBackgroundImage ? safeSlides[currentSlide].mobileBackgroundImage : safeSlides[currentSlide].backgroundImage})` }">
                <div class="carousel-overlay">
                  <div class="carousel-content d-flex align-items-center justify-content-start h-100">
                    <div class="text-and-image-wrapper d-flex flex-column align-items-start text-start">
                      <div class="title-button-row d-flex align-items-start gap-4 mb-3">
                        <h1 class="display-3 fw-bold main_title italic-title animate-from-top">{{ safeSlides[currentSlide].title }}</h1>
                        <div class="button-wrapper flex-shrink-0 animate-from-top delayed">
                          <button v-if="safeSlides[currentSlide].buttonText" class="btn btn-md px-4 single-line-btn" @click="handleBuy(safeSlides[currentSlide].buttonLink)">{{ safeSlides[currentSlide].buttonText }}</button>
                        </div>
                      </div>
                      <p v-if="safeSlides[currentSlide].subtitle" class="lead text-white mt-2 mb-4 animate-from-top delayed-more">{{ safeSlides[currentSlide].subtitle }}</p>
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
<div v-else class="text-center py-5 mt-5">
  <div class="pro-spinner">
    <svg viewBox="0 0 50 50" class="spin-svg">
      <circle cx="25" cy="25" r="20" fill="none" stroke="#e0e0e0" stroke-width="4"/>
      <circle cx="25" cy="25" r="20" fill="none" stroke="#000" stroke-width="4"
        stroke-dasharray="60 200" stroke-linecap="round"
        class="spin-arc"/>
    </svg>
    <div class="spin-dot"></div>
  </div>
  <p class="spin-text">Loading...</p>
</div>
      <!-- Sports Icons -->
      <section class="sports-icons-section">
        <div class="icons-track" :class="{ 'scrolling': isMobile || sportsIcons.length > 12 }">
          <div class="icon-item" v-for="(icon, index) in scrollingIcons" :key="index" @click="selectTeam(icon.teamId)" :class="{ active: activeTeam === icon.teamId }">
            <div class="icon-circle"><img :src="icon.highlight_image || icon.image" :alt="icon.name" class="icon-image" /></div>
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
          <div class="deals-layout">
            <div class="deals-banner-col">
              <div v-if="deal.banners?.length" class="deal-banner-box">
                <Transition name="fade" mode="out-in">
                  <img :key="currentBanner" :src="deal.banners[currentBanner].image_path" class="banner-img" alt="Banner" />
                </Transition>
              </div>
            </div>
            <div class="deals-cards-col">
              <div class="deals-cards-grid">
                <div v-for="img in deal.images" :key="img.id" class="deal-card">
                  <div v-if="img.label" class="deal-ribbon">{{ img.label }}</div>
                  <img :src="img.image_path" class="deal-card-img" />
                  <div class="deal-overlay"><a :href="img.link || '#'" class="btn btn-light btn-sm">View More</a></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Featured Products -->
      <section class="featured-products py-5">
        <div class="full-container">
          <div class="featured-header">
            <div class="left-ribbon featured-ribbon"><span>Trending Now</span></div>
            <div class="tabs">
              <button v-for="tab in tabs" :key="tab.value" class="tab-btn" :class="{ active: activeTab === tab.value }" @click="setTab(tab.value)">{{ tab.label }}</button>
            </div>
          </div>
          <div class="carousel-wrapper position-relative overflow-hidden">
            <div class="products-track d-flex" :style="featuredTrackStyle">
              <div v-for="(product, idx) in infiniteFeatured" :key="idx" class="product-card flex-shrink-0">
                <div class="product-card-inner bg-white rounded shadow-sm text-center">
                  <div class="home-card-img-wrap">
                    <button v-if="product.type === 'model'" class="home-cart-icon-btn" @click.stop="router.push(`/product/${product.id}?type=model`)" title="View Product">
                      <i class="bi bi-cart" style="transform:scaleX(-1);display:inline-block;"></i>
                    </button>
                    <img :src="product.image" :alt="product.name" class="product-img img-fluid" loading="lazy" />
                  </div>
                  <div class="product-meta-row">
                    <h5 class="product-title">{{ product.name }}</h5>
                    <p class="product-price">${{ product.price.toFixed(2) }}</p>
                  </div>
                  <button v-if="product.type === 'product'" class="add-cart-btn btn btn-dark w-100 py-2" @click="goToProduct(product)">View &amp; Buy</button>
                  <button v-else-if="product.type === 'model'" class="add-cart-btn btn btn-dark w-100 py-2" @click="goToCustomizer(product)">Customize</button>
                </div>
              </div>
              <div v-if="displayedProducts.length === 0" class="w-100 text-center py-5"><p class="text-muted fs-4">No products found in this category...</p></div>
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
            <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;"><div class="icon mb-4"><i class="bi bi-truck fs-1"></i></div><h4 class="mb-3">Free Shipping Delivery &amp; Returns</h4><p class="mb-0">Shop with confidence and have your favorite Furniture delivered right to your doorstep without any additional cost.</p></div>
            <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;"><div class="icon mb-4"><i class="bi bi-heart fs-1"></i></div><h4 class="mb-3">30 Days Money Back Guarantee</h4><p class="mb-0">We Guarantee to rectify any unsatisfactory experience you may have with your purchase. No Queries posed.</p></div>
            <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;"><div class="icon mb-4"><i class="bi bi-headset fs-1"></i></div><h4 class="mb-3">Online free custom support 24/7</h4><p class="mb-0">Need help with your electronics? Get in touch with us anytime, anywhere and let's get your tech sorted.</p></div>
          </div>
          <!-- Mobile chain carousel for benefits -->
          <div class="benefits-mobile-carousel">
            <div class="benefits-chain-track" :style="benefitChainStyle">
              <div class="benefit-chain-slide" v-for="(item, idx) in infiniteBenefits" :key="idx">
                <div class="benefit-card text-center p-4">
                  <div class="icon mb-4"><i :class="item.icon + ' fs-1'"></i></div>
                  <h4 class="mb-3">{{ item.title }}</h4>
                  <p class="mb-0">{{ item.text }}</p>
                </div>
              </div>
            </div>
            <button class="benefit-btn benefit-btn-prev" @click="prevBenefit"><i class="bi bi-chevron-left"></i></button>
            <button class="benefit-btn benefit-btn-next" @click="nextBenefit"><i class="bi bi-chevron-right"></i></button>
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
              <div v-for="(product, idx) in infiniteApparel" :key="idx" class="product-card flex-shrink-0">
                <div class="product-card-inner apparel-card-inner text-center">
                  <div class="product-image-wrapper home-card-img-wrap">
                    <button v-if="product.type === 'model'" class="home-cart-icon-btn apparel-cart-icon" @click.stop="router.push(`/product/${product.id}?type=model`)" title="View Product">
                      <i class="bi bi-cart" style="transform:scaleX(-1);display:inline-block;"></i>
                    </button>
                    <img :src="product.image" :alt="product.name" class="product-img img-fluid" loading="lazy" />
                  </div>
                  <div class="product-meta-row apparel-meta-row">
                    <h5 class="product-name  fw-semibold">{{ product.name }}</h5>
                    <p class="product-price  fw-bold">${{ product.price.toFixed(2) }}</p>
                  </div>
                  <button v-if="product.type === 'product'" class="btn btn-light w-100 py-2 fw-bold add-to-cart-btn" @click="goToProduct(product)">View &amp; Buy</button>
                  <button v-else-if="product.type === 'model'" class="btn button-apperal w-100 py-2 fw-bold add-to-cart-btn" @click="goToCustomizer(product)">Customize</button>
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

    <div class="float-icons-left">
      <div class="float-icon-wrap">
        <a href="https://wa.me/19292104402" target="_blank" class="float-icon-btn wa-icon-btn"><i class="bi bi-whatsapp"></i></a>
        <div class="float-icon-pulse wa-pulse"></div>
        <div class="float-icon-tooltip">WhatsApp</div>
      </div>
    </div>
    <div class="float-icons-right">
      <div class="float-icon-wrap" @click="openTawkTo" title="Live Chat">
        <div class="float-icon-btn"><i class="bi bi-headset"></i></div>
        <div class="float-icon-pulse"></div>
        <div class="float-icon-tooltip">Live Chat</div>
      </div>
    </div>

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
          <div class="modal-body text-center px-5 pb-2">
            <h5 class="fw-bold mb-2">Login Required</h5>
            <p class="text-muted mb-0">To use the Customizer, you must first log in to your account.</p>
          </div>
          <div class="modal-footer justify-content-center border-0 px-5 pb-4 gap-3">
            <button class="btn btn-outline-secondary rounded-pill px-4" @click="showLoginModal = false">Cancel</button>
            <router-link :to="{ path: '/user-login', query: { redirect: `/models/${pendingModelId}` } }" class="btn btn-dark rounded-pill px-4" @click="showLoginModal = false">Login</router-link>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showLoginModal" class="modal-backdrop fade show" @click="showLoginModal = false"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useCartStore } from '@/store/cart'

const cartStore = useCartStore()
const router = useRouter()

const activeTeam = ref(null)
const activeTab = ref('all')
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
const slides = ref([])
const safeSlides = computed(() => slides.value.filter(s => s && typeof s.backgroundImage === 'string' && s.backgroundImage.trim() !== ''))
const sportsIcons = ref([])
const currentBanner = ref(0)
let bannerInterval = null
const showLoginModal = ref(false)
const pendingModelId = ref(null)

// ============================================================
// UNIVERSAL INFINITE CAROUSEL — FIXED
// Hamesha items ko repeat karo jab tak track full na ho
// Sports icons ki tarah seamless infinite scroll
// ============================================================


function useInfiniteCarousel(items, itemsPerView, autoSpeed) {
  const index = ref(0)
  const isTransitioning = ref(false)
  const transitionVal = ref('transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)')

  // Items ko itna repeat karo ke kam se kam itemsPerView * 3 ho jayen
  const paddedItems = computed(() => {
    if (items.value.length === 0) return []
const needed = Math.max(itemsPerView.value * 20, 60)
    let result = []
    while (result.length < needed) {
      result = [...result, ...items.value]
    }
    return result
  })

  // Infinite ke liye teen copies
  const infinite = computed(() => {
    if (paddedItems.value.length === 0) return []
    return [...paddedItems.value, ...paddedItems.value, ...paddedItems.value]
  })

  const chunkSize = computed(() => paddedItems.value.length)

  const trackStyle = computed(() => {
    const pct = 100 / itemsPerView.value
    const translateX = (index.value + chunkSize.value) * pct
    return { transform: `translateX(-${translateX}%)`, transition: transitionVal.value }
  })

  const next = () => {
    if (isTransitioning.value) return
    isTransitioning.value = true
    transitionVal.value = 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)'
    index.value++
    setTimeout(() => {
      if (index.value >= chunkSize.value) {
        transitionVal.value = 'none'
        index.value = 0
        nextTick(() => { isTransitioning.value = false })
      } else {
        isTransitioning.value = false
      }
    }, 460)
  }

  const prev = () => {
    if (isTransitioning.value) return
    isTransitioning.value = true
    transitionVal.value = 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1)'
    index.value--
    setTimeout(() => {
      if (index.value < 0) {
        transitionVal.value = 'none'
        index.value = chunkSize.value - 1
        nextTick(() => { isTransitioning.value = false })
      } else {
        isTransitioning.value = false
      }
    }, 460)
  }

  let autoTimer = null
  const startAuto = () => {
    stopAuto()
    autoTimer = setInterval(() => { next() }, autoSpeed)
  }
  const stopAuto = () => { if (autoTimer) { clearInterval(autoTimer); autoTimer = null } }

  return { trackStyle, infinite, next, prev, startAuto, stopAuto }
}

// ── Featured Products ──────────────────────────────────────
const featuredProducts = ref([])
const tabs = ref([
  { label: 'All', value: 'all' },
  { label: 'New Arrivals', value: 'new' },
  { label: 'Best Sellers', value: 'bestsellers' }
])
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

const featuredItemsPerView = computed(() => {
  if (typeof window === 'undefined') return 5
  if (window.innerWidth < 576)  return 2
  if (window.innerWidth < 768)  return 2
  if (window.innerWidth < 992)  return 3
  if (window.innerWidth < 1400) return 4
  return 5
})
const { trackStyle: featuredTrackStyle, infinite: infiniteFeatured, next: nextProduct, prev: prevProduct, startAuto: startFeaturedAuto, stopAuto: stopFeaturedAuto } = useInfiniteCarousel(displayedProducts, featuredItemsPerView, 2500)

// ── Apparel ────────────────────────────────────────────────
const apparelProducts = ref([])
const fetchApparelProducts = async () => {
  try { const res = await axios.get('/api/apparel-products'); apparelProducts.value = res.data || [] } catch (e) { console.error(e) }
}
const apparelItemsPerView = computed(() => {
  if (typeof window === 'undefined') return 5
  if (window.innerWidth < 576)  return 2
  if (window.innerWidth < 768)  return 2
  if (window.innerWidth < 992)  return 3
  if (window.innerWidth < 1400) return 4
  return 5
})
const { trackStyle: apparelTrackStyle, infinite: infiniteApparel, next: nextApparel, prev: prevApparel, startAuto: startApparelAuto, stopAuto: stopApparelAuto } = useInfiniteCarousel(apparelProducts, apparelItemsPerView, 2500)

// ── Videos ─────────────────────────────────────────────────
const sportsVideos = ref([])
const videoItemsPerView = computed(() => { if (window.innerWidth < 576) return 1; if (window.innerWidth < 992) return 2; return 3 })
const fetchVideos = async () => { try { const res = await axios.get('/api/videos'); sportsVideos.value = res.data || [] } catch (e) { console.error(e) } }
const { trackStyle: videoTrackStyle, infinite: infiniteVideos, next: nextVideo, prev: prevVideo, startAuto: startVideoAuto, stopAuto: stopVideoAuto } = useInfiniteCarousel(sportsVideos, videoItemsPerView, 3000)
const playVideo = (video) => { currentVideoUrl.value = video.video_url; showVideoModal.value = true; stopVideoAuto() }
const closeVideo = () => { showVideoModal.value = false; currentVideoUrl.value = ''; startVideoAuto() }

// ── Testimonials ───────────────────────────────────────────
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

// ── Benefits mobile chain carousel ────────────────────────
const benefitsData = ref([
  { icon: 'bi bi-truck',   title: 'Free Shipping Delivery & Returns', text: 'Shop with confidence and have your favorite Furniture delivered right to your doorstep without any additional cost.' },
  { icon: 'bi bi-heart',   title: '30 Days Money Back Guarantee',      text: 'We Guarantee to rectify any unsatisfactory experience you may have with your purchase. No Queries posed.' },
  { icon: 'bi bi-headset', title: 'Online free custom support 24/7',   text: "Need help with your electronics? Get in touch with us anytime, anywhere and let's get your tech sorted." }
])
const benefitItemsPerView = computed(() => 1)
const { trackStyle: benefitChainStyle, infinite: infiniteBenefits, next: nextBenefit, prev: prevBenefit, startAuto: startBenefitAuto, stopAuto: stopBenefitAuto } = useInfiniteCarousel(benefitsData, benefitItemsPerView, 3000)

// ── Categories ─────────────────────────────────────────────
const fetchCategories = async () => {
  try {
    const res = await axios.get('/api/highlighted')
    if (!Array.isArray(res.data)) { sportsIcons.value = []; return }
    sportsIcons.value = res.data.map(cat => ({ teamId: cat.id, name: cat.name, image: cat.icon_image, highlight_image: cat.highlight_image }))
  } catch { sportsIcons.value = [] }
}
const scrollingIcons = computed(() => {
  if (isMobile.value || sportsIcons.value.length > 12) return [...sportsIcons.value, ...sportsIcons.value]
  return sportsIcons.value
})

const deal = ref(null)
const fetchDeal = async () => { try { const res = await axios.get('/api/latest-deal'); deal.value = res.data } catch (e) { console.error(e) } }
const blogs = ref([])
const fetchBlogs = async () => { try { const res = await axios.get('/api/blogs'); blogs.value = res.data } catch (e) { console.error(e) } }
const fetchSlides = async () => {
  try {
    const res = await axios.get('/api/banners')
    slides.value = (res.data || []).map(s => ({ ...s, backgroundImage: s.backgroundImage ? (s.backgroundImage.startsWith('http') ? s.backgroundImage : `${window.location.origin}${s.backgroundImage}`) : '', mobileBackgroundImage: s.mobileBackgroundImage ? (s.mobileBackgroundImage.startsWith('http') ? s.mobileBackgroundImage : `${window.location.origin}${s.mobileBackgroundImage}`) : '' }))
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
const openTawkTo = () => { if (window.Tawk_API && window.Tawk_API.maximize) window.Tawk_API.maximize() }
const handleResize = () => {
  updateMobile()
  // Restart auto so shouldLoop re-evaluates on resize
  startFeaturedAuto(); startApparelAuto(); startVideoAuto(); startTestimonialAuto(); startBenefitAuto()
}
const goToProduct = (product) => { router.push(`/product/${product.id}`) }
const goToCustomizer = (product) => {
  const token = localStorage.getItem('auth_token')
  if (token) { router.push(`/models/${product.id}`) }
  else { pendingModelId.value = product.id; showLoginModal.value = true }
}
const addToCart = (product) => { cartStore.addItem(product) }

onMounted(async () => {
  updateMobile()
  window.addEventListener('resize', handleResize)
  window.addEventListener('scroll', handleScroll)
  await fetchCategories()
  fetchDeal(); fetchBlogs(); fetchVideos(); fetchFeaturedProducts(); fetchTestimonials()
  await fetchApparelProducts()
  await fetchSlides()
  startFeaturedAuto(); startApparelAuto(); startVideoAuto(); startTestimonialAuto(); startBenefitAuto()
  setTimeout(() => {
    carouselInterval = setInterval(() => { if (slides.value.length) currentSlide.value = (currentSlide.value + 1) % slides.value.length }, 4000)
  }, 3000)
  bannerInterval = setInterval(() => { if (deal.value?.banners?.length) currentBanner.value = (currentBanner.value + 1) % deal.value.banners.length }, 3000)
})

onUnmounted(() => {
  if (carouselInterval) clearInterval(carouselInterval)
  if (bannerInterval) clearInterval(bannerInterval)
  stopFeaturedAuto(); stopApparelAuto(); stopVideoAuto(); stopBenefitAuto()
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
.pro-spinner {
  display: inline-block;
  position: relative;
  width: 70px;
  height: 70px;
}
.spin-svg {
  width: 70px;
  height: 70px;
  animation: rotate 1.4s linear infinite;
}
.spin-arc {
  animation: dash 1.4s ease-in-out infinite;
}
.spin-dot {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  width: 12px; height: 12px;
  background: #000;
  border-radius: 50%;
  animation: pulse 1.4s ease-in-out infinite;
}
.spin-text {
  margin-top: 16px;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 3px;
  text-transform: uppercase;
  color: #888;
  animation: pulse 1.4s ease-in-out infinite;
}
@keyframes rotate {
  to { transform: rotate(360deg); }
}
@keyframes dash {
  0%   { stroke-dasharray: 1 200; stroke-dashoffset: 0; }
  50%  { stroke-dasharray: 89 200; stroke-dashoffset: -35px; }
  100% { stroke-dasharray: 89 200; stroke-dashoffset: -124px; }
}
@keyframes pulse {
  0%, 100% { opacity: 0.4; }
  50%       { opacity: 1; }
}
.scroll-top-btn { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px) scale(0.8); z-index: 9997; width: 52px; height: 52px; border-radius: 50%; background: #000; color: #fff; border: 2px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; cursor: pointer; box-shadow: 0 4px 20px rgba(0,0,0,0.35); opacity: 0; pointer-events: none; transition: opacity 0.35s ease, transform 0.35s ease, background 0.25s; }
.scroll-top-btn.visible { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); pointer-events: auto; }
.scroll-top-btn:hover { background: #fff; color: #000; transform: translateX(-50%) translateY(-3px) scale(1.08); }
.float-icons-left { position: fixed; bottom: 30px; left: 20px; z-index: 99990; display: flex; flex-direction: column; gap: 14px; align-items: flex-start; font-family: 'Poppins', sans-serif; }
.float-icon-wrap { position: relative; display: flex; align-items: center; cursor: pointer; }
.float-icon-btn { width: 54px; height: 54px; border-radius: 50%; background: #000; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; border: 2.5px solid #fff; box-shadow: 0 6px 22px rgba(0,0,0,0.35); transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), background 0.2s; position: relative; z-index: 2; text-decoration: none; cursor: pointer; }
.float-icon-btn:hover { background: #222; color: #fff; transform: scale(1.12); }
.wa-icon-btn { background: #fff !important; color: #000 !important; border: 2.5px solid #000 !important; box-shadow: 0 6px 22px rgba(0,0,0,0.2) !important; }
.wa-icon-btn:hover { background: #f0f0f0 !important; color: #000 !important; transform: scale(1.12); }
.float-icon-pulse { position: absolute; left: 0; top: 0; width: 54px; height: 54px; border-radius: 50%; background: rgba(0,0,0,0.12); animation: floatPulse 2.6s ease-out infinite; pointer-events: none; z-index: 1; }
.wa-pulse { background: rgba(0,0,0,0.08) !important; }
@keyframes floatPulse { 0% { transform: scale(1); opacity: 0.7; } 100% { transform: scale(2.1); opacity: 0; } }
.float-icon-tooltip { position: absolute; left: 64px; top: 50%; transform: translateY(-50%) translateX(-6px); background: #000; color: #fff; font-size: 0.75rem; font-weight: 600; padding: 5px 12px; border-radius: 20px; white-space: nowrap; opacity: 0; transition: all 0.25s; pointer-events: none; }
.float-icon-wrap:hover .float-icon-tooltip { opacity: 1; transform: translateY(-50%) translateX(0); }
.float-icons-right { position: fixed; bottom: 30px; right: 20px; z-index: 99990; display: flex; flex-direction: column; gap: 14px; align-items: flex-end; }
.float-icons-right .float-icon-tooltip { left: auto; right: 64px; transform: translateY(-50%) translateX(6px); }
.float-icons-right .float-icon-wrap:hover .float-icon-tooltip { opacity: 1; transform: translateY(-50%) translateX(0); }
.hero-carousel { position: relative; height: 100vh; min-height: 100vh; overflow: hidden; }
.carousel-container { position: relative; height: 100%; }
.carousel-slide { height: 100vh; position: absolute; inset: 0; background-size: cover; background-position: center center; background-repeat: no-repeat; animation: slideBackgroundFromRight 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
@media (max-width: 1400px) { .carousel-slide { background-position: 70% center; } }
@media (max-width: 1100px) { .carousel-slide { background-position: 75% center; } }
@media (max-width: 900px)  { .carousel-slide { background-position: 80% center; } }
@media (max-width: 991px)  { .carousel-slide { background-position: center center !important; background-size: cover !important; } }
.carousel-overlay { position: absolute; height: 100%; inset: 0; z-index: 2; display: flex; align-items: center; background: linear-gradient(to right, rgba(255,255,255,0.55) 0%, rgba(255,255,255,0.1) 60%, transparent 100%); }
.carousel-content { max-width: 100%; margin: 0 5%; width: 90%; height: 100%; }
.text-and-image-wrapper { max-width: 60%; text-align: center; margin-top: 12%; }
.title-button-row { width: 100%; display: flex; align-items: flex-start; flex-wrap: nowrap; justify-content: space-between; }
.main_title { width: 60%; font-size: 4.5rem; line-height: 0.9; font-weight: 800; font-style: italic; text-align: left; margin: 0; padding: 0; word-break: break-word; flex-shrink: 0; color: black; }
.button-wrapper { margin-top: 50px; flex-shrink: 0; align-self: flex-start; }
.single-line-btn { white-space: nowrap; display: inline-block; text-align: center; line-height: 1.2; padding: 12px 34px; background: black; color: white; border-radius: 20px; font-size: 1.2rem; letter-spacing: 1px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s ease; }
.single-line-btn:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.4); transform: translateY(-2px); }
.hero-png { max-width: 110%; max-height: 850px; height: auto; animation: float 6s ease-in-out infinite; }
.lead { color: #000; font-size: 1.2rem; }
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
.icon-name { color: white; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; max-width: 110px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.icon-circle { width: 110px; height: 110px; display: flex; align-items: center; justify-content: center; background: black; border-radius: 50%; overflow: hidden; transition: all 0.4s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.3); position: relative; }
.icon-circle::after { content: ''; position: absolute; inset: 0; border: 3px dotted #fff; border-radius: 50%; opacity: 0; transition: opacity 0.35s, transform 0.35s; transform: scale(0.92); pointer-events: none; }
@keyframes rotateDotted { from { transform: scale(0.92) rotate(0deg); } to { transform: scale(0.92) rotate(360deg); } }

.left-ribbon { position: relative; display: inline-flex; align-items: center; justify-content: center; background: #000; color: #fff; padding: 10px 40px 10px 30px; font-weight: 800; font-size: 2rem; margin-left: -80px; letter-spacing: 1px; margin-bottom: 40px; border-radius: 0px 25px 25px 0px; text-align: center; }
.left-ribbon span { position: relative; z-index: 2; display: block; text-align: center; }
.left2-ribbon { position: relative; display: inline-block; background: #ffffff; color: #000000; padding: 14px 40px; font-weight: 800; font-size: 2rem; margin-left: -80px; letter-spacing: 1px; margin-bottom: 40px; border-radius: 0px 25px 25px 0px; }
.deals-section { background-color: #e0e0e0; }
.deals-layout { display: flex; gap: 50px; align-items: stretch; }
.deals-banner-col { flex: 0 0 42%; max-width: 42%; }
.deal-banner-box { width: 100%; height: 100%; min-height: 420px; border-radius: 18px; overflow: hidden; }
.banner-img { width: 100%; height: 100%; object-fit: cover; object-position: center; display: block; }
.deals-cards-col { flex: 1; }
.deals-cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; height: 100%; }
.deal-card { position: relative; width: 100%; padding-bottom: 110%; border-radius: 14px; overflow: hidden; transition: 0.3s ease; }
.deal-card-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center top; transition: 0.4s ease; }
.deal-card:hover .deal-card-img { transform: scale(1.08); }
.deal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.65); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s ease; }
.deal-card:hover .deal-overlay { opacity: 1; }
.deal-ribbon { position: absolute; top: 15px; left: -35px; background: #000; color: #fff; padding: 2px 40px; font-size: 13px; font-weight: 700; transform: rotate(-45deg); box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 5; letter-spacing: 1px; }
.fade-enter-active, .fade-leave-active { transition: all 0.7s ease; }
.fade-enter-from { opacity: 0; filter: blur(15px); transform: scale(1.1); }
.fade-leave-to { opacity: 0; filter: blur(10px); }
.featured-products { position: relative; padding: 80px 0; overflow: hidden; background: url('/assets/images/lines texture.svg') no-repeat center center; background-size: cover; }
.featured-products::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,0.933); z-index: 1; }
.featured-products > * { position: relative; z-index: 2; }
.featured-header { display: flex;  align-items: center;  justify-content: space-between;flex-wrap: nowrap; gap: 10px;  margin-bottom: 3rem; }
.featured-ribbon {  margin-bottom: 0 !important;  flex-shrink: 0;  white-space: nowrap;}
.tabs { display: flex; gap: 8px; flex-wrap: nowrap; align-items: center; }
.tab-btn { padding: 10px 22px; border: 2px solid #000; background: #fff; color: #000; font-weight: 600; cursor: pointer; transition: all 0.3s; border-radius: 4px; font-size: 0.95rem; white-space: nowrap; }
.tab-btn:hover, .tab-btn.active { background: #000; color: #fff; }
.carousel-wrapper { position: relative; overflow: hidden; padding: 0 55px; }
.products-track { display: flex; will-change: transform; }
.home-card-img-wrap { position: relative; overflow: hidden; margin: -10px -10px 8px -10px; border-radius: 12px 12px 0 0; background: #f8f8f8; }
.home-cart-icon-btn { position: absolute; top: 6px; right: 6px; width: 28px; height: 28px; background: rgba(255,255,255,0.92); border: 1px solid #e0e0e0; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; font-size: 12px; transition: 0.2s; color: #333; flex-shrink: 0; }
.home-cart-icon-btn:hover { background: #000; color: #fff; border-color: #000; }
.apparel-cart-icon {  color: #000000 !important; }
.apparel-cart-icon:hover { background: #000000 !important; color: #ffffff !important; }
.product-card { padding: 0 4px; box-sizing: border-box; }
.product-card-inner { background: #fff; border-radius: 12px; padding: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s; display: flex; flex-direction: column; height: 100%; overflow: hidden; }
/*.product-card:hover .product-card-inner { transform: translateY(-8px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); } */
.product-img { width: 100%; height: 240px; object-fit: contain; margin-bottom: 8px; border-radius: 0; display: block; }
.product-meta-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; padding: 0 4px; }
.product-title { font-size: 0.88rem; font-weight: 600; color: #000; margin: 0; flex: 1; text-align: left; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-price { color: #000; font-weight: 800; font-size: 1rem; margin: 0; white-space: nowrap; flex-shrink: 0; }
.add-cart-btn { background: #000; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.3s; margin-top: auto; font-size: 0.85rem; }
.add-cart-btn:hover { background: #333; transform: translateY(-2px); }
.carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 46px; height: 46px; border-radius: 50%; background: #000; border: none; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; box-shadow: 0 8px 20px rgba(0,0,0,0.2); transition: all 0.3s; z-index: 10; }
.carousel-btn i { font-size: 20px; }
.carousel-btn-prev { left: 5px; }
.carousel-btn-next { right: 5px; }
.carousel-btn:hover { background: #222; transform: translateY(-50%) scale(1.05); }
.apparel-card-inner { background:#ffffff; color: #000;   border: 1px solid #444; border-radius: 12px; padding: 10px; transition: all 0.35s ease; display: flex; flex-direction: column; height: 100%; overflow: hidden; }
.product-image-wrapper { margin: -10px -10px 8px -10px; border-radius: 12px 12px 0 0; overflow: hidden; background: #ffffff; }
.apparel-meta-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 12px; padding: 0 4px; }
.product-name { font-size: 0.88rem; font-weight: 600; margin: 0; flex: 1; text-align: left; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.add-to-cart-btn { margin-top: auto; font-size: 0.85rem; }
.button-apperal { background-color: #000; color: white; }
/* Benefits desktop */
.benefits-section { background: #111; }
.benefit-card { background: white; border: 1px solid #333; transition: all 0.3s; color: black; border-radius: 8px; }
.benefit-card:hover { background: black; color: white; transform: translateY(-8px); }
.benefit-card:hover .icon, .benefit-card:hover h4, .benefit-card:hover p { color: white !important; }
.icon { color: black; line-height: 1; transition: all 0.3s; }
.benefit-card h4 { font-size: 1.1rem; font-weight: 700; }
.benefit-card p { font-size: 0.95rem; }
.benefits-desktop { display: flex; }
.benefits-mobile-carousel { display: none; }
/* Benefits mobile chain */
.benefits-mobile-carousel { position: relative; overflow: hidden; padding: 0 46px; }
.benefits-chain-track { display: flex; will-change: transform; }
.benefit-chain-slide { flex-shrink: 0; width: 100%; padding: 0 8px; box-sizing: border-box; }
.benefit-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 38px; height: 38px; border-radius: 50%; background: #fff; border: 2px solid #fff; color: #000; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; cursor: pointer; z-index: 5; transition: all 0.3s; }
.benefit-btn:hover { background: #ddd; }
.benefit-btn-prev { left: 2px; }
.benefit-btn-next { right: 2px; }
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
.video-title-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); color: white; padding: 20px 15px 15px; font-size: 1rem; font-weight: 600; text-align: center; opacity: 0; transition: opacity 0.3s; }
.video-thumbnail:hover .video-title-overlay { opacity: 1; }
.video-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.video-modal-content { position: relative; width: 90%; max-width: 1400px; height: 80vh; background: #000; border-radius: 12px; overflow: hidden; }
.fullscreen-video { width: 100%; height: 100%; object-fit: contain; background: #000; }
.close-video-btn { position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; color: #000; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
.close-video-btn:hover { background: black; color: white; transform: scale(1.1) rotate(90deg); }
.testimonials-section { background-color: #eeecec; padding: 100px 0; }
.testimonial-card, .testimonial-text, .author-name, .author-position, .stars-rating { user-select: none; -webkit-user-select: none; }
.testimonials-outer-wrapper { display: flex; align-items: center; width: 100%; gap: 0; }
.t-arrow { flex-shrink: 0; width: 48px; height: 48px; background: #000; color: #fff; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: all 0.3s; }
.t-arrow:hover { background: #333; transform: scale(1.1); }
.t-arrow-left { margin-right: 16px; }
.t-arrow-right { margin-left: 16px; }
.testimonials-carousel-inner { flex: 1; overflow: hidden; padding-top: 46px; }
.testimonials-carousel { display: flex; will-change: transform; }
.testimonial-item { flex: 0 0 33.333%; padding: 0 12px; box-sizing: border-box; }
.t-card-wrapper { position: relative; padding-top: 38px; }
.quote-float { position: absolute; top: -38px; left: 50%; transform: translateX(-50%); z-index: 10; font-size: 8rem; line-height: 1; color: #000; pointer-events: none; }
.testimonial-card { background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 20px; min-height: auto; position: relative; transition: all 0.35s; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
.testimonial-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); border-color: #333; }
.stars-rating { font-size: 1.1rem; letter-spacing: 3px; margin-bottom: 12px; text-align: left; }
.stars-rating .bi-star-fill { color: #000 !important; }
.stars-rating .bi-star { color: #ccc !important; }
.testimonial-text { font-size: 1rem; line-height: 1.75; color: #444; margin-bottom: 20px; }
.testimonial-author { display: flex; align-items: center; gap: 15px; }
.author-image { width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 3px solid #f0f0f0; flex-shrink: 0; }
.author-image img { width: 100%; height: 100%; object-fit: cover; }
.author-name { font-size: 1.1rem; font-weight: 700; color: #111; margin: 0; }
.author-position { font-size: 0.88rem; color: #777; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 0.8px; }
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
.read-more { color: #000; font-weight: 600; text-decoration: none; transition: color 0.3s; font-size: 0.95rem; }
.btn-view-all { background: #000; color: white; padding: 14px 40px; border-radius: 50px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s; font-size: 1rem; }
.btn-view-all:hover { background: #333; transform: translateY(-3px); }
.login-icon-wrap { width: 72px; height: 72px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

/* ===========================================================
   RESPONSIVE
   =========================================================== */
@media (min-width: 1400px) {
  .featured-products .product-card, .apparel-products .product-card { min-width: 20% !important; }
}
@media (min-width: 992px) and (max-width: 1399px) {
  .featured-products .product-card, .apparel-products .product-card { min-width: 25% !important; }
}
@media (min-width: 768px) and (max-width: 991px) {
  .featured-products .product-card, .apparel-products .product-card { min-width: 33.333% !important; }
  .video-card { width: 50% !important; }
  .testimonial-item { flex: 0 0 50% !important; }
  .t-arrow { width: 38px !important; height: 38px !important; }
  .t-arrow-left { margin-right: 8px !important; }
  .t-arrow-right { margin-left: 8px !important; }
  .icons-track { gap: 35px !important; }
  .icon-circle { width: 75px !important; height: 75px !important; }
  .icon-name { font-size: 12px !important; }
  .main_title { font-size: 3.5rem !important; width: 60% !important; }
  .hero-png { max-height: 550px !important; }
  .carousel-wrapper { padding: 0 50px !important; }
  .video-thumbnail { height: 280px !important; }
  .blog-card { flex-direction: row !important; }
  .blog-image { flex: 0 0 40% !important; height: auto !important; }
  .float-icon-btn { width: 50px; height: 50px; font-size: 1.4rem; }
  }
@media (max-width: 991px) {
  .benefits-desktop { display: none !important; }
  .benefits-mobile-carousel { display: block !important; }
  .text-and-image-wrapper { max-width: 100% !important; width: 100% !important; height: 88vh !important; margin-top: 0 !important; display: flex !important; flex-direction: column !important; justify-content: flex-end !important; padding-bottom: 20px !important; gap: 10px !important; }
 .main_title {
  width: 100% !important;
  font-size: 2.4rem !important;
  position: absolute !important;
  top: 20% !important;
  left: 0 !important;
  text-align: left !important;
  padding: 0 10px !important;
}
.button-wrapper {
  position: absolute !important;
  top: 53% !important;
  left: 0 !important;
  width: 100% !important;
  display: flex !important;
  justify-content: left !important; /* button center me */
  padding: 0 10px !important;
}
  .image-content { order: 3 !important; width: 100% !important; display: flex !important; justify-content: center !important; }
  .hero-png { max-height: 45vh !important; max-width: 90% !important; }
  .title-button-row { flex-direction: column !important; align-items: center !important; gap: 10px !important; order: 1 !important; }
  .carousel-slide { background-position: center center !important; background-size: cover !important; }
}
@media (max-width: 767px) {
  .featured-products .product-card, .apparel-products .product-card { min-width: 50% !important; max-width: 50% !important; padding: 0 4px !important; }
  .video-card { width: 50% !important; }
  .testimonial-item { flex: 0 0 100% !important; }
  .product-img { height: 180px !important; }
  .video-thumbnail { height: 260px !important; }
  .float-icons-left { bottom: 20px; left: 12px; gap: 10px; }
  .float-icon-btn { width: 46px !important; height: 46px !important; font-size: 1.3rem !important; }
  .float-icon-pulse { width: 46px !important; height: 46px !important; }
}

/* ── max 575px: Mobile ────────────────────────────────────── */
@media (max-width: 575px) {
  /* ✅ HERO HEIGHT FIX */
  .hero-carousel {
    height: 85vh !important;
    min-height: unset !important;
  }

  .carousel-slide {
    height: 80vh !important;
    background-position: center center !important;
  }

  .carousel-content {
    margin: 0 !important;
    width: 100% !important;
    padding: 0 15px !important;
  }

  /* ✅ MAIN WRAPPER FIX (IMPORTANT) */
  .text-and-image-wrapper {
    max-width: 100% !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    margin-top: 0 !important;
    padding: 15px 0 !important;
  }

  .title-button-row {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 10px !important;
  }

  /* ✅ REMOVE ABSOLUTE (MAIN FIX 🔥) */
  .main_title {
    width: 85% !important;
    font-size: 2.5rem !important;
    position: relative !important;
    top: 50% !important;
    left: auto !important;
    padding: 0 10px !important;
    line-height: 1.3;
  }

  .button-wrapper {
    position: relative !important;
    top: 30% !important;
    left: auto !important;
    width: 100% !important;
    display: flex !important;
    justify-content: flex-start !important;
    padding: 0 10px !important;
  }

  .hero-png {
    max-width: 100% !important;
    max-height: 40vh !important;
  }

  .image-content {
    display: flex !important;
    justify-content: center !important;
  }

  /* BUTTON */
  .single-line-btn {
    font-size: 0.95rem !important;
    padding: 10px 28px !important;
  }
    .deals-layout {
    flex-direction: column !important;
    gap: 12px !important;
  }

  .deals-banner-col {
    width: 100% !important;
    max-width: 100% !important;
  }

  .deal-banner-box {
    height: 300px !important;
    min-height: 300px !important;
  }

  .deals-cards-col {
    width: 100% !important;
  }

  .deals-cards-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 8px !important;
  }

  .deal-card {
    padding-bottom: 100% !important;
  }

}

@media (min-width: 768px) and (max-width: 991px) {
  .deals-layout { flex-direction: column; gap: 16px; min-height: auto; }
  .deals-banner-col { flex: none; max-width: 100%; width: 100%; }
  .deal-banner-box { height: 500px !important; min-height: 500px !important; }
  .deals-cards-grid { grid-template-columns: repeat(4, 1fr); height: auto; gap: 10px; }
  .deal-card { height: 0 !important; padding-bottom: 90% !important; min-height: unset; }
}
@media (max-width: 767px) {
  .deal-banner-box { height: 240px; min-height: 240px; }
  .deals-cards-grid { grid-template-columns: repeat(4, 1fr); gap: 8px; }
  .deal-card { padding-bottom: 95%; }
  .deal-ribbon { font-size: 10px; padding: 2px 28px; top: 12px; left: -28px; }
}
@media (orientation: landscape) and (max-height: 600px) {
  .hero-carousel { height: 100vh !important; }
  .text-and-image-wrapper { margin-top: 3% !important; }
  .main_title { font-size: 2.5rem !important; }
  .hero-png { max-height: 500px !important; }
}
@media (min-width: 2000px) {
  .featured-products .product-card, .apparel-products .product-card { min-width: 20% !important; }
  .video-card { width: 20% !important; }
  .testimonial-item { flex: 0 0 25% !important; }
  .product-img { height: 320px !important; }
}

img { max-width: 100%; height: auto; }
*:focus-visible { outline: 2px solid #000; outline-offset: 2px; }
#tawk-bubble-container, .tawk-min-container, iframe[title="chat widget"] { display: none !important; }
</style>
