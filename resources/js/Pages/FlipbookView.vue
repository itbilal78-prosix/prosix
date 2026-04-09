<template>

  <div id="fb-show" v-if="book">

    <!-- Loader -->
    <div class="loader-overlay" :class="{ hidden: !loading }">
      <div class="loader-book"></div>
      <div class="loader-text">Loading pages…</div>
      <div class="loader-progress">
        <div class="loader-bar" :style="{ width: loadPct + '%' }"></div>
      </div>
    </div>

    <!-- FAB buttons -->
    <div class="fab-row">
      <button class="fab-btn" :class="{ 'sound-on': soundOn }" @click="toggleSound" :title="soundOn ? 'Sound On' : 'Enable Sound'">
        <svg v-if="!soundOn" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
          <line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/>
          <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
          <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
        </svg>
      </button>

      <button class="fab-btn" :class="{ 'fs-on': isFullscreen }" @click="toggleFS" :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'">
        <svg v-if="!isFullscreen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/>
          <line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/>
        </svg>
        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="4 14 10 14 10 20"/><polyline points="20 10 14 10 14 4"/>
          <line x1="10" y1="14" x2="3" y2="21"/><line x1="21" y1="3" x2="14" y2="10"/>
        </svg>
      </button>
    </div>

    <!-- Header -->
    <div class="book-header">
      <div class="book-label">Now Reading</div>
      <h1 class="book-title">{{ book.title }}</h1>
    </div>

    <!-- Book Stage -->
    <div class="book-stage">

        <!-- Top hard cover -->

        <!-- Body -->
        <div class="book-body">
          <!-- Left gold binding -->

          <!-- PageFlip mounts here -->
          <div ref="flipbookContainer" class="flipbook-wrap"></div>

          <!-- Right stacked pages edge -->

          <!-- Center spine highlight -->
          <div class="spine-overlay"></div>

          <!-- Inner page shadows -->
          <div class="page-edge-left"></div>
          <div class="page-edge-right"></div>
        </div>

        <!-- Bottom hard cover -->

    </div>

    <!-- Table shadow -->
    <div class="book-table-shadow"></div>

    <!-- Controls -->
    <div class="book-controls">
      <button class="ctrl-btn" @click="flipPrev">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
      </button>
      <div class="ctrl-divider"></div>
      <div class="page-indicator">
        <span>{{ currentPage }}</span> / <span>{{ totalPages }}</span>
      </div>
      <div class="ctrl-divider"></div>
      <button class="ctrl-btn" @click="flipNext">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </button>
    </div>



  </div>

  <!-- Show if book not loaded -->
  <div v-else-if="loadFailed" id="fb-error">
    <p>Failed to load book. Please try again.</p>
  </div>
</template>

<script>
import axios from 'axios'
import { PageFlip } from 'page-flip'

