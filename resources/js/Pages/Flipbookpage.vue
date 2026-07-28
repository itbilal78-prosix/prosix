<template>
  <nav-component />
  <breadcrumb-component />

  <main class="jacket-page">
    <!-- Sticky scroll area -->
    <section ref="scrollSection" class="jacket-scroll-section">
      <div
        class="jacket-sticky"
        :style="{
          background: currentProduct.background,
          color: currentProduct.textColor
        }"
      >
        <!-- Decorative glow -->
        <div
          class="hero-glow"
          :style="{ background: currentProduct.glow }"
        ></div>

        <!-- Navbar inside hero -->
        <header class="hero-navbar">
          <div class="brand">
            <span class="brand-icon">MM</span>
            <span>MOUNT MASTERS</span>
          </div>

          <nav class="hero-links">
            <button class="active">PUFFER JACKET</button>
            <button>ALL PRODUCTS</button>
            <button>ABOUT US</button>
            <button>CONTACT</button>
          </nav>

          <div class="hero-actions">
            <button aria-label="Cart">🛒</button>
            <button aria-label="Favourite">♡</button>
          </div>
        </header>

        <div class="hero-content">
          <!-- Left content -->
          <div class="hero-copy">
            <div class="arrow-buttons">
              <button @click="previousProduct">‹</button>
              <button @click="nextProduct">›</button>
            </div>

            <p class="product-number">
              0{{ activeIndex + 1 }} / 0{{ products.length }}
            </p>

            <h1 :key="`title-${activeIndex}`" class="hero-title">
              Stand out
              <span>Without trying</span>
            </h1>

            <p class="hero-description">
              It’s not just about staying warm. It’s about stepping outside
              and instantly feeling confident, comfortable and completely
              yourself. Designed to elevate even the simplest outfit.
            </p>

            <button class="look-button">
              Get the look
              <span>›</span>
            </button>

            <div class="social-links">
              <a href="#">◎</a>
              <a href="#">f</a>
              <a href="#">p</a>
              <a href="#">in</a>
            </div>
          </div>

          <!-- Main jacket -->
          <div class="product-visual">
            <div class="floating-label">
              {{ currentProduct.name }}
            </div>

            <transition name="jacket-change" mode="out-in">
              <div
                :key="currentProduct.id"
                class="main-jacket-wrapper"
              >
                <img
                  :src="currentProduct.image"
                  :alt="currentProduct.name"
                  class="main-jacket"
                  :class="currentProduct.filterClass"
                />

                <div class="product-shadow"></div>
              </div>
            </transition>

            <p class="confidence-text">
              Confidence,<br />
              wrapped in warmth
            </p>
          </div>

          <!-- Right content -->
          <div class="product-details">
            <div class="price-area">
              <strong>${{ currentProduct.price }}</strong>
              <del>${{ currentProduct.oldPrice }}</del>
            </div>

            <p class="size-title">Choose your size</p>

            <div class="size-buttons">
              <button
                v-for="size in sizes"
                :key="size"
                :class="{ selected: selectedSize === size }"
                @click="selectedSize = size"
              >
                {{ size }}
              </button>
            </div>

            <div class="product-thumbnails">
              <button
                v-for="(product, index) in products"
                :key="product.id"
                :class="{ active: activeIndex === index }"
                @click="setProduct(index)"
              >
                <img
                  :src="product.image"
                  :alt="product.name"
                  :class="product.filterClass"
                />
              </button>
            </div>
          </div>
        </div>

        <!-- Bottom progress -->
        <div class="scroll-progress">
          <div class="progress-track">
            <div
              class="progress-fill"
              :style="{ width: `${scrollProgress}%` }"
            ></div>
          </div>

          <span>Scroll to explore</span>
        </div>
      </div>
    </section>

    <!-- Content after scroll section -->
    <section class="after-section">
      <p>Premium Collection</p>
      <h2>Designed for every season.</h2>
      <span>
        Scroll animation complete. Your next website section can start here.
      </span>
    </section>
  </main>

  <footer-component />
</template>

