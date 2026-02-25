@extends('layouts.dashboard')

@section('content')
<div class="container mt-4" style="max-width: 700px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('videos.index') }}" class="btn btn-outline-secondary btn-sm">← Back</a>
        <h2 class="mb-0" style="font-size:20px;font-weight:700;">Add New Video</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data" id="videoForm">
        @csrf

        {{-- Hidden canvas-generated thumbnail --}}
        <input type="hidden" name="auto_thumbnail" id="autoThumbnailData">

        <!-- Title -->
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;">Title</label>
            <input type="text" name="title" class="form-control" style="background:#1a1a1a;color:#fff;border-color:#444;font-size:13.5px;" required>
        </div>

        <!-- Video Upload -->
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;">Video</label>
            <input type="file" name="video_url" class="form-control" accept="video/*" id="videoInput"
                style="background:#1a1a1a;color:#fff;border-color:#444;font-size:13px;" required>
            <div class="mt-3" id="videoPreviewWrap" style="display:none;">
                <video id="videoPreview" controls crossorigin="anonymous"
                    style="width:100%;max-height:260px;border-radius:8px;border:1px solid #333;background:#000;">
                </video>
            </div>
        </div>

        <!-- Thumbnail -->
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.4px;">
                Thumbnail
                <span style="color:#666;font-weight:400;font-size:11px;text-transform:none;margin-left:6px;">(Optional — auto-generated from video if skipped)</span>
            </label>

            <!-- Toggle -->
            <div class="d-flex gap-2 mb-3">
                <button type="button" id="btnAutoThumb" class="thumb-toggle active" onclick="setMode('auto')">
                    Auto from Video
                </button>
                <button type="button" id="btnCustomThumb" class="thumb-toggle" onclick="setMode('custom')">
                    Upload Custom
                </button>
            </div>

            <!-- Auto mode preview -->
            <div id="autoThumbSection">
                <div id="autoThumbPreview" style="display:none;">
                    <p style="font-size:12px;color:#888;margin-bottom:8px;">First frame captured as thumbnail:</p>
                    <img id="autoThumbImg" src="#"
                        style="max-width:200px;border-radius:6px;border:1px solid #333;" />
                    <canvas id="thumbCanvas" style="display:none;"></canvas>
                </div>
                <div id="autoThumbPlaceholder" style="color:#666;font-size:13px;">
                    <i class="bi bi-camera-video me-1"></i> Upload a video above to auto-generate thumbnail
                </div>
            </div>

            <!-- Custom upload mode -->
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
            Add Video
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
let currentMode = 'auto';

function setMode(mode) {
    currentMode = mode;
    document.getElementById('btnAutoThumb').classList.toggle('active', mode === 'auto');
    document.getElementById('btnCustomThumb').classList.toggle('active', mode === 'custom');
    document.getElementById('autoThumbSection').style.display = mode === 'auto' ? 'block' : 'none';
    document.getElementById('customThumbSection').style.display = mode === 'custom' ? 'block' : 'none';

    // If switching to auto, clear file input so server uses auto_thumbnail
    if (mode === 'auto') {
        document.getElementById('thumbnailInput').value = '';
    }
}

// ── Video upload → preview + capture first frame ──
document.getElementById('videoInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const videoEl = document.getElementById('videoPreview');
    const videoWrap = document.getElementById('videoPreviewWrap');
    const url = URL.createObjectURL(file);

    videoEl.src = url;
    videoWrap.style.display = 'block';

    // Wait for metadata, seek to 0.5s, capture frame
    videoEl.addEventListener('loadedmetadata', function () {
        videoEl.currentTime = 0.5;
    }, { once: true });

    videoEl.addEventListener('seeked', function () {
        captureFrame(videoEl);
    }, { once: true });
});

function captureFrame(videoEl) {
    const canvas = document.getElementById('thumbCanvas');
    canvas.width = videoEl.videoWidth || 640;
    canvas.height = videoEl.videoHeight || 360;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);

    // Show preview
    document.getElementById('autoThumbImg').src = dataUrl;
    document.getElementById('autoThumbPreview').style.display = 'block';
    document.getElementById('autoThumbPlaceholder').style.display = 'none';

    // Store base64 in hidden input
    document.getElementById('autoThumbnailData').value = dataUrl;
}

// ── Custom thumbnail preview ──
document.getElementById('thumbnailInput').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const wrap = document.getElementById('customPreviewWrap');
    const img = document.getElementById('customThumbPreview');
    img.src = URL.createObjectURL(file);
    wrap.style.display = 'block';
});
</script>
@endsection
