@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-black">Edit Banner</h2>

    <a href="{{ route('banners.index') }}" class="btn btn-outline-dark mb-3">
        <i class="bi bi-arrow-left-circle me-2"></i> Back to List
    </a>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('banners.update', $banner->id) }}"
          method="POST" enctype="multipart/form-data"
          class="p-4 border rounded shadow-sm bg-white">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Title</label>
            <input type="text" name="title"
                   class="form-control border-dark"
                   value="{{ $banner->title }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Button Text</label>
                <input type="text"
                       name="button_text"
                       class="form-control border-dark"
                       value="{{ $banner->button_text }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Button Link</label>
                <input type="url"
                       name="button_link"
                       class="form-control border-dark"
                       value="{{ $banner->button_link }}">
            </div>
        </div>

        {{-- Background Image --}}
        <div class="mb-3">
            <label class="form-label fw-semibold text-black d-block">Current Background</label>
            <img id="backgroundPreview" 
                 src="{{ asset('storage/' . $banner->background_image) }}" 
                 width="180" class="mb-2 border rounded">
            <input type="file" name="background_image" 
                   class="form-control border-dark" 
                   accept="image/*" 
                   onchange="previewImage(this, 'backgroundPreview')">
        </div>

        {{-- PNG Image --}}
        <div class="mb-3">
            <label class="form-label fw-semibold text-black d-block">Current PNG Image</label>
            @if($banner->png_image)
                <img id="pngPreview" 
                     src="{{ asset('storage/' . $banner->png_image) }}" 
                     width="120" class="mb-2 border rounded">
            @else
                <p class="text-muted">No PNG image</p>
                <img id="pngPreview" src="" width="120" class="mb-2 border rounded" style="display:none;">
            @endif
            <input type="file" name="png_image" 
                   class="form-control border-dark" 
                   accept="image/*" 
                   onchange="previewImage(this, 'pngPreview')">
        </div>

        <button class="btn btn-dark px-4 py-2">
            <i class="bi bi-pencil-square me-2"></i> Update Banner
        </button>
    </form>
</div>

{{-- Include Bootstrap Icons if not already --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

{{-- Image Preview Script --}}
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style scoped>
/* Black & White Theme for Form */
.form-control.border-dark {
    border-width: 1px !important;
    border-color: #000 !important;
    color: #000;
}

.form-control::placeholder {
    color: #6c757d;
}

.btn-dark {
    background-color: #000;
    color: #fff;
    border: 1px solid #000;
    transition: all 0.3s ease;
}

.btn-dark:hover {
    background-color: #fff;
    color: #000;
    border-color: #000;
}

.btn-outline-dark {
    color: #000;
    border: 1px solid #000;
    transition: all 0.3s ease;
}

.btn-outline-dark:hover {
    background-color: #000;
    color: #fff;
}

/* Image Styling */
img.border {
    border: 1px solid #000;
    border-radius: 8px;
}
</style>
@endsection