<script>
export default {
  name: 'JacketScrollShowcase',

  data() {
    return {
      activeIndex: 0,
      selectedSize: 36,
      scrollProgress: 0,

      sizes: [36, 38, 40],

      products: [
        {
          id: 1,
          name: 'Midnight Black',
          price: 149,
          oldPrice: 199,
          textColor: '#ffffff',
          background:
            'radial-gradient(circle at 52% 46%, #4d2412 0%, #191413 28%, #0a0a0a 72%)',
          glow:
            'radial-gradient(circle, rgba(255,108,28,.48) 0%, rgba(255,108,28,.10) 42%, transparent 72%)',

          /*
            Testing ke liye online PNG use ho rahi hai.
            Baad mein is URL ko:
            /images/jackets/jacket-black.png
            se replace kar dena.
          */
          image: 'https://pngimg.com/d/jacket_PNG8058.png',
          filterClass: 'jacket-black'
        },
        {
          id: 2,
          name: 'Sunset Orange',
          price: 149,
          oldPrice: 199,
          textColor: '#ffffff',
          background:
            'radial-gradient(circle at 52% 45%, #f46516 0%, #9a3511 35%, #35170d 100%)',
          glow:
            'radial-gradient(circle, rgba(255,151,67,.75) 0%, rgba(255,91,0,.22) 45%, transparent 72%)',
          image: 'https://pngimg.com/d/jacket_PNG8058.png',
          filterClass: 'jacket-orange'
        },
        {
          id: 3,
          name: 'Arctic White',
          price: 149,
          oldPrice: 199,
          textColor: '#ffffff',
          background:
            'radial-gradient(circle at 52% 45%, #eef1f3 0%, #aeb4b8 40%, #777e83 100%)',
          glow:
            'radial-gradient(circle, rgba(255,255,255,.92) 0%, rgba(255,255,255,.25) 45%, transparent 72%)',
          image: 'https://pngimg.com/d/jacket_PNG8058.png',
          filterClass: 'jacket-white'
        }
      ]
    }
  },

  computed: {
    currentProduct() {
      return this.products[this.activeIndex]
    }
  },

  mounted() {
    window.addEventListener('scroll', this.handleScroll, { passive: true })
    this.handleScroll()
  },

  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll)
  },

  methods: {
    handleScroll() {
      const section = this.$refs.scrollSection

      if (!section) return

      const rect = section.getBoundingClientRect()
      const scrollableDistance = section.offsetHeight - window.innerHeight

      let passedDistance = -rect.top
      passedDistance = Math.max(0, Math.min(passedDistance, scrollableDistance))

      const progress =
        scrollableDistance > 0
          ? passedDistance / scrollableDistance
          : 0

      this.scrollProgress = Math.round(progress * 100)

      const newIndex = Math.min(
        this.products.length - 1,
        Math.floor(progress * this.products.length)
      )

      if (newIndex !== this.activeIndex) {
        this.activeIndex = newIndex
        this.selectedSize = 36
      }
    },

    setProduct(index) {
      this.activeIndex = index
      this.selectedSize = 36

      const section = this.$refs.scrollSection

      if (!section) return

      const sectionTop = window.scrollY + section.getBoundingClientRect().top
      const availableScroll = section.offsetHeight - window.innerHeight

      const targetProgress =
        index / Math.max(this.products.length - 1, 1)

      window.scrollTo({
        top: sectionTop + availableScroll * targetProgress,
        behavior: 'smooth'
      })
    },

    previousProduct() {
      const index =
        this.activeIndex === 0
          ? this.products.length - 1
          : this.activeIndex - 1

      this.setProduct(index)
    },

    nextProduct() {
      const index =
        this.activeIndex === this.products.length - 1
          ? 0
          : this.activeIndex + 1

      this.setProduct(index)
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800&display=swap');

* {
  box-sizing: border-box;
}

.jacket-page {
  width: 100%;
  overflow: clip;
  background: #f1a51f;
  font-family: 'DM Sans', sans-serif;
}

/*
  Is section ki height scrolling duration control karti hai.
  360vh ka matlab 3 product stages ke liye extra scrolling.
*/
.jacket-scroll-section {
  position: relative;
  height: 360vh;
  padding: 30px;
  background:
    linear-gradient(
      180deg,
      #f9a514 0%,
      #cf7810 100%
    );
}

.jacket-sticky {
  position: sticky;
  top: 20px;
  width: 100%;
  height: calc(100vh - 40px);
  min-height: 650px;
  border-radius: 28px;
  overflow: hidden;
  transition:
    background 0.8s cubic-bezier(.22, 1, .36, 1),
    color 0.4s ease;
  box-shadow:
    0 35px 80px rgba(0, 0, 0, 0.32),
    inset 0 1px 0 rgba(255, 255, 255, 0.14);
}

.jacket-sticky::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  background:
    linear-gradient(
      90deg,
      rgba(0, 0, 0, 0.38) 0%,
      transparent 34%,
      transparent 68%,
      rgba(0, 0, 0, 0.3) 100%
    );
}

.jacket-sticky::after {
  content: '';
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  background-image:
    radial-gradient(
      rgba(255, 255, 255, 0.12) 0.7px,
      transparent 0.7px
    );
  background-size: 5px 5px;
  opacity: 0.06;
}

.hero-glow {
  position: absolute;
  width: 850px;
  height: 850px;
  left: 50%;
  top: 48%;
  transform: translate(-50%, -50%);
  border-radius: 50%;
  filter: blur(12px);
  transition: background 0.8s ease;
  pointer-events: none;
}

.hero-navbar {
  position: relative;
  z-index: 10;
  height: 100px;
  padding: 24px 40px;
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: start;
}

.brand {
  display: flex;
  align-items: center;
  gap: 11px;
  font-family: 'Montserrat', sans-serif;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 1px;
}

.brand-icon {
  width: 30px;
  height: 25px;
  display: grid;
  place-items: center;
  background: #ffffff;
  color: #111111;
  font-size: 8px;
  font-weight: 800;
}

.hero-links {
  padding: 6px;
  display: flex;
  gap: 4px;
  border-radius: 50px;
  background: rgba(0, 0, 0, 0.26);
  backdrop-filter: blur(18px);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
}

.hero-links button {
  height: 37px;
  padding: 0 20px;
  border: 0;
  border-radius: 40px;
  background: transparent;
  color: inherit;
  font-family: inherit;
  font-size: 10px;
  font-weight: 600;
  cursor: pointer;
  opacity: 0.78;
  transition: 0.25s ease;
}

.hero-links button:hover {
  opacity: 1;
}

.hero-links button.active {
  background: #ffffff;
  color: #111111;
  opacity: 1;
}

.hero-actions {
  justify-self: end;
  display: flex;
  gap: 14px;
}

.hero-actions button {
  padding: 5px;
  border: 0;
  background: transparent;
  color: inherit;
  font-size: 22px;
  cursor: pointer;
}

.hero-content {
  position: relative;
  z-index: 5;
  height: calc(100% - 155px);
  padding: 10px 54px 25px;
  display: grid;
  grid-template-columns: minmax(250px, 1fr) minmax(380px, 1.35fr) minmax(220px, .75fr);
  align-items: center;
  gap: 28px;
}

.hero-copy {
  align-self: center;
  max-width: 450px;
}

.arrow-buttons {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
}

.arrow-buttons button {
  width: 31px;
  height: 31px;
  border: 1px solid rgba(255, 255, 255, 0.16);
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.22);
  color: #ffffff;
  font-size: 22px;
  line-height: 1;
  cursor: pointer;
  backdrop-filter: blur(8px);
  transition: 0.25s ease;
}