export default {
  data() {
    return {
      book: null,
      pageFlip: null,
      loading: true,
      loadFailed: false,
      loadPct: 0,
      currentPage: 1,
      totalPages: 0,
      soundOn: false,
      isFullscreen: false,
      audioCtx: null,
      bookW: 420,
      bookH: 600,
    }
  },

  async mounted() {
    // Load PDF.js from CDN dynamically — no import errors
    await this.loadPdfJs()

    this.calcSize()

    const id = this.$route.params.id
    try {
      const res = await axios.get(`/api/flipbooks/${id}`)
      this.book = res.data
      await this.$nextTick()
      await this.initFlipbook()
    } catch (e) {
      console.error('API Error:', e)
      this.loadFailed = true
      this.loading = false
    }

    window.addEventListener('keydown', this.onKey)
    document.addEventListener('fullscreenchange', this.syncFS)
    document.addEventListener('webkitfullscreenchange', this.syncFS)
  },

  beforeUnmount() {
    window.removeEventListener('keydown', this.onKey)
    document.removeEventListener('fullscreenchange', this.syncFS)
    document.removeEventListener('webkitfullscreenchange', this.syncFS)
    if (this.pageFlip) this.pageFlip.destroy()
  },

  methods: {

    // Load PDF.js from CDN — avoids all import/legacy errors
    loadPdfJs() {
      return new Promise((resolve) => {
        if (window['pdfjs-dist/build/pdf']) { resolve(); return }

        const script = document.createElement('script')
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js'
        script.onload = () => {
          window['pdfjs-dist/build/pdf'].GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js'
          resolve()
        }
        document.head.appendChild(script)
      })
    },

    calcSize() {
      const vw = window.innerWidth
      if (vw < 480)      { this.bookW = Math.min(vw - 32, 260); this.bookH = 380 }
      else if (vw < 800) { this.bookW = 310; this.bookH = 460 }
      else               { this.bookW = 420; this.bookH = 600 }
    },

    async initFlipbook() {
      const container = this.$refs.flipbookContainer
      if (!container) return

      // Must set explicit pixel size BEFORE PageFlip init
      container.style.width   = this.bookW + 'px'
      container.style.height  = this.bookH + 'px'
      container.style.margin = '0 auto'
      container.style.display = 'block'
      container.style.flexShrink = '0'

      this.pageFlip = new PageFlip(container, {
        width: this.bookW,
        height: this.bookH,
        size: 'fixed',
        showCover: true,
        mobileScrollSupport: false,
        drawShadow: true,
        flippingTime: 680,
usePortrait: false,
        startPage: 0,
        autoSize: false,
      })

      this.pageFlip.on('flip', () => {
        this.playFlip()
        this.currentPage = this.pageFlip.getCurrentPageIndex() + 1
      })

      const pdfjsLib = window['pdfjs-dist/build/pdf']
      const pdfUrl = `/storage/${this.book.file_path}`

      try {
        const pdf = await pdfjsLib.getDocument(pdfUrl).promise
        this.totalPages = pdf.numPages
        const pages = []

        for (let i = 1; i <= pdf.numPages; i++) {
          const page = await pdf.getPage(i)
          const vp = page.getViewport({ scale: 1.8 })

          const canvas = document.createElement('canvas')
          const ctx = canvas.getContext('2d')
          canvas.width = vp.width
          canvas.height = vp.height
          await page.render({ canvasContext: ctx, viewport: vp }).promise

          const wrap = document.createElement('div')
          wrap.style.cssText = 'width:100%;height:100%;overflow:hidden;background:#fff;'

          const img = document.createElement('img')
          img.src = canvas.toDataURL('image/jpeg', 0.92)
          img.style.cssText = 'width:100%;height:100%;object-fit:contain;display:block;'

          wrap.appendChild(img)
          pages.push(wrap)

          this.loadPct = Math.round((i / pdf.numPages) * 100)
        }

        this.pageFlip.loadFromHTML(pages)
        this.pageFlip.turnToPage(0)
        setTimeout(() => { this.loading = false }, 400)

      } catch (e) {
        console.error('PDF Error:', e)
        this.loading = false
      }
    },

    flipPrev() { this.pageFlip?.flipPrev() },
    flipNext() { this.pageFlip?.flipNext() },

    onKey(e) {
      if (e.key === 'ArrowRight') this.flipNext()
      if (e.key === 'ArrowLeft')  this.flipPrev()
    },

    /* ── Sound ── */
    getACtx() {
      if (!this.audioCtx)
        this.audioCtx = new (window.AudioContext || window.webkitAudioContext)()
      return this.audioCtx
    },
    toggleSound() {
      this.soundOn = !this.soundOn
      this.getACtx().resume()
    },
    playFlip() {
      if (!this.soundOn) return
      try {
        const ctx = this.getACtx(), dur = 0.20, now = ctx.currentTime
        const len = Math.floor(ctx.sampleRate * dur)
        const buf = ctx.createBuffer(1, len, ctx.sampleRate)
        const d = buf.getChannelData(0)
        for (let i = 0; i < len; i++) d[i] = Math.random() * 2 - 1
        const src = ctx.createBufferSource(); src.buffer = buf
        const bpf = ctx.createBiquadFilter(); bpf.type = 'bandpass'
        bpf.frequency.value = 1800; bpf.Q.value = 1.0
        const g = ctx.createGain()
        g.gain.setValueAtTime(0, now)
        g.gain.linearRampToValueAtTime(0.45, now + 0.025)
        g.gain.exponentialRampToValueAtTime(0.001, now + dur)
        src.connect(bpf); bpf.connect(g); g.connect(ctx.destination)
        src.start(now); src.stop(now + dur + 0.01)
      } catch(e) {}
    },

    /* ── Fullscreen ── */
    toggleFS() {
      const el = document.getElementById('fb-show') || document.documentElement
      if (!this.isFullscreen) {
        ;(el.requestFullscreen || el.webkitRequestFullscreen).call(el)
      } else {
        ;(document.exitFullscreen || document.webkitExitFullscreen).call(document)
      }
    },
    syncFS() {
      this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement)
    },
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

:root {
  --gold: #c9a84c;
  --gold-light: #e8d5a3;
  --ink: #1a1410;
}

#fb-show {
  min-height: 100vh;
  background: #f5f3ef;
  display: flex;
  flex-direction: column;
  align-items: center;
  font-family: 'DM Sans', sans-serif;
  box-sizing: border-box;
  position: relative;
    overflow-x: hidden;

}
#fb-show * { box-sizing: border-box; }

