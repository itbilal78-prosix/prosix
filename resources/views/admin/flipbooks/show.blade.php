@extends('layouts.dashboard')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

    :root {
        --gold: #000000;
        --gold-light: #424242;
    }



    body { overflow: hidden !important; }
    #flipbook-section * { box-sizing: border-box; }

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
    .fab-btn.fs-on { background: #1a1410; border-color: #1a1410; color: #fff; }

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

    /* ══ REALISTIC BOOK SHADOW ══ */
    .book-outer {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Corner + bottom shadow exactly like real open book */
    .book-outer::before {
        content: '';
        position: absolute;
        top: 3px; left: -10px; right: -10px; bottom: -8px;
        border-radius: 1px;
        background: transparent;
        box-shadow:
            /* bottom center */
            0 22px 45px rgba(0,0,0,0.30),
            /* bottom-left */
            -12px 16px 32px rgba(0,0,0,0.25),
            /* bottom-right */
            12px 16px 32px rgba(0,0,0,0.25),
            /* top-left corner subtle */
            -6px -3px 18px rgba(0,0,0,0.10),
            /* top-right corner subtle */
            6px -3px 18px rgba(0,0,0,0.10);
        pointer-events: none;
        z-index: 0;
    }

    /* The flipbook container itself */
    .book-inner {
        position: relative;
        z-index: 1;
        display: block;
    }

    /* Spine shadow overlay */
    .spine-shadow {
        position: absolute;
        top: 0; left: 50%;
        transform: translateX(-50%);
        width: 48px; height: 100%;
        background: linear-gradient(to right,
            transparent 0%,
            rgba(0,0,0,0.08) 22%,
            rgba(0,0,0,0.22) 48%,
            rgba(0,0,0,0.22) 52%,
            rgba(0,0,0,0.08) 78%,
            transparent 100%
        );
        pointer-events: none;
        z-index: 10;
        transition: opacity 0.3s;
    }
    .spine-shadow.hidden { opacity: 0; }

    /* Left/right inner page shadows */
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

    #flipbook { display: block; }

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
    .ctrl-btn:hover { background: rgba(201,168,76,0.10); color: var(--gold); transform: scale(1.12); }
    .ctrl-btn.playing { color: var(--gold); }
    .ctrl-btn svg { width: 15px; height: 15px; }
    .ctrl-divider { width: 1px; height: 16px; background: #eee; flex-shrink: 0; }

    /* Page indicator + jump */
    .page-indicator-wrap { position: relative; }
    .page-indicator {
        font-family: 'Playfair Display', serif;
        font-size: 13px; color: #aaa;
        min-width: 60px; text-align: center;
        cursor: pointer; padding: 3px 6px; border-radius: 6px;
        transition: background 0.2s; white-space: nowrap;
    }
    .page-indicator:hover { background: rgba(201,168,76,0.08); }
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
        outline: none; font-family: 'DM Sans', sans-serif; color: #333;
    }
    .jump-popup input:focus { border-color: var(--gold); }
    .jump-popup button {
        background: var(--gold); color: #fff; border: none;
        border-radius: 6px; padding: 4px 9px; font-size: 12px;
        cursor: pointer;
    }

    /* ══ THUMBNAIL STRIP ══ */
    .thumb-strip {
        position: fixed;
        right: -220px;
        top: 50%;
        transform: translateY(-50%);
        width: 200px;
        max-height: 80vh;
        background: rgba(255,255,255,0.96);
        border-radius: 12px 0 0 12px;
        box-shadow: -4px 0 24px rgba(0,0,0,0.14);
        overflow-y: auto;
        overflow-x: hidden;
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
        position: relative;
        margin-bottom: 8px;
        cursor: pointer;
        border-radius: 6px;
        overflow: hidden;
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
        font-family: 'DM Sans', sans-serif;
    }

    /* Toggle thumb button */
    .thumb-toggle {
        position: fixed;
        right: 0; top: 50%;
        transform: translateY(-50%);
        width: 28px; height: 56px;
        background: #fff;
        border: 1px solid #ddd;
        border-right: none;
        border-radius: 8px 0 0 8px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: -2px 0 10px rgba(0,0,0,0.08);
        z-index: 501;
        transition: background 0.2s;
        color: #999;
    }
    .thumb-toggle:hover { background: #fafafa; color: var(--gold); }
    .thumb-toggle svg { width: 14px; height: 14px; }

    /* ══ LOADER ══ */
    .loader-overlay {
        position: fixed; inset: 0;
        background: #ffffff;
        z-index: 9999;
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
    .loader-book::after  { right: 0; background: rgb(49, 49, 49); transform-origin: center left; animation-delay: 0.55s; }
    @keyframes bookFlip {
        0%,100% { transform: rotateY(0); opacity: 1; }
        50%      { transform: rotateY(-68deg); opacity: 0.35; }
    }
    .loader-text { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; color: #000000; }
    .loader-progress { width: 140px; height: 2px; background: #bbb; border-radius: 2px; overflow: hidden; }
    .loader-bar { height: 100%; background: black; width: 0; transition: width 0.25s ease; }

    :fullscreen #flipbook-section,
    :-webkit-full-screen #flipbook-section {
            background: #ddd9d3; !important;
        height: 100vh; padding: 8px 20px 6px;
    }

    @media(max-width:600px){
        .book-controls { gap:4px; padding:5px 8px; }
        #flipbook-section { padding:6px 8px 5px; }
        .thumb-strip { display: none; }
        .thumb-toggle { display: none; }
    }
</style>

<div class="loader-overlay" id="loaderOverlay">
    <div class="loader-book"></div>
    <div class="loader-text">Loading pages…</div>
    <div class="loader-progress"><div class="loader-bar" id="loaderBar"></div></div>
</div>

<div id="flipbook-section">

    <!-- Top Bar -->
    <div class="top-bar">
        <h1 class="book-title">{{ $flipbook->title }}</h1>
        <div class="fab-row">
            <button class="fab-btn" id="btnSound" title="Sound">
                <svg id="iSoundOff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                    <line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line>
                </svg>
                <svg id="iSoundOn" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                    <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                    <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
                </svg>
            </button>
            <button class="fab-btn" id="btnFS" title="Fullscreen">
                <svg id="iExpand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline>
                    <line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line>
                </svg>
                <svg id="iCompress" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                    <polyline points="4 14 10 14 10 20"></polyline><polyline points="20 10 14 10 14 4"></polyline>
                    <line x1="10" y1="14" x2="3" y2="21"></line><line x1="21" y1="3" x2="14" y2="10"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- Book Area -->
    <div class="book-wrap">
        <div class="book-outer" id="bookOuter">
            <div class="book-inner">
                <div id="flipbook"></div>
                <div class="inner-left  hidden" id="leftShadow"></div>
                <div class="inner-right hidden" id="rightShadow"></div>
                <div class="spine-shadow hidden" id="spineShadow"></div>
            </div>
        </div>
    </div>

    <!-- Bottom Controls -->
    <div class="book-controls">
        <button class="ctrl-btn" id="btnFirst" title="First Page">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline>
            </svg>
        </button>
        <button class="ctrl-btn" id="btnPrev" title="Previous">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <div class="ctrl-divider"></div>
        <div class="page-indicator-wrap">
            <div class="page-indicator" id="pageInd" title="Click to jump to page">
                <span class="cur" id="curPg">1</span> / <span id="totalPg">—</span>
            </div>
            <div class="jump-popup" id="jumpPopup">
                <input type="number" id="jumpInput" min="1" placeholder="Pg" />
                <button id="jumpGo">Go</button>
            </div>
        </div>
        <div class="ctrl-divider"></div>
        <button class="ctrl-btn" id="btnPlay" title="Auto Play">
            <svg id="iPlay" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
            <svg id="iPause" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect>
            </svg>
        </button>
        <div class="ctrl-divider"></div>
        <button class="ctrl-btn" id="btnNext" title="Next">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
        <button class="ctrl-btn" id="btnLast" title="Last Page">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline>
            </svg>
        </button>
        <div class="ctrl-divider"></div>
        <!-- Thumbnail toggle inside controls too -->
        <button class="ctrl-btn" id="btnThumbCtrl" title="Page Thumbnails">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect>
            </svg>
        </button>
    </div>

</div>

<!-- Thumbnail Strip -->
<div class="thumb-strip" id="thumbStrip"></div>
<button class="thumb-toggle" id="thumbToggle" title="Page Thumbnails">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect>
    </svg>
</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
<script src="https://unpkg.com/page-flip/dist/js/page-flip.browser.js"></script>

<script>
(function(){

    /* ── Sound ── */
    let soundOn = false, audioCtx = null;
    function getACtx(){
        if(!audioCtx) audioCtx = new (window.AudioContext||window.webkitAudioContext)();
        return audioCtx;
    }
    function playFlip(){
        if(!soundOn) return;
        try{
            const ctx=getACtx(), dur=0.18, now=ctx.currentTime;
            const len=Math.floor(ctx.sampleRate*dur);
            const buf=ctx.createBuffer(1,len,ctx.sampleRate);
            const d=buf.getChannelData(0);
            for(let i=0;i<len;i++) d[i]=(Math.random()*2-1)*Math.pow(1-(i/len),0.5);
            const src=ctx.createBufferSource(); src.buffer=buf;
            const bpf=ctx.createBiquadFilter(); bpf.type='bandpass';
            bpf.frequency.value=2200; bpf.Q.value=1.2;
            const g=ctx.createGain();
            g.gain.setValueAtTime(0,now);
            g.gain.linearRampToValueAtTime(0.38,now+0.02);
            g.gain.exponentialRampToValueAtTime(0.001,now+dur);
            src.connect(bpf); bpf.connect(g); g.connect(ctx.destination);
            src.start(now); src.stop(now+dur+0.01);
        }catch(e){}
    }

    document.getElementById('btnSound').addEventListener('click',()=>{
        soundOn=!soundOn; getACtx().resume();
        document.getElementById('iSoundOff').style.display = soundOn?'none':'';
        document.getElementById('iSoundOn').style.display  = soundOn?'':'none';
        document.getElementById('btnSound').classList.toggle('sound-on', soundOn);
    });

    /* ── Fullscreen ── */
    const section = document.getElementById('flipbook-section');
    function isFS(){ return !!(document.fullscreenElement||document.webkitFullscreenElement); }
    function syncFS(){
        const on = isFS();
        document.getElementById('iExpand').style.display   = on?'none':'';
        document.getElementById('iCompress').style.display = on?'':'none';
        document.getElementById('btnFS').classList.toggle('fs-on', on);
    }
    document.getElementById('btnFS').addEventListener('click',()=>{
        if(!isFS()) (section.requestFullscreen||section.webkitRequestFullscreen).call(section);
        else        (document.exitFullscreen||document.webkitExitFullscreen).call(document);
    });
    document.addEventListener('fullscreenchange', syncFS);
    document.addEventListener('webkitfullscreenchange', syncFS);

    /* ── Book size ── */
    function bookSize(){
        const topH  = (document.querySelector('.top-bar').offsetHeight||44) + 6;
        const ctrlH = 62;
        const padH  = 22;
        const avH   = window.innerHeight - topH - ctrlH - padH;
        const avW   = window.innerWidth - 48;
        const mob   = window.innerWidth < 600;
        const ratio = 1.414;

        let h = Math.min(avH, 700);
        let w = Math.round(h / ratio);

        if(!mob && w * 2 > avW){
            w = Math.floor(avW / 2);
            h = Math.round(w * ratio);
        }
        if(mob){
            w = Math.min(avW, 300);
            h = Math.round(w * ratio);
        }
        return { w: Math.max(w,180), h: Math.max(h,260) };
    }

    const { w, h } = bookSize();
    const isMob    = window.innerWidth < 600;

    /* ── PageFlip — KEY FIX: usePortrait:true for cover (odd pages = portrait mode shows single page) ── */
    const container = document.getElementById('flipbook');
    const pageFlip  = new St.PageFlip(container, {
        width: w, height: h,
        size: 'fixed',
        minWidth:180, maxWidth:660,
        minHeight:260, maxHeight:920,
        showCover:           true,   // cover shows as single page centered
        mobileScrollSupport: false,
        drawShadow:          true,
        maxShadowOpacity:    0.5,
        flippingTime:        820,
        usePortrait:         isMob,
        startPage:           0,
        autoSize:            false,
    });

    /* ── Shadows ── */
    const spineShadow = document.getElementById('spineShadow');
    const leftShadow  = document.getElementById('leftShadow');
    const rightShadow = document.getElementById('rightShadow');

    function updateShadows(idx, total){
        const single = isMob || idx === 0 || idx === total - 1;
        spineShadow.classList.toggle('hidden', single);
        leftShadow.classList.toggle('hidden',  single);
        rightShadow.classList.toggle('hidden', single);
    }

    /* ── Page indicator ── */
    const curPgEl   = document.getElementById('curPg');
    const totalPgEl = document.getElementById('totalPg');
    const thumbItems = [];

    function updateInd(){
        const idx = pageFlip.getCurrentPageIndex();
        const tot = pageFlip.getPageCount();
        curPgEl.textContent   = idx + 1;
        totalPgEl.textContent = tot;
        updateShadows(idx, tot);
        // Update active thumbnail
        thumbItems.forEach((el, i) => el.classList.toggle('active', i === idx));
        // Scroll active thumb into view
        if(thumbItems[idx]){
            thumbItems[idx].scrollIntoView({ block:'nearest', behavior:'smooth' });
        }
    }

    pageFlip.on('flip', ()=>{ playFlip(); updateInd(); });

    /* ── Nav ── */
    document.getElementById('btnFirst').addEventListener('click', ()=> pageFlip.flip(0));
    document.getElementById('btnLast' ).addEventListener('click', ()=> pageFlip.flip(pageFlip.getPageCount()-1));
    document.getElementById('btnPrev' ).addEventListener('click', ()=> pageFlip.flipPrev());
    document.getElementById('btnNext' ).addEventListener('click', ()=> pageFlip.flipNext());

    document.addEventListener('keydown', e=>{
        if(e.target.tagName==='INPUT') return;
        if(e.key==='ArrowRight') pageFlip.flipNext();
        if(e.key==='ArrowLeft')  pageFlip.flipPrev();
        if(e.key==='Home')       pageFlip.flip(0);
        if(e.key==='End')        pageFlip.flip(pageFlip.getPageCount()-1);
    });

    /* ── Jump ── */
    const jumpPopup = document.getElementById('jumpPopup');
    const jumpInput = document.getElementById('jumpInput');
    document.getElementById('pageInd').addEventListener('click', e=>{
        e.stopPropagation();
        jumpInput.max   = pageFlip.getPageCount();
        jumpInput.value = pageFlip.getCurrentPageIndex() + 1;
        jumpPopup.classList.toggle('open');
        if(jumpPopup.classList.contains('open')) jumpInput.focus();
    });
    document.getElementById('jumpGo').addEventListener('click', ()=>{
        const pg = parseInt(jumpInput.value,10) - 1;
        if(!isNaN(pg) && pg >= 0 && pg < pageFlip.getPageCount()) pageFlip.flip(pg);
        jumpPopup.classList.remove('open');
    });
    jumpInput.addEventListener('keydown', e=>{
        if(e.key==='Enter')  document.getElementById('jumpGo').click();
        if(e.key==='Escape') jumpPopup.classList.remove('open');
    });
    document.addEventListener('click', ()=> jumpPopup.classList.remove('open'));

    /* ── Auto play ── */
    let autoTimer = null;
    const btnPlay = document.getElementById('btnPlay');
    function stopAuto(){
        if(autoTimer){ clearInterval(autoTimer); autoTimer=null; }
        document.getElementById('iPlay').style.display  = '';
        document.getElementById('iPause').style.display = 'none';
        btnPlay.classList.remove('playing');
    }
    function startAuto(){
        autoTimer = setInterval(()=>{
            if(pageFlip.getCurrentPageIndex() >= pageFlip.getPageCount()-1){ stopAuto(); return; }
            pageFlip.flipNext();
        }, 3000);
        document.getElementById('iPlay').style.display  = 'none';
        document.getElementById('iPause').style.display = '';
        btnPlay.classList.add('playing');
    }
    btnPlay.addEventListener('click', ()=>{ if(autoTimer) stopAuto(); else startAuto(); });

    /* ── Thumbnail strip toggle ── */
    const thumbStrip  = document.getElementById('thumbStrip');
    const thumbToggle = document.getElementById('thumbToggle');
    const btnThumbCtrl= document.getElementById('btnThumbCtrl');
    let thumbOpen = false;
    function toggleThumb(){
        thumbOpen = !thumbOpen;
        thumbStrip.classList.toggle('open', thumbOpen);
    }
    thumbToggle.addEventListener('click', toggleThumb);
    btnThumbCtrl.addEventListener('click', e=>{ e.stopPropagation(); toggleThumb(); });

    /* ── PDF rendering ── */
    const pdfUrl   = "{{ asset('storage/'.$flipbook->file_path) }}";
    const pdfjsLib = window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js';

    const loaderBar = document.getElementById('loaderBar');
    const loaderEl  = document.getElementById('loaderOverlay');

    pdfjsLib.getDocument(pdfUrl).promise.then(async pdf => {
        const total = pdf.numPages;
        totalPgEl.textContent = total;
        const pages = [];

        for(let i = 1; i <= total; i++){
            const page = await pdf.getPage(i);

            /* ── High-res for main flipbook ── */
            const vp     = page.getViewport({ scale: 1.8 });
            const canvas = document.createElement('canvas');
            const ctx2d  = canvas.getContext('2d');
            canvas.width  = vp.width;
            canvas.height = vp.height;
            await page.render({ canvasContext: ctx2d, viewport: vp }).promise;
            const dataUrl = canvas.toDataURL('image/jpeg', 0.92);

            /* Main page element */
            const wrap = document.createElement('div');
            wrap.style.cssText = 'width:100%;height:100%;overflow:hidden;background:#fff;display:flex;align-items:center;justify-content:center;';
            const img = new Image();
            img.src = dataUrl;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
            wrap.appendChild(img);
            pages.push(wrap);

            /* ── Thumbnail (low-res) ── */
            const tvp     = page.getViewport({ scale: 0.25 });
            const tCanvas = document.createElement('canvas');
            const tCtx    = tCanvas.getContext('2d');
            tCanvas.width  = tvp.width;
            tCanvas.height = tvp.height;
            await page.render({ canvasContext: tCtx, viewport: tvp }).promise;

            const tItem = document.createElement('div');
            tItem.className = 'thumb-item';
            tItem.dataset.page = i - 1;
            const tImg = new Image();
            tImg.src = tCanvas.toDataURL('image/jpeg', 0.7);
            tImg.alt = 'Page ' + i;
            const tPg = document.createElement('span');
            tPg.className = 'thumb-pg';
            tPg.textContent = i;
            tItem.appendChild(tImg);
            tItem.appendChild(tPg);
            tItem.addEventListener('click', ()=>{
                pageFlip.flip(parseInt(tItem.dataset.page, 10));
                // Close thumb on mobile
                if(window.innerWidth < 900){ thumbOpen=false; thumbStrip.classList.remove('open'); }
            });
            thumbStrip.appendChild(tItem);
            thumbItems.push(tItem);

            loaderBar.style.width = Math.round((i/total)*100) + '%';
        }

        pageFlip.loadFromHTML(pages);
        setTimeout(()=>{
            loaderEl.classList.add('hidden');
            updateInd();
        }, 400);

    }).catch(err=>{
        console.error(err);
        loaderEl.querySelector('.loader-text').textContent = 'Failed to load PDF';
    });

})();
</script>

@endsection
