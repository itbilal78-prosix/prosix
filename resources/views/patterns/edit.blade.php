@extends('layouts.dashboard')
@section('content')
<div class="container mt-4">
    <h2>Edit Pattern</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('patterns.update', $pattern->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control  text-black border-secondary"
                   value="{{ old('name', $pattern->name) }}" required>
        </div>

        <div class="mb-3">
            <label>Current SVG</label>
            <div class="mb-2">
                <img src="{{ asset('storage/' . $pattern->svg_path) }}" width="100" style="border:1px solid #444;">
            </div>
        </div>

        <div class="mb-3">
            <label>Replace SVG (optional)</label>
            <input type="file" name="svg" id="svgEditInput" class="form-control  text-black border-secondary" accept=".svg">
            <small class="text-muted">Leave empty to keep current SVG.</small>
            <img id="svgEditPreview" class="mt-2 d-none" style="max-width:150px; border:1px solid #444;">
        </div>

        <button type="submit" class="btn btn-dark">Update Pattern</button>
        <a href="{{ route('patterns.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
document.getElementById('svgEditInput').addEventListener('change', function() {
    const preview = document.getElementById('svgEditPreview');
    if(this.files && this.files[0]){
        preview.src = URL.createObjectURL(this.files[0]);
        preview.classList.remove('d-none');
    }
});
</script>
@endsection
