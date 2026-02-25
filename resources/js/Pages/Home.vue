<template>
  <div class="d-flex flex-column min-vh-100" :class="{ 'dark-mode': isDarkMode }">
    <!-- Fixed Icons Left Side -->
    <div class="fixed-icons-left">
      <a href="https://wa.me/03316566200" target="_blank" class="icon-btn" title="WhatsApp">
        <i class="bi bi-whatsapp"></i>
      </a>
    </div>

    <!-- Navbar -->
    <nav-component />

    <!-- Main content -->
    <main class="flex-grow-1">
        <div v-if="safeSlides.length > 0">
      <!-- Hero Carousel Banner -->
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
                      <img
                        :src="safeSlides[currentSlide].pngImage"
                        :alt="safeSlides[currentSlide].title"
                        class="hero-png img-fluid animate-from-left"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </Transition>

          <template v-if="safeSlides.length > 0">
            <button class="carousel-control-left prev" @click="prevSlide">
              <i class="bi bi-chevron-left"></i>
            </button>
            <button class="carousel-control-right next" @click="nextSlide">
              <i class="bi bi-chevron-right"></i>
            </button>

            <div class="carousel-dots">
              <button
                v-for="(slide, index) in safeSlides"
                :key="index"
                class="dot"
                :class="{ active: currentSlide === index }"
                @click="goToSlide(index)"
              ></button>
            </div>
          </template>
        </div>
      </section>
    </div>
    <div v-else class="text-center py-5">
  <h3>No Banner Available</h3>
</div>
      <!-- Clickable Moving Sports Icons -->
   <section class="sports-icons-section">
<div
  class="icons-track"
  :class="{ scrolling: sportsIcons.length > 12 }"
>        <div
      class="icon-item"
v-for="(icon, index) in scrollingIcons"
     :key="index"
      @click="selectTeam(icon.teamId)"
      :class="{ active: activeTeam === icon.teamId }"
    >
      <div class="icon-circle">
<img
  :src="icon.highlight_image || icon.image"
  :alt="icon.name"
  class="icon-image"
/>      </div>
      <div class="icon-name">{{ icon.name }}</div>
    </div>
  </div>
</section>


      <!-- Deals / Offers Section -->
  <section class="deals-section py-5">
    <div class="container">
      <div v-if="deal" class="row align-items-center">
        <div class="col-lg-6 mb-5  mb-lg-0">
          <h2 class="fw-bold text-black display-5 mb-3">{{ deal.title }}</h2>
          <h4 class="mb-3 text-black">{{ deal.subtitle || '' }}</h4>
          <p class="lead mb-4">
            {{ deal.description || 'Limited time offers on premium sports gear!' }}
          </p>
          <a
            v-if="deal.button_text"
            :href="deal.button_link || '#'"
            class="btn single-line-btn btn-lg px-4"
          >
            {{ deal.button_text }}
          </a>
        </div>
        <div class="col-lg-6">
          <div v-if="deal.images && deal.images.length" class="row g-3">
            <div v-for="img in deal.images" :key="img.id" class="col-6 col-md-4">
              <div class="deal-image-box">
                <img :src="img.image_path" class="deal-image" alt="Deal Image">
                <div class="deal-overlay">
                  <a :href="img.link || '#'" target="_blank" class="btn btn-light btn-sm px-3 py-2 shadow">View More</a>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-4">
            <p class="text-muted">No images added for this deal yet.</p>
          </div>
        </div>
      </div>
      <div v-else class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-3">Loading latest deals...</p>
      </div>
    </div>
  </section>

      <!-- Featured Products -->
<section class="featured-products py-5">
  <div class="container">
    <div class="featured-header d-flex align-items-center justify-content-between mb-5 flex-wrap gap-3">
      <h2 class="section-title text-black mb-0">featured products</h2>
      <div class="tabs d-flex gap-2">
        <button v-for="tab in tabs" :key="tab.value" class="tab-btn px-4 py-2" :class="{ active: activeTab === tab.value }" @click="setTab(tab.value)">
          {{ tab.label }}
        </button>
      </div>
    </div>
    <div class="carousel-wrapper position-relative overflow-hidden">
      <div class="products-track d-flex transition-transform duration-500 ease-in-out" :style="{ transform: `translateX(-${currentIndex * slidePercentage}%)` }">
        <div v-for="(product, idx) in displayedProducts" :key="product.id || idx" class="product-card flex-shrink-0 px-3">
          <div class="product-card-inner bg-white rounded shadow-sm p-4 text-center">
            <img :src="product.image" :alt="product.name" class="product-img img-fluid mb-3" style="height: 220px; object-fit: contain;" loading="lazy" />
            <h5 class="product-title mb-2 fw-semibold">{{ product.name }}</h5>
            <p class="product-price text-dark fw-bold mb-3">${{ product.price.toFixed(2) }}</p>
            <button class="add-cart-btn btn btn-dark w-100 py-2 ">ADD TO CART</button>
          </div>
        </div>
        <div v-if="displayedProducts.length === 0" class="w-100 text-center py-5">
          <p class="text-muted fs-4">No products found in this category...</p>
        </div>
      </div>
      <button class="carousel-btn carousel-btn-prev" @click="prevProduct" :disabled="currentIndex === 0"><i class="bi bi-chevron-left"></i></button>
      <button class="carousel-btn carousel-btn-next" @click="nextProduct" :disabled="currentIndex >= maxSlides"><i class="bi bi-chevron-right"></i></button>
    </div>
  </div>
</section>

      <!-- Benefits -->
