<template>
  <div>

    <!-- ══ LOADER ══ -->
    <div class="loader-overlay" :class="{ hidden: !loading }">
      <div class="loader-book"></div>
      <div class="loader-text">Loading pages…</div>
      <div class="loader-progress">
        <div class="loader-bar" :style="{ width: loadPct + '%' }"></div>
      </div>
    </div>

    <!-- ══ MAIN SECTION ══ -->
    <div id="flipbook-section" v-if="book">

      <!-- Top Bar -->
      <div class="top-bar">
        <h1 class="book-title">{{ book.title }}</h1>
        <div class="fab-row">

          <!-- Sound Button -->
          <button class="fab-btn" :class="{ 'sound-on': soundOn }" @click="toggleSound" title="Sound">
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

          <!-- Fullscreen Button -->
          <button class="fab-btn" :class="{ 'fs-on': isFullscreen }" @click="toggleFS" title="Fullscreen">
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
      </div>

      <!-- Book Area -->
      <div class="book-wrap">
        <div class="book-outer">
          <div class="book-inner">
            <div ref="flipbookEl"></div>
            <div class="inner-left"  :class="{ hidden: isSinglePage }"></div>
            <div class="inner-right" :class="{ hidden: isSinglePage }"></div>
            <div class="spine-shadow" :class="{ hidden: isSinglePage }"></div>
          </div>
        </div>
      </div>

      <!-- Bottom Controls -->
      <div class="book-controls">

        <button class="ctrl-btn" @click="goFirst" title="First Page">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/>
          </svg>
        </button>

        <button class="ctrl-btn" @click="goPrev" title="Previous">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
          </svg>
        </button>

        <div class="ctrl-divider"></div>

        <!-- Page Indicator + Jump Popup -->
        <div class="page-indicator-wrap">
          <div class="page-indicator" @click.stop="toggleJump" title="Click to jump to page">
            <span class="cur">{{ currentPage + 1 }}</span> / <span>{{ totalPages }}</span>
          </div>
          <div class="jump-popup" :class="{ open: jumpOpen }" @click.stop>
            <input
              ref="jumpInput"
              type="number"
              v-model.number="jumpVal"
              :min="1"
              :max="totalPages"
              placeholder="Pg"
              @keydown.enter="doJump"
              @keydown.esc="jumpOpen = false"
            />
            <button @click="doJump">Go</button>
          </div>
        </div>

        <div class="ctrl-divider"></div>

        <!-- Auto Play -->
        <button class="ctrl-btn" :class="{ playing: isPlaying }" @click="togglePlay" title="Auto Play">
          <svg v-if="!isPlaying" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="5 3 19 12 5 21 5 3"/>
          </svg>
          <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>
          </svg>
        </button>

        <div class="ctrl-divider"></div>

        <button class="ctrl-btn" @click="goNext" title="Next">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"/>
          </svg>
        </button>

        <button class="ctrl-btn" @click="goLast" title="Last Page">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/>
          </svg>
        </button>

        <div class="ctrl-divider"></div>

        <!-- Thumbnail Toggle -->
        <button class="ctrl-btn" @click.stop="toggleThumb" title="Page Thumbnails">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
          </svg>
        </button>

      </div>
    </div>

    <!-- Error State -->
    <div v-if="loadFailed" style="min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:sans-serif;color:#aaa;">
      <p>Failed to load book. Please try again.</p>
    </div>

    <!-- ══ THUMBNAIL STRIP ══ -->
    <div class="thumb-strip" :class="{ open: thumbOpen }" ref="thumbStrip">
      <div
        v-for="(src, i) in thumbs"
        :key="i"
        class="thumb-item"
        :class="{ active: i === currentPage }"
        @click="jumpToThumb(i)"
      >
        <img :src="src" :alt="'Page ' + (i+1)" />
        <span class="thumb-pg">{{ i + 1 }}</span>
      </div>
    </div>

    <!-- Thumb Toggle Side Button -->
    <button class="thumb-toggle" @click="toggleThumb" title="Page Thumbnails">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
      </svg>
    </button>

  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'FlipbookShow',

  data() {
    return {
      book:        null,
      loadFailed:  false,
      loading:     true,
      loadPct:     0,
      pageFlip:    null,
      currentPage: 0,
      totalPages:  0,
      thumbs:      [],
      thumbOpen:   false,
      jumpOpen:    false,
      jumpVal:     1,
      soundOn:     false,
      isFullscreen:false,
      isPlaying:   false,
      autoTimer:   null,
      audioCtx:    null,
      isMob:       window.innerWidth < 600,
    }
  },

  computed: {
    isSinglePage() {
      return this.isMob
        || this.currentPage === 0
        || this.currentPage === this.totalPages - 1
    }
  },

  async mounted() {
    // Step 1: Load PDF.js from CDN
    await this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js')
    window['pdfjs-dist/build/pdf'].GlobalWorkerOptions.workerSrc =
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js'

    // Step 2: Load page-flip from CDN
    await this.loadScript('https://unpkg.com/page-flip/dist/js/page-flip.browser.js')

    // Step 3: Fetch book data from API
    const id = this.$route.params.id
    try {
      const res  = await axios.get(`/api/flipbooks/${id}`)
      this.book  = res.data

      // Step 4: Wait for DOM (v-if="book" needs to render first)
      await this.$nextTick()

      // Step 5: Init flipbook
      await this.initFlipbook()

    } catch (e) {
      console.error('Error:', e)
      this.loadFailed = true
      this.loading    = false
    }

    // Global events
    window.addEventListener('keydown',    this.onKey)
    document.addEventListener('click',    this.onDocClick)
    document.addEventListener('fullscreenchange',       this.syncFS)
    document.addEventListener('webkitfullscreenchange', this.syncFS)
  },

  beforeUnmount() {
    window.removeEventListener('keydown',    this.onKey)
    document.removeEventListener('click',    this.onDocClick)
    document.removeEventListener('fullscreenchange',       this.syncFS)
    document.removeEventListener('webkitfullscreenchange', this.syncFS)
    this.stopAuto()
    if (this.pageFlip) this.pageFlip.destroy()
    if (this.audioCtx) { this.audioCtx.close(); this.audioCtx = null }
  },

  methods: {

    // ── Load external script ──
    loadScript(src) {
      return new Promise((resolve) => {
        if (document.querySelector(`script[src="${src}"]`)) { resolve(); return }
        const s = document.createElement('script')
        s.src = src
        s.onload = resolve
        document.head.appendChild(s)
      })
    },

    // ── Calculate book dimensions (same logic as blade) ──
    bookSize() {
      const topH  = 50
      const ctrlH = 62
      const padH  = 22
      const avH   = window.innerHeight - topH - ctrlH - padH
      const avW   = window.innerWidth  - 48
      const ratio = 1.414

      let h = Math.min(avH, 700)
      let w = Math.round(h / ratio)

      if (!this.isMob && w * 2 > avW) {
        w = Math.floor(avW / 2)
        h = Math.round(w * ratio)
      }
      if (this.isMob) {
        w = Math.min(avW, 300)
        h = Math.round(w * ratio)
      }
      return { w: Math.max(w, 180), h: Math.max(h, 260) }
    },

    // ── Init PageFlip + render PDF ──
    async initFlipbook() {
      const container = this.$refs.flipbookEl
      if (!container) return

      const { w, h } = this.bookSize()

      // St = global from page-flip browser build
      this.pageFlip = new St.PageFlip(container, {
        width:  w,
        height: h,
        size:   'fixed',
        minWidth:  180, maxWidth:  660,
        minHeight: 260, maxHeight: 920,
        showCover:           true,
        mobileScrollSupport: false,
        drawShadow:          true,
        maxShadowOpacity:    0.5,
        flippingTime:        820,
        usePortrait:         this.isMob,
        startPage:           0,
        autoSize:            false,
      })

      this.pageFlip.on('flip', () => {
        this.playFlip()
        this.currentPage = this.pageFlip.getCurrentPageIndex()
        this.$nextTick(() => this.scrollActiveThumb())
      })

      // ── Render PDF pages ──
      const pdfjsLib = window['pdfjs-dist/build/pdf']
      const pdfUrl   = `/storage/${this.book.file_path}`

      try {
        const pdf        = await pdfjsLib.getDocument(pdfUrl).promise
        this.totalPages  = pdf.numPages
        const pages      = []

        for (let i = 1; i <= pdf.numPages; i++) {
          const page = await pdf.getPage(i)

          // High-res for flipbook
          const vp     = page.getViewport({ scale: 1.8 })
          const canvas = document.createElement('canvas')
          canvas.width  = vp.width
          canvas.height = vp.height
          await page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise
          const dataUrl = canvas.toDataURL('image/jpeg', 0.92)

          const wrap = document.createElement('div')
          wrap.style.cssText = 'width:100%;height:100%;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center;'
          const img = new Image()
          img.src = dataUrl
          img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;'
          wrap.appendChild(img)
          pages.push(wrap)

          // Low-res thumbnail
          const tvp = page.getViewport({ scale: 0.25 })
          const tc  = document.createElement('canvas')
          tc.width  = tvp.width
          tc.height = tvp.height
          await page.render({ canvasContext: tc.getContext('2d'), viewport: tvp }).promise
          this.thumbs.push(tc.toDataURL('image/jpeg', 0.7))

          this.loadPct = Math.round((i / pdf.numPages) * 100)
        }

        this.pageFlip.loadFromHTML(pages)
        setTimeout(() => {
          this.loading     = false
          this.currentPage = this.pageFlip.getCurrentPageIndex()
        }, 400)

      } catch (e) {
        console.error('PDF error:', e)
        this.loading = false
      }
    },

    // ── Navigation ──
    goFirst() { this.pageFlip?.flip(0) },
    goLast()  { this.pageFlip?.flip(this.totalPages - 1) },
    goPrev()  { this.pageFlip?.flipPrev() },
    goNext()  { this.pageFlip?.flipNext() },

    jumpToThumb(i) {
      this.pageFlip?.flip(i)
      if (window.innerWidth < 900) this.thumbOpen = false
    },

    // ── Jump popup ──
    toggleJump() {
      this.jumpOpen = !this.jumpOpen
      this.jumpVal  = this.currentPage + 1
      if (this.jumpOpen) this.$nextTick(() => this.$refs.jumpInput?.focus())
    },
    doJump() {
      const pg = this.jumpVal - 1
      if (!isNaN(pg) && pg >= 0 && pg < this.totalPages) this.pageFlip?.flip(pg)
      this.jumpOpen = false
    },

    // ── Thumbnail scroll ──
    scrollActiveThumb() {
      const strip = this.$refs.thumbStrip
      if (!strip) return
      const items = strip.querySelectorAll('.thumb-item')
      if (items[this.currentPage]) {
        items[this.currentPage].scrollIntoView({ block: 'nearest', behavior: 'smooth' })
      }
    },

    toggleThumb() { this.thumbOpen = !this.thumbOpen },

    // ── Auto play ──
    stopAuto() {
      if (this.autoTimer) { clearInterval(this.autoTimer); this.autoTimer = null }
      this.isPlaying = false
    },
    startAuto() {
      this.autoTimer = setInterval(() => {
        if (this.currentPage >= this.totalPages - 1) { this.stopAuto(); return }
        this.pageFlip?.flipNext()
      }, 3000)
      this.isPlaying = true
    },
    togglePlay() { this.isPlaying ? this.stopAuto() : this.startAuto() },

    // ── Sound ──
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
        const ctx = this.getACtx(), dur = 0.18, now = ctx.currentTime
        const len = Math.floor(ctx.sampleRate * dur)
        const buf = ctx.createBuffer(1, len, ctx.sampleRate)
        const d   = buf.getChannelData(0)
        for (let i = 0; i < len; i++) d[i] = (Math.random() * 2 - 1) * Math.pow(1 - (i / len), 0.5)
        const src = ctx.createBufferSource(); src.buffer = buf
        const bpf = ctx.createBiquadFilter(); bpf.type = 'bandpass'
        bpf.frequency.value = 2200; bpf.Q.value = 1.2
        const g = ctx.createGain()
        g.gain.setValueAtTime(0, now)
        g.gain.linearRampToValueAtTime(0.38, now + 0.02)
        g.gain.exponentialRampToValueAtTime(0.001, now + dur)
        src.connect(bpf); bpf.connect(g); g.connect(ctx.destination)
        src.start(now); src.stop(now + dur + 0.01)
      } catch (e) {}
    },

    // ── Fullscreen ──
    toggleFS() {
      const el = document.getElementById('flipbook-section') || document.documentElement
      if (!this.isFullscreen) {
        ;(el.requestFullscreen || el.webkitRequestFullscreen)?.call(el)
      } else {
        ;(document.exitFullscreen || document.webkitExitFullscreen)?.call(document)
      }
    },
    syncFS() {
      this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement)
    },

    // ── Keyboard ──
    onKey(e) {
      if (e.target.tagName === 'INPUT') return
      if (e.key === 'ArrowRight') this.goNext()
      if (e.key === 'ArrowLeft')  this.goPrev()
      if (e.key === 'Home')       this.goFirst()
      if (e.key === 'End')        this.goLast()
    },

    onDocClick() { this.jumpOpen = false },
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