#fb-error {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'DM Sans', sans-serif;
  color: #aaa;
  font-size: 14px;
}

/* ── Loader ── */
.loader-overlay {
  position: fixed; inset: 0;
  background: #ffffff;
  z-index: 9999;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 16px;
  transition: opacity .5s ease, visibility .5s ease;
}
.loader-overlay.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.loader-book { width: 44px; height: 54px; position: relative; }
.loader-book::before, .loader-book::after {
  content: ''; position: absolute;
  top: 0; bottom: 0; width: 50%; border-radius: 2px;
  animation: bookFlip 1.1s ease-in-out infinite;
}
.loader-book::before { left: 0; background: black; transform-origin: center right; }
.loader-book::after  { right: 0; background: rgb(56, 56, 56); transform-origin: center left; animation-delay: .55s; }
@keyframes bookFlip {
  0%,100% { transform: rotateY(0); opacity: 1; }
  50%      { transform: rotateY(-68deg); opacity: .35; }
}
.loader-text { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; color: #000000; }
.loader-progress { width: 140px; height: 2px; background: #ffffff; border-radius: 2px; overflow: hidden; }
.loader-bar { height: 100%; background: black; border-radius: 2px; transition: width .25s ease; }
.book-stage {
  position: relative;
  display: flex;
  justify-content: center;
  width: 100%;
  overflow: hidden;
}
/* ── FAB ── */
.fab-row {
  width: 100%; display: flex; justify-content: flex-end;
  gap: 10px; margin-bottom: 20px;
  position: sticky; top: 10px; z-index: 200; padding-right: 4px;
}
.fab-btn {
  width: 42px; height: 42px; border-radius: 50%;
  border: 1.5px solid #e0dbd0; background: #fff;
  box-shadow: 0 2px 14px rgba(0,0,0,0.09);
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: box-shadow .2s, background .2s, transform .15s, border-color .2s;
  color: #888;
}
.fab-btn:hover {
  background: #fafafa; box-shadow: 0 4px 20px rgba(0,0,0,0.13);
  transform: scale(1.08); border-color: black; color: black;
}
.fab-btn:active { transform: scale(0.95); }
.fab-btn svg { width: 18px; height: 18px; }
.fab-btn.sound-on { background: black; border-color: black; color: #fff; }
.fab-btn.fs-on    { background: black;  border-color: black;  color: #fff; }

/* ── Header ── */
.book-header { text-align: center; margin-bottom: 32px; animation: fadeDown .6s ease both; }
.book-label  { font-size: 10px; font-weight: 500; letter-spacing: 4px; text-transform: uppercase; color: black; margin-bottom: 6px; }
.book-title  { font-family: 'Cormorant Garamond', serif; font-size: clamp(22px,3.5vw,42px); font-weight: 700; color: var(--ink); line-height: 1.15; margin: 0; }

/* ── Book Stage ── */
.book-stage { position: relative; display: flex; justify-content: center; animation: fadeUp .7s .15s ease both; }



/* Hard covers */



/* Body row */
.book-body {
  position: relative;
  display: flex;
  z-index: 3;
}

/* Left gold spine/binding */


/* Flipbook container */
.flipbook-wrap {
  position: relative;
  z-index: 3;
  margin: 0 auto;
  display: block;
  flex-shrink: 0;
}

/* Right stacked-pages edge */


/* Center spine highlight */
.spine-overlay {
  position: absolute; top: 0; bottom: 0;
  left: calc(22px + 50%); /* after binding, then center of flipbook */
  transform: translateX(-50%);
  width: 18px; z-index: 20; pointer-events: none;
  background: linear-gradient(
    to right,
    rgba(0,0,0,0.12) 0%,
    rgba(255,255,255,0.55) 30%,
    rgba(255,255,255,0.70) 50%,
    rgba(255,255,255,0.55) 70%,
    rgba(0,0,0,0.12) 100%
  );
}

/* Inner page shadows */
.page-edge-left, .page-edge-right {
  position: absolute; top: 0; bottom: 0;
  width: 28px; z-index: 15; pointer-events: none;
}
.page-edge-left  { left: 22px;  background: linear-gradient(to right, rgba(0,0,0,0.07), transparent); }
.page-edge-right { right: 10px; background: linear-gradient(to left,  rgba(0,0,0,0.07), transparent); }

/* ── Table shadow ── */
.book-table-shadow {
  width: 88%; height: 22px;
  background: radial-gradient(ellipse at center, rgba(0,0,0,0.22) 0%, transparent 70%);
  filter: blur(6px); pointer-events: none; margin-top: 0;
}

/* ── Controls ── */
.book-controls {
  display: flex; align-items: center; gap: 14px;
  margin-top: 30px;
  animation: fadeUp .7s .3s ease both;
  background: #fff; border: 1px solid #e8e4dc;
  padding: 9px 20px; border-radius: 50px;
  box-shadow: 0 2px 14px rgba(0,0,0,0.06);
}
.ctrl-btn {
  background: none; border: none; cursor: pointer; color: #aaa;
  display: flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border-radius: 50%;
  transition: background .2s, color .2s, transform .15s;
}
.ctrl-btn:hover { background: rgba(172, 172, 172, 0.1); color: black; transform: scale(1.1); }
.ctrl-btn:active { transform: scale(0.95); }
.ctrl-btn svg { width: 18px; height: 18px; }
.ctrl-divider { width: 1px; height: 20px; background: #fafafa; }
.page-indicator {
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px; color: #cccbcb; min-width: 76px; text-align: center; letter-spacing: 1px;
}
.page-indicator span { color: black; font-weight: 600; }

.key-hint {
  margin-top: 16px; font-size: 10px; color: #c8c4bc;
  letter-spacing: 1.5px; text-transform: uppercase;
  animation: fadeUp .7s .5s ease both;
}
.key-hint kbd {
  background: #f0ece4; border: 1px solid #dedad2; border-radius: 4px;
  padding: 1px 6px; font-size: 9px; font-family: 'DM Sans', sans-serif; color: #999;
}

/* ── Fullscreen ── */
:fullscreen #fb-show,
:-webkit-full-screen #fb-show {
  background: #292929 !important;
  justify-content: center;
  padding: 16px 20px;
}
:fullscreen .book-header,
:-webkit-full-screen .book-header { display: none; }

@keyframes fadeDown { from{opacity:0;transform:translateY(-16px)} to{opacity:1;transform:translateY(0)} }
@keyframes fadeUp   { from{opacity:0;transform:translateY(16px)}  to{opacity:1;transform:translateY(0)} }

/* ── Mobile ── */
@media(max-width:600px) {
  .book-controls { gap: 8px; padding: 8px 14px; }
  .key-hint { display: none; }
  .fab-btn { width: 38px; height: 38px; }
  .page-edge-left { left: 14px; }
  .page-edge-right { right: 7px; }
}
</style>
