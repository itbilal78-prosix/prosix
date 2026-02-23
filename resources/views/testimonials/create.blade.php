@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">

    <h2 class="text-black mb-4">Create New Testimonial</h2>

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="alert alert-dark border border-dark mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li class="text-danger">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('testimonials.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-white">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control border-dark" value="{{ old('name') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Position <span class="text-danger">*</span></label>
                <input type="text" name="position" class="form-control border-dark" value="{{ old('position') }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Rating (1-5) <span class="text-danger">*</span></label>
                <input type="number" name="rating" min="1" max="5" class="form-control border-dark" value="{{ old('rating',5) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-black">Image</label>
                <input type="file" name="image" class="form-control border-dark" accept="image/*" onchange="previewImage(this,'imagePreview')">
                <div class="mt-2">
                    <img id="imagePreview" style="display:none;" width="150" class="border rounded">
                </div>
                <small class="text-muted">Recommended size: 400x400 px</small>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label fw-semibold text-black">Testimonial Text <span class="text-danger">*</span></label>
                <textarea name="text" rows="5" class="form-control border-dark" required>{{ old('text') }}</textarea>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-dark px-4 py-2">💾 Save Testimonial</button>
            <a href="{{ route('testimonials.index') }}" class="btn btn-outline-dark px-4 py-2">Cancel</a>
        </div>
    </form>
</div>

<script>
function previewImage(input, previewId){
    const preview = document.getElementById(previewId);
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style scoped>
.form-control.border-dark {
    border: 1px solid #000;
    color: #000;
}
.form-control::placeholder { color: #6c757d; }
.btn-dark { background-color: #000; color:#fff; border:1px solid #000; transition:0.3s; }
.btn-dark:hover { background-color:#fff; color:#000; }
.btn-outline-dark { border:1px solid #000; color:#000; transition:0.3s; }
.btn-outline-dark:hover { background-color:#000; color:#fff; }
img.border { border:1px solid #000; border-radius:8px; }
</style>

@endsection
