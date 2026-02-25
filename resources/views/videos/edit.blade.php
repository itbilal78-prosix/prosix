@extends('layouts.dashboard')

@section('content')
<div class="container mt-4" style="max-width: 700px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('videos.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
        <h2 class="mb-0" style="font-size:20px;font-weight:700;">Edit Video</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data" id="videoForm">
        @csrf
        @method('PUT')

        {{-- Hidden canvas-generated thumbnail --}}
        <input type="hidden" name="auto_thumbnail" id="autoThumbnailData">

        <!-- Title -->
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;">Title</label>
            <input type="text" name="title" class="form-control" value="{{ $video->title }}"
                style="background:#1a1a1a;color:#fff;border-color:#444;font-size:13.5px;" required>
        </div>

        <!-- Video Upload -->
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;">Video</label>
            <input type="file" name="video_url" class="form-control" accept="video/*" id="videoInput"
                style="background:#1a1a1a;color:#fff;border-color:#444;font-size:13px;">
            <div class="mt-3">
                <p style="font-size:12px;color:#888;margin-bottom:6px;">Current video:</p>
                <video id="videoPreview" controls crossorigin="anonymous"
                    style="width:100%;max-height:260px;border-radius:8px;border:1px solid #333;background:#000;">
                    <source src="{{ asset('storage/' . $video->video_url) }}" type="video/mp4">
                </video>
            </div>
        </div>

        <!-- Thumbnail -->
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;">
                Thumbnail
                <span style="color:#666;font-weight:400;font-size:11px;text-transform:none;margin-left:6px;">(Optional — leave as-is to keep current)</span>
            </label>

            <!-- Toggle -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <button type="button" id="btnKeepThumb"   class="thumb-toggle active" onclick="setMode('keep')">Keep Current</button>
                <button type="button" id="btnAutoThumb"   class="thumb-toggle"        onclick="setMode('auto')">Auto from Video</button>
                <button type="button" id="btnCustomThumb" class="thumb-toggle"        onclick="setMode('custom')">Upload Custom</button>
            </div>

            <!-- Keep current -->
            <div id="keepThumbSection">
                @if($video->thumbnail)
                    <p style="font-size:12px;color:#888;margin-bottom:8px;">Current thumbnail:</p>
                    <img src="{{ asset('storage/' . $video->thumbnail) }}"
                        style="max-width:200px;border-radius:6px;border:1px solid #333;" />
                @else
                    <p style="font-size:13px;color:#666;"><i class="bi bi-image me-1"></i> No thumbnail set</p>
                @endif
            </div>

            <!-- Auto mode -->
            <div id="autoThumbSection" style="display:none;">
                <canvas id="thumbCanvas" style="display:none;"></canvas>
                <div id="autoThumbPreview" style="display:none;">
                    <p style="font-size:12px;color:#888;margin-bottom:8px;">First frame captured as thumbnail:</p>
                    <img id="autoThumbImg" src="#"
                        style="max-width:200px;border-radius:6px;border:1px solid #333;" />
                </div>
                <div id="autoThumbPlaceholder" style="color:#666;font-size:13px;">
                    <i class="bi bi-camera-video me-1"></i> Upload a new video above to auto-generate thumbnail
                </div>
            </div>

            <!-- Custom upload -->
            <div id="customThumbSection" style="display:none;">
                <input type="file" name="thumbnail" class="form-control" accept="image/*" id="thumbnailInput"
                    style="background:#1a1a1a;color:#fff;border-color:#444;font-size:13px;">
                <div class="mt-2" id="customPreviewWrap" style="display:none;">
                    <img id="customThumbPreview" src="#"
                        style="max-width:200px;border-radius:6px;border:1px solid #333;" />
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-light px-4 fw-semibold" style="font-size:13.5px;">
            Update Video
        </button>
    </form>
</div>

<style>
.thumb-toggle {
    padding: 7px 18px;
    font-size: 12.5px;
    font-weight: 600;
    border: 1px solid #444;
    border-radius: 5px;
    background: transparent;
    color: #aaa;
    cursor: pointer;
    transition: all 0.2s;
    letter-spacing: 0.3px;
}
.thumb-toggle.active {
    background: #fff;
    color: #000;
    border-color: #fff;
}
.thumb-toggle:hover:not(.active) {
    border-color: #888;
    color: #fff;
}
</style>

<script>
let currentMode = 'keep';

function setMode(mode) {
    currentMode = mode;

    document.getElementById('btnKeepThumb').classList.toggle('active',   mode === 'keep');
    document.getElementById('btnAutoThumb').classList.toggle('active',   mode === 'auto');
    document.getElementById('btnCustomThumb').classList.toggle('active', mode === 'custom');

    document.getElementById('keepThumbSection').style.display   = mode === 'keep'   ? 'block' : 'none';
    document.getElementById('autoThumbSection').style.display   = mode === 'auto'   ? 'block' : 'none';
    document.getElementById('customThumbSection').style.display = mode === 'custom' ? 'block' : 'none';

    // Clear inputs when switching away
    if (mode !== 'custom') document.getElementById('thumbnailInput').value = '';
    if (mode !== 'auto')   document.getElementById('autoThumbnailData').value = '';
}

// ── New video uploaded → update preview + capture frame if auto mode ──
document.getElementById('videoInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const videoEl = document.getElementById('videoPreview');
    const url = URL.createObjectURL(file);

    videoEl.src = url;
    videoEl.load();

    videoEl.addEventListener('loadedmetadata', function () {
        videoEl.currentTime = 0.5;
    }, { once: true });

    videoEl.addEventListener('seeked', function () {
        if (currentMode === 'auto') captureFrame(videoEl);
    }, { once: true });
});

// ── If user switches to Auto mode after new video already loaded ──
document.getElementById('btnAutoThumb').addEventListener('click', function () {
    const videoEl = document.getElementById('videoPreview');
    if (videoEl.src.startsWith('blob:') && !document.getElementById('autoThumbnailData').value) {
        captureFrame(videoEl);
    }
});

function captureFrame(videoEl) {
    const canvas = document.getElementById('thumbCanvas');
    canvas.width  = videoEl.videoWidth  || 640;
    canvas.height = videoEl.videoHeight || 360;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

    document.getElementById('autoThumbImg').src = dataUrl;
    document.getElementById('autoThumbPreview').style.display = 'block';
    document.getElementById('autoThumbPlaceholder').style.display = 'none';
    document.getElementById('autoThumbnailData').value = dataUrl;
}

// ── Custom thumbnail preview ──
document.getElementById('thumbnailInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const img = document.getElementById('customThumbPreview');
    img.src = URL.createObjectURL(file);
    document.getElementById('customPreviewWrap').style.display = 'block';
});
</script>
@endsection
