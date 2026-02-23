<template>
  <div class="d-flex flex-column min-vh-100" :class="{ 'dark-mode': isDarkMode }">
    <!-- Welcome Popup -->
    <!-- <div v-if="showWelcomePopup" class="welcome-popup">
      <div class="welcome-content">
        <h1 class="welcome-text">WELCOME TO PROSIX</h1>
      </div>
    </div> -->

    <!-- Fixed Icons Left Side -->
    <div class="fixed-icons-left">
      <!-- <button @click="toggleDarkMode" class="icon-btn" :title="isDarkMode ? 'Light Mode' : 'Dark Mode'">
        <i class="bi" :class="isDarkMode ? 'bi-sun-fill' : 'bi-moon-fill'"></i>
      </button> -->
      <a href="https://wa.me/03316566200" target="_blank" class="icon-btn" title="WhatsApp">
        <i class="bi bi-whatsapp"></i>
      </a>
    </div>

    <!-- Navbar -->
    <nav-component />

    <!-- Main content -->
    <main class="flex-grow-1">
      <!-- Hero Carousel Banner -->
      <section class="hero-carousel" id="hero">
        <div class="carousel-container">
          <Transition name="slide" mode="out-in">
            <div
              v-if="safeSlides.length > 0"
              :key="currentSlide"
              class="carousel-slide active"
              :style="{ backgroundImage: `url(${safeSlides[currentSlide].backgroundImage})` }"
            >
              <div class="carousel-overlay">
                <div class="carousel-content d-flex align-items-center justify-content-start h-100">
                  <div class="text-and-image-wrapper d-flex flex-column align-items-start text-start">
                    <div class="title-button-row d-flex align-items-start gap-4 mb-3">
                      <h1 class="display-3 fw-bold main_title italic-title animate-from-top">
                        {{ safeSlides[currentSlide].title || 'Premium Sports Gear' }}
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

      <!-- Clickable Moving Sports Icons -->
   <section class="sports-icons-section">
  <div class="icons-track">
    <div
      class="icon-item"
      v-for="(icon, index) in [...sportsIcons, ...sportsIcons]" 
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

      <!-- Team Customizer Section -->

      <!-- Deals / Offers Section -->
  <section class="deals-section py-5">
    <div class="container">

      <!-- Deal Found -->
      <div v-if="deal" class="row align-items-center">
        <!-- Left Content -->
        <div class="col-lg-6 mb-5  mb-lg-0">
          <h2 class="fw-bold text-black display-5 mb-3">{{ deal.title }}</h2>
          <h4 class="mb-3 text-black">{{ deal.subtitle || '' }}</h4>
          <p class="lead mb-4">
            {{ deal.description || 'Limited time offers on premium sports gear!' }}
          </p>

          <a
            v-if="deal.button_text"
            :href="deal.button_link || '#'"
            class="btn single-line-btn btn-lg px-5 py-3"
          >
            {{ deal.button_text }}
          </a>
        </div>

        <!-- Right Images Grid -->
        <div class="col-lg-6">
          <div v-if="deal.images && deal.images.length" class="row g-3">

            <div
              v-for="img in deal.images"
              :key="img.id"
              class="col-6 col-md-4"
            >
              <div class="deal-image-box">
                <img
                  :src="img.image_path"
                  class="deal-image"
                  alt="Deal Image"
                >

                <!-- Hover Overlay - HAMESHA SHOW HOGA -->
                <div class="deal-overlay">
                  <a
                    :href="img.link || '#'"
                    target="_blank"
                    class="btn btn-light btn-sm px-3 py-2 shadow"
                  >
                    View More
                  </a>
                </div>
              </div>
            </div>

          </div>

          <!-- No Images -->
          <div v-else class="text-center py-4">
            <p class="text-muted">No images added for this deal yet.</p>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-else class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-3">Loading latest deals...</p>
      </div>

    </div>
  </section>







      <!-- Featured Products -->
<section class="featured-products py-5">
  <div class="container">

    <!-- Header -->
    <div class="featured-header d-flex align-items-center justify-content-between mb-5 flex-wrap gap-3">
      <h2 class="section-title text-black mb-0">featured products</h2>

      <div class="tabs d-flex gap-2">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          class="tab-btn px-4 py-2"
          :class="{ active: activeTab === tab.value }"
          @click="setTab(tab.value)"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Carousel Wrapper -->
    <div class="carousel-wrapper position-relative overflow-hidden">

      <!-- Products Track -->
      <div
        class="products-track d-flex transition-transform duration-500 ease-in-out"
        :style="{ transform: `translateX(-${currentIndex * slidePercentage}%)` }"
      >
        <div
          v-for="(product, idx) in displayedProducts"
          :key="product.id || idx"
          class="product-card flex-shrink-0 px-3"
        >
          <div class="product-card-inner bg-white rounded shadow-sm p-4 text-center">
            <img
              :src="product.image"
              :alt="product.name"
              class="product-img img-fluid mb-3"
              style="height: 220px; object-fit: contain;"
              loading="lazy"
            />
            <h5 class="product-title mb-2 fw-semibold">{{ product.name }}</h5>
            <p class="product-price text-dark fw-bold mb-3">${{ product.price.toFixed(2) }}</p>
            <button class="add-cart-btn btn btn-dark w-100 py-2 ">ADD TO CART</button>
          </div>
        </div>

        <div v-if="displayedProducts.length === 0" class="w-100 text-center py-5">
          <p class="text-muted fs-4">No products found in this category...</p>
        </div>
      </div>

      <!-- Previous / Next Buttons -->
    <button
  class="carousel-btn carousel-btn-prev"
  @click="prevProduct"
  :disabled="currentIndex === 0"
>
  <i class="bi bi-chevron-left"></i>
</button>

<button
  class="carousel-btn carousel-btn-next"
  @click="nextProduct"
  :disabled="currentIndex >= maxSlides"
>
  <i class="bi bi-chevron-right"></i>
</button>


    </div>

  </div>
</section>

      <!-- Benefits -->
<section class="benefits-section py-5" style="background: #111;">
  <div class="container">
    <!-- flex row with justify-content-between -->
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
        <!-- Carousel Track -->
        <div class="videos-track" 
             :style="{ transform: `translateX(-${videoCurrentIndex * 33.333}%)`, transition: 'transform 0.5s ease' }">
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

      <!-- Navigation Arrows -->
      <button class="video-arrow prev-arrow" @click="prevVideo" :disabled="videoCurrentIndex === 0">‹</button>
      <button class="video-arrow next-arrow" @click="nextVideo" :disabled="videoCurrentIndex >= maxVideoSlides">›</button>

    </div>
  </div>
</section>

<!-- Video Modal -->
<div v-if="showVideoModal" class="video-modal" @click="closeVideo">
  <div class="video-modal-content" @click.stop>
    <button class="close-video-btn" @click="closeVideo"><i class="bi bi-x-lg"></i></button>
    <video ref="videoPlayer" :src="currentVideoUrl" controls autoplay class="fullscreen-video"></video>
  </div>
</div>

      <!-- Team Showcase -->
    <!-- Apparel Products Section -->
