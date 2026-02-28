@extends('layouts.dashboard')
@section('content')
<div class="container mt-4 d-flex justify-content-center">

    <div class="card shadow-sm p-4" style="max-width: 600px; width: 100%;">
        <h2 class="text-center mb-4">
            {{ isset($flipbook) ? 'Edit Flipbook' : 'Add Flipbook' }}
        </h2>

        <form action="{{ isset($flipbook) ? route('admin.flipbooks.update',$flipbook->id) : route('admin.flipbooks.store') }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($flipbook))
                @method('PUT')
            @endif

            {{-- Title --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Title</label>
                <input type="text" name="title" class="form-control"
                       value="{{ $flipbook->title ?? old('title') }}" required>
            </div>

            {{-- File Upload --}}
            <div class="mb-3">
                <label class="form-label fw-bold">PDF File</label>

                <div class="file-upload-box"
                     onclick="document.getElementById('fileInput').click()">
                    <span id="file-name">
                        {{ isset($flipbook) ? basename($flipbook->file_path) : 'Click to upload PDF' }}
                    </span>
                    <input type="file" name="file" id="fileInput"
                           accept=".pdf" required>
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100">
                {{ isset($flipbook) ? 'Update' : 'Create' }}
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('fileInput').addEventListener('change', function(e){
    const fileName = e.target.files[0]?.name || 'Click to upload PDF';
    document.getElementById('file-name').innerText = fileName;
});
</script>

<style>
.file-upload-box {
    border:2px dashed #999;
    padding:30px;
    text-align:center;
    cursor:pointer;
}
.file-upload-box input { display:none; }
</style>
@endsection