.arrow-buttons button:hover {
  transform: scale(1.08);
  background: #ffffff;
  color: #111111;
}

.product-number {
  margin: 0 0 8px;
  font-size: 10px;
  letter-spacing: 3px;
  opacity: 0.55;
}

.hero-title {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
  font-size: clamp(42px, 4.2vw, 72px);
  font-weight: 700;
  line-height: 0.95;
  letter-spacing: -3px;
  animation: titleReveal 0.75s cubic-bezier(.22, 1, .36, 1);
}

.hero-title span {
  display: block;
}

.hero-description {
  max-width: 415px;
  margin: 22px 0 25px;
  font-size: 13px;
  line-height: 1.65;
  opacity: 0.78;
}

.look-button {
  height: 45px;
  padding: 0 18px 0 22px;
  display: inline-flex;
  align-items: center;
  gap: 28px;
  border: 0;
  border-radius: 50px;
  background: #ffffff;
  color: #111111;
  font-family: inherit;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.22);
  transition: 0.25s ease;
}

.look-button:hover {
  transform: translateY(-3px);
}

.look-button span {
  font-size: 22px;
  line-height: 1;
}

.social-links {
  margin-top: 48px;
  display: flex;
  gap: 23px;
}

.social-links a {
  color: inherit;
  text-decoration: none;
  font-size: 12px;
  font-weight: 700;
  opacity: 0.7;
}