<!-- Apparel Products Section - Full Black Theme -->
<!-- Apparel Products Carousel - Dark Theme -->
<section class="apparel-products py-5" style="background: #000000;">
  <div class="container">

    <h2 class="section-title text-white text-start mb-5 fw-bold">
      APPAREL COLLECTION
    </h2>

    <!-- Carousel Wrapper -->
    <div class="carousel-wrapper position-relative overflow-hidden">

      <!-- Products Track -->
      <div
        class="products-track d-flex transition-transform duration-500 ease-in-out"
        :style="{ transform: `translateX(-${apparelCurrentIndex * slidePercentage}%)` }"
      >
        <div
          v-for="(product, idx) in apparelProducts"
          :key="product.id || idx"
          class="product-card flex-shrink-0 px-3"
        >
          <div class="product-card-inner text-center"
               style="
                 background: transparent;
                 border: 1px solid #444444;
                 border-radius: 12px;
                 padding: 20px 15px;
                 transition: all 0.35s ease;
               ">
            
            <!-- Image -->
            <div class="product-image-wrapper mb-3">
              <img
                :src="product.image"
                :alt="product.name"
                class="product-img img-fluid"
                style="height: 240px; object-fit: contain; background: transparent;"
                loading="lazy"
              />
            </div>

            <!-- Text - white -->
            <h5 class="product-name text-white fw-semibold mb-2">
              {{ product.name }}
            </h5>
            
            <p class="product-price text-white fw-bold mb-3 fs-5">
              ${{ product.price.toFixed(2) }}
            </p>

            <!-- Button - white bg, black text -->
            <button class="btn btn-light w-100 py-2 fw-bold add-to-cart-btn">
              ADD TO CART
            </button>
          </div>
        </div>

        <!-- No products fallback -->
        <div v-if="apparelProducts.length === 0" class="w-100 text-center py-5 text-white">
          <h4>No Apparel Products Found</h4>
          <small class="text-white-50">Make sure some products have is_apparel = true</small>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <button
        class="carousel-btn carousel-btn-prev"
        @click="prevApparel"
        :disabled="apparelCurrentIndex === 0"
      >
        <i class="bi bi-chevron-left"></i>
      </button>

      <button
        class="carousel-btn carousel-btn-next"
        @click="nextApparel"
        :disabled="apparelCurrentIndex >= maxApparelIndex"
      >
        <i class="bi bi-chevron-right"></i>
      </button>

    </div>

  </div>
</section>


      <!-- Testimonials -->
<!-- Testimonials Section -->
<section class="testimonials-section py-5">
  <div class="container">
    <div class="section-header text-center mb-5">
      <p class="testimonial-label">TESTIMONIAL</p>
      <h2 class="section-title text-black">HAPPY PEOPLE</h2>
    </div>

    <!-- Loading State -->
    <div v-if="loadingTestimonials" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-3">Loading customer reviews...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="testimonialsError" class="text-center py-5 text-danger">
      <p>{{ testimonialsError }}</p>
      <button class="btn btn-outline-dark mt-3" @click="fetchTestimonials">
        Try Again
      </button>
    </div>

    <!-- No Data -->
    <div v-else-if="testimonials.length === 0" class="text-center py-5 text-muted">
      <p>No testimonials available yet.</p>
    </div>

    <!-- Actual Carousel -->
    <div v-else class="testimonials-carousel-wrapper position-relative">
      <div 
        class="testimonials-carousel d-flex"
        :style="{ transform: `translateX(-${currentTestimonialIndex * (100 / itemsPerSlide)}%)` }"
      >
        <div 
          v-for="(testimonial, index) in testimonials"
          :key="index"
          class="testimonial-item flex-shrink-0"
          :style="{ width: `${100 / itemsPerSlide}%` }"
        >
          <div class="testimonial-card bg-white rounded shadow-sm p-4 h-100">
            <div class="quote-icon">"</div>
            
            <!-- Stars -->
           <div class="stars-rating mb-3">
  <i 
    v-for="star in 5" 
    :key="star"
    class="bi"
    :class="{
      'bi-star-fill text-warning': star <= testimonial.rating,
      'bi-star text-muted': star > testimonial.rating
    }"
  ></i>
</div>

            <p class="testimonial-text mb-4">{{ testimonial.text }}</p>

            <div class="testimonial-author d-flex align-items-center">
              <div class="author-image me-3">
                <img 
                  :src="testimonial.image" 
                  :alt="testimonial.name" 
                  class="rounded-circle"
                />
              </div>
              <div class="author-info">
                <h5 class="author-name mb-0">{{ testimonial.name }}</h5>
                <p class="author-position text-muted mb-0">{{ testimonial.position }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Arrows -->
      <button 
        class="carousel-arrow left" 
        @click="prevTestimonial"
        :disabled="currentTestimonialIndex === 0"
      >‹</button>
      <button 
        class="carousel-arrow right" 
        @click="nextTestimonial"
        :disabled="currentTestimonialIndex >= maxIndex"
      >›</button>

      <div class="carousel-dots mt-4 text-center">
        <span 
          v-for="n in Math.ceil(testimonials.length / itemsPerSlide)"
          :key="n"
          class="dot"
          :class="{ active: currentTestimonialIndex === (n-1) }"
          @click="goToTestimonial(n-1)"
        ></span>
      </div>
    </div>
  </div>
</section>


<!-- Recent Blog Section -->
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
        <img :src="`http://127.0.0.1:8000/storage/${blog.image}`" :alt="blog.title" class="img-fluid" />
      </div>
      <div class="blog-content">
        <div class="blog-meta">
          <span class="date">{{ new Date(blog.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</span>
          <span class="comments">0 COMMENTS</span> <!-- optionally add comment count -->
        </div>
        <h3 class="blog-title">{{ blog.title }}</h3>
        <p class="blog-excerpt">{{ blog.description }}</p>
<a
  :href="`/blog/${blog.slug}`"
  class="read-more"
>
  Read More →
</a>
      </div>
    </div>
  </div>
</div>


    <!-- Optional: View All Blogs Button -->
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

const activeTeam = ref(0)
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

// const toggleDarkMode = () => {
//   isDarkMode.value = !isDarkMode.value
//   if (typeof localStorage !== 'undefined') {
//     localStorage.setItem('darkMode', isDarkMode.value)
//   }
// }

const slides = ref([])
const safeSlides = computed(() => 
  slides.value.filter(s => s && typeof s.backgroundImage === 'string' && s.backgroundImage.trim() !== '')
)
const sportsIcons = ref([])



const fetchCategories = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/highlighted');

    console.log('Highlighted Categories Full Response:', res.data);

    if (!Array.isArray(res.data)) {
      console.error('API ne array nahi diya!', res.data);
      sportsIcons.value = [];
      return;
    }

    sportsIcons.value = res.data.map(cat => ({
      teamId: cat.id,
      name: cat.name,
      image: cat.icon_image,                // normal image
      highlight_image: cat.highlight_image, // special highlighted image
      customTitle: cat.custom_title || null,
      customSubtitle: cat.custom_subtitle || null,
      customDescription: cat.custom_description || null,
      customButtonText: cat.custom_button_text || null,
    }));

    console.log('Processed sportsIcons (with highlight_image):', sportsIcons.value);

    if (sportsIcons.value.length > 0) {
      activeTeam.value = sportsIcons.value[0].teamId;
    }
  } catch (error) {
    console.error('Fetch error:', error);
    sportsIcons.value = [];
  }
};
const deal = ref(null)

const fetchDeal = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/latest-deal')
    console.log('Deal API Full Response:', res)          // Pura response
    console.log('Deal Data:', res.data)                  // Sirf data
    console.log('Images Array:', res.data?.images)       // Images check
    deal.value = res.data
  } catch (error) {
    console.error('Deals Fetch FAILED:', error)
  }
}

onMounted(() => {
  fetchDeal()
})

const teamContent = computed(() => {
  const activeCat = sportsIcons.value.find(cat => cat.teamId === activeTeam.value)

  if (!activeCat) {
    return {
      title: 'Select a Category',
      subtitle: 'Choose from highlighted collections',
      description: 'Click any icon above to explore',
      buttonText: 'Explore Now',
    }
  }

  return {
    title: activeCat.customTitle || activeCat.name,
    subtitle: activeCat.customSubtitle || 'Premium Collection',
    description: activeCat.customDescription || 'Discover high-quality gear for ' + activeCat.name,
    buttonText: activeCat.customButtonText || 'Shop Now',
  }
})


// Videos
const sportsVideos = ref([])
const maxVideoSlides = computed(() => Math.max(0, Math.ceil(sportsVideos.value.length / 3) - 1))

// Fetch videos from backend
const fetchVideos = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/videos');
    console.log('Videos from API:', res.data);
    console.log('Count:', res.data.length);
    sportsVideos.value = res.data || [];
  } catch (err) {
    console.error('Videos fetch error:', err);
    if (err.response) {
      console.log('Status:', err.response.status);
      console.log('Error:', err.response.data);
    }
  }
};

