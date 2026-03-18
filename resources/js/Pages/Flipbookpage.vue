<template>
  <nav-component />
        <breadcrumb-component />

  <div id="fb-index">

    <!-- Header -->
    <div class="fb-header mt-5">
      <h1 class="fb-heading">Flipbook Collection</h1>
      <div class="title-rule"></div>
    </div>

    <!-- ✅ COMING SOON Empty State -->
    <div v-if="flipbooks.length === 0" class="fb-coming-soon">
      <div class="cs-icon">
        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="8" y="8" width="48" height="48" rx="6" stroke="#000" stroke-width="2"/>
          <path d="M32 20v24M20 32h24" stroke="#000" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
      <p class="cs-eyebrow">Stay Tuned</p>
      <h2 class="cs-title">Coming Soon</h2>
      <div class="cs-rule"></div>
      <p class="cs-sub">Our flipbook catalogue is being prepared.<br/>Check back soon for exclusive content.</p>
      <div class="cs-dots">
        <span></span><span></span><span></span>
      </div>
    </div>

    <!-- Grid (when data exists) -->
    <div v-else class="fb-grid">
      <div
        v-for="book in flipbooks"
        :key="book.id"
        class="fb-card"
        @click="goToBook(book.id)"
      >
        <div class="fb-card-book">
          <div class="fb-card-binding"></div>
          <div class="fb-card-pages">
            <iframe
              :src="`/storage/${book.file_path}#toolbar=0&navpanes=0&scrollbar=0`"
              scrolling="no"
              tabindex="-1"
            ></iframe>
            <div class="fb-card-overlay">
              <span class="fb-open-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                Open Book
              </span>
            </div>
          </div>
          <div class="fb-card-edge"></div>
        </div>
        <div class="fb-card-shadow"></div>
        <div class="fb-card-title">{{ book.title }}</div>
      </div>
    </div>

  </div>

  <footer-component />
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return { flipbooks: [] }
  },
  methods: {
    goToBook(id) {
      this.$router.push(`/catalogue/${id}`)
    }
  },
 async mounted() {
    try {
      const res = await axios.get('/api/flipbooks')
      this.flipbooks = res.data
    } catch (e) {
      console.error(e)
    }
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

:root {
  --ink: #1a1410;
  --paper: #f5f3ef;
}

#fb-index {
  min-height: 100vh;
  background: var(--paper);
  padding: 48px 32px 80px;
  font-family: 'DM Sans', sans-serif;
}

/* Header */
.fb-header { text-align: center; margin-bottom: 48px; }
.fb-heading {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(28px, 4vw, 48px);
  font-weight: 700;
  color: var(--ink);
  margin: 0;
  line-height: 1.15;
}
.title-rule {
  width: 52px; height: 2px;
  background: linear-gradient(90deg, transparent, #000, transparent);
  margin: 12px auto 0;
}

/* ── COMING SOON ── */
.fb-coming-soon {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 14px;
  min-height: 50vh;
  text-align: center;
  padding: 20px;
}

.cs-icon {
  width: 72px;
  height: 72px;
  background: #fff;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  margin-bottom: 8px;
}
.cs-icon svg { width: 36px; height: 36px; }

.cs-eyebrow {
  font-size: 11px;
  letter-spacing: 4px;
  text-transform: uppercase;
  color: #555;
  margin: 0;
  font-weight: 500;
}

.cs-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(32px, 5vw, 52px);
  font-weight: 700;
  color: #000;
  margin: 0;
  line-height: 1.1;
}

.cs-rule {
  width: 48px; height: 2px;
  background: linear-gradient(90deg, transparent, #000, transparent);
  margin: 4px auto;
}

.cs-sub {
  font-size: 14px;
  color: #777;
  line-height: 1.8;
  margin: 0;
}

/* Animated dots — black */
.cs-dots {
  display: flex;
  gap: 8px;
  margin-top: 10px;
  align-items: center;
  justify-content: center;
}
.cs-dots span {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #000;
  animation: csDot 1.4s ease-in-out infinite;
}
.cs-dots span:nth-child(1) { animation-delay: 0s; }
.cs-dots span:nth-child(2) { animation-delay: 0.2s; }
.cs-dots span:nth-child(3) { animation-delay: 0.4s; }
@keyframes csDot {
  0%, 80%, 100% { transform: scale(0.6); opacity: 0.25; }
  40%            { transform: scale(1.2); opacity: 1; }
}

/* ── Grid & Cards ── */
.fb-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 48px 32px;
  max-width: 1100px;
  margin: 0 auto;
}
.fb-card {
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: transform 0.25s ease;
}
.fb-card:hover { transform: translateY(-6px); }
.fb-card:hover .fb-card-overlay { opacity: 1; }
.fb-card:hover .fb-card-shadow { width: 80%; opacity: 0.8; filter: blur(10px); }
.fb-card-book {
  display: flex;
  border-radius: 2px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.18), 0 2px 8px rgba(0,0,0,0.10);
}
.fb-card-binding {
  width: 16px;
  flex-shrink: 0;
  border-radius: 2px 0 0 2px;
  background: linear-gradient(to right, #1a1008 0%, #3a2a14 20%, #c9a84c 38%, #e8d5a3 50%, #c9a84c 62%, #3a2a14 80%, #1a1008 100%);
  box-shadow: inset -2px 0 5px rgba(0,0,0,0.25);
}
.fb-card-pages {
  width: 160px; height: 220px;
  position: relative; overflow: hidden; background: #fff;
}
.fb-card-pages iframe {
  width: 100%; height: 100%; border: none; pointer-events: none; display: block;
}
.fb-card-overlay {
  position: absolute; inset: 0;
  background: rgba(26,20,16,0.55);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; transition: opacity 0.25s ease;
  backdrop-filter: blur(2px);
}
.fb-open-btn {
  display: flex; flex-direction: column; align-items: center;
  gap: 6px; color: #fff; font-size: 11px;
  letter-spacing: 2px; text-transform: uppercase; font-weight: 500;
}
.fb-open-btn svg { width: 22px; height: 22px; }
.fb-card-edge {
  width: 8px; flex-shrink: 0; border-radius: 0 2px 2px 0;
  background: linear-gradient(to right, #e8e0d0, #f5f0e8, #e0d8c8);
  position: relative; overflow: hidden;
}
.fb-card-edge::after {
  content: ''; position: absolute; inset: 0;
  background: repeating-linear-gradient(to bottom, transparent 0px, transparent 3px, rgba(0,0,0,0.04) 3px, rgba(0,0,0,0.04) 4px);
}
.fb-card-shadow {
  width: 70%; height: 14px;
  background: radial-gradient(ellipse at center, rgba(0,0,0,0.22) 0%, transparent 70%);
  filter: blur(6px); opacity: 0.6;
  transition: width 0.25s, opacity 0.25s, filter 0.25s;
}
.fb-card-title {
  margin-top: 14px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px; font-weight: 600;
  color: var(--ink); text-align: center; line-height: 1.3;
}

@media (max-width: 600px) {
  #fb-index { padding: 32px 16px 60px; }
  .fb-grid { gap: 36px 20px; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
  .fb-card-pages { width: 130px; height: 180px; }
  .fb-card-binding { width: 12px; }
}
</style>