<section class="benefits-section py-5" style="background: #111;">
  <div class="container">
    <div class="d-flex flex-wrap justify-content-between" style="gap: 20px;">
      <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;">
        <div class="icon mb-4"><i class="bi bi-truck fs-1"></i></div>
        <h4 class="mb-3">Free Shipping Delivery & Returns</h4>
        <p class="mb-0">Shop with confidence and have your favorite Furniture delivered right to your doorstep without any additional cost.</p>
      </div>
      <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;">
        <div class="icon mb-4"><i class="bi bi-heart fs-1"></i></div>
        <h4 class="mb-3">30 Days Money Back Guarantee</h4>
        <p class="mb-0">We Guarantee to rectify any unsatisfactory experience you may have with your purchase. No Queries posed.</p>
      </div>
      <div class="benefit-card text-center p-4 flex-grow-1" style="max-width: 32%;">
        <div class="icon mb-4"><i class="bi bi-headset fs-1"></i></div>
        <h4 class="mb-3">Online free custom support 24/7</h4>
        <p class="mb-0">Need help with your electronics? Get in touch with us anytime, anywhere and let's get your tech sorted.</p>
      </div>
    </div>
  </div>
</section>

      <!-- Latest Videos -->
   <section class="latest-videos-section">
  <div class="container">
    <div class="videos-header">
      <div class="header-icon">🗲</div>
      <h2 class="videos-title">LATEST VIDEOS</h2>
    </div>
    <div class="videos-carousel-wrapper">
      <div class="videos-carousel-container">
        <div class="videos-track" :style="{ transform: `translateX(-${videoCurrentIndex * 33.333}%)`, transition: 'transform 0.5s ease' }">
          <div v-for="(video, idx) in sportsVideos" :key="video.id" class="video-card">
            <div class="video-thumbnail" @click="playVideo(video)">
              <img :src="video.thumbnail" :alt="video.title" />
              <div class="play-overlay">
                <div class="play-button"><i class="bi bi-play-fill"></i></div>
              </div>
              <div class="video-title-overlay">{{ video.title }}</div>
            </div>
          </div>
        </div>
      </div>
      <button class="video-arrow prev-arrow" @click="prevVideo" :disabled="videoCurrentIndex === 0">‹</button>
      <button class="video-arrow next-arrow" @click="nextVideo" :disabled="videoCurrentIndex >= maxVideoSlides">›</button>
    </div>
  </div>
</section>

<div v-if="showVideoModal" class="video-modal" @click="closeVideo">
  <div class="video-modal-content" @click.stop>
    <button class="close-video-btn" @click="closeVideo"><i class="bi bi-x-lg"></i></button>
    <video ref="videoPlayer" :src="currentVideoUrl" controls autoplay class="fullscreen-video"></video>
  </div>
</div>

<section class="apparel-products py-5" style="background: #000000;">
  <div class="container">
    <h2 class="section-title text-white text-start mb-5 fw-bold">APPAREL COLLECTION</h2>
    <div class="carousel-wrapper position-relative overflow-hidden">
      <div class="products-track d-flex transition-transform duration-500 ease-in-out" :style="{ transform: `translateX(-${apparelCurrentIndex * slidePercentage}%)` }">
        <div v-for="(product, idx) in apparelProducts" :key="product.id || idx" class="product-card flex-shrink-0 px-3">
          <div class="product-card-inner text-center" style="background: transparent; border: 1px solid #444444; border-radius: 12px; padding: 20px 15px; transition: all 0.35s ease;">
            <div class="product-image-wrapper mb-3">
              <img :src="product.image" :alt="product.name" class="product-img img-fluid" style="height: 240px; object-fit: contain; background: transparent;" loading="lazy" />
            </div>
            <h5 class="product-name text-white fw-semibold mb-2">{{ product.name }}</h5>
            <p class="product-price text-white fw-bold mb-3 fs-5">${{ product.price.toFixed(2) }}</p>
            <button class="btn btn-light w-100 py-2 fw-bold add-to-cart-btn">ADD TO CART</button>
          </div>
        </div>
        <div v-if="apparelProducts.length === 0" class="w-100 text-center py-5 text-white">
          <h4>No Apparel Products Found</h4>
        </div>
      </div>
      <button class="carousel-btn carousel-btn-prev" @click="prevApparel" :disabled="apparelCurrentIndex === 0"><i class="bi bi-chevron-left"></i></button>
      <button class="carousel-btn carousel-btn-next" @click="nextApparel" :disabled="apparelCurrentIndex >= maxApparelIndex"><i class="bi bi-chevron-right"></i></button>
    </div>
  </div>
</section>

    <section class="testimonials-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
        <p class="testimonial-label">TESTIMONIAL</p>
        <h2 class="section-title text-black">HAPPY PEOPLE</h2>
        </div>
        <div v-if="loadingTestimonials" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-3">Loading customer reviews...</p>
        </div>
        <div v-else-if="testimonialsError" class="text-center py-5 text-danger">
        <p>{{ testimonialsError }}</p>
        <button class="btn btn-outline-dark mt-3" @click="fetchTestimonials">Try Again</button>
        </div>
        <div v-else-if="testimonials.length === 0" class="text-center py-5 text-muted">
        <p>No testimonials available yet.</p>
        </div>
        <div v-else class="testimonials-carousel-wrapper position-relative">
        <div class="testimonials-carousel d-flex" :style="{ transform: `translateX(-${currentTestimonialIndex * (100 / itemsPerSlide)}%)` }">
            <div v-for="(testimonial, index) in testimonials" :key="index" class="testimonial-item flex-shrink-0" :style="{ width: `${100 / itemsPerSlide}%` }">
            <div class="testimonial-card bg-white rounded shadow-sm p-4 h-100">
                <div class="quote-icon">"</div>
                <div class="stars-rating mb-3">
    <i v-for="star in 5" :key="star" class="bi" :class="{ 'bi-star-fill text-warning': star <= testimonial.rating, 'bi-star text-muted': star > testimonial.rating }"></i>
    </div>
                <p class="testimonial-text mb-4">{{ testimonial.text }}</p>
                <div class="testimonial-author d-flex align-items-center">
                <div class="author-image me-3">
                    <img :src="testimonial.image" :alt="testimonial.name" class="rounded-circle" />
                </div>
                <div class="author-info">
                    <h5 class="author-name mb-0">{{ testimonial.name }}</h5>
                    <p class="author-position text-muted mb-0">{{ testimonial.position }}</p>
                </div>
                </div>
            </div>
            </div>
        </div>
        <button class="carousel-arrow left" @click="prevTestimonial" :disabled="currentTestimonialIndex === 0">‹</button>
        <button class="carousel-arrow right" @click="nextTestimonial" :disabled="currentTestimonialIndex >= maxIndex">›</button>
        <div class="carousel-dots mt-4 text-center">
            <span v-for="n in Math.ceil(testimonials.length / itemsPerSlide)" :key="n" class="dot" :class="{ active: currentTestimonialIndex === (n-1) }" @click="goToTestimonial(n-1)"></span>
        </div>
        </div>
    </div>
    </section>

