<template>
  <nav-component />
  <breadcrumb-component />

  <main class="product-page">
    <section ref="showcase" class="scroll-showcase">
      <div
        class="single-banner"
        :style="{
          background: currentItem.background,
          color: currentItem.textColor
        }"
      >
        <div
          class="banner-glow"
          :style="{ background: currentItem.glow }"
        ></div>

        <header class="banner-nav">
          <div class="brand">
            <span class="brand-box">MM</span>
            <span>MOUNT MASTERS</span>
          </div>

          <div class="nav-links">
            <button class="active">PUFFER JACKET</button>
            <button>ALL PRODUCTS</button>
            <button>ABOUT US</button>
            <button>CONTACT</button>
          </div>

          <div class="nav-icons">
            <button>♡</button>
            <button>🛒</button>
          </div>
        </header>

        <div class="banner-grid">
          <div class="banner-copy">
            <div class="small-arrows">
              <button @click="goPrevious">‹</button>
              <button @click="goNext">›</button>
            </div>

            <h1>
              Stand out
              <span>Without trying</span>
            </h1>

            <p>
              It’s not just about staying warm. It’s about stepping outside
              and instantly feeling confident, comfortable and completely
              yourself.
            </p>

            <button class="cta-button">
              Get the look
              <span>›</span>
            </button>

            <div class="socials">
              <span>◎</span>
              <span>f</span>
              <span>p</span>
              <span>in</span>
            </div>
          </div>

          <div class="jacket-stage">
            <transition name="jacket-slide" mode="out-in">
              <div :key="currentItem.id" class="jacket-holder">
                <img
                  :src="currentItem.image"
                  :alt="currentItem.name"
                  class="jacket-image"
                  :class="currentItem.className"
                />

                <div class="jacket-shadow"></div>
              </div>
            </transition>

            <div class="center-caption">
              Confidence,<br />
              wrapped in warmth
            </div>
          </div>

          <div class="banner-details">
            <div class="price">
              <strong>${{ currentItem.price }}</strong>
              <del>${{ currentItem.oldPrice }}</del>
            </div>

            <p>Choose your size</p>

            <div class="sizes">
              <button
                v-for="size in sizes"
                :key="size"
                :class="{ selected: selectedSize === size }"
                @click="selectedSize = size"
              >
                {{ size }}
              </button>
            </div>

            <div class="mini-preview">
              <img
                :src="nextItem.image"
                :alt="nextItem.name"
                :class="nextItem.className"
              />
            </div>
          </div>
        </div>

        <div class="scroll-line">
          <div class="track">
            <span :style="{ width: `${progress}%` }"></span>
          </div>
          <small>Scroll to change jacket</small>
        </div>
      </div>
    </section>
  </main>

  <footer-component />
</template>

