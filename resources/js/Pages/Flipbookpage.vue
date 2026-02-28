<template>
        <nav-component />

  <div id="fb-index">

    <!-- Header -->
    <div class="fb-header mt-5">
      <h1 class="fb-heading">Flipbook Collection</h1>
      <div class="title-rule"></div>
    </div>

    <!-- Empty -->
    <div v-if="flipbooks.length === 0" class="fb-empty">
      <svg viewBox="0 0 48 48" fill="none" stroke="#c9a84c" stroke-width="1.5">
        <path d="M8 40V10a2 2 0 0 1 2-2h22l8 8v30a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2z"/>
        <path d="M30 8v8h8M16 22h16M16 28h10"/>
      </svg>
      <p>No flipbooks found</p>
    </div>

    <!-- Grid -->
    <div v-else class="fb-grid">
      <div
        v-for="book in flipbooks"
        :key="book.id"
        class="fb-card"
        @click="goToBook(book.id)"
      >
        <!-- Book visual -->
        <div class="fb-card-book">
          <!-- Binding -->
          <div class="fb-card-binding"></div>
          <!-- Preview area -->
          <div class="fb-card-pages">
            <iframe
              :src="`http://127.0.0.1:8000/storage/${book.file_path}#toolbar=0&navpanes=0&scrollbar=0`"
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
          <!-- Right edge -->
          <div class="fb-card-edge"></div>
        </div>
        <!-- Shadow -->
        <div class="fb-card-shadow"></div>
        <!-- Title -->
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
      this.$router.push(`/flipbook/${id}`)
    }
  },
  async mounted() {
    try {
      const res = await axios.get('http://127.0.0.1:8000/api/flipbooks')
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
  --gold: #c9a84c;
  --gold-light: #e8d5a3;
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
.fb-header {
  text-align: center;
  margin-bottom: 48px;
}
.fb-eyebrow {
  font-size: 10px;
  letter-spacing: 4px;
  text-transform: uppercase;
  color: var(--gold);
  margin-bottom: 6px;
}
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
  background: linear-gradient(90deg, transparent, var(--gold), transparent);
  margin: 12px auto 0;
}

/* Empty */
.fb-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  margin-top: 80px;
  color: #bbb;
  font-size: 14px;
  letter-spacing: 1px;
}
.fb-empty svg { width: 52px; height: 52px; opacity: 0.5; }

/* Grid */
.fb-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 48px 32px;
  max-width: 1100px;
  margin: 0 auto;
}

/* Card */
.fb-card {
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
  transition: transform 0.25s ease;
}
.fb-card:hover { transform: translateY(-6px); }
.fb-card:hover .fb-card-overlay { opacity: 1; }
.fb-card:hover .fb-card-shadow {
  width: 80%;
  opacity: 0.8;
  filter: blur(10px);
}

/* Book visual */
.fb-card-book {
  display: flex;
  position: relative;
  border-radius: 2px;
  box-shadow:
    0 8px 32px rgba(0,0,0,0.18),
    0 2px 8px rgba(0,0,0,0.10);
  overflow: visible;
}

/* Left binding */
.fb-card-binding {
  width: 16px;
  flex-shrink: 0;
  border-radius: 2px 0 0 2px;
  background: linear-gradient(
    to right,
    #1a1008 0%,
    #3a2a14 20%,
    #c9a84c 38%,
    #e8d5a3 50%,
    #c9a84c 62%,
    #3a2a14 80%,
    #1a1008 100%
  );
  box-shadow: inset -2px 0 5px rgba(0,0,0,0.25);
  z-index: 2;
}

/* Pages area */
.fb-card-pages {
  width: 160px;
  height: 220px;
  position: relative;
  overflow: hidden;
  background: #fff;
  border-left: none;
}
.fb-card-pages iframe {
  width: 100%;
  height: 100%;
  border: none;
  pointer-events: none;
  display: block;
  transform-origin: top left;
}

/* Hover overlay */
.fb-card-overlay {
  position: absolute;
  inset: 0;
  background: rgba(26, 20, 16, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.25s ease;
  backdrop-filter: blur(2px);
}
.fb-open-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  color: #fff;
  font-size: 11px;
  letter-spacing: 2px;
  text-transform: uppercase;
  font-weight: 500;
}
.fb-open-btn svg { width: 22px; height: 22px; }

/* Right stacked-pages edge */
.fb-card-edge {
  width: 8px;
  flex-shrink: 0;
  border-radius: 0 2px 2px 0;
  background: linear-gradient(to right, #e8e0d0, #f5f0e8, #e0d8c8);
  position: relative;
  overflow: hidden;
}
.fb-card-edge::after {
  content: '';
  position: absolute;
  inset: 0;
  background: repeating-linear-gradient(
    to bottom,
    transparent 0px, transparent 3px,
    rgba(0,0,0,0.04) 3px, rgba(0,0,0,0.04) 4px
  );
}

/* Shadow under book */
.fb-card-shadow {
  width: 70%;
  height: 14px;
  background: radial-gradient(ellipse at center, rgba(0,0,0,0.22) 0%, transparent 70%);
  filter: blur(6px);
  transition: width 0.25s, opacity 0.25s, filter 0.25s;
  opacity: 0.6;
  margin-top: 0;
}

/* Title below */
.fb-card-title {
  margin-top: 14px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px;
  font-weight: 600;
  color: var(--ink);
  text-align: center;
  line-height: 1.3;
}

@media (max-width: 600px) {
  #fb-index { padding: 32px 16px 60px; }
  .fb-grid { gap: 36px 20px; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
  .fb-card-pages { width: 130px; height: 180px; }
  .fb-card-binding { width: 12px; }
}
</style>
