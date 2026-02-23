@extends('layouts.dashboard')
@section('content')
<div class="container mt-4 d-flex justify-content-center">

    <div class="card shadow-sm p-4" style="max-width: 500px; width: 100%; border-radius: 10px;" style="border-color: white">

        <h2 class="text-center mb-4">Edit Font</h2>

        <form action="{{ route('fonts.update', $font->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Font Name --}}
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Font Name</label>
                <input type="text" name="name" class="form-control" value="{{ $font->name ?? old('name') }}" required>
            </div>

            {{-- File Upload --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Font File (ttf, otf, woff)</label>

                <div class="file-upload-box" onclick="document.getElementById('fileInput').click()">
                    <span id="file-name">{{ $font->file ? basename($font->file) : 'Click to upload font file' }}</span>
                    <input type="file" name="file" id="fileInput" accept=".ttf,.otf,.woff,.woff2">
                </div>
            </div>

            {{-- Submit + Cancel Buttons --}}
            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-success w-45 font-btn">Update</button>
                <a href="{{ route('fonts.index') }}" class="btn btn-secondary w-45">Cancel</a>
            </div>

        </form>
    </div>
</div>

{{-- Script to show selected file name --}}
<script>
document.getElementById('fileInput').addEventListener('change', function(e){
    const fileName = e.target.files[0]?.name || 'Click to upload font file';
    document.getElementById('file-name').innerText = fileName;
});
</script>

{{-- Styles --}}
<style>
.file-upload-box {
    border: 2px dashed #6c757d;
    border-radius: 6px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    color: #6c757d;
    font-weight: 500;
}

.file-upload-box:hover {
    background-color: #f8f9fa;
    border-color: #495057;
}

.file-upload-box input[type="file"] {
    display: none;
}

/* Submit button light/dark mode */
.font-btn {
    transition: all 0.2s;
}

/* Light mode */
@media (prefers-color-scheme: light) {
     .font-btn {
        background-color: #141414;
        color: #fff;
        border-color: #ffffff;
    }
    .font-btn:hover {
        background-color: #ffffff;
        border-color: #000000;
        color: black;
    }
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
     .font-btn {
        background-color: #141414;
        color: #fff;
        border-color: #141414;
    }
    .font-btn:hover {
        background-color: #141414;
        border-color: #141414;
    }
}

/* Cancel button */
.btn-secondary {
    transition: 0.2s;
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}
.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}
.w-45 {
    width: 45%;
}
</style>
@endsection