<script>
export default {
  name: 'SingleBannerJackets',

  data() {
    return {
      activeIndex: 0,
      selectedSize: 36,
      progress: 0,
      sizes: [36, 38, 40],

      items: [
        {
          id: 1,
          name: 'Black Jacket',
          price: 149,
          oldPrice: 199,
          textColor: '#ffffff',
          background:
            'radial-gradient(circle at 52% 45%, #482512 0%, #191312 30%, #090909 72%)',
          glow:
            'radial-gradient(circle, rgba(255,112,35,.48) 0%, rgba(255,112,35,.12) 42%, transparent 72%)',
          image: 'https://pngimg.com/d/jacket_PNG8058.png',
          className: 'black-jacket'
        },
        {
          id: 2,
          name: 'Orange Jacket',
          price: 149,
          oldPrice: 199,
          textColor: '#ffffff',
          background:
            'radial-gradient(circle at 52% 45%, #ee6417 0%, #9d3b12 38%, #42200f 100%)',
          glow:
            'radial-gradient(circle, rgba(255,154,77,.78) 0%, rgba(255,92,10,.22) 45%, transparent 72%)',
          image: 'https://pngimg.com/d/jacket_PNG8058.png',
          className: 'orange-jacket'
        },
        {
          id: 3,
          name: 'White Jacket',
          price: 149,
          oldPrice: 199,
          textColor: '#ffffff',
          background:
            'radial-gradient(circle at 52% 45%, #eef1f3 0%, #b6bdc2 43%, #777f84 100%)',
          glow:
            'radial-gradient(circle, rgba(255,255,255,.98) 0%, rgba(255,255,255,.24) 48%, transparent 72%)',
          image: 'https://pngimg.com/d/jacket_PNG8058.png',
          className: 'white-jacket'
        }
      ]
    }
  },

  computed: {
    currentItem() {
      return this.items[this.activeIndex]
    },

    nextItem() {
      return this.items[(this.activeIndex + 1) % this.items.length]
    }
  },

  mounted() {
    window.addEventListener('scroll', this.handleScroll, {
      passive: true
    })

    this.handleScroll()
  },

  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll)
  },

  methods: {
    handleScroll() {
      const section = this.$refs.showcase

      if (!section) return

      const rect = section.getBoundingClientRect()
      const scrollDistance = section.offsetHeight - window.innerHeight

      let travelled = -rect.top
      travelled = Math.max(0, Math.min(travelled, scrollDistance))

      const ratio = scrollDistance > 0
        ? travelled / scrollDistance
        : 0

      this.progress = Math.round(ratio * 100)

      let newIndex = 0

      if (ratio >= 0.66) {
        newIndex = 2
      } else if (ratio >= 0.33) {
        newIndex = 1
      }

      if (newIndex !== this.activeIndex) {
        this.activeIndex = newIndex
        this.selectedSize = 36
      }
    },

    moveToIndex(index) {
      const section = this.$refs.showcase

      if (!section) return

      this.activeIndex = index

      const sectionTop =
        window.scrollY + section.getBoundingClientRect().top

      const available =
        section.offsetHeight - window.innerHeight

      const ratios = [0, 0.5, 1]

      window.scrollTo({
        top: sectionTop + available * ratios[index],
        behavior: 'smooth'
      })
    },

    goPrevious() {
      const index =
        this.activeIndex === 0
          ? this.items.length - 1
          : this.activeIndex - 1

      this.moveToIndex(index)
    },

    goNext() {
      const index =
        this.activeIndex === this.items.length - 1
          ? 0
          : this.activeIndex + 1

      this.moveToIndex(index)
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Montserrat:wght@500;600;700;800&display=swap');

* {
  box-sizing: border-box;
}

.product-page {
  background: #f29d16;
  font-family: 'DM Sans', sans-serif;
}

.scroll-showcase {
  position: relative;
  height: 300vh;
  padding: 22px;
  background:
    linear-gradient(
      180deg,
      #f3a01b 0%,
      #d67910 100%
    );
}

.single-banner {
  position: sticky;
  top: 18px;
  height: calc(100vh - 36px);
  min-height: 650px;
  border-radius: 28px;
  overflow: hidden;
  box-shadow: 0 35px 80px rgba(0, 0, 0, 0.34);
  transition:
    background 0.8s cubic-bezier(.22, 1, .36, 1),
    color 0.45s ease;
}

.single-banner::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  background:
    linear-gradient(
      90deg,
      rgba(0, 0, 0, 0.38) 0%,
      transparent 36%,
      transparent 68%,
      rgba(0, 0, 0, 0.28) 100%
    );
}

.banner-glow {
  position: absolute;
  z-index: 0;
  width: 850px;
  height: 850px;
  left: 52%;
  top: 48%;
  transform: translate(-50%, -50%);
  border-radius: 50%;
  transition: background 0.8s ease;
}

.banner-nav {
  position: relative;
  z-index: 5;
  height: 95px;
  padding: 24px 38px;
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: start;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: 'Montserrat', sans-serif;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 1px;
}

.brand-box {
  width: 30px;
  height: 24px;
  display: grid;
  place-items: center;
  background: white;
  color: #111;
  font-size: 8px;
  font-weight: 800;
}

.nav-links {
  padding: 5px;
  display: flex;
  border-radius: 50px;
  background: rgba(0, 0, 0, 0.24);
  backdrop-filter: blur(18px);
}

.nav-links button {
  height: 36px;
  padding: 0 19px;
  border: none;
  border-radius: 40px;
  background: transparent;
  color: inherit;
  font-family: inherit;
  font-size: 9px;
  font-weight: 600;
  cursor: pointer;
}

.nav-links button.active {
  background: white;
  color: #111;
}

.nav-icons {
  justify-self: end;
  display: flex;
  gap: 12px;
}

.nav-icons button {
  border: none;
  background: transparent;
  color: inherit;
  font-size: 21px;
  cursor: pointer;
}

.banner-grid {
  position: relative;
  z-index: 3;
  height: calc(100% - 145px);
  padding: 5px 50px 25px;
  display: grid;
  grid-template-columns:
    minmax(260px, 1fr)
    minmax(390px, 1.35fr)
    minmax(210px, .72fr);
  align-items: center;
  gap: 28px;
}

.banner-copy {
  max-width: 440px;
}

.small-arrows {
  display: flex;
  gap: 8px;
  margin-bottom: 17px;
}

.small-arrows button {
  width: 30px;
  height: 30px;
  border: 1px solid rgba(255,255,255,.14);
  border-radius: 50%;
  background: rgba(0,0,0,.2);
  color: white;
  font-size: 22px;
  cursor: pointer;
}

.banner-copy h1 {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
  font-size: clamp(42px, 4.2vw, 72px);
  line-height: .95;
  letter-spacing: -3px;
}

.banner-copy h1 span {
  display: block;
}

.banner-copy p {
  max-width: 400px;
  margin: 21px 0 25px;
  font-size: 13px;
  line-height: 1.65;
  opacity: .78;
}

.cta-button {
  height: 44px;
  padding: 0 17px 0 21px;
  display: inline-flex;
  align-items: center;
  gap: 27px;
  border: none;
  border-radius: 50px;
  background: white;
  color: #111;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
}

.cta-button span {
  font-size: 22px;
}

.socials {
  margin-top: 48px;
  display: flex;
  gap: 22px;
  font-size: 12px;
  font-weight: 700;
  opacity: .72;
}

.jacket-stage {
  position: relative;
  height: 100%;
  min-height: 480px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.jacket-holder {
  position: relative;
  width: min(590px, 43vw);
  display: flex;
  justify-content: center;
  align-items: center;
}

.jacket-image {
  position: relative;
  z-index: 2;
  width: 100%;
  max-height: 520px;
  object-fit: contain;
  user-select: none;
  pointer-events: none;
  animation: floatJacket 4s ease-in-out infinite;
}

.jacket-shadow {
  position: absolute;
  left: 50%;
  bottom: 1%;
  width: 50%;
  height: 36px;
  transform: translateX(-50%);
  border-radius: 50%;
  background: rgba(0, 0, 0, .38);
  filter: blur(18px);
}

.center-caption {
  position: absolute;
  bottom: 1%;
  text-align: center;
  font-size: 11px;
  line-height: 1.2;
  opacity: .66;
}

.banner-details {
  justify-self: end;
  min-width: 210px;
}

.price {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 27px;
}

.price strong {
  font-family: 'Montserrat', sans-serif;
  font-size: clamp(35px, 3vw, 52px);
  line-height: 1;
}

.price del {
  margin-top: 7px;
  font-size: 25px;
  opacity: .5;
}

.banner-details p {
  margin: 0 0 13px;
  font-size: 11px;
  opacity: .72;
}

.sizes {
  display: flex;
  gap: 9px;
}

.sizes button {
  width: 42px;
  height: 42px;
  border: none;
  border-radius: 50%;
  background: rgba(0, 0, 0, .22);
  color: white;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
}

.sizes button.selected {
  background: white;
  color: #111;
}

.mini-preview {
  width: 100px;
  height: 115px;
  margin-top: 64px;
  margin-left: auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mini-preview img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  opacity: .7;
}

.scroll-line {
  position: absolute;
  z-index: 6;
  left: 50%;
  bottom: 18px;
  width: 210px;
  transform: translateX(-50%);
  text-align: center;
}

.track {
  height: 2px;
  overflow: hidden;
  background: rgba(255,255,255,.25);
}

.track span {
  display: block;
  height: 100%;
  background: white;
  transition: width .08s linear;
}

.scroll-line small {
  display: block;
  margin-top: 8px;
  font-size: 8px;
  letter-spacing: 2px;
  text-transform: uppercase;
  opacity: .52;
}

/*
  Important animation:
  Purani jacket upar nikalti hai.
  Nayi jacket neeche se aati hai.
*/
.jacket-slide-enter-active,
.jacket-slide-leave-active {
  transition:
    transform .72s cubic-bezier(.22, 1, .36, 1),
    opacity .55s ease;
}

.jacket-slide-enter-from {
  opacity: 0;
  transform: translateY(115%);
}

.jacket-slide-enter-to {
  opacity: 1;
  transform: translateY(0);
}

.jacket-slide-leave-from {
  opacity: 1;
  transform: translateY(0);
}

.jacket-slide-leave-to {
  opacity: 0;
  transform: translateY(-115%);
}

.black-jacket {
  filter:
    grayscale(1)
    brightness(.42)
    contrast(1.5)
    drop-shadow(0 35px 30px rgba(0,0,0,.5));
}

.orange-jacket {
  filter:
    sepia(1)
    saturate(7)
    hue-rotate(335deg)
    brightness(1.13)
    contrast(1.05)
    drop-shadow(0 35px 30px rgba(65,15,0,.45));
}

.white-jacket {
  filter:
    grayscale(1)
    brightness(2.1)
    contrast(.72)
    drop-shadow(0 35px 30px rgba(0,0,0,.25));
}

@keyframes floatJacket {
  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-14px);
  }
}

