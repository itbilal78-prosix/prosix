<template>
  <nav-component />
  <breadcrumb-component />

  <div id="fb-index">

    <!-- Header -->
    <div class="fb-header mt-5">
      <h1 class="fb-heading">Catalogues</h1>
    </div>

    <!-- Coming Soon Empty State -->
    <div v-if="flipbooks.length === 0" class="fb-coming-soon">

      <h2 class="cs-title">Coming Soon</h2>
      <p class="cs-sub">Our  catalogue is being prepared.<br/>Check back soon for exclusive content.</p>
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
    <strong style="font-size:22px;">{{ book.page_count }}</strong>
    Open Book
  </span>
</div>
          </div>
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
  background: white;
  padding: 48px 32px 80px;
  font-family: 'DM Sans', sans-serif;
}

.fb-header { text-align: center; margin-bottom: 48px; }
.fb-heading {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(28px, 4vw, 48px);
  font-weight: 700;
  color: var(--ink);
  margin: 0;
  line-height: 1.15;
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


.cs-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(32px, 5vw, 52px);
  font-weight: 700;
  color: #000;
  margin: 0;
  line-height: 1.1;
}

.cs-sub {
  font-size: 14px;
  color: #777;
  line-height: 1.8;
  margin: 0;
}
.cs-dots {
  display: flex;
  gap: 8px;
  margin-top: 10px;
  align-items: center;
  justify-content: center;
}
.cs-dots span {
  width: 7px; height: 7px;
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
  width: 12px;
  background: linear-gradient(to right, #111 0%, #333 40%, #555 50%, #333 60%, #111 100%);
  box-shadow: none;
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