<section class="recent-blog-section py-5">
  <div class="container">
    <div class="section-header text-center mb-5">
      <p class="section-label text-uppercase fw-bold">OUR BLOG</p>
      <h2 class="section-title text-black">RECENT BLOG</h2>
    </div>
   <div class="row g-4">
  <div class="col-lg-6" v-for="blog in blogs" :key="blog.id">
    <div class="blog-card d-flex flex-column flex-md-row">
      <div class="blog-image" v-if="blog.image">
        <img :src="`/storage/${blog.image}`" :alt="blog.title" class="img-fluid" />
      </div>
      <div class="blog-content">
        <div class="blog-meta">
          <span class="date">{{ new Date(blog.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</span>
          <span class="comments">0 COMMENTS</span>
        </div>
        <h3 class="blog-title">{{ blog.title }}</h3>
        <p class="blog-excerpt">{{ blog.description }}</p>
        <a :href="`/blog/${blog.slug}`" class="read-more">Read More →</a>
      </div>
    </div>
  </div>
</div>
    <div class="text-center mt-5">
      <a href="#" class="btn-view-all">VIEW ALL BLOGS</a>
    </div>
  </div>
</section>
    </main>

    <!-- Footer -->
    <footer-component />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import NavComponent from '@/Component/nav.vue'
import FooterComponent from '@/Component/footer.vue'
import axios from 'axios'

const activeTeam = ref(null)
const activeTab = ref('all')
const currentIndex = ref(0)
const router = useRouter()
const currentSlide = ref(0)
let carouselInterval
const videoCurrentIndex = ref(0)
let videoInterval = null
const showVideoModal = ref(false)
const currentVideoUrl = ref('')
const videoPlayer = ref(null)
const showWelcomePopup = ref(false)
const isDarkMode = ref(false)

const slides = ref([])
const safeSlides = computed(() =>
  slides.value.filter(s => s && typeof s.backgroundImage === 'string' && s.backgroundImage.trim() !== '')
)
const sportsIcons = ref([])

const fetchCategories = async () => {
  try {
    const res = await axios.get('/api/highlighted');
    if (!Array.isArray(res.data)) { sportsIcons.value = []; return; }
 sportsIcons.value = res.data.map(cat => ({
  teamId: cat.id,
  name: cat.name,
  image: cat.icon_image,
  highlight_image: cat.highlight_image,
}))
  } catch (error) { sportsIcons.value = []; }
};

const deal = ref(null)
const fetchDeal = async () => {
  try { const res = await axios.get('/api/latest-deal'); deal.value = res.data }
  catch (error) { console.error('Deals Fetch FAILED:', error) }
}
onMounted(() => { fetchDeal() })

const teamContent = computed(() => {
  const activeCat = sportsIcons.value.find(cat => cat.teamId === activeTeam.value)
  if (!activeCat) return { title: 'Select a Category', subtitle: 'Choose from highlighted collections', description: 'Click any icon above to explore', buttonText: 'Explore Now' }
  return { title: activeCat.customTitle || activeCat.name, subtitle: activeCat.customSubtitle || 'Premium Collection', description: activeCat.customDescription || 'Discover high-quality gear for ' + activeCat.name, buttonText: activeCat.customButtonText || 'Shop Now' }
})

const sportsVideos = ref([])
const maxVideoSlides = computed(() => Math.max(0, Math.ceil(sportsVideos.value.length / 3) - 1))
const fetchVideos = async () => {
  try { const res = await axios.get('/api/videos'); sportsVideos.value = res.data || []; }
  catch (err) { console.error('Videos fetch error:', err); }
};
onMounted(() => { fetchVideos() })

const prevVideo = () => { if (videoCurrentIndex.value > 0) videoCurrentIndex.value-- }
const nextVideo = () => { if (videoCurrentIndex.value < maxVideoSlides.value) videoCurrentIndex.value++ }

const playVideo = (video) => {
  currentVideoUrl.value = video.video_url;
  showVideoModal.value = true;
  if (videoInterval) clearInterval(videoInterval);
};

const closeVideo = () => {
  showVideoModal.value = false; currentVideoUrl.value = '';
  videoInterval = setInterval(() => {
    if (videoCurrentIndex.value < maxVideoSlides.value) videoCurrentIndex.value++
    else videoCurrentIndex.value = 0
  }, 6000)
}

onMounted(() => {
  fetchVideos()
  videoInterval = setInterval(() => {
    if (videoCurrentIndex.value < maxVideoSlides.value) videoCurrentIndex.value++
    else videoCurrentIndex.value = 0
  }, 6000)
})
onUnmounted(() => { if (videoInterval) clearInterval(videoInterval) })

const blogs = ref([])
const fetchBlogs = async () => {
  try { const res = await axios.get('/api/blogs'); blogs.value = res.data }
  catch (err) { console.error(err) }
}
onMounted(() => { fetchBlogs() })

const apparelProducts = ref([])
const apparelCurrentIndex = ref(0)
const apparelItemsPerView = 4
const apparelSlidePercentage = 100 / apparelItemsPerView
const maxApparelSlides = computed(() => Math.max(0, Math.ceil(apparelProducts.value.length / apparelItemsPerView) - 1))
const maxApparelIndex = maxApparelSlides

const fetchApparelProducts = async () => {
  try { const res = await axios.get('/api/apparel-products'); apparelProducts.value = res.data || [] }
  catch (err) { console.error("Apparel fetch failed:", err) }
}

const nextApparel = () => { if (apparelCurrentIndex.value < maxApparelSlides.value) apparelCurrentIndex.value++ }
const prevApparel = () => { if (apparelCurrentIndex.value > 0) apparelCurrentIndex.value-- }

let apparelAutoInterval = null
onMounted(async () => {
  await fetchApparelProducts()
  apparelAutoInterval = setInterval(() => {
    if (apparelCurrentIndex.value < maxApparelSlides.value) apparelCurrentIndex.value++
    else apparelCurrentIndex.value = 0
  }, 5000)
})
onUnmounted(() => { if (apparelAutoInterval) clearInterval(apparelAutoInterval) })

const featuredProducts = ref([])
const tabs = ref([
  { label: 'All', value: 'all' },
  { label: 'New Arrivals', value: 'new' },
  { label: 'Best Sellers', value: 'bestsellers' }
])

const fetchFeaturedProducts = async () => {
  try { const res = await axios.get('/api/featured-products'); featuredProducts.value = res.data || [] }
  catch (err) { console.error('Featured fetch error:', err) }
}

const setTab = (value) => { activeTab.value = value; currentIndex.value = 0 }

const displayedProducts = computed(() => {
  let products = [...featuredProducts.value]
  if (activeTab.value === 'all') return products
  if (activeTab.value === 'new') {
    const twoWeeksAgo = new Date(); twoWeeksAgo.setDate(twoWeeksAgo.getDate() - 14)
    return products.filter(p => new Date(p.created_at || 0) >= twoWeeksAgo)
  }
  if (activeTab.value === 'bestsellers') return products.slice(0, 12)
  return products
})

const itemsPerView = 4
const slidePercentage = 100 / itemsPerView
const maxSlides = computed(() => Math.max(0, Math.ceil(displayedProducts.value.length / itemsPerView) - 1))
const nextProduct = () => { if (currentIndex.value < maxSlides.value) currentIndex.value++ }
const prevProduct = () => { if (currentIndex.value > 0) currentIndex.value-- }

let autoInterval = null
onMounted(() => { fetchFeaturedProducts() })
onUnmounted(() => { if (autoInterval) clearInterval(autoInterval) })

const fetchSlides = async () => {
  try { const res = await axios.get('/api/banners'); slides.value = res.data }
  catch (err) { slides.value = [] }
}
const nextSlide = () => { if (slides.value.length) currentSlide.value = (currentSlide.value + 1) % slides.value.length }
const prevSlide = () => { if (slides.value.length) currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length }
const goToSlide = (index) => { if (slides.value.length) currentSlide.value = index }
const handleBuy = (url) => { if (url) window.location.href = url }
const selectTeam = (teamId) => {
  activeTeam.value = teamId
router.push({ name:'CategoryProducts', params:{ id: teamId } })
}
const testimonials = ref([])
const loadingTestimonials = ref(true)
const testimonialsError = ref(null)
const currentTestimonialIndex = ref(0)
const itemsPerSlide = ref(3)
const maxIndex = computed(() => Math.max(0, testimonials.value.length - itemsPerSlide.value))

const fetchTestimonials = async () => {
  loadingTestimonials.value = true; testimonialsError.value = null
  try {
    const res = await axios.get('/api/testimonials')
    testimonials.value = res.data.map(item => ({
      text: item.text || 'No review text', name: item.name || 'Customer',
      position: item.position || 'Happy Buyer',
      image: item.image || 'https://via.placeholder.com/80?text=No+Photo',
      rating: item.rating || 5,
    }))
  } catch (err) { testimonialsError.value = 'Could not load testimonials' }
  finally { loadingTestimonials.value = false }
}

const prevTestimonial = () => { if (currentTestimonialIndex.value > 0) currentTestimonialIndex.value-- }
const nextTestimonial = () => { if (currentTestimonialIndex.value < maxIndex.value) currentTestimonialIndex.value++ }
const goToTestimonial = (index) => { currentTestimonialIndex.value = index }
onMounted(() => { fetchTestimonials() })

onMounted(async () => {
  await fetchCategories()
  showWelcomePopup.value = true
  setTimeout(() => { showWelcomePopup.value = false }, 3000)
  if (typeof localStorage !== 'undefined') isDarkMode.value = localStorage.getItem('darkMode') === 'true'
  await fetchSlides()
  setTimeout(() => {
    carouselInterval = setInterval(() => { if (slides.value.length) currentSlide.value = (currentSlide.value + 1) % slides.value.length }, 4000)
  }, 3000)
  setInterval(() => { if (currentIndex.value < maxSlides.value) currentIndex.value++; else currentIndex.value = 0 }, 5000)
  videoInterval = setInterval(() => { if (videoCurrentIndex.value < maxVideoSlides.value) videoCurrentIndex.value++; else videoCurrentIndex.value = 0 }, 6000)
})

onUnmounted(() => {
  if (carouselInterval) clearInterval(carouselInterval)
  if (videoInterval) clearInterval(videoInterval)
})
const scrollingIcons = computed(() => {
  if (sportsIcons.value.length > 12) {
    return [...sportsIcons.value, ...sportsIcons.value]
  }
  return sportsIcons.value
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
* { font-family: 'Smooch Sans', sans-serif !important; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body, html { font-family: 'Poppins', sans-serif; background: white; color: #000; }

.dark-mode { background: #0a0a0a; color: #fff; }
.dark-mode .team-customizer-section { background: #111; }
.dark-mode .main-heading, .dark-mode .sub-heading { color: #fff; }
.dark-mode .description { color: #ccc; }
.dark-mode .featured-products { background: #0a0a0a; }
.dark-mode .section-title { color: #fff; }
.dark-mode .product-card-inner { background: #1a1a1a; color: #fff; }
.dark-mode .testimonials-section { background: #111; }
.dark-mode .testimonial-content { background: #1a1a1a; }

.welcome-popup {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
  z-index: 99999; display: flex; align-items: center; justify-content: center;
}
.fixed-icons-left {
  position: fixed; left: 20px; top: 85%; transform: translateY(-50%);
  z-index: 9998; display: flex; flex-direction: column; gap: 15px;
}
.icon-btn {
  width: 55px; height: 55px; border-radius: 50%; background: #000; color: #fff;
  border: 2px solid #fff; display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem; cursor: pointer; transition: all 0.3s ease;
  text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.icon-btn:hover { background: #fff; color: #000; transform: scale(1.1); }

.hero-carousel { position: relative; height: 100vh; min-height: 100vh; overflow: hidden; }
.carousel-container { position: relative; height: 100%; }
.carousel-slide {
  height: 100vh; position: absolute; inset: 0; background-size: cover;
  background-position: center; background-repeat: no-repeat;
  transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
  animation: slideBackgroundFromRight 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}
.carousel-overlay { position: absolute; height: 100%; inset: 0; z-index: 2; display: flex; align-items: center; }
.carousel-content { max-width: 1400px; margin: 0 5%; width: 100%; height: 100%; }
.text-and-image-wrapper { max-width: 60%; text-align: center; margin-top: 12%; }
.title-button-row { width: 100%; display: flex; align-items: flex-start; flex-wrap: nowrap; justify-content: space-between; }
.main_title { width: 60%; font-size: 4rem; line-height: 0.9; font-weight: 800; font-style: italic; text-align: left; margin: 0; padding: 0; white-space: normal; word-break: break-word; flex-shrink: 0; color: black; }
.button-wrapper { margin-top: 50px; flex-shrink: 0; align-self: flex-start; }
.single-line-btn { white-space: nowrap; display: inline-block; text-align: center; line-height: 1.2; padding: 10px 30px; background: black; color: white; border-radius: 20px; font-size: 20px; letter-spacing: 1px; font-weight: 550; transition: all 0.3s ease; box-shadow: none; border: none; cursor: pointer; }
.single-line-btn:hover { box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); transform: translateY(-2px); }
.hero-png { max-width: 110%; max-height: 850px; height: auto; margin-right: auto; margin-left: auto; animation: float 6s ease-in-out infinite; }
.animate-from-left { opacity: 0; transform: translateX(-150%); animation: slideInFromLeft 1.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; animation-delay: 0.4s; }
.animate-from-top { opacity: 0; transform: translate(-100%, -100%); animation: slideInFromTopLeft 1.1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; animation-delay: 0.2s; }
.delayed { opacity: 0; transform: translate(-100%, 100%); animation: slideInFromBottomLeft 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; animation-delay: 0.6s; }
.delayed-more { animation-delay: 0.55s; }

@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
.slide-enter-active, .slide-leave-active { transition: opacity 0.9s ease-in-out; }
.slide-enter-from, .slide-leave-to { opacity: 0; }
@keyframes slideBackgroundFromRight { 0% { transform: translateX(100%); opacity: 0; } 100% { transform: translateX(0); opacity: 1; } }
@keyframes slideInFromLeft { 0% { transform: translateX(-150%); opacity: 0; } 70% { transform: translateX(6%); opacity: 1; } 100% { transform: translateX(0); opacity: 1; } }
@keyframes slideInFromTopLeft { 0% { transform: translate(-100%, -100%); opacity: 0; } 70% { transform: translate(3%, 3%); opacity: 1; } 100% { transform: translate(0, 0); opacity: 1; } }
@keyframes slideInFromBottomLeft { 0% { transform: translate(-100%, 100%); opacity: 0; } 70% { transform: translate(3%, -3%); opacity: 1; } 100% { transform: translate(0, 0); opacity: 1; } }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.carousel-control-left, .carousel-control-right {
  position: absolute; top: 50%; transform: translateY(-50%); z-index: 4;
  width: 60px; height: 60px; cursor: pointer; background: transparent;
  border: none; outline: none; display: flex; align-items: center; justify-content: center; transition: all 0.3s;
}
.carousel-control-left i, .carousel-control-right i { color: #000; font-weight: 700; font-size: 2.2rem; }
.carousel-control-left:hover, .carousel-control-right:hover { transform: translateY(-50%) scale(1.1); }
.carousel-control-left { left: 10px; }
.carousel-control-right { right: 10px; }

/* =============================================
   CAROUSEL DOTS - FIXED (CHOTE AUR ANDAR)
   ============================================= */
.carousel-dots {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 4;
  display: flex;
  gap: 6px;
  align-items: center;
}

.dot {
  display: inline-block;
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.4);
  border: none;
  padding: 0;
  margin: 0;
  cursor: pointer;
  transition: all 0.3s;
  flex-shrink: 0;
}

.dot.active {
  background: #000;
  width: 20px;
  border-radius: 10px;
}

/* Sports Icons */

.sports-icons-section { background: black; padding: 25px 0; overflow: hidden; position: relative; }
.sports-icons-section::before, .sports-icons-section::after { content: ''; position: absolute; top: 0; bottom: 0; width: 70px; background: black; z-index: 2; }
.sports-icons-section::before { left: 0; }
.sports-icons-section::after { right: 0; }
.scrolling{
  animation: iconScroll 25s linear infinite;
}

.scrolling:hover{
  animation-play-state: paused;
}

@keyframes iconScroll{
  from { transform: translateX(0); }
  to   { transform: translateX(-50%); }
}
.icons-track{
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  gap: 50px;
  width: max-content;
  align-items: center;
   justify-content: flex-start;
  padding-left: 100px;
}
.icons-track:hover { animation-play-state: paused; }
.icon-item { display: flex; flex-direction: column; align-items:  center; gap: 10px; cursor: pointer; transition: all 0.3s ease;   padding: 0;
 }
.icon-item:hover { transform: translateY(-4px); }
.icon-item:hover .icon-circle::after { opacity: 1; transform: scale(1); animation: rotateDotted 8s linear infinite; }
.icon-item.active .icon-circle::after { opacity: 1; transform: scale(1); }
.icon-item:hover .icon-circle, .icon-item.active .icon-circle { transform: scale(1.08); }
.icon-image { width: 90%; height: 90%; object-fit: cover; }
.icon-name { color: white; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; max-width: 100px; transition: all 0.3s ease; }
.icon-item:hover .icon-name, .icon-item.active .icon-name { color: #ffffff; text-shadow: 0 0 8px rgba(255,255,255,0.6); }
.icon-circle { width: 110px; height: 110px; display: flex; align-items: center; justify-content: center; background: black; border-radius: 50%; overflow: hidden; transition: all 0.4s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.3); position: relative; }
.icon-circle::after { content: ''; position: absolute; inset: 0; border: 3px dotted #ffffff; border-radius: 50%; opacity: 0; transition: opacity 0.35s ease, transform 0.35s ease; transform: scale(0.92); pointer-events: none; }
@keyframes rotateDotted { 0% { transform: scale(0.92) rotate(0deg); } 100% { transform: scale(0.92) rotate(360deg); } }

.container { max-width: 1300px; margin: 0 auto; padding: 0 30px; }
.lead{
    color: #000;
}
.deals-section { position: relative; background-color: #eeecec; }
.deal-image-box { position: relative; overflow: hidden; border-radius: 12px; transition: box-shadow 0.3s ease; cursor: pointer; margin: 12px; }
.deal-image-box:hover { box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); }
.deal-image { width: 100%; height: 180px; object-fit: cover; transition: transform 0.35s ease; display: block; }
.deal-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.65); opacity: 0; visibility: hidden; transition: all 0.35s ease; display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: 12px; pointer-events: none; }
.deal-image-box:hover .deal-overlay { opacity: 1; visibility: visible; pointer-events: auto; }
.deal-image-box:hover .deal-image { transform: scale(1.07); }

.featured-products { position: relative; padding: 80px 0; overflow: hidden; background: url('/assets/images/lines texture.svg')no-repeat center center; background-size: cover; }
.featured-products::before { content: ''; position: absolute; inset: 0; background: rgba(255, 255, 255, 0.933); z-index: 1; }
.featured-products > * { position: relative; z-index: 2; }
.featured-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 3rem; }
.section-title { font-size: 2.8rem; font-weight: 800; color: #000; margin: 0; }
.tabs { display: flex; gap: 10px; flex-wrap: wrap; }
.tab-btn { padding: 10px 20px; border: 2px solid #000; background: #fff; color: #000; font-weight: 600; cursor: pointer; transition: all 0.3s ease; border-radius: 4px; }
.tab-btn:hover { background: #000; color: #fff; }
.tab-btn.active { background: #000; color: #fff; border-color: #000; }
.carousel-wrapper { position: relative; overflow: hidden; padding: 0 60px; }
.products-track { display: flex; transition: transform 0.5s ease; }
.product-card { min-width: 25%; padding: 0 15px; box-sizing: border-box; }
.product-card-inner { background: #fff; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; height: 100%; }
.product-card:hover .product-card-inner { transform: translateY(-8px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
.product-img { width: 100%; height: 220px; object-fit: contain; background: #f8f9fa; margin-bottom: 15px; border-radius: 4px; }
.product-title { font-size: 14px; font-weight: 600; margin-bottom: 10px; color: #000; flex-grow: 1; }
.product-price { color: #000; font-weight: 700; font-size: 1.2rem; margin-bottom: 15px; }
.add-cart-btn { background: #000; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.3s ease; }
.add-cart-btn:hover { background: #333; transform: translateY(-2px); }
.carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); width: 46px; height: 46px; border-radius: 50%; background: #000; border: none; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; box-shadow: 0 8px 20px rgba(0,0,0,0.2); transition: all 0.3s ease; z-index: 10; }
.carousel-btn i { font-size: 20px; }
.carousel-btn-prev { left: 15px; }
.carousel-btn-next { right: 15px; }
.carousel-btn:hover { background: #222; transform: translateY(-50%) scale(1.05); }
.carousel-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.benefit-card { background: white; border: 1px solid #333; transition: all 0.3s ease; color: black; border-radius: 8px; }
.benefit-card:hover { background: black; color: white; transform: translateY(-8px); }
.benefit-card:hover .icon, .benefit-card:hover h4, .benefit-card:hover p { color: white !important; }
.icon { color: black; line-height: 1; transition: all 0.3s ease; }

.latest-videos-section { position: relative; padding: 80px 0; overflow: hidden; background: url('/assets/images/lines texture.svg') no-repeat center center; background-size: cover; }
.latest-videos-section::before { content: ''; position: absolute; inset: 0; background: rgba(255, 255, 255, 0.933); z-index: 1; }
.latest-videos-section > * { position: relative; z-index: 2; }
.videos-header { display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 50px; position: relative; z-index: 2; }
.header-icon { font-size: 2.5rem; color: black; animation: pulse 2s ease-in-out infinite; }
@keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
.videos-title { font-size: 3rem; font-weight: 800; color: black; letter-spacing: 3px; margin: 0; }
.videos-carousel-wrapper { position: relative; padding: 0 80px; z-index: 2; }
.videos-carousel-container { overflow: hidden; border-radius: 12px; }
.videos-track { display: flex; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
.video-card { min-width: 33.333%; padding: 0 15px; box-sizing: border-box; }
.video-thumbnail { position: relative; width: 100%; height: 350px; border-radius: 12px; overflow: hidden; cursor: pointer; transition: all 0.4s ease; }
.video-thumbnail:hover { transform: translateY(-10px) scale(1.02); }
.video-thumbnail img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
.video-thumbnail:hover img { transform: scale(1.1); }
.play-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.6) 100%); display: flex; align-items: center; justify-content: center; transition: all 0.4s ease; }
.play-button { width: 70px; height: 70px; background: rgba(255, 255, 255, 0.95); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: black; font-size: 2.2rem; transition: all 0.4s ease; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3); }
.video-thumbnail:hover .play-button { transform: scale(1.15); background: black; color: white; }
.video-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 60px; height: 60px; background: rgba(255, 255, 255, 0.95); color: #000; border: none; border-radius: 50%; font-size: 2rem; font-weight: bold; cursor: pointer; z-index: 10; transition: all 0.3s ease; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3); }
.video-arrow:hover:not(:disabled) { background: black; color: white; transform: translateY(-50%) scale(1.1); }
.video-arrow:disabled { opacity: 0.3; cursor: not-allowed; }
.video-arrow.prev-arrow { left: 0; }
.video-arrow.next-arrow { right: 0; }
.video-title-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%); color: white; padding: 20px 15px 15px; font-size: 16px; font-weight: 600; text-align: center; opacity: 0; transition: opacity 0.3s ease; }
.video-thumbnail:hover .video-title-overlay { opacity: 1; }
.video-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.video-modal-content { position: relative; width: 90%; max-width: 1400px; height: 80vh; background: #000; border-radius: 12px; overflow: hidden; }
.fullscreen-video { width: 100%; height: 100%; object-fit: contain; background: #000; }
.close-video-btn { position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; color: #000; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
.close-video-btn:hover { background: black; color: white; transform: scale(1.1) rotate(90deg); }

.testimonials-section { background-color: #eeecec; padding: 100px 0; }
.testimonial-label { color: #000000; font-size: 1rem; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; }
.testimonials-carousel-wrapper { position: relative; overflow: hidden; width: 100%; }
.testimonials-carousel { display: flex; transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); will-change: transform; }
.testimonial-item { flex: 0 0 calc(100% / 3); padding: 0 12px; }
.testimonial-card { background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 15px 15px; min-height: 260px; position: relative; transition: all 0.35s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
.testimonial-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); border-color: #333; }
.quote-icon { position: absolute; top: -15px; left: 25px; font-size: 6rem; color: rgba(0,0,0,0.07); line-height: 1; font-weight: bold; }
.testimonial-text { font-size: 1.05rem; line-height: 1.75; color: #444; margin-bottom: 25px; }
.testimonial-author { display: flex; align-items: center; gap: 15px; }
.stars-rating { color: #000000; font-size: 1rem; letter-spacing: 5px; margin: 15px 0 20px 0; text-align: left; }
.author-image { width: 65px; height: 65px; border-radius: 50%; overflow: hidden; border: 3px solid #f0f0f0; }
.author-image img { width: 100%; height: 100%; object-fit: cover; }
.author-name { font-size: 1.15rem; font-weight: 700; color: #111; margin: 0; }
.author-position { font-size: 0.9rem; color: #777; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 0.8px; }
.carousel-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; background: rgba(0,0,0,0.7); color: white; border: none; border-radius: 50%; font-size: 1.6rem; cursor: pointer; z-index: 5; transition: all 0.3s; }
.carousel-arrow:hover { background: #000; transform: translateY(-50%) scale(1.15); }
.carousel-arrow.left { left: 0; }
.carousel-arrow.right { right: 0; }

.recent-blog-section { position: relative; padding: 80px 0; overflow: hidden; background: url('/assets/images/lines texture.svg') no-repeat center center; background-size: cover; }
.recent-blog-section::before { content: ''; position: absolute; inset: 0; background: rgba(255, 255, 255, 0.933); z-index: 1; }
.recent-blog-section * { position: relative; z-index: 2; }
.section-label { color: #000000; font-size: 1rem; letter-spacing: 2px; margin-bottom: 8px; }
.blog-card { background: white; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.35s ease; }
.blog-card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
.blog-image { flex: 0 0 45%; overflow: hidden; }
.blog-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.blog-card:hover .blog-image img { transform: scale(1.08); }
.blog-content { flex: 1; padding: 25px 30px; display: flex; flex-direction: column; justify-content: center; }
.blog-meta { display: flex; gap: 20px; margin-bottom: 12px; font-size: 0.9rem; color: #777; text-transform: uppercase; }
.blog-title { font-size: 1.4rem; font-weight: 700; color: #000; margin-bottom: 12px; line-height: 1.3; }
.blog-excerpt { font-size: 1rem; color: #555; line-height: 1.6; margin-bottom: 18px; }
.read-more { color: #000; font-weight: 600; text-decoration: none; transition: color 0.3s; }
.read-more:hover { color: #dc3545; }
.btn-view-all { background: #000; color: white; padding: 14px 40px; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s; }
.btn-view-all:hover { background: #333; transform: translateY(-3px); }

/* =============================================
   RESPONSIVE
   ============================================= */
@media (max-width: 575px) {
  .hero-carousel { height: 100vh !important; min-height: 600px !important; }
  .carousel-slide { background-size: cover !important; background-position: center center !important; }
  .carousel-content { margin: 0 !important; width: 100% !important; max-width: 100% !important; padding: 0 15px !important; }
  .text-and-image-wrapper { max-width: 100% !important; margin-top: 20% !important; width: 100% !important; }
  .title-button-row { flex-direction: column !important; align-items: center !important; gap: 20px !important; width: 100% !important; }
  .main_title { width: 100% !important; font-size: 2.2rem !important; text-align: center !important; color: #000 !important; padding: 0 10px !important; }
  .button-wrapper { margin-top: 0 !important; width: 100% !important; display: flex !important; justify-content: center !important; }
  .single-line-btn { font-size: 15px !important; padding: 12px 35px !important; }
  .lead { font-size: 14px !important; text-align: center !important; padding: 0 20px !important; color: #000000 !important; }
  .hero-png { max-width: 90% !important; max-height: 300px !important; }

  /* ✅ DOTS FIX - MOBILE */
  .carousel-dots {
    bottom: 10px !important;
    gap: 4px !important;
  }
  .dot {
    width: 5px !important;
    height: 5px !important;
    margin: 0 !important;
  }
  .dot.active {
    width: 13px !important;
    border-radius: 7px !important;
  }

  .carousel-control-left, .carousel-control-right { width: 40px !important; height: 40px !important; }
  .carousel-control-left i, .carousel-control-right i { font-size: 1.5rem !important; }
  .fixed-icons-left { left: 10px !important; }
  .icon-btn { width: 45px !important; height: 45px !important; font-size: 1.2rem !important; }
  .sports-icons-section { padding: 20px 0 !important; }
  .icons-track { gap: 40px !important; }
  .icon-circle { width: 60px !important; height: 60px !important; }
  .icon-name { font-size: 10px !important; max-width: 70px !important; }
  .deals-section { padding: 40px 0 !important; }
  .deal-image { height: 140px !important; }
  .featured-products { padding: 40px 0 !important; }
  .featured-header { flex-direction: column !important; align-items: flex-start !important; gap: 20px !important; }
  .section-title { font-size: 1.8rem !important; }
  .tabs { width: 100%; flex-wrap: wrap !important; }
  .tab-btn { font-size: 12px !important; padding: 8px 15px !important; flex: 1; min-width: 80px; }
  .carousel-wrapper { padding: 0 50px !important; }
  .product-card { min-width: 100% !important; padding: 0 10px !important; }
  .product-img { height: 180px !important; }
  .carousel-btn { width: 35px !important; height: 35px !important; }
  .carousel-btn i { font-size: 16px !important; }
  .carousel-btn-prev { left: 5px !important; }
  .carousel-btn-next { right: 5px !important; }
  .benefits-section { padding: 40px 0 !important; }
  .benefits-section .d-flex { flex-direction: column !important; gap: 15px !important; }
  .benefit-card { max-width: 100% !important; padding: 25px 15px !important; }
  .latest-videos-section { padding: 40px 0 !important; }
  .videos-title { font-size: 1.8rem !important; letter-spacing: 1px !important; }
  .videos-carousel-wrapper { padding: 0 50px !important; }
  .video-card { min-width: 100% !important; padding: 0 8px !important; }
  .video-thumbnail { height: 250px !important; }
  .video-modal-content { width: 95% !important; height: 50vh !important; }
  .apparel-products { padding: 40px 0 !important; }
  .testimonials-section { padding: 40px 0 !important; }
  .testimonial-item { flex: 0 0 100% !important; padding: 0 10px !important; }
  .testimonial-card { padding: 30px 20px !important; min-height: auto !important; }
  .blog-card { flex-direction: column !important; }
  .blog-image { flex: 0 0 100% !important; height: 220px !important; }
  .blog-content { padding: 20px 15px !important; }
  .recent-blog-section { padding: 40px 0 !important; }
}

@media (min-width: 576px) and (max-width: 767px) {
  .hero-carousel { height: 100vh !important; min-height: 650px !important; }
  .main_title { font-size: 2.8rem !important; text-align: center !important; width: 100% !important; }
  .title-button-row { flex-direction: column !important; align-items: center !important; }
  .hero-png { max-width: 85% !important; max-height: 400px !important; }

  /* ✅ DOTS FIX */
  .carousel-dots { bottom: 12px !important; gap: 5px !important; }
  .dot { width: 6px !important; height: 6px !important; margin: 0 !important; }
  .dot.active { width: 16px !important; }

  .icon-circle { width: 70px !important; height: 70px !important; }
  .product-card { min-width: 50% !important; }
  .video-card { min-width: 50% !important; }
  .testimonial-item { flex: 0 0 100% !important; }
  .blog-image { height: 250px !important; }
}

@media (min-width: 768px) and (max-width: 991px) {
  .hero-carousel { height: 100vh !important; min-height: 700px !important; }
  .text-and-image-wrapper { max-width: 85% !important; margin-top: 12% !important; }
  .main_title { font-size: 3.2rem !important; width: 60% !important; }
  .hero-png { max-height: 550px !important; max-width: 100% !important; }
  .section-title { font-size: 2.2rem !important; }
  .product-card { min-width: 33.333% !important; }
  .carousel-wrapper { padding: 0 55px !important; }
  .benefit-card { max-width: 48% !important; }
  .video-card { min-width: 50% !important; }
  .testimonial-item { flex: 0 0 50% !important; }
  .blog-card { flex-direction: row !important; }
  .blog-image { flex: 0 0 40% !important; height: auto !important; }
}

@media (min-width: 992px) and (max-width: 1199px) {
  .text-and-image-wrapper { max-width: 65% !important; }
  .main_title { font-size: 3.5rem !important; }
  .product-card { min-width: 25% !important; }
  .video-card { min-width: 33.333% !important; }
  .testimonial-item { flex: 0 0 33.333% !important; }
  .benefit-card { max-width: 32% !important; }
}

@media (min-width: 1200px) {
  .container { max-width: 1320px !important; }
}

@media (orientation: landscape) and (max-height: 600px) {
  .hero-carousel { height: 100vh !important; }
  .text-and-image-wrapper { margin-top: 3% !important; }
  .main_title { font-size: 2.5rem !important; }
  .hero-png { max-height: 500px !important; }
}

body, html { overflow-x: hidden !important; max-width: 100vw; }
img { max-width: 100%; height: auto; }
* { transition: all 0.3s ease; }
*:focus-visible { outline: 2px solid #000; outline-offset: 2px; }
</style>