onMounted(() => {
  fetchVideos()
})


// Carousel navigation
const prevVideo = () => { if (videoCurrentIndex.value > 0) videoCurrentIndex.value-- }
const nextVideo = () => { if (videoCurrentIndex.value < maxVideoSlides.value) videoCurrentIndex.value++ }

// Video modal
const playVideo = (video) => {
  console.log('Clicked video object:', video);   // ← yeh add karo, console check karne ke liye

  currentVideoUrl.value = video.video_url;       // ← underscore use karo (backend ke mutabiq)

  showVideoModal.value = true;

  if (videoInterval) clearInterval(videoInterval);
};

const closeVideo = () => {
  showVideoModal.value = false
  currentVideoUrl.value = ''
  videoInterval = setInterval(() => {
    if (videoCurrentIndex.value < maxVideoSlides.value) videoCurrentIndex.value++
    else videoCurrentIndex.value = 0
  }, 6000)
}

// Auto-scroll carousel
onMounted(() => {
  fetchVideos()
  videoInterval = setInterval(() => {
    if (videoCurrentIndex.value < maxVideoSlides.value) videoCurrentIndex.value++
    else videoCurrentIndex.value = 0
  }, 6000)
})

onUnmounted(() => {
  if (videoInterval) clearInterval(videoInterval)
})
const blogs = ref([])

// Fetch all blogs
const fetchBlogs = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/blogs')
    blogs.value = res.data
  } catch (err) {
    console.error(err)
  }
}

// Fetch single blog by slug
const fetchBlogDetail = async (slug) => {
  try {
    const res = await axios.get(`http://127.0.0.1:8000/api/blogs/${slug}`)
    currentBlog.value = res.data
  } catch (err) {
    console.error(err)
  }
}


onMounted(() => {
  fetchBlogs()
})


// Yeh line **bilkul galat** hai
const fetchApparelProducts = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/apparel-products')
    apparelProducts.value = res.data || []
    console.log("Apparel products loaded:", apparelProducts.value.length)
  } catch (err) {
    console.error("Apparel fetch failed:", err)
  }
}
// Apparel Carousel – FULL WORKING LOGIC
const apparelProducts = ref([])
const apparelCurrentIndex = ref(0)                      // ← yeh missing tha
const apparelItemsPerView = 4                           // 4 cards visible
const apparelSlidePercentage = 100 / apparelItemsPerView  // 25%

const maxApparelSlides = computed(() => {
  const total = apparelProducts.value.length
  return Math.max(0, Math.ceil(total / apparelItemsPerView) - 1)
})

const nextApparel = () => {
  if (apparelCurrentIndex.value < maxApparelSlides.value) {
    apparelCurrentIndex.value++
  }
}

const prevApparel = () => {
  if (apparelCurrentIndex.value > 0) {
    apparelCurrentIndex.value--
  }
}

// Auto scroll start
let apparelAutoInterval = null

onMounted(async () => {
  await fetchApparelProducts()   // already tha

  // Start auto scroll for apparel
  apparelAutoInterval = setInterval(() => {
    if (apparelCurrentIndex.value < maxApparelSlides.value) {
      apparelCurrentIndex.value++
    } else {
      apparelCurrentIndex.value = 0
    }
  }, 5000)  // 5 seconds – change kar sakte ho
})

// Clear interval on destroy
onUnmounted(() => {
  if (apparelAutoInterval) clearInterval(apparelAutoInterval)
})


const featuredProducts = ref([])


const tabs = ref([
  { label: 'All', value: 'all' },
  { label: 'New Arrivals', value: 'new' },
  { label: 'Best Sellers', value: 'bestsellers' }
])

// Fetch featured (is_featured = true) products
const fetchFeaturedProducts = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/featured-products')
    featuredProducts.value = res.data || []
  } catch (err) {
    console.error('Featured fetch error:', err)
  }
}

const setTab = (value) => {
  activeTab.value = value
  currentIndex.value = 0  // reset carousel jab tab change ho
}

const displayedProducts = computed(() => {
  let products = [...featuredProducts.value]

  if (activeTab.value === 'all') {
    return products
  }

  if (activeTab.value === 'new') {
    // New Arrivals: last 14 days ke products (created_at chahiye API mein)
    const twoWeeksAgo = new Date()
    twoWeeksAgo.setDate(twoWeeksAgo.getDate() - 14)
    return products.filter(p => {
      const created = new Date(p.created_at || 0)
      return created >= twoWeeksAgo
    })
  }

  if (activeTab.value === 'bestsellers') {
    // Best Sellers: abhi placeholder – future mein sales_count field add kar ke sort kar sakte ho
    return products.slice(0, 12) // temporary top 12
  }

  return products
})

// Carousel logic: 4 products visible at a time
const itemsPerView = 4
const slidePercentage = 100 / itemsPerView

const maxSlides = computed(() => {
  const total = displayedProducts.value.length
  return Math.max(0, Math.ceil(total / itemsPerView) - 1)
})

const nextProduct = () => {
  if (currentIndex.value < maxSlides.value) {
    currentIndex.value++
  }
}

const prevProduct = () => {
  if (currentIndex.value > 0) {
    currentIndex.value--
  }
}

// Auto-slide (optional – comment out if nahi chahiye)
let autoInterval = null
onMounted(() => {
  fetchFeaturedProducts()
  // autoInterval = setInterval(() => {
  //   if (currentIndex.value < maxSlides.value) currentIndex.value++
  //   else currentIndex.value = 0
  // }, 6000)
})

