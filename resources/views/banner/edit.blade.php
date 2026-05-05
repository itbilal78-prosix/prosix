{{-- resources/views/admin/banners/edit.blade.php --}}
@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-black">Edit Banner</h2>

    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-dark mb-3">
        <i class="bi bi-arrow-left-circle me-2"></i> Back to List
    </a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST"
          enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-semibold">Title</label>
            <input type="text" name="title" class="form-control border-dark"
                   value="{{ old('title', $banner->title) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Subtitle</label>
            <input type="text" name="subtitle" class="form-control border-dark"
                   value="{{ old('subtitle', $banner->subtitle) }}">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Button Text</label>
                <input type="text" name="button_text" class="form-control border-dark"
                       value="{{ old('button_text', $banner->button_text) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Button Link</label>
                <input type="url" name="button_link" class="form-control border-dark"
                       value="{{ old('button_link', $banner->button_link) }}">
            </div>
        </div>

        {{-- Desktop --}}
        <div class="mb-3">
            <label class="form-label fw-semibold"> Desktop Background Image</label>
            @if($banner->background_image)
                <div class="mb-2">
                    <img src="{{ $banner->background_image }}" style="max-width:200px; border:1px solid #000; border-radius:4px;">
                </div>
            @endif
            <input type="file" name="background_image" class="form-control border-dark"
                   onchange="previewImage(this, 'bgPreview')">
            <img id="bgPreview" src="" class="mt-2"
                 style="max-width:200px; display:none; border:1px solid #000; border-radius:4px;">
        </div>

        {{-- ✅ Mobile --}}
        <div class="mb-3">
            <label class="form-label fw-semibold"> Mobile Background Image</label>
            @if($banner->mobile_background_image)
                <div class="mb-2">
                    <img src="{{ $banner->mobile_background_image }}"
                         style="max-width:100px; border:1px solid #000; border-radius:4px;">
                    <small class="d-block text-muted mt-1">Current Mobile Image</small>
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="remove_mobile_image" id="removeMobile">
                        <label class="form-check-label text-danger" for="removeMobile">Remove mobile image</label>
                    </div>
                </div>
            @endif
            <input type="file" name="mobile_background_image" class="form-control border-dark"
                   onchange="previewImage(this, 'mobileBgPreview')">
            <img id="mobileBgPreview" src="" class="mt-2"
                 style="max-width:100px; display:none; border:1px solid #000; border-radius:4px;">
        </div>

        {{-- PNG --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">PNG Overlay Image</label>
            @if($banner->png_image)
                <div class="mb-2">
                    <img src="{{ $banner->png_image }}" style="max-width:100px; border:1px solid #000; border-radius:4px;">
                </div>
            @endif
            <input type="file" name="png_image" class="form-control border-dark"
                   onchange="previewImage(this, 'pngPreview')">
            <img id="pngPreview" src="" class="mt-2"
                 style="max-width:120px; display:none; border:1px solid #000; border-radius:4px;">
        </div>

        <button class="btn btn-dark px-4 py-2">
            <i class="bi bi-save me-2"></i> Update Banner
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
.form-control.border-dark { border-width: 1px !important; border-color: #000 !important; }
.btn-dark { background: #000; color: #fff; border: 1px solid #000; transition: all 0.3s; }
.btn-dark:hover { background: #fff; color: #000; }
.btn-outline-dark { color: #000; border: 1px solid #000; transition: all 0.3s; }
.btn-outline-dark:hover { background: #000; color: #fff; }
</style>
@endsection