@media (max-width: 1000px) {
  .nav-links {
    display: none;
  }

  .banner-nav {
    grid-template-columns: 1fr auto;
  }

  .banner-grid {
    grid-template-columns: 1fr 1.25fr;
  }

  .banner-details {
    position: absolute;
    right: 35px;
    top: 28%;
  }
}

@media (max-width: 700px) {
  .scroll-showcase {
    height: 300vh;
    padding: 9px;
  }

  .single-banner {
    top: 8px;
    height: calc(100vh - 16px);
    min-height: 690px;
    border-radius: 20px;
  }

  .banner-nav {
    height: 68px;
    padding: 17px;
  }

  .brand {
    font-size: 10px;
  }

  .banner-grid {
    height: calc(100% - 95px);
    padding: 0 20px 20px;
    display: block;
  }

  .banner-copy h1 {
    font-size: 39px;
    letter-spacing: -2px;
  }

  .banner-copy p {
    max-width: 285px;
    margin: 12px 0 14px;
    font-size: 10px;
  }

  .socials {
    display: none;
  }

  .jacket-stage {
    position: absolute;
    left: 50%;
    bottom: 67px;
    width: 100%;
    height: 48%;
    min-height: 0;
    transform: translateX(-50%);
  }

  .jacket-holder {
    width: 78vw;
    max-width: 390px;
  }

  .jacket-image {
    max-height: 330px;
  }

  .center-caption {
    display: none;
  }

  .banner-details {
    position: absolute;
    z-index: 5;
    top: 47%;
    right: 17px;
    min-width: auto;
    text-align: right;
  }

  .price {
    align-items: flex-end;
    margin-bottom: 12px;
  }

  .price strong {
    font-size: 26px;
  }

  .price del {
    font-size: 17px;
  }

  .sizes {
    justify-content: flex-end;
  }

  .sizes button {
    width: 31px;
    height: 31px;
    font-size: 8px;
  }

  .mini-preview {
    display: none;
  }

  .scroll-line {
    width: 150px;
    bottom: 14px;
  }
}
</style>