onUnmounted(() => {
  if (autoInterval) clearInterval(autoInterval)
})
const fetchSlides = async () => {
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/banners')
    slides.value = res.data
  } catch (err) {
    slides.value = [{ title: 'Premium Sports Gear', buttonText: 'Shop Now', buttonLink: '#', backgroundImage: 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=1200&h=600&fit=crop', pngImage: null }]
  }
}

const nextSlide = () => { if (slides.value.length) currentSlide.value = (currentSlide.value + 1) % slides.value.length }
const prevSlide = () => { if (slides.value.length) currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length }
const goToSlide = (index) => { if (slides.value.length) currentSlide.value = index }
const handleBuy = (url) => { if (url) window.location.href = url }
const selectTeam = (teamId) => {
  router.push(`/products?category=${teamId}`) 
 
}
const goTo = (index) => { currentIndex.value = index }






// ────────────── Testimonials ──────────────
const testimonials = ref([])
const loadingTestimonials = ref(true)
const testimonialsError = ref(null)

const currentTestimonialIndex = ref(0)
const itemsPerSlide = ref(3)  // 3 cards ایک وقت میں دکھائیں (responsive کے لیے change کر سکتے ہو)

const maxIndex = computed(() => {
  return Math.max(0, testimonials.value.length - itemsPerSlide.value)
})

const fetchTestimonials = async () => {
  loadingTestimonials.value = true
  testimonialsError.value = null

  try {
    const res = await axios.get('http://127.0.0.1:8000/api/testimonials')
    console.log('Testimonials raw data:', res.data)

    testimonials.value = res.data.map(item => ({
      text:     item.text     || 'No review text',
      name:     item.name     || 'Customer',
      position: item.position || 'Happy Buyer',
      image:    item.image || 'https://via.placeholder.com/80?text=No+Photo',
      rating:   item.rating   || 5,
    }))
  } catch (err) {
    console.error('Fetch error:', err)
    testimonialsError.value = 'Could not load testimonials'
  } finally {
    loadingTestimonials.value = false
  }
}

// Navigation functions
const prevTestimonial = () => {
  if (currentTestimonialIndex.value > 0) {
    currentTestimonialIndex.value--
  }
}

const nextTestimonial = () => {
  if (currentTestimonialIndex.value < maxIndex.value) {
    currentTestimonialIndex.value++
  }
}

const goToTestimonial = (index) => {
  currentTestimonialIndex.value = index
}


onMounted(() => {
  fetchTestimonials()
})
onMounted(async () => {
    await fetchCategories()   
  showWelcomePopup.value = true
  setTimeout(() => { showWelcomePopup.value = false }, 3000)
  
  if (typeof localStorage !== 'undefined') {
    isDarkMode.value = localStorage.getItem('darkMode') === 'true'
  }
  
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
/////////////////

</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
* { font-family: 'Smooch Sans', sans-serif !important; }

* { margin: 0; padding: 0; box-sizing: border-box; }
body, html { font-family: 'Poppins', sans-serif; background: white; color: #000; }

/* Dark Mode */
.dark-mode { background: #0a0a0a; color: #fff; }
.dark-mode .team-customizer-section { background: #111; }
.dark-mode .main-heading, .dark-mode .sub-heading { color: #fff; }
.dark-mode .description { color: #ccc; }
.dark-mode .featured-products { background: #0a0a0a; }
.dark-mode .section-title { color: #fff; }
.dark-mode .product-card-inner { background: #1a1a1a; color: #fff; }
.dark-mode .testimonials-section { background: #111; }
.dark-mode .testimonial-content { background: #1a1a1a; }
.dark-mode .section-title { color: #fff; }

/* Welcome Popup */
.welcome-popup {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
  z-index: 99999; display: flex; align-items: center; justify-content: center;
  animation: fadeIn 0.5s ease-in-out;
}
/* Fixed Icons Left */
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

/* Hero Carousel */
.hero-carousel { position: relative; height: 100vh; min-height: 700px; overflow: hidden; }
.carousel-container { position: relative; height: 85%; }
.carousel-slide {
  position: absolute; inset: 0; background-size: cover; background-position: center;
  background-repeat: no-repeat; transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    animation: slideBackgroundFromRight 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;

}
.carousel-overlay { position: absolute; inset: 0; z-index: 2; display: flex; align-items: center; }
.carousel-content { max-width: 1400px; margin: 0 5%; width: 100%; }
.text-and-image-wrapper { max-width: 60%; text-align: center; margin-top: 12%; }

.title-button-row {
  width: 100%; display: flex; align-items: flex-start; flex-wrap: nowrap;
  justify-content: space-between;
}
.main_title {
  width: 60%; font-size: 4rem; line-height: 0.9; font-weight: 800; font-style: italic;
  text-align: left; margin: 0; padding: 0; white-space: normal; word-break: break-word;
  flex-shrink: 0; color: black;
}
.button-wrapper { margin-top: 50px; flex-shrink: 0; align-self: flex-start; }
.single-line-btn {
  white-space: nowrap; display: inline-block; text-align: center; line-height: 1.2;
  padding: 10px 30px; background: black; color: white; border-radius: 20px;
  font-size: 20px; letter-spacing: 1px; font-weight: 550; transition: all 0.3s ease;
  box-shadow: none; border: none; cursor: pointer;
}
.single-line-btn:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); transform: translateY(-2px);
}

.hero-png {
  max-width: 110%; max-height: 850px; height: auto; margin-right: auto; margin-left: auto;
  animation: float 6s ease-in-out infinite;
}
/* PNG image from LEFT side of screen */
.animate-from-left {
  opacity: 0;
  transform: translateX(-150%);
  animation: slideInFromLeft 1.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
  animation-delay: 0.4s;
}
/* Title from TOP-LEFT corner of screen */
.animate-from-top {
  opacity: 0;
  transform: translate(-100%, -100%);
  animation: slideInFromTopLeft 1.1s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
  animation-delay: 0.2s;
}
/* Button from BOTTOM-LEFT corner of screen */
.delayed {
  opacity: 0;
  transform: translate(-100%, 100%);
  animation: slideInFromBottomLeft 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
  animation-delay: 0.6s;
}
@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }

.slide-enter-active,
.slide-leave-active {
  transition: opacity 0.9s ease-in-out;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
}
@keyframes slideBackgroundFromRight {
  0% {
    transform: translateX(100%);
    opacity: 0;
  }
  100% {
    transform: translateX(0);
    opacity: 1;
  }
}
@keyframes slideInFromLeft {
  0% {
    transform: translateX(-150%);
    opacity: 0;
  }
  70% {
    transform: translateX(6%);
    opacity: 1;
  }
  100% {
    transform: translateX(0);
    opacity: 1;
  }
}
@keyframes slideInFromTopLeft {
  0% {
    transform: translate(-100%, -100%);
    opacity: 0;
  }
  70% {
    transform: translate(3%, 3%);
    opacity: 1;
  }
  100% {
    transform: translate(0, 0);
    opacity: 1;
  }
}
@keyframes slideInFromBottomLeft {
  0% {
    transform: translate(-100%, 100%);
    opacity: 0;
  }
  70% {
    transform: translate(3%, -3%);
    opacity: 1;
  }
  100% {
    transform: translate(0, 0);
    opacity: 1;
  }
  
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.welcome-content { text-align: center; }
.welcome-text {
  font-size: 5rem; font-weight: 900; color: white; letter-spacing: 8px;
  text-transform: uppercase; animation: slideUp 1s ease-out;
  text-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
}
@keyframes slideUp { 0% { transform: translateY(50px); opacity: 0; } 100% { transform: translateY(0); opacity: 1; } }

/* Subtitle stays same */
.delayed-more {
  animation-delay: 0.55s;
}
.carousel-control-left, .carousel-control-right {
  position: absolute; top: 50%; transform: translateY(-50%); z-index: 4;
  width: 60px; height: 60px; cursor: pointer; background: transparent;
  border: none; outline: none; clip-path: none; display: flex;
  align-items: center; justify-content: center; transition: all 0.3s;
}
.carousel-control-left i, .carousel-control-right i {
  color: #000; font-weight: 700; font-size: 2.2rem; display: flex;
  align-items: center; justify-content: center; pointer-events: none;
}
.carousel-control-left:hover, .carousel-control-right:hover {
  transform: translateY(-50%) scale(1.1); background: transparent; border: none;
}
.carousel-control-left { left: 10px; }
.carousel-control-right { right: 10px; }

.carousel-dots {
  position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);
  z-index: 4; display: flex; gap: 12px;
}
.dot {
  display: inline-block;  width: 12px;  height: 12px;  border-radius: 50%;  background: #ccc;  margin: 0 8px;   cursor: pointer;  transition: all 0.3s;
}

.dot.active {
  background: #000;  width: 36px;  border-radius: 20px;
}
.loading-placeholder { background: rgba(0,0,0,0.7); z-index: 5; }
/* Sports Icons */
.sports-icons-section {
  background: black; padding: 25px 0; overflow: hidden; position: relative;
}
.sports-icons-section::before, .sports-icons-section::after {
  content: ''; position: absolute; top: 0; bottom: 0; width: 70px;
  background: black; z-index: 2;
}
.sports-icons-section::before { left: 0; }
.sports-icons-section::after { right: 0; }
.icons-track {
  display: flex; gap: 80px; width: max-content; animation: scrollIcons 40s linear infinite;
}
.icons-track:hover { animation-play-state: paused; }
.icon-item {
  display: flex; flex-direction: column; align-items: center; gap: 10px;
  cursor: pointer; transition: all 0.3s ease; padding: 0;
}
.icon-item:hover { transform: translateY(-4px); }
.icon-item:hover .icon-circle::after{
    opacity: 1; transform: scale(1);
   transform: scale(1);
  animation: rotateDotted 8s linear infinite; 
 } .icon-item.active .icon-circle::after {
  opacity: 1; transform: scale(1);
}
.icon-item:hover .icon-circle, .icon-item.active .icon-circle { transform: scale(1.08); }
.icon-image { width: 100%; height: 100%; object-fit: cover; }
.icon-name {
  color: white; font-size: 12px; font-weight: 600; text-transform: uppercase;
  letter-spacing: 0.5px; text-align: center; max-width: 100px; transition: all 0.3s ease;
}
.icon-item:hover .icon-name, .icon-item.active .icon-name {
  color: #ffffff; text-shadow: 0 0 8px rgba(255,255,255,0.6);
}
.icon-circle {
  width: 85px; height: 85px; display: flex; align-items: center; justify-content: center;
  background: black; border-radius: 50%; overflow: hidden; transition: all 0.4s ease;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3); position: relative;
}
.icon-circle::after {
  content: ''; position: absolute; inset: 0; border: 3px dotted #ffffff;
  border-radius: 50%; opacity: 0; transition: opacity 0.35s ease, transform 0.35s ease;
  transform: scale(0.92); pointer-events: none;
}
@keyframes rotateDotted {
  0% { transform: scale(0.92) rotate(0deg); }
  100% { transform: scale(0.92) rotate(360deg); }
}
@keyframes scrollIcons { 0% { transform: translateX(0); } 100% { transform: translateX(-70%); } }
/* Team Customizer */
.team-customizer-section { padding: 80px 0; background: #f8f9fa; }
.container { max-width: 1300px; margin: 0 auto; padding: 0 30px; }
.main-heading {
  font-size: 56px; font-weight: 800; color: #000; margin-bottom: 20px; letter-spacing: -1px;
}
.sub-heading {
  font-size: 24px; font-weight: 600; color: #333; margin-bottom: 25px; line-height: 1.4;
}
.description {
  font-size: 17px; color: #666; line-height: 1.8; margin-bottom: 35px;
}
.btn-get-started {
  background: #000; color: white; padding: 16px 50px; border: none; border-radius: 50px;
  font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;
  text-transform: uppercase; letter-spacing: 1px;
}
.btn-get-started:hover {
  background: #333; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
.products-grid-2x2 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
.product-item { cursor: pointer; transition: all 0.3s ease; }
.product-image-wrapper {
  position: relative; overflow: hidden; border-radius: 12px; margin-bottom: 15px;
}
.product-image {
  width: 100%; height: 160px; object-fit: cover; filter: grayscale(100%);
  transition: all 0.5s ease;
}
.product-item:hover .product-image { filter: grayscale(0%); transform: scale(1.08); }
.product-item:hover { transform: translateY(-5px); }
.product-name {
  text-align: center; font-size: 16px; font-weight: 600; color: #000; margin: 0;
}
.deals-section {
  position: relative;
  background-color: #eeecec;
}



/* Image box */
.deal-image-box {
  position: relative;
  overflow: hidden;
  border-radius: 12px;
  /* box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); */
  transition: box-shadow 0.3s ease;
  cursor: pointer;
  margin: 12px;
}

.deal-image-box:hover {
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* Image */
.deal-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  transition: transform 0.35s ease;
  display: block;
}

/* Overlay */
.deal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.65);
  opacity: 0;
  visibility: hidden;
  transition: all 0.35s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  border-radius: 12px;
  pointer-events: none;
}

/* Show overlay on hover */
.deal-image-box:hover .deal-overlay {
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}

.deal-image-box:hover .deal-image {
  transform: scale(1.07);
}

/* Button styling */
.deal-overlay .btn {
  z-index: 20;
  position: relative;
  font-weight: 600;
  text-decoration: none;
  pointer-events: auto;
  white-space: nowrap;
}

.deal-overlay .btn:hover {
  transform: scale(1.05);
  transition: transform 0.2s ease;
}

/* Featured Products */
.featured-products {
  position: relative;
  padding: 80px 0;
  overflow: hidden;
  background: url('/public/assets/images/lines texture.svg') no-repeat center center;
  background-size: cover;
}

/* Overlay */
.featured-products::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.933); /* halka overlay */
  z-index: 1;
}

/* Content upar rahe */
.featured-products > * {
  position: relative;
  z-index: 2;
}

.featured-header {
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; margin-bottom: 3rem;
}
.section-title { font-size: 2.8rem; font-weight: 800; color: #000; margin: 0; }
.tabs { display: flex; gap: 10px; flex-wrap: wrap; }
.tab-btn {
  padding: 10px 20px; border: 2px solid #000; background: #fff; color: #000;
  font-weight: 600; cursor: pointer; transition: all 0.3s ease; border-radius: 4px;
}
.tab-btn:hover { background: #000; color: #fff; }
.tab-btn.active { background: #000; color: #fff; border-color: #000; }

.carousel-wrapper { position: relative; overflow: hidden; padding: 0 60px; }
.products-track { display: flex; transition: transform 0.5s ease; }
.product-card { min-width: 25%; padding: 0 15px; box-sizing: border-box; }
.product-card-inner {
  background: #fff; border-radius: 8px; padding: 20px; text-align: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s ease;
  display: flex; flex-direction: column; height: 100%;
}
.product-card:hover .product-card-inner {
  transform: translateY(-8px); box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.product-img {
  width: 100%; height: 220px; object-fit: contain; background: #f8f9fa;
  margin-bottom: 15px; border-radius: 4px;
}
.product-title {
  font-size: 14px; font-weight: 600; margin-bottom: 10px; color: #000; flex-grow: 1;
}
.product-price {
  color: #000; font-weight: 700; font-size: 1.2rem; margin-bottom: 15px;
}
.add-cart-btn {
  background: #000; color: #fff; border: none; padding: 10px 20px; border-radius: 4px;
  font-weight: 600; cursor: pointer; width: 100%; transition: all 0.3s ease;
}
.add-cart-btn:hover { background: #333; transform: translateY(-2px); }

.carousel-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 46px;
  height: 46px;
  border-radius: 50%;
  background: #000;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
  z-index: 10;
}

.carousel-btn i {
  font-size: 20px;
}

.carousel-btn-prev {
  left: 15px;
}

.carousel-btn-next {
  right: 15px;
}

.carousel-btn:hover {
  background: #222;
  transform: translateY(-50%) scale(1.05);
}

.carousel-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}


/* Benefits */


.benefit-card {
  background: white; 
  border: 1px solid #333; 
  transition: all 0.3s ease; 
  color: black;
  border-radius: 8px;
}

.benefit-card:hover {
  background: black; 
  color: white; 
  transform: translateY(-8px); 
  border: 1px solid #333;
}

.benefit-card:hover .icon,
.benefit-card:hover h4,
.benefit-card:hover p {
  color: white !important;
}

.icon { 
  color: black; 
  line-height: 1; 
  transition: all 0.3s ease; 
}
.benefit-card:hover .icon i { 
  transform: scale(1.15); 
}

h4 { 
  font-size: 1.4rem; 
  transition: color 0.3s ease; 
  color: black;
}

p { 
  color: black;
  font-size: 0.95rem; 
  line-height: 1.6; 
  transition: color 0.3s ease; 
}




/* Latest Videos */
.latest-videos-section {
  position: relative;
  padding: 80px 0;
  overflow: hidden;
  background: url('/public/assets/images/lines texture.svg') no-repeat center center;
  background-size: cover;
}

/* Overlay */
.latest-videos-section::before {
  content: '';
  position: absolute;
  inset: 0;
background: rgba(255, 255, 255, 0.933);  z-index: 1;
}

/* Content upar rahe */
.latest-videos-section > * {
  position: relative;
  z-index: 2;
}

.videos-header {
  display: flex; align-items: center; justify-content: center; gap: 15px;
  margin-bottom: 50px; position: relative; z-index: 2;
}
.header-icon { font-size: 2.5rem; color: black; animation: pulse 2s ease-in-out infinite; }
@keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
.videos-title {
  font-size: 3rem; font-weight: 800; color: black; letter-spacing: 3px; margin: 0;
}
.videos-carousel-wrapper { position: relative; padding: 0 80px; z-index: 2; }
.videos-carousel-container { overflow: hidden; border-radius: 12px; }
.videos-track { display: flex; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
.video-card { min-width: 33.333%; padding: 0 15px; box-sizing: border-box; }
.video-thumbnail {
  position: relative; width: 100%; height: 350px; border-radius: 12px; overflow: hidden;
  cursor: pointer;  transition: all 0.4s ease;
}
.video-thumbnail:hover { transform: translateY(-10px) scale(1.02); }
.video-thumbnail img {
  width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;
}
.video-thumbnail:hover img { transform: scale(1.1); }
.play-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.6) 100%);
  display: flex; align-items: center; justify-content: center; transition: all 0.4s ease;
}
.play-button {
  width: 70px; height: 70px; background: rgba(255, 255, 255, 0.95); border-radius: 50%;
  display: flex; align-items: center; justify-content: center; color: black;
  font-size: 2.2rem; transition: all 0.4s ease; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}
.video-thumbnail:hover .play-button {
  transform: scale(1.15); background: black; color: white;
}
.video-arrow {
  position: absolute; top: 50%; transform: translateY(-50%); width: 60px; height: 60px;
  background: rgba(255, 255, 255, 0.95); color: #000; border: none; border-radius: 50%;
  font-size: 2rem; font-weight: bold; cursor: pointer; z-index: 10; transition: all 0.3s ease;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}
.video-arrow:hover:not(:disabled) {
  background: black; color: white; transform: translateY(-50%) scale(1.1);
  
}
.video-arrow:disabled { opacity: 0.3; cursor: not-allowed; }
.video-arrow.prev-arrow { left: 0; }
.video-arrow.next-arrow { right: 0; }
.video-title-overlay {
  position: absolute; bottom: 0; left: 0; right: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
  color: white; padding: 20px 15px 15px; font-size: 16px; font-weight: 600;
  text-align: center; opacity: 0; transition: opacity 0.3s ease;
}
.video-thumbnail:hover .video-title-overlay { opacity: 1; }

/* Video Modal */
.video-modal {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.95); z-index: 9999; display: flex;
  align-items: center; justify-content: center; animation: fadeIn 0.3s ease;
}
.video-modal-content {
  position: relative; width: 90%; max-width: 1400px; height: 80vh;
  background: #000; border-radius: 12px; overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
}
.fullscreen-video { width: 100%; height: 100%; object-fit: contain; background: #000; }
.close-video-btn {
  position: absolute; top: 20px; right: 20px; z-index: 10000; width: 50px; height: 50px;
  background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; color: #000;
  font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}
.close-video-btn:hover {
  background: black; color: white; transform: scale(1.1) rotate(90deg);
}



/* Testimonials */
.testimonials-section { background-color: #eeecec; padding: 100px 0; }
.section-header { margin-bottom: 60px; }
.testimonial-label {
  color: #000000; font-size: 1rem; font-weight: 600; text-transform: uppercase;
  letter-spacing: 2px; margin-bottom: 10px;
}


.testimonials-carousel-wrapper {
  position: relative;
  overflow: hidden;
  width: 100%;
}

.testimonials-carousel {
  display: flex;
  transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* bahut smooth */
  will-change: transform; /* performance better */
}

.testimonial-item {
  flex: 0 0 calc(100% / 3);          /* 3 dikhao */
  padding: 0 12px;
}

.testimonial-card {
  background: white;
  border: 1px solid #e0e0e0;          /* ← added border */
  border-radius: 12px;
  padding: 15px 15px;
  min-height: 260px;
  position: relative;
  transition: all 0.35s ease;
  box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

.testimonial-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.12);
  border-color: #333;
}

.quote-icon {
  position: absolute;
  top: -15px;
  left: 25px;
  font-size: 6rem;
  color: rgba(0,0,0,0.07);
  line-height: 1;
  font-weight: bold;
}

.testimonial-text {
  font-size: 1.05rem;
  line-height: 1.75;
  color: #444;
  margin-bottom: 25px;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 15px;
}
.stars-rating {
  color: #000000;               /* pure black */
  font-size: 1rem;            /* size adjust kar sakte ho 1.2rem ya 1.6rem */
  letter-spacing: 5px;          /* stars ke beech gap */
  margin: 15px 0 20px 0;        /* top/bottom space */
  text-align: left;             /* left aligned – center chahiye to center likh dena */
}

.stars-rating i {
  transition: all 0.25s ease;
}

.testimonial-card:hover .stars-rating i {
  transform: scale(1.15);      
  color: #111111;            
}
.author-image {
  width: 65px;
  height: 65px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid #f0f0f0;
}

.author-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.author-name {
  font-size: 1.15rem;
  font-weight: 700;
  color: #111;
  margin: 0;
}

.author-position {
  font-size: 0.9rem;
  color: #777;
  margin: 4px 0 0;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}





/* Arrows */
.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 50px;
  height: 50px;
  background: rgba(0,0,0,0.7);
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 1.6rem;
  cursor: pointer;
  z-index: 5;
  transition: all 0.3s;
}

.carousel-arrow:hover {
  background: #000;
  transform: translateY(-50%) scale(1.15);
}

.carousel-arrow.left  { left: 0; }
.carousel-arrow.right { right: 0; }



.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  font-size: 30px;
  background: transparent;
  border: none;
  cursor: pointer;
}

.carousel-arrow.left {
  left: 10px;
}

.carousel-arrow.right {
  right: 10px;
}

.quote-icon {
  position: absolute; left: 50%; transform: translateX(-50%);
  font-size: 5rem; color: black; font-weight: bold; line-height: 1;
}
.testimonial-text {
  font-size: 1.1rem; line-height: 1.8; color: #555; margin-bottom: 30px;
}
.testimonial-author {
  display: flex; align-items: center; justify-content: left; gap: 15px;
}
.author-image {
  width: 70px; height: 70px; border-radius: 50%; overflow: hidden; border: 4px solid #fff;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.author-image img { width: 100%; height: 100%; object-fit: cover; }
.author-info { text-align: left; }
.author-name {
  font-size: 1.3rem; font-weight: 700; color: #000000; margin: 0 0 5px;
}
.author-position {
  font-size: 0.95rem; color: #777; margin: 0; text-transform: uppercase; letter-spacing: 1px;
}



.team-showcase-section {
  background: #0a0a0a;
  color: white;
  padding: 80px 0;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
}

.team-header {
  flex-wrap: wrap;
  gap: 20px;
}

.team-title {
  font-size: 3.2rem;
  font-weight: 800;
  letter-spacing: 2px;
  text-transform: uppercase;
}

.lightning {
  color: white;
  font-size: 3.5rem;
  margin-right: 10px;
}

.btn-join-team {
  background: transparent;
  border: 1px solid #ffff;
  color: #ffff;
  font-weight: 700;
  font-size: 1.1rem;
  padding: 10px 15px;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  transition: all 0.3s ease;
}

.btn-join-team:hover {
  background: black;
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(255, 255, 255, 0.4);
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 10px;
}

.team-member-card {
  position: relative;
  overflow: hidden;
  border-radius: 12px;
  background: #111;
  transition: all 0.4s ease;
}

.team-member-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
}

.member-image-wrapper {
  position: relative;
  height: 320px;
  overflow: hidden;
}

.member-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.team-member-card:hover .member-image {
  transform: scale(1.08);
}

.member-number {
  position: absolute;
  top: 260px;              
  left: 50%;
  transform: translateX(-50%);
 
  width:100px;
  height: 80px;
  background: black;

  display: flex;
  align-items: center;
  justify-content: center;

  color: white;
  font-size: 2.6rem;
  font-weight: 900;

  /* TRIANGLE SHAPE */
  clip-path: polygon(
    50% 0%,
    100% 100%,
    0% 100%
  );

  z-index: 5;
}


.member-info {
  padding: 30px 20px 40px;
  text-align: center;
}

.member-name {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 8px;
  color: white;
}

.member-position {
  font-size: 1.1rem;
  color: #ffff;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
}
.recent-blog-section {
  position: relative;
  padding: 80px 0;
  overflow: hidden;
  background: url('/public/assets/images/lines texture.svg') no-repeat center center;
  background-size: cover;}
  .recent-blog-section::before {
  content: '';
  position: absolute;
  inset: 0;
background: rgba(255, 255, 255, 0.933);  z-index: 1;
}

/* Content upar rahe */
.recent-blog-section  * {
  position: relative;
  z-index: 2;
}

.section-label {
  color: #000000;
  font-size: 1rem;
  letter-spacing: 2px;
  margin-bottom: 8px;
}

.section-title {
  font-size: 2.8rem;
  font-weight: 800;
  color: #111;
}

.blog-card {
  background: white;
  /* border-radius: 12px; */
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  transition: all 0.35s ease;
}

.blog-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.blog-image {
  flex: 0 0 45%;
  overflow: hidden;
}

.blog-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.blog-card:hover .blog-image img {
  transform: scale(1.08);
}

.blog-content {
  flex: 1;
  padding: 25px 30px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.blog-meta {
  display: flex;
  gap: 20px;
  margin-bottom: 12px;
  font-size: 0.9rem;
  color: #777;
  text-transform: uppercase;
}

.blog-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #000;
  margin-bottom: 12px;
  line-height: 1.3;
}

.blog-excerpt {
  font-size: 1rem;
  color: #555;
  line-height: 1.6;
  margin-bottom: 18px;
}

.read-more {
  color: #000;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.3s;
}

.read-more:hover {
  color: #dc3545;
}

.btn-view-all {
  background: #000;
  color: white;
  padding: 14px 40px;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
}

.btn-view-all:hover {
  background: #333;
  transform: translateY(-3px);
}







.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 15px;
}

.section-header {
  margin-bottom: 60px;
}

.testimonial-label {
  color: #000000;
  font-size: 1rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 10px;
}

.section-title {
  font-size: 3rem;
  font-weight: 800;
  color: #0d1e3a;
  margin: 0;
}




/* ==========================================
   PROSIX - FULLY RESPONSIVE STYLES
   Har device pe perfect dikhega
   ========================================== */

/* Mobile First Approach - Chhote screens se shuru */

/* ========== EXTRA SMALL DEVICES (Mobile Portrait - 320px to 575px) ========== */
@media (max-width: 575px) {
  
  /* Hero Section - FULL FIX */
  .hero-carousel {
    height: 100vh !important;
    min-height: 800px !important;
  }
  
  .carousel-slide {
    background-size: cover !important;
    background-position: center center !important;
  }
  
  .carousel-overlay {
    padding: 20px 10px !important;
  }
  
  .carousel-content {
    margin: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 15px !important;
  }
  
  .text-and-image-wrapper {
    max-width: 100% !important;
    margin-top: 20% !important;
    padding: 0 !important;
    width: 100% !important;
    text-align: center !important;
  }
  
  .title-button-row {
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 20px !important;
    width: 100% !important;
  }
  
  .main_title {
    width: 100% !important;
    font-size: 2.2rem !important;
    line-height: 1.1 !important;
    margin-bottom: 0 !important;
    text-align: center !important;
    color: #000 !important;
    font-weight: 900 !important;
    padding: 0 10px !important;
  }
  
  .button-wrapper {
    margin-top: 0 !important;
    width: 100% !important;
    display: flex !important;
    justify-content: center !important;
  }
  
  .single-line-btn {
    font-size: 15px !important;
    padding: 12px 35px !important;
    width: auto !important;
    text-align: center !important;
    white-space: nowrap !important;
    display: inline-block !important;
  }
  
  .lead {
    font-size: 14px !important;
    margin-bottom: 20px !important;
    text-align: center !important;
    padding: 0 20px !important;
    color: #fff !important;
  }
  
  .image-content {
    margin-top: 25px !important;
    width: 100% !important;
    display: flex !important;
    justify-content: center !important;
  }
  
  .hero-png {
    max-width: 90% !important;
    max-height: 350px !important;
    margin: 0 auto !important;
    object-fit: contain !important;
  }
  
  /* Carousel Controls */
  .carousel-control-left,
  .carousel-control-right {
    width: 40px !important;
    height: 40px !important;
  }
  
  .carousel-control-left i,
  .carousel-control-right i {
    font-size: 1.5rem !important;
  }
  
  .carousel-dots {
    bottom: 20px !important;
    gap: 8px !important;
  }
  
  .dot {
    width: 8px !important;
    height: 8px !important;
    margin: 0 4px !important;
  }
  
  .dot.active {
    width: 24px !important;
  }
  
  /* Fixed Icons */
  .fixed-icons-left {
    left: 10px !important;
    gap: 10px !important;
  }
  
  .icon-btn {
    width: 45px !important;
    height: 45px !important;
    font-size: 1.2rem !important;
  }
  
  /* Sports Icons Section */
  .sports-icons-section {
    padding: 20px 0 !important;
  }
  
  .icons-track {
    gap: 40px !important;
  }
  
  .icon-circle {
    width: 60px !important;
    height: 60px !important;
  }
  
  .icon-name {
    font-size: 10px !important;
    max-width: 70px !important;
  }
  
  /* Deals Section */
  .deals-section {
    padding: 40px 0 !important;
  }
  
  .deals-section h2 {
    font-size: 2rem !important;
    margin-bottom: 15px !important;
  }
  
  .deals-section h4 {
    font-size: 1.2rem !important;
  }
  
  .deals-section .lead {
    font-size: 14px !important;
  }
  
  .deal-image-box {
    margin: 8px !important;
  }
  
  .deal-image {
    height: 140px !important;
  }
  
  /* Featured Products */
  .featured-products {
    padding: 40px 0 !important;
  }
  
  .featured-header {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 20px !important;
    margin-bottom: 30px !important;
  }
  
  .section-title {
    font-size: 1.8rem !important;
  }
  
  .tabs {
    width: 100%;
    flex-wrap: wrap !important;
  }
  
  .tab-btn {
    font-size: 12px !important;
    padding: 8px 15px !important;
    flex: 1;
    min-width: 80px;
  }
  
  .carousel-wrapper {
    padding: 0 50px !important;
  }
  
  .product-card {
    min-width: 100% !important;
    padding: 0 10px !important;
  }
  
  .product-img {
    height: 180px !important;
  }
  
  .product-title {
    font-size: 13px !important;
  }
  
  .product-price {
    font-size: 1rem !important;
  }
  
  .carousel-btn {
    width: 35px !important;
    height: 35px !important;
  }
  
  .carousel-btn i {
    font-size: 16px !important;
  }
  
  .carousel-btn-prev {
    left: 5px !important;
  }
  
  .carousel-btn-next {
    right: 5px !important;
  }
  
  /* Benefits Section */
  .benefits-section {
    padding: 40px 0 !important;
  }
  
  .benefits-section .d-flex {
    flex-direction: column !important;
    gap: 15px !important;
  }
  
  .benefit-card {
    max-width: 100% !important;
    padding: 25px 15px !important;
  }
  
  .benefit-card .icon {
    margin-bottom: 15px !important;
  }
  
  .benefit-card .icon i {
    font-size: 2.5rem !important;
  }
  
  .benefit-card h4 {
    font-size: 1.1rem !important;
    margin-bottom: 10px !important;
  }
  
  .benefit-card p {
    font-size: 0.85rem !important;
  }
  
  /* Latest Videos */
  .latest-videos-section {
    padding: 40px 0 !important;
  }
  
  .videos-header {
    margin-bottom: 30px !important;
  }
  
  .header-icon {
    font-size: 1.8rem !important;
  }
  
  .videos-title {
    font-size: 1.8rem !important;
    letter-spacing: 1px !important;
  }
  
  .videos-carousel-wrapper {
    padding: 0 50px !important;
  }
  
  .video-card {
    min-width: 100% !important;
    padding: 0 8px !important;
  }
  
  .video-thumbnail {
    height: 250px !important;
  }
  
  .play-button {
    width: 50px !important;
    height: 50px !important;
    font-size: 1.5rem !important;
  }
  
  .video-arrow {
    width: 45px !important;
    height: 45px !important;
    font-size: 1.4rem !important;
  }
  
  .video-title-overlay {
    font-size: 13px !important;
    padding: 15px 10px 10px !important;
  }
  
  /* Video Modal */
  .video-modal-content {
    width: 95% !important;
    height: 50vh !important;
  }
  
  .close-video-btn {
    width: 40px !important;
    height: 40px !important;
    top: 10px !important;
    right: 10px !important;
    font-size: 1.2rem !important;
  }
  
  /* Apparel Section */
  .apparel-products {
    padding: 40px 0 !important;
  }
  
  .apparel-products .section-title {
    font-size: 1.8rem !important;
    margin-bottom: 30px !important;
  }
  
  /* Testimonials */
  .testimonials-section {
    padding: 40px 0 !important;
  }
  
  .testimonial-label {
    font-size: 0.85rem !important;
  }
  
  .testimonial-item {
    flex: 0 0 100% !important;
    padding: 0 10px !important;
  }
  
  .testimonial-card {
    padding: 30px 20px !important;
    min-height: auto !important;
  }
  
  .quote-icon {
    font-size: 4rem !important;
    top: -10px !important;
    left: 15px !important;
  }
  
  .stars-rating {
    font-size: 0.9rem !important;
    letter-spacing: 3px !important;
  }
  
  .testimonial-text {
    font-size: 0.95rem !important;
    margin-bottom: 20px !important;
  }
  
  .author-image {
    width: 50px !important;
    height: 50px !important;
  }
  
  .author-name {
    font-size: 1rem !important;
  }
  
  .author-position {
    font-size: 0.8rem !important;
  }
  
  .carousel-arrow {
    width: 40px !important;
    height: 40px !important;
    font-size: 1.3rem !important;
  }
  
  /* Blog Section */
  .recent-blog-section {
    padding: 40px 0 !important;
  }
  
  .section-label {
    font-size: 0.85rem !important;
  }
  
  .blog-card {
    flex-direction: column !important;
    margin-bottom: 20px !important;
  }
  
  .blog-image {
    flex: 0 0 100% !important;
    height: 220px !important;
  }
  
  .blog-content {
    padding: 20px 15px !important;
  }
  
  .blog-meta {
    font-size: 0.8rem !important;
    gap: 15px !important;
  }
  
  .blog-title {
    font-size: 1.2rem !important;
  }
  
  .blog-excerpt {
    font-size: 0.9rem !important;
  }
  
  .btn-view-all {
    padding: 12px 30px !important;
    font-size: 14px !important;
  }
}

/* ========== SMALL DEVICES (Mobile Landscape - 576px to 767px) ========== */
@media (min-width: 576px) and (max-width: 767px) {
  
  .hero-carousel {
    height: 100vh !important;
    min-height: 650px !important;
  }
  
  .text-and-image-wrapper {
    max-width: 90% !important;
    margin-top: 15% !important;
  }
  
  .main_title {
    font-size: 2.8rem !important;
    text-align: center !important;
    width: 100% !important;
  }
  
  .title-button-row {
    flex-direction: column !important;
    align-items: center !important;
  }
  
  .single-line-btn {
    font-size: 16px !important;
    padding: 12px 40px !important;
  }
  
  .hero-png {
    max-width: 85% !important;
    max-height: 450px !important;
  }
  
  .lead {
    font-size: 15px !important;
    text-align: center !important;
  }
  
  .icon-circle {
    width: 70px !important;
    height: 70px !important;
  }
  
  .product-card {
    min-width: 50% !important;
  }
  
  .video-card {
    min-width: 50% !important;
  }
  
  .video-thumbnail {
    height: 280px !important;
  }
  
  .testimonial-item {
    flex: 0 0 100% !important;
  }
  
  .blog-image {
    height: 250px !important;
  }
}

/* ========== MEDIUM DEVICES (Tablets - 768px to 991px) ========== */
@media (min-width: 768px) and (max-width: 991px) {
  
  .hero-carousel {
    height: 100vh !important;
    min-height: 700px !important;
  }
  
  .text-and-image-wrapper {
    max-width: 85% !important;
    margin-top: 12% !important;
  }
  
  .title-button-row {
    flex-direction: row !important;
    flex-wrap: wrap !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
  }
  
  .main_title {
    font-size: 3.2rem !important;
    width: 60% !important;
    text-align: left !important;
  }
  
  .button-wrapper {
    margin-top: 30px !important;
    width: auto !important;
  }
  
  .single-line-btn {
    font-size: 17px !important;
    padding: 12px 30px !important;
  }
  
  .hero-png {
    max-height: 550px !important;
    max-width: 100% !important;
  }
  
  .lead {
    text-align: left !important;
    padding: 0 !important;
  }
  
  .section-title {
    font-size: 2.2rem !important;
  }
  
  .product-card {
    min-width: 33.333% !important;
  }
  
  .carousel-wrapper {
    padding: 0 55px !important;
  }
  
  .benefit-card {
    max-width: 48% !important;
  }
  
  .video-card {
    min-width: 50% !important;
  }
  
  .video-thumbnail {
    height: 300px !important;
  }
  
  .testimonial-item {
    flex: 0 0 50% !important;
  }
  
  .blog-card {
    flex-direction: row !important;
  }
  
  .blog-image {
    flex: 0 0 40% !important;
    height: auto !important;
  }
}

/* ========== LARGE DEVICES (Small Desktops - 992px to 1199px) ========== */
@media (min-width: 992px) and (max-width: 1199px) {
  
  .text-and-image-wrapper {
    max-width: 65% !important;
  }
  
  .main_title {
    font-size: 3.5rem !important;
  }
  
  .product-card {
    min-width: 25% !important;
  }
  
  .video-card {
    min-width: 33.333% !important;
  }
  
  .testimonial-item {
    flex: 0 0 33.333% !important;
  }
  
  .benefit-card {
    max-width: 32% !important;
  }
}

/* ========== EXTRA LARGE DEVICES (Large Desktops - 1200px+) ========== */
@media (min-width: 1200px) {
  .container {
    max-width: 1320px !important;
  }
}

/* ========== LANDSCAPE ORIENTATION FIX ========== */
@media (orientation: landscape) and (max-height: 600px) {
  
  .hero-carousel {
    height: 100vh !important;
    min-height: auto !important;
  }
  
  .text-and-image-wrapper {
    margin-top: 3% !important;
  }
  
  .main_title {
    font-size: 2.5rem !important;
  }
  
  .hero-png {
    max-height: 500px !important;
  }
  
  .video-modal-content {
    height: 90vh !important;
  }
}

/* ========== COMMON RESPONSIVE UTILITIES ========== */

/* Container Responsive Padding */
@media (max-width: 575px) {
  .container {
    padding: 0 15px !important;
  }
}

/* Images Responsive */
img {
  max-width: 100%;
  height: auto;
}

/* Text Responsive - Prevent Overflow */
.main_title,
.section-title,
.videos-title {
  word-wrap: break-word;
  overflow-wrap: break-word;
  hyphens: auto;
}

/* Button Touch Target (min 44px for mobile) */
@media (max-width: 767px) {
  button,
  .btn,
  .icon-btn,
  .tab-btn {
    min-height: 44px;
    min-width: 44px;
  }
}

/* Hide Horizontal Overflow */
body,
html {
  overflow-x: hidden !important;
  max-width: 100vw;
}

/* Smooth Transitions */
* {
  transition: all 0.3s ease;
}

/* Grid Responsive Helper */
@media (max-width: 575px) {
  .row {
    margin-left: -8px !important;
    margin-right: -8px !important;
  }
  
  .row > * {
    padding-left: 8px !important;
    padding-right: 8px !important;
  }
}

/* ========== ACCESSIBILITY & TOUCH IMPROVEMENTS ========== */

/* Increase tap targets on mobile */
@media (max-width: 767px) {
  a,
  button {
    padding: 8px;
  }
}

/* Focus visible for keyboard navigation */
*:focus-visible {
  outline: 2px solid #000;
  outline-offset: 2px;
}

/* ========== PRINT STYLES (BONUS) ========== */
@media print {
  .fixed-icons-left,
  .carousel-control-left,
  .carousel-control-right,
  .carousel-dots,
  .carousel-btn,
  .video-arrow {
    display: none !important;
  }
}
</style>