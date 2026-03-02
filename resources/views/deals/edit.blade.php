@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h1 class="text-black mb-4">Edit Deal</h1>

    <form action="{{ route('deals.update', $deal) }}"
          method="POST"
          enctype="multipart/form-data"
          class="p-4 border rounded shadow-sm bg-white">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Title *</label>
            <input type="text" name="title"
                   class="form-control border-dark text-black"
                   value="{{ old('title', $deal->title) }}" required>
        </div>

        <!-- Subtitle -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Subtitle</label>
            <input type="text" name="subtitle"
                   class="form-control border-dark text-black"
                   value="{{ old('subtitle', $deal->subtitle) }}">
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Description</label>
            <textarea name="description"
                      class="form-control border-dark text-black"
                      rows="4">{{ old('description', $deal->description) }}</textarea>
        </div>

        <!-- Button -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Button Text</label>
                <input type="text" name="button_text"
                       class="form-control border-dark text-black"
                       value="{{ old('button_text', $deal->button_text) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Button Link</label>
                <input type="text" name="button_link"
                       class="form-control border-dark text-black"
                       value="{{ old('button_link', $deal->button_link) }}">
            </div>
        </div>

        <hr>

        <!-- ================= IMAGES SECTION ================= -->
        <h5 class="fw-bold text-black mb-3">Deal Images</h5>

        <div class="row">
            @foreach($deal->images as $img)
            <div class="col-md-3 mb-4 text-center">

                <div class="position-relative image-box">
                    <img src="{{ $img->image_path }}"
                         class="img-fluid img-thumbnail mb-2"
                         id="preview{{ $img->id }}">
                </div>

                <input type="file"
                       name="replace_images[{{ $img->id }}]"
                       class="form-control border-dark mb-2"
                       accept="image/*"
                       onchange="previewImage(event, {{ $img->id }})">

                <input type="text"
                       name="existing_links[{{ $img->id }}]"
                       class="form-control border-dark"
                       value="{{ $img->link }}"
                       placeholder="Image Link">
            </div>
            @endforeach
        </div>

        <!-- Add New Images -->
        <div class="mb-4">
            <label class="form-label fw-semibold text-black">
                Add New Images
            </label>
            <input type="file"
                   name="images[]"
                   multiple
                   class="form-control border-dark"
                   accept="image/*">
        </div>

        <hr>

        <!-- ================= BANNERS SECTION ================= -->
        <h5 class="fw-bold text-black mb-3">Deal Banners</h5>

        <div class="row">
            @foreach($deal->banners as $banner)
            <div class="col-md-4 mb-4 text-center">

                <img src="{{ $banner->image_path }}"
                     class="img-fluid img-thumbnail mb-2"
                     id="bannerPreview{{ $banner->id }}">

                <input type="file"
                       name="replace_banners[{{ $banner->id }}]"
                       class="form-control border-dark"
                       accept="image/*"
                       onchange="previewBanner(event, {{ $banner->id }})">

            </div>
            @endforeach
        </div>

        <!-- Add New Banners -->
        <div class="mb-4">
            <label class="form-label fw-semibold text-black">
                Add New Banners
            </label>
            <input type="file"
                   name="banners[]"
                   multiple
                   class="form-control border-dark"
                   accept="image/*">
        </div>

        <button type="submit" class="btn btn-dark">Update Deal</button>
        <a href="{{ route('deals.index') }}" class="btn btn-outline-dark">Cancel</a>

    </form>
</div>

<script>
function previewImage(event, id) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('preview' + id).src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

function previewBanner(event, id) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('bannerPreview' + id).src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endsection
