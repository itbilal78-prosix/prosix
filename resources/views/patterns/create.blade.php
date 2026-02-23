@extends('layouts.dashboard')
@section('content')
<div class="container mt-4">
    <h2>Add Pattern</h2>
    <form action="{{ route('patterns.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control  text-black border-secondary" required>
        </div>
        <div class="mb-3">
            <label>SVG File</label>
            <input type="file" name="svg" id="svgInput" class="form-control text-black border-secondary" accept=".svg" required>
            <img id="svgPreview" class="mt-2 d-none" style="max-width:150px; border:1px solid #444;">
        </div>
        <button type="submit" class="btn btn-dark">Save</button>
    </form>
</div>

<script>
document.getElementById('svgInput').addEventListener('change', function() {
    const preview = document.getElementById('svgPreview');
    if(this.files && this.files[0]){
        preview.src = URL.createObjectURL(this.files[0]);
        preview.classList.remove('d-none');
    }
});
</script>
@endsection