.product-visual {
  position: relative;
  height: 100%;
  min-height: 460px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.floating-label {
  position: absolute;
  top: 7%;
  padding: 8px 14px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 40px;
  background: rgba(0, 0, 0, 0.15);
  font-size: 9px;
  letter-spacing: 2px;
  text-transform: uppercase;
  opacity: 0.7;
  backdrop-filter: blur(10px);
}

.main-jacket-wrapper {
  position: relative;
  width: min(590px, 42vw);
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-jacket {
  position: relative;
  z-index: 2;
  display: block;
  width: 100%;
  max-height: 510px;
  object-fit: contain;
  user-select: none;
  pointer-events: none;
  animation: jacketFloat 4s ease-in-out infinite;
  filter:
    drop-shadow(0 35px 30px rgba(0, 0, 0, 0.45));
}

.product-shadow {
  position: absolute;
  z-index: 1;
  left: 50%;
  bottom: 3%;
  width: 52%;
  height: 35px;
  transform: translateX(-50%);
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.38);
  filter: blur(18px);
}

.confidence-text {
  position: absolute;
  bottom: 1%;
  margin: 0;
  text-align: center;
  font-size: 11px;
  line-height: 1.15;
  opacity: 0.65;
}

.product-details {
  justify-self: end;
  align-self: center;
  min-width: 210px;
}

.price-area {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 28px;
}

.price-area strong {
  font-family: 'Montserrat', sans-serif;
  font-size: clamp(34px, 3vw, 53px);
  line-height: 1;
}

.price-area del {
  margin-top: 7px;
  font-family: 'Montserrat', sans-serif;
  font-size: 26px;
  opacity: 0.52;
}

.size-title {
  margin: 0 0 13px;
  font-size: 11px;
  opacity: 0.72;
}

.size-buttons {
  display: flex;
  gap: 9px;
}

.size-buttons button {
  width: 42px;
  height: 42px;
  border: 0;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.22);
  color: #ffffff;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  transition: 0.25s ease;
  backdrop-filter: blur(8px);
}

.size-buttons button:hover {
  transform: translateY(-3px);
}

.size-buttons button.selected {
  background: #ffffff;
  color: #111111;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
}

.product-thumbnails {
  margin-top: 65px;
  display: flex;
  align-items: flex-end;
  gap: 12px;
}

.product-thumbnails button {
  width: 60px;
  height: 65px;
  padding: 4px;
  border: 1px solid transparent;
  border-radius: 15px;
  background: rgba(0, 0, 0, 0.13);
  opacity: 0.48;
  cursor: pointer;
  transition: 0.3s ease;
}

.product-thumbnails button:hover,
.product-thumbnails button.active {
  opacity: 1;
  transform: translateY(-7px);
  border-color: rgba(255, 255, 255, 0.4);
}

.product-thumbnails img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.scroll-progress {
  position: absolute;
  z-index: 10;
  left: 50%;
  bottom: 22px;
  width: 220px;
  transform: translateX(-50%);
  text-align: center;
}

.progress-track {
  width: 100%;
  height: 2px;
  overflow: hidden;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.25);
}

.progress-fill {
  height: 100%;
  border-radius: inherit;
  background: #ffffff;
  transition: width 0.12s linear;
}

.scroll-progress span {
  display: block;
  margin-top: 8px;
  font-size: 8px;
  letter-spacing: 2px;
  text-transform: uppercase;
  opacity: 0.5;
}

/* Jacket recoloring */
.jacket-black {
  filter:
    grayscale(1)
    brightness(0.45)
    contrast(1.45)
    drop-shadow(0 35px 30px rgba(0, 0, 0, 0.5));
}

.jacket-orange {
  filter:
    sepia(1)
    saturate(7)
    hue-rotate(335deg)
    brightness(1.13)
    contrast(1.05)
    drop-shadow(0 35px 30px rgba(70, 15, 0, 0.45));
}

