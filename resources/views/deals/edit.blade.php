@extends('layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h1 class="text-black mb-4">Edit Deal</h1>

    <form action="{{ route('deals.update', $deal) }}" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
        @csrf
        @method('PUT')

        <!-- Title -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Title *</label>
            <input type="text" name="title" class="form-control border-dark text-black" value="{{ old('title', $deal->title) }}" required>
        </div>

        <!-- Subtitle -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Subtitle</label>
            <input type="text" name="subtitle" class="form-control border-dark text-black" value="{{ old('subtitle', $deal->subtitle) }}">
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Description</label>
            <textarea name="description" class="form-control border-dark text-black" rows="4">{{ old('description', $deal->description) }}</textarea>
        </div>

        <!-- Button Text -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Button Text</label>
            <input type="text" name="button_text" class="form-control border-dark text-black" value="{{ old('button_text', $deal->button_text) }}">
        </div>

        <!-- Button Link -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Button Link</label>
            <input type="text" name="button_link" class="form-control border-dark text-black" value="{{ old('button_link', $deal->button_link) }}">
        </div>

        <!-- Existing Images Editable -->
        <div class="mb-3">
            <label class="form-label fw-semibold text-black">Existing Images (max 6)</label>
            <div class="row">
                @foreach($deal->images as $img)
                <div class="col-md-3 mb-3 text-center">
                    <!-- Current Image Preview -->
                    <img src="{{ $img->image_path }}" class="img-fluid img-thumbnail mb-2" style="width:100px; height:100px; object-fit:cover;" id="preview{{ $img->id }}">
                    
                    <!-- Replace Image -->
                    <input type="file" name="replace_images[{{ $img->id }}]" class="form-control border-dark text-black mb-1" accept="image/*" onchange="previewImage(event, {{ $img->id }})">
                    
                    <!-- Image Link -->
                    <input type="text" name="existing_links[{{ $img->id }}]" class="form-control border-dark text-black mb-1" value="{{ $img->link }}" placeholder="Image Link">
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-dark">Update Deal</button>
        <a href="{{ route('deals.index') }}" class="btn btn-outline-dark">Cancel</a>
    </form>
</div>

<script>
function previewImage(event, id) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('preview' + id);
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

<style scoped>
.text-black { color: #000 !important; }

.form-control.border-dark {
    border: 1px solid #000 !important;
    color: #000 !important;
    background-color: #fff;
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
.img-thumbnail {
    border: 1px solid #000;
    border-radius: 8px;
}
</style>
@endsection