:root {
  --gold: #000000;
  --gold-light: #424242;
}

body { overflow: hidden !important; }

#flipbook-section {
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  font-family: 'DM Sans', sans-serif;
  position: relative;
  overflow: hidden;
  padding: 8px 16px 6px;
  background: #ddd9d3;
}
#flipbook-section * { box-sizing: border-box; }

/* ══ TOP BAR ══ */
.top-bar {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-shrink: 0;
  margin-bottom: 6px;
  z-index: 200;
}
.book-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(14px, 1.8vw, 24px);
  font-weight: 700;
  color: #1a1410;
  margin: 0;
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.fab-row { display: flex; gap: 8px; flex-shrink: 0; }
.fab-btn {
  width: 38px; height: 38px;
  border-radius: 50%;
  border: 1.5px solid #ccc;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.10);
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.2s; color: #666;
}
.fab-btn:hover { border-color: var(--gold); color: var(--gold); transform: scale(1.08); }
.fab-btn svg { width: 16px; height: 16px; pointer-events: none; }
.fab-btn.sound-on { background: var(--gold); border-color: var(--gold); color: #fff; }
.fab-btn.fs-on    { background: #1a1410; border-color: #1a1410; color: #fff; }

/* ══ BOOK WRAP ══ */
.book-wrap {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  min-height: 0;
  position: relative;
}
.book-outer {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.book-outer::before {
  content: '';
  position: absolute;
  top: 3px; left: -10px; right: -10px; bottom: -8px;
  border-radius: 1px;
  background: transparent;
  box-shadow:
    0 22px 45px rgba(0,0,0,0.30),
    -12px 16px 32px rgba(0,0,0,0.25),
    12px 16px 32px rgba(0,0,0,0.25),
    -6px -3px 18px rgba(0,0,0,0.10),
    6px -3px 18px rgba(0,0,0,0.10);
  pointer-events: none;
  z-index: 0;
}
.book-inner {
  position: relative;
  z-index: 1;
  display: block;
}
.spine-shadow {
  position: absolute;
  top: 0; left: 50%;
  transform: translateX(-50%);
  width: 48px; height: 100%;
  background: linear-gradient(to right,
    transparent 0%, rgba(0,0,0,0.08) 22%,
    rgba(0,0,0,0.22) 48%, rgba(0,0,0,0.22) 52%,
    rgba(0,0,0,0.08) 78%, transparent 100%
  );
  pointer-events: none; z-index: 10;
  transition: opacity 0.3s;
}
.spine-shadow.hidden { opacity: 0; }
.inner-left {
  position: absolute; top: 0; left: 0;
  width: 20%; height: 100%;
  background: linear-gradient(to right, rgba(0,0,0,0.09) 0%, transparent 100%);
  pointer-events: none; z-index: 9;
  border-radius: 2px 0 0 2px;
  transition: opacity 0.3s;
}
.inner-right {
  position: absolute; top: 0; right: 0;
  width: 20%; height: 100%;
  background: linear-gradient(to left, rgba(0,0,0,0.09) 0%, transparent 100%);
  pointer-events: none; z-index: 9;
  border-radius: 0 2px 2px 0;
  transition: opacity 0.3s;
}
.inner-left.hidden, .inner-right.hidden { opacity: 0; }

/* ══ BOTTOM CONTROLS ══ */
.book-controls {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 10px;
  flex-shrink: 0;
  background: #fff;
  border: 1px solid #ddd;
  padding: 6px 12px;
  border-radius: 50px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  z-index: 100;
}
.ctrl-btn {
  background: none; border: none; cursor: pointer;
  color: #aaa;
  display: flex; align-items: center; justify-content: center;
  width: 30px; height: 30px; border-radius: 50%;
  transition: all 0.2s; flex-shrink: 0;
}
.ctrl-btn:hover { background: rgba(0,0,0,0.06); color: var(--gold); transform: scale(1.12); }
.ctrl-btn.playing { color: var(--gold); }
.ctrl-btn svg { width: 15px; height: 15px; }
.ctrl-divider { width: 1px; height: 16px; background: #eee; flex-shrink: 0; }

/* Page indicator + Jump */
.page-indicator-wrap { position: relative; }
.page-indicator {
  font-family: 'Playfair Display', serif;
  font-size: 13px; color: #aaa;
  min-width: 60px; text-align: center;
  cursor: pointer; padding: 3px 6px; border-radius: 6px;
  transition: background 0.2s; white-space: nowrap;
}
.page-indicator:hover { background: rgba(0,0,0,0.05); }
.page-indicator .cur { color: var(--gold); font-weight: 700; }
.jump-popup {
  position: absolute;
  bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%);
  background: #fff; border: 1px solid #e0e0e0;
  border-radius: 10px; padding: 8px 10px;
  box-shadow: 0 6px 24px rgba(0,0,0,0.12);
  display: none; align-items: center; gap: 6px;
  white-space: nowrap; z-index: 300;
}
.jump-popup.open { display: flex; }
.jump-popup input {
  width: 50px; border: 1.5px solid #ddd; border-radius: 6px;
  padding: 4px 6px; font-size: 13px; text-align: center;
  outline: none; color: #333;
}
.jump-popup input:focus { border-color: var(--gold); }
.jump-popup button {
  background: var(--gold); color: #fff; border: none;
  border-radius: 6px; padding: 4px 9px; font-size: 12px; cursor: pointer;
}

/* ══ THUMBNAIL STRIP ══ */
.thumb-strip {
  position: fixed;
  right: -220px; top: 50%;
  transform: translateY(-50%);
  width: 200px; max-height: 80vh;
  background: rgba(255,255,255,0.96);
  border-radius: 12px 0 0 12px;
  box-shadow: -4px 0 24px rgba(0,0,0,0.14);
  overflow-y: auto; overflow-x: hidden;
  transition: right 0.35s cubic-bezier(0.4,0,0.2,1);
  z-index: 500;
  padding: 10px 8px;
  scrollbar-width: thin;
  scrollbar-color: #ddd transparent;
}
.thumb-strip.open { right: 0; }
.thumb-strip::-webkit-scrollbar { width: 4px; }
.thumb-strip::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }

.thumb-item {
  position: relative; margin-bottom: 8px;
  cursor: pointer; border-radius: 6px; overflow: hidden;
  border: 2px solid transparent;
  transition: border-color 0.2s, transform 0.15s;
  background: #f5f5f5;
}
.thumb-item:hover { border-color: var(--gold); transform: scale(1.03); }
.thumb-item.active { border-color: var(--gold); }
.thumb-item img { width: 100%; display: block; border-radius: 4px; }
.thumb-pg {
  position: absolute; bottom: 3px; right: 5px;
  font-size: 10px; color: #fff;
  background: rgba(0,0,0,0.45);
  border-radius: 3px; padding: 1px 4px;
}

.thumb-toggle {
  position: fixed; right: 0; top: 50%;
  transform: translateY(-50%);
  width: 28px; height: 56px;
  background: #fff; border: 1px solid #ddd; border-right: none;
  border-radius: 8px 0 0 8px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  box-shadow: -2px 0 10px rgba(0,0,0,0.08);
  z-index: 501; transition: background 0.2s; color: #999;
}
.thumb-toggle:hover { background: #fafafa; color: var(--gold); }
.thumb-toggle svg { width: 14px; height: 14px; }

/* ══ LOADER ══ */
.loader-overlay {
  position: fixed; inset: 0;
  background: #ffffff; z-index: 9999;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 16px;
  transition: opacity 0.5s ease, visibility 0.5s ease;
}
.loader-overlay.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.loader-book { width: 44px; height: 54px; position: relative; }
.loader-book::before, .loader-book::after {
  content: ''; position: absolute; top: 0; bottom: 0; width: 50%;
  border-radius: 2px; animation: bookFlip 1.1s ease-in-out infinite;
}
.loader-book::before { left: 0; background: black; transform-origin: center right; }
.loader-book::after  { right: 0; background: rgb(49,49,49); transform-origin: center left; animation-delay: 0.55s; }
@keyframes bookFlip {
  0%,100% { transform: rotateY(0); opacity: 1; }
  50%     { transform: rotateY(-68deg); opacity: 0.35; }
}
.loader-text { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; color: #000; }
.loader-progress { width: 140px; height: 2px; background: #bbb; border-radius: 2px; overflow: hidden; }
.loader-bar { height: 100%; background: black; transition: width 0.25s ease; }

/* ══ FULLSCREEN ══ */
:fullscreen #flipbook-section,
:-webkit-full-screen #flipbook-section {
  background: #ddd9d3 !important;
  height: 100vh; padding: 8px 20px 6px;
}

/* ══ MOBILE ══ */
@media (max-width: 600px) {
  .book-controls { gap: 4px; padding: 5px 8px; }
  #flipbook-section { padding: 6px 8px 5px; }
  .thumb-strip, .thumb-toggle { display: none; }
}
</style>
