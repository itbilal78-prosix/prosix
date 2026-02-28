@extends('layouts.dashboard')

@section('content')
<div class="container mt-4 d-flex justify-content-center">

    <div class="card shadow-sm p-4"
         style="max-width:600px;width:100%;border-radius:10px;">

        <h2 class="text-center mb-4">Edit Flipbook</h2>

        <form action="{{ route('admin.flipbooks.update',$flipbook->id) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

            {{-- Title --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Title</label>
                <input type="text"
                       name="title"
                       class="form-control"
                       value="{{ old('title',$flipbook->title) }}"
                       required>
            </div>

            {{-- Current File Preview --}}
            @if($flipbook->file_path)
            <div class="mb-3">
                <label class="form-label fw-bold">Current File</label>

                <div class="border rounded p-2">
                    <iframe src="{{ asset('storage/'.$flipbook->file_path) }}"
                            width="100%" height="250">
                    </iframe>
                </div>

                <small class="text-muted">
                    Leave empty if you don't want to change file
                </small>
            </div>
            @endif

            {{-- New File Upload --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Replace File (PDF)</label>

                <div class="file-upload-box"
                     onclick="document.getElementById('fileInput').click()">

                    <span id="file-name">Click to upload new PDF</span>

                    <input type="file"
                           name="file"
                           id="fileInput"
                           accept=".pdf"
                           hidden>
                </div>
            </div>

            <button type="submit"
                    class="btn btn-dark w-100">
                Update Flipbook
            </button>

        </form>

    </div>
</div>

<script>
document.getElementById('fileInput').addEventListener('change', function(e){
    const fileName = e.target.files[0]?.name || 'Click to upload new PDF';
    document.getElementById('file-name').innerText = fileName;
});
</script>

<style>
.file-upload-box {
    border:2px dashed #6c757d;
    border-radius:6px;
    padding:30px;
    text-align:center;
    cursor:pointer;
    transition:0.2s;
    color:#6c757d;
    font-weight:500;
}
.file-upload-box:hover {
    background:#f8f9fa;
}
</style>

@endsection
