{{-- resources/views/admin/banners/create.blade.php --}}
@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-black">Add New Banner</h2>

    <a href="{{ route('banners.index') }}" class="btn btn-outline-dark mb-3">
        <i class="bi bi-arrow-left-circle me-2"></i> Back to List
    </a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data"
          class="p-4 border rounded shadow-sm bg-white">
        @csrf

        {{-- Title --}}
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Title</label>
            <input type="text" name="title" class="form-control border-dark"
                   placeholder="Enter banner title" value="{{ old('title') }}" required>
        </div>

        {{-- Subtitle --}}
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Subtitle <span class="text-muted fw-normal">(Optional)</span></label>
            <input type="text" name="subtitle" class="form-control border-dark"
                   placeholder="Short description" value="{{ old('subtitle') }}">
        </div>

        {{-- Button --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Button Text</label>
                <input type="text" name="button_text" class="form-control border-dark"
                       placeholder="e.g. Shop Now" value="{{ old('button_text') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Button Link</label>
                <input type="url" name="button_link" class="form-control border-dark"
                       placeholder="https://example.com" value="{{ old('button_link') }}">
            </div>
        </div>

        {{-- Desktop Background --}}
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">
                 Background Image <span class="text-muted fw-normal">(Desktop — Landscape)</span>
            </label>
            <input type="file" name="background_image" class="form-control border-dark" required
                   onchange="previewImage(this, 'bgPreview')">
            <img id="bgPreview" src="" class="mt-2"
                 style="max-width: 200px; display:none; border:1px solid #000; border-radius:4px;">
        </div>

        {{-- ✅ Mobile Background --}}
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">
                 Background Image <span class="text-muted fw-normal">(Mobile — Portrait, Optional)</span>
            </label>

            <input type="file" name="mobile_background_image" class="form-control border-dark"
                   onchange="previewImage(this, 'mobileBgPreview')">
            <img id="mobileBgPreview" src="" class="mt-2"
                 style="max-width: 100px; display:none; border:1px solid #000; border-radius:4px;">
        </div>

        {{-- PNG Overlay --}}
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">
                PNG Image <span class="text-muted fw-normal">(Overlay/Product image, Optional)</span>
            </label>
            <input type="file" name="png_image" class="form-control border-dark"
                   onchange="previewImage(this, 'pngPreview')">
            <img id="pngPreview" src="" class="mt-2"
                 style="max-width: 120px; display:none; border:1px solid #000; border-radius:4px;">
        </div>

        <button class="btn btn-dark px-4 py-2">
            <i class="bi bi-plus-circle me-2"></i> Save Banner
        </button>
    </form>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = ''; preview.style.display = 'none';
    }
}
</script>

<style>
.form-control.border-dark { border-width: 1px !important; border-color: #000 !important; color: #000; }
.form-control::placeholder { color: #6c757d; }
.btn-dark { background-color: #000; color: #fff; border: 1px solid #000; transition: all 0.3s; }
.btn-dark:hover { background-color: #fff; color: #000; border-color: #000; }
.btn-outline-dark { color: #000; border: 1px solid #000; transition: all 0.3s; }
.btn-outline-dark:hover { background-color: #000; color: #fff; }
</style>
@endsection
