@extends('layouts.dashboard')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

    :root {
        --gold: #c9a84c;
        --gold-light: #e8d5a3;
    }

    /* ══ Force white background everywhere ══ */
    html,
    body,
    .wrapper,
    .content-wrapper,
    .main-content,
    .page-content,
    .dashboard-content,
    [class*="content"],
    [class*="main"],
    [class*="wrapper"] {
        background: #ffffff !important;
        background-color: #ffffff !important;
    }

    /* ══ Flipbook section reset ══ */
    #flipbook-section * {
        box-sizing: border-box;
    }

    #flipbook-section {
        background: #ffffff;
        min-height: 100vh;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-family: 'DM Sans', sans-serif;
        position: relative;
    }

    /* ══ Top-right action buttons ══
       Using margin-left:auto trick inside section so they
       stay in content area regardless of sidebar */
    .fab-row {
        width: 100%;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 10px;
        position: sticky;
        top: 10px;
        z-index: 200;
        padding-right: 4px;
    }

    .fab-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1.5px solid #e0e0e0;
        background: #ffffff;
        box-shadow: 0 2px 14px rgba(0,0,0,0.10);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: box-shadow 0.2s, background 0.2s, transform 0.15s, border-color 0.2s;
        color: #666;
        flex-shrink: 0;
    }
    .fab-btn:hover {
        background: #fafafa;
        box-shadow: 0 4px 20px rgba(0,0,0,0.13);
        transform: scale(1.08);
        border-color: var(--gold);
        color: var(--gold);
    }
    .fab-btn:active { transform: scale(0.95); }
    .fab-btn svg { width: 18px; height: 18px; pointer-events: none; }

    .fab-btn.sound-on {
        background: var(--gold);
        border-color: var(--gold);
        color: #fff;
        box-shadow: 0 4px 18px rgba(201,168,76,0.35);
    }
    .fab-btn.fs-on {
        background: #1a1410;
        border-color: #1a1410;
        color: #fff;
    }

    /* ══ Header ══ */
    .book-header {
        text-align: center;
        animation: fadeDown 0.6s ease both;
    }
    .book-label {
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 4px;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 8px;
    }
    .book-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(22px, 3.5vw, 40px);
        font-weight: 700;
        color: #1a1410;
        line-height: 1.2;
    }
    .title-rule {
        width: 52px;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--gold), transparent);
        margin: 10px auto 0;
    }

    /* ══ Book stage ══ */
    .book-stage {
        position: relative;
        animation: fadeUp 0.7s 0.15s ease both;
    }
    .book-stage::after {
        content: '';
        position: absolute;
        bottom: -14px;
        left: 50%;
        transform: translateX(-50%);
        width: 75%;
        height: 16px;
        background: radial-gradient(ellipse, rgba(0,0,0,0.13) 0%, transparent 70%);
        filter: blur(5px);
        pointer-events: none;
    }

    #flipbook { border-radius: 2px; }

    /* ══ Bottom controls ══ */
    .book-controls {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 30px;
        animation: fadeUp 0.7s 0.3s ease both;
        background: #f9f9f9;
        border: 1px solid #ebebeb;
        padding: 9px 20px;
        border-radius: 50px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    }
    .ctrl-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        transition: background 0.2s, color 0.2s, transform 0.15s;
    }
    .ctrl-btn:hover {
        background: rgba(201,168,76,0.10);
        color: var(--gold);
        transform: scale(1.1);
    }
    .ctrl-btn:active { transform: scale(0.95); }
    .ctrl-btn svg { width: 18px; height: 18px; }

    .ctrl-divider { width: 1px; height: 20px; background: #e8e8e8; }

    .page-indicator {
        font-family: 'Playfair Display', serif;
        font-size: 14px;
        color: #bbb;
        min-width: 76px;
        text-align: center;
        letter-spacing: 1px;
    }
    .page-indicator span { color: var(--gold); font-weight: 600; }

    /* ══ Key hint ══ */
    .key-hint {
        margin-top: 16px;
        font-size: 10px;
        color: #ccc;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        animation: fadeUp 0.7s 0.5s ease both;
    }
    .key-hint kbd {
        background: #f2f2f2;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 1px 6px;
        font-size: 9px;
        font-family: 'DM Sans', sans-serif;
        color: #888;
    }

    /* ══ Loader ══ */
    .loader-overlay {
        position: fixed;
        inset: 0;
        background: #fff;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    .loader-overlay.hidden { opacity: 0; visibility: hidden; pointer-events: none; }

    .loader-book { width: 44px; height: 54px; position: relative; }
    .loader-book::before, .loader-book::after {
        content: ''; position: absolute;
        top: 0; bottom: 0; width: 50%;
        border-radius: 2px;
        animation: bookFlip 1.1s ease-in-out infinite;
    }
    .loader-book::before { left: 0; background: var(--gold); transform-origin: center right; }
    .loader-book::after  { right: 0; background: var(--gold-light); transform-origin: center left; animation-delay: 0.55s; }

    @keyframes bookFlip {
        0%,100% { transform: rotateY(0); opacity: 1; }
        50%      { transform: rotateY(-68deg); opacity: 0.35; }
    }
    .loader-text { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; color: #bbb; }
    .loader-progress { width: 140px; height: 2px; background: #eee; border-radius: 2px; overflow: hidden; }
    .loader-bar { height: 100%; background: linear-gradient(90deg,var(--gold),var(--gold-light)); border-radius: 2px; width:0; transition: width 0.25s ease; }

    /* ══ Fullscreen — show only the book ══ */
    :fullscreen #flipbook-section,
    :-webkit-full-screen #flipbook-section {
        background: #c77b7b !important;
        justify-content: center;
        padding: 16px 20px;
    }

    @keyframes fadeDown { from{opacity:0;transform:translateY(-16px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeUp   { from{opacity:0;transform:translateY(16px)}  to{opacity:1;transform:translateY(0)} }

    @media(max-width:600px){
        .book-controls { gap:8px; padding:8px 14px; }
        .key-hint { display:none; }
        .fab-btn { width:38px; height:38px; }
    }
</style>

<!-- Loader -->
<div class="loader-overlay" id="loaderOverlay">
    <div class="loader-book"></div>
    <div class="loader-text">Loading pages…</div>
    <div class="loader-progress"><div class="loader-bar" id="loaderBar"></div></div>
</div>

<!-- ══ Main section (this is what goes fullscreen) ══ -->
<div id="flipbook-section">

    <!-- TOP RIGHT: Sound + Fullscreen buttons -->
    <div class="fab-row">
        <button class="fab-btn" id="btnSound" title="Enable Sound">
            <!-- Muted -->
            <svg id="iSoundOff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <line x1="23" y1="9" x2="17" y2="15"></line>
                <line x1="17" y1="9" x2="23" y2="15"></line>
            </svg>
            <!-- Sound on -->
            <svg id="iSoundOn" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                <path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path>
            </svg>
        </button>

        <button class="fab-btn" id="btnFS" title="Fullscreen">
            <!-- Expand -->
            <svg id="iExpand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 3 21 3 21 9"></polyline>
                <polyline points="9 21 3 21 3 15"></polyline>
                <line x1="21" y1="3" x2="14" y2="10"></line>
                <line x1="3" y1="21" x2="10" y2="14"></line>
            </svg>
            <!-- Compress -->
            <svg id="iCompress" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <polyline points="4 14 10 14 10 20"></polyline>
                <polyline points="20 10 14 10 14 4"></polyline>
                <line x1="10" y1="14" x2="3" y2="21"></line>
                <line x1="21" y1="3" x2="14" y2="10"></line>
            </svg>
        </button>
    </div>

    <!-- Header -->
    <div class="book-header">
        <div class="book-label">Now Reading</div>
        <h1 class="book-title">{{ $flipbook->title }}</h1>
        <div class="title-rule"></div>
    </div>

    <!-- Book -->
    <div class="book-stage">
        <div id="flipbook"></div>
    </div>

    <!-- Controls -->
    <div class="book-controls">
        <button class="ctrl-btn" id="btnPrev">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <div class="ctrl-divider"></div>
        <div class="page-indicator" id="pageInd"><span>1</span> / <span id="totalPg">—</span></div>
        <div class="ctrl-divider"></div>
        <button class="ctrl-btn" id="btnNext">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>

    <div class="key-hint"><kbd>←</kbd> Prev &nbsp;|&nbsp; Next <kbd>→</kbd> &nbsp;|&nbsp; Click edges to flip</div>

</div>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
<!-- PageFlip -->
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
            const ctx=getACtx(), dur=0.20, now=ctx.currentTime;
            const len=Math.floor(ctx.sampleRate*dur);
            const buf=ctx.createBuffer(1,len,ctx.sampleRate);
            const d=buf.getChannelData(0);
            for(let i=0;i<len;i++) d[i]=(Math.random()*2-1);
            const src=ctx.createBufferSource(); src.buffer=buf;
            const bpf=ctx.createBiquadFilter(); bpf.type='bandpass';
            bpf.frequency.value=1800; bpf.Q.value=1.0;
            const g=ctx.createGain();
            g.gain.setValueAtTime(0,now);
            g.gain.linearRampToValueAtTime(0.45,now+0.025);
            g.gain.exponentialRampToValueAtTime(0.001,now+dur);
            src.connect(bpf); bpf.connect(g); g.connect(ctx.destination);
            src.start(now); src.stop(now+dur+0.01);
        }catch(e){}
    }

    const btnSound=document.getElementById('btnSound');
    const iSoundOn=document.getElementById('iSoundOn');
    const iSoundOff=document.getElementById('iSoundOff');

    btnSound.addEventListener('click',()=>{
        soundOn=!soundOn;
        getACtx().resume();
        if(soundOn){
            iSoundOff.style.display='none'; iSoundOn.style.display='';
            btnSound.classList.add('sound-on'); btnSound.title='Sound On';
        } else {
            iSoundOn.style.display='none'; iSoundOff.style.display='';
            btnSound.classList.remove('sound-on'); btnSound.title='Enable Sound';
        }
    });

    /* ── Fullscreen — fullscreen the #flipbook-section div only ── */
    const section=document.getElementById('flipbook-section');
    const btnFS=document.getElementById('btnFS');
    const iExpand=document.getElementById('iExpand');
    const iCompress=document.getElementById('iCompress');

    function isFS(){ return !!(document.fullscreenElement||document.webkitFullscreenElement); }

    function syncFS(){
        if(isFS()){
            iExpand.style.display='none'; iCompress.style.display='';
            btnFS.classList.add('fs-on'); btnFS.title='Exit Fullscreen';
        } else {
            iCompress.style.display='none'; iExpand.style.display='';
            btnFS.classList.remove('fs-on'); btnFS.title='Fullscreen';
        }
    }

    btnFS.addEventListener('click',()=>{
        if(!isFS()){
            (section.requestFullscreen||section.webkitRequestFullscreen).call(section);
        } else {
            (document.exitFullscreen||document.webkitExitFullscreen).call(document);
        }
    });

    document.addEventListener('fullscreenchange', syncFS);
    document.addEventListener('webkitfullscreenchange', syncFS);
    /* ── PageFlip ── */
    const pdfUrl="{{ asset('storage/'.$flipbook->file_path) }}";
    const pdfjsLib=window['pdfjs-dist/build/pdf'];
    pdfjsLib.GlobalWorkerOptions.workerSrc=
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js';

    const container=document.getElementById('flipbook');
    const loaderBar=document.getElementById('loaderBar');
    const loaderEl=document.getElementById('loaderOverlay');
    const pageInd=document.getElementById('pageInd');
    const totalPg=document.getElementById('totalPg');

    function bookSize(){
        const vw=window.innerWidth;
        if(vw<480) return{w:Math.min(vw-32,290),h:420};
        if(vw<800) return{w:330,h:490};
        return{w:430,h:610};

    }

    const{w,h}=bookSize();

    const pageFlip=new St.PageFlip(container,{
        width:w, height:h,
        size:'fixed',
        minWidth:260, maxWidth:580,
        minHeight:380, maxHeight:820,
        showCover:true,
        mobileScrollSupport:false,
        drawShadow:true,
        flippingTime:650,
        usePortrait:window.innerWidth<600,
        startPage:0,
        autoSize:true,
    });

    function updateInd(){
        const cur=pageFlip.getCurrentPageIndex()+1;
        const tot=pageFlip.getPageCount();
        pageInd.innerHTML=`<span>${cur}</span> / <span>${tot}</span>`;
    }

    pageFlip.on('flip',()=>{ playFlip(); updateInd(); });

    document.getElementById('btnPrev').addEventListener('click',()=>pageFlip.flipPrev());
    document.getElementById('btnNext').addEventListener('click',()=>pageFlip.flipNext());
    document.addEventListener('keydown',e=>{
        if(e.key==='ArrowRight') pageFlip.flipNext();
        if(e.key==='ArrowLeft')  pageFlip.flipPrev();
    });

    pdfjsLib.getDocument(pdfUrl).promise.then(async pdf=>{
        const total=pdf.numPages;
        totalPg.textContent=total;
        const pages=[];

        for(let i=1;i<=total;i++){
            const page=await pdf.getPage(i);
            const vp=page.getViewport({scale:1.8});
            const canvas=document.createElement('canvas');
            const ctx=canvas.getContext('2d');
            canvas.width=vp.width; canvas.height=vp.height;
            await page.render({canvasContext:ctx,viewport:vp}).promise;

            const wrap=document.createElement('div');
            wrap.style.cssText='width:100%;height:100%;overflow:hidden;background:#fff;';
            const img=document.createElement('img');
            img.src=canvas.toDataURL('image/jpeg',0.92);
            img.style.cssText='width:100%;height:100%;object-fit:contain;display:block;';
            wrap.appendChild(img);
            pages.push(wrap);

            loaderBar.style.width=Math.round((i/total)*100)+'%';
        }

        pageFlip.loadFromHTML(pages);
        setTimeout(()=>{ loaderEl.classList.add('hidden'); updateInd(); },400);

    }).catch(err=>{
        console.error(err);
        loaderEl.querySelector('.loader-text').textContent='Failed to load PDF';
    });

})();
</script>

@endsection