.jacket-white {
  filter:
    grayscale(1)
    brightness(2.1)
    contrast(0.7)
    drop-shadow(0 35px 30px rgba(0, 0, 0, 0.26));
}

/* Vue transition */
.jacket-change-enter-active,
.jacket-change-leave-active {
  transition:
    opacity 0.45s ease,
    transform 0.55s cubic-bezier(.22, 1, .36, 1),
    filter 0.45s ease;
}

.jacket-change-enter-from {
  opacity: 0;
  transform: translateY(90px) scale(0.76) rotate(7deg);
}

.jacket-change-leave-to {
  opacity: 0;
  transform: translateY(-80px) scale(0.78) rotate(-7deg);
}

.after-section {
  min-height: 75vh;
  padding: 120px 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #ffffff;
  color: #111111;
  text-align: center;
}

.after-section p {
  margin: 0 0 12px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 4px;
  text-transform: uppercase;
}

.after-section h2 {
  max-width: 850px;
  margin: 0;
  font-family: 'Montserrat', sans-serif;
  font-size: clamp(42px, 7vw, 94px);
  line-height: 0.95;
  letter-spacing: -5px;
}

.after-section span {
  margin-top: 28px;
  color: #777777;
}

@keyframes jacketFloat {
  0%,
  100% {
    transform: translateY(0) rotate(-1deg);
  }

  50% {
    transform: translateY(-18px) rotate(1deg);
  }
}

@keyframes titleReveal {
  from {
    opacity: 0;
    transform: translateY(40px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Tablet */
@media (max-width: 1050px) {
  .hero-navbar {
    grid-template-columns: 1fr auto;
  }

  .hero-links {
    display: none;
  }

  .hero-content {
    grid-template-columns: 1fr 1.2fr;
  }

  .product-details {
    position: absolute;
    right: 40px;
    top: 28%;
  }

  .main-jacket-wrapper {
    width: min(500px, 48vw);
  }
}

/* Mobile */
@media (max-width: 700px) {
  .jacket-scroll-section {
    height: 330vh;
    padding: 10px;
  }

  .jacket-sticky {
    top: 8px;
    height: calc(100vh - 16px);
    min-height: 690px;
    border-radius: 20px;
  }

  .hero-navbar {
    height: 70px;
    padding: 18px 18px;
  }

  .brand {
    font-size: 10px;
  }

  .brand-icon {
    width: 25px;
    height: 22px;
  }

  .hero-actions button {
    font-size: 18px;
  }

  .hero-content {
    height: calc(100% - 100px);
    padding: 0 22px 25px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    gap: 0;
  }

  .hero-copy {
    position: relative;
    z-index: 4;
    width: 100%;
    padding-top: 5px;
  }

  .arrow-buttons {
    margin-bottom: 8px;
  }

  .product-number {
    display: none;
  }

  .hero-title {
    font-size: 39px;
    letter-spacing: -2px;
  }

  .hero-description {
    max-width: 295px;
    margin: 12px 0 14px;
    font-size: 10px;
    line-height: 1.5;
  }

  .look-button {
    height: 37px;
    padding: 0 14px;
    gap: 18px;
    font-size: 10px;
  }

  .social-links {
    display: none;
  }

  .product-visual {
    position: absolute;
    left: 50%;
    bottom: 70px;
    width: 100%;
    height: 48%;
    min-height: unset;
    transform: translateX(-50%);
  }

  .main-jacket-wrapper {
    width: 78vw;
    max-width: 390px;
  }

  .main-jacket {
    max-height: 320px;
  }

  .floating-label,
  .confidence-text {
    display: none;
  }

  .product-details {
    position: absolute;
    z-index: 5;
    top: 48%;
    right: 18px;
    min-width: auto;
    text-align: right;
  }

  .price-area {
    align-items: flex-end;
    margin-bottom: 12px;
  }

  .price-area strong {
    font-size: 26px;
  }

  .price-area del {
    font-size: 17px;
  }

  .size-title {
    font-size: 8px;
  }

  .size-buttons {
    justify-content: flex-end;
  }

  .size-buttons button {
    width: 31px;
    height: 31px;
    font-size: 8px;
  }

  .product-thumbnails {
    display: none;
  }

  .scroll-progress {
    bottom: 15px;
    width: 150px;
  }
}
</style>
